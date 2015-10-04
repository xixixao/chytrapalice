<?php



class Front_DefaultPresenter extends Front_BasePresenter
{

    protected $where = array();
    protected $order = array();
    protected $grade;
    protected $palicka;
    protected $palice;
    protected $celaPalicka;
    protected $search;
    protected $searchFull;
    protected $searchFulltext;

    public function actionSearch($text, $fulltext){
      $search = $text;
      $this->search = $search;//"MATCH(title) AGAINST('".$search."')";
      $this->template->search = $search;
      $this->template->fulltext = ($fulltext)? TRUE : FALSE;
      if($fulltext){
        $this->searchFulltext = $search;
      }else{
        $words = explode(" ", $this->search);
        $long = array();
        $short = array();
        foreach($words as $key=>$word){
          $eng = "";
          if(strlen($eng = Model::getEnglishAlpha($word)) < 4){
            $short[] = $eng;
            if($eng != $word) $short[] = $word;
          }else{
            $long[] = $cut = substr($word, 0, 3 + strlen($word) - strlen($eng));
            if($cut != ($engCut = substr($eng,0,3))) $long[] = $engCut;
          }
        }
        $this->search = $long;
        $this->searchFull = $short;
      }
    }
    public function renderSearch($search, $fulltext){
      $this->template->worksCount = $this['works']->dataSource->count();
      if(!$fulltext) $this->template->authorsCount = $this['authors']->dataSource->count();
    }

    public function actionWorks($year=NULL, $award=NULL, $type=NULL, $grade=NULL, $category=NULL){

      $filter = NULL;
      if ($year){ $this->where['year'] = $year; $filter = "Chytrá palice " . $year; }
      if ($award){ $this->where['award'] = $award; $filter = "Získané ocenění - " . $award; }
      if ($type){ $this->where['type'] = $type; $filter = "Typ práce - " . $type; }

      if ($grade){ $this->grade = array_search($grade, Model::rocniky()); $filter = "Napsány v ročníku - " . $grade; }

      if($category){
        if ($category == "palice"){
          $this->palice = true;
          $filter = "Chytrá palice";
        } else if($category == "palicka"){
          $this->celaPalicka = true;
          $filter = "Chytrá palička";
        }else {
          $this->palicka = array_search($category,Model::palicky());
          $filter = "Chytrá palička, kategorie: " . $category;
        }
      }
      $this->template->filter = $filter;
    }
    public function actionAuthors($class=NULL){
      if ($class){ $this->where['class'] = $class; $filter = "Maturitní ročník " . $class; }
      $this->template->filter = $filter;
    }
    public function actionNewest(){
      $this->order = array("added"=>"desc");
    }
    public function actionMostread(){
      $this->order = array("read"=>"desc");
    }
    public function actionDefault(){
      $texy = new Texy();
      $this->template->text = $texy->process(dibi::query('SELECT text FROM [options] WHERE [name]=%s','home')->fetchSingle());
    }


//|Nazev|Autor|Rocnik|Oceneni|Typ|Pocet stran
  protected function createComponentWorks($name)
  {
  //url as link,
            //title,

      $grid = new DataGrid;
      $search = NULL;
      if(isset($this->searchFull)){
        $search = "";
        foreach($this->search as $one){
          $search .= "title RLIKE '(^| )".$one."' OR ";
        }
        foreach($this->searchFull as $one){
          $search .= "title RLIKE '(^| )".$one." ' OR ";
        }
        $search = substr($search, 0, -3);
      }


      $grid->bindDataTable(
        //Model::worksSource
        dibi::getConnection()->dataSource(
          'SELECT
          url as link,
          title,
          class,',
          Model::sqlCategory().' as category,',
          Model::sqlWorkClassName() . 'as grade,',
          'CONCAT_WS(" ", name, surname) as authorName,',
          $this->formColumns(array('year', 'award', 'type')),
          'pages,
          [read]
          FROM [works]
          join [authors] on author = authorId',
          '%if', isset($this->palice), 'AND class < [year] + 4', '%end',
          '%if', isset($this->palicka), 'AND class=[year]+'.(7 - $this->palicka), '%end',
          '%if', isset($this->celaPalicka), 'AND class >= [year] + 4', '%end',
          '%if', isset($this->grade), 'AND class=[year]+'.(3 - $this->grade), '%end',
          'WHERE %and', $this->where,
          '%if', isset($search), 'AND %sql', $search, '%end',//"MATCH(title) AGAINST('".$search."')";
          '%if', isset($this->searchFulltext), 'MATCH(text) AGAINST(%s)', $this->searchFulltext, '%end',
          '%if', count($this->order) > 0, 'ORDER BY %by', $this->order, '%end'

        )
      );
      $grid->addColumn('title', 'Název')->getCellPrototype()->addClass('first');
      if(count($this->order) ==0) $grid['title']->addDefaultSorting();
      $grid->addColumn('authorName', 'Autor');

      if($this->notCol('year') && !isset($this->order['read'])) $grid->addNumericColumn('year', 'Rok')->getCellPrototype()->style('text-align: center;');

      //if(!isset($this->palicka) && !isset($this->palice) ) $grid->addColumn('category', 'Kategorie');
      $grid->addColumn('grade', 'Třída');

      if($this->notCol('award')) $grid->addColumn('award', 'Ocenění');

      if($this->notCol('type')) $grid->addColumn('type', 'Typ');

      $grid->addNumericColumn('pages', 'Stran')->getCellPrototype()->style('text-align: right; padding-left: 0px; padding-right: 5px');

      if(isset($this->order['read'])) $grid->addColumn('read', 'Čtenost')->getCellPrototype()->style('text-align: right;');


      //if($this->advanced){
        $grid->addActionColumn('Akce');
        $grid['title']->addFilter();
        $grid['authorName']->addFilter();
        if(isset($grid['year'])) $grid['year']->addSelectboxFilter();
        if(isset($grid['award'])) $grid['award']->addSelectboxFilter();
        if(isset($grid['category'])) $grid['category']->addSelectboxFilter();
        if(isset($grid['type'])) $grid['type']->addSelectboxFilter();
        if(isset($grid['pages'])) $grid['pages']->addSelectboxFilter();
      //}

      //$grid->itemsPerPage = 1;
      $grid->multiOrder = FALSE;
      $grid->displayedItems = array('vše',5,8,10,20,50,100);
      $grid->itemsPerPage = $this->getCookiesItemsCount(8);
      $grid->nothingMessage = "Žádná práce nebyla nalezena.";
      $grid->hiddenFiltering = TRUE;

      $renderer = $grid->getRenderer();
      $renderer->paginatorFormat = '%label% %input% z %count%';
      $renderer->infoFormat = 'Práce %from% - %to% z %count% | Zobrazit: %selectbox% | %reset%';
      $renderer->onRowRender[]  = array($this, 'worksOnRowRendered');
      $renderer->onCellRender[] = array($this, 'worksOnCellRendered');
      //$grid->rememberState = TRUE;
      return $grid;
      //$this->addComponent($grid, $name);
  }

  protected function formColumns($array){
    $res = '';
    foreach($array as $col){
      if(!array_key_exists($col, $this->where)) $res .= $col . ', ';
    }
    return $res;
  }
  protected function notCol($name){
    return !array_key_exists($name,$this->where);
  }

  private function getCookiesItemsCount($default){
      if(isset($_COOKIE['itemsPerPage'])){
        return $_COOKIE['itemsPerPage'];
      }else{
        return $default;
      }
  }


//|Jméno|Příjmení|Maturita|Pocet praci
  protected function createComponentAuthors($name)
  {
      $grid = new DataGrid;

      $search = "";
      if(isset($this->searchFull)){
        foreach($this->search as $one){
          //$search .= "name LIKE '%".$one."%' OR surname LIKE '% ".$one."%' OR ";
          $search .= "name RLIKE '(^| )$one' OR surname RLIKE '(^| )$one' OR ";
        }
        foreach($this->searchFull as $one){
          $search .= "name RLIKE '(^| )".$one." ' OR surname RLIKE '(^| )".$one." ' OR ";
        }
        $search = substr($search, 0, -3);
      }

      $grid->bindDataTable(
        dibi::getConnection()->dataSource(
          'SELECT
            authorUrl as link,
            name,
            surname,',
            $this->formColumns(array('class')),
            Model::sqlClassName(Model::getSchoolYear()) . 'as classMark,',
            Model::sqlSumWorks() . 'as [sum],',
            Model::sqlSumReads() . 'as [read]',
            'FROM [authors]',
            'WHERE %and', $this->where,
            '%if', isset($search), 'AND %sql', $search, '%end'//"MATCH(title) AGAINST('".$search."')";
          )
      );
      $grid->addColumn('name', 'Jméno')->getCellPrototype()->addClass('first');
      $grid->addColumn('surname', 'Příjmení')->addDefaultSorting('asc');

      if($this->notCol('class'))  $grid->addColumn('class', 'Maturita')->getCellPrototype()->style('text-align: center;');

      $grid->addColumn('classMark', 'Třída')->getCellPrototype()->style('text-align: right;');

      $grid->addColumn('sum', 'Počet prací')->getCellPrototype()->style('text-align: right;');

      $grid->addColumn('read', 'Čtenost')->getCellPrototype()->style('text-align: right;');

      $grid->multiOrder = FALSE;
      $grid->displayedItems = array('vše',5,8,10,20,50,100);
      $grid->itemsPerPage = $this->getCookiesItemsCount(8);
      $grid->nothingMessage = "Žádný autor nebyl nalezen.";
      $grid->hiddenFiltering = TRUE;

      $renderer = $grid->getRenderer();
      $renderer->paginatorFormat = '%label% %input% z %count%';
      $renderer->infoFormat = 'Autoři %from% - %to% z %count% | Zobrazit: %selectbox% | %reset%';
      $renderer->onRowRender[]  = array($this, 'authorsOnRowRendered');
      //$renderer->onCellRender[] = array($this, 'gridOnCellRendered');

      return $grid;
      //$this->addComponent($grid, $name);
  }

  public function worksOnRowRendered(Html $row, DibiRow $data){
    $this->placeLink($row, $data, "Work", 'title');
  }
  public function authorsOnRowRendered(Html $row, DibiRow $data){
    $this->placeLink($row, $data, "Author", 'surname');
  }
  public function worksOnCellRendered(Html $cell, $column, $value){
    if($column=="category" && $value!="palice"){
      $cell->setText(Model::palicky($value));
    }
  }
  public function placeLink(Html $row, DibiRow $data, $pres, $titleField)
  {
      foreach($row->getChildren() as $cell){
        $inside = $cell->getText();
        $cell->setText('')->style .= "padding: 0px;";
        $cell->add(Html::el('a')
          ->href($this->link(":Front:$pres:", $data['link']))
          ->title($data[$titleField])
          ->setText($inside));
      }
  }
}

