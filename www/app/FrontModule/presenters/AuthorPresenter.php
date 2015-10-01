<?php



class Front_AuthorPresenter extends Front_BasePresenter
{
  
  protected $id;
  
  public function actionDefault($url){
    $this->id = $url;
    $res = dibi::query('
          SELECT
            authorId,
            name,
            surname,
            class,',
            Model::sqlClassName(Model::getSchoolYear()) . 'as classMark,',
            Model::sqlSumWorks().' as sum                        
            FROM [authors]                      
            WHERE `authorUrl`=%s',$url
    )->fetchAll();
    $this->template->data = $res[0];
    $this->template->sum = $this->formCount($res[0]['sum'], 'prací','práce','práce','prací');
  }
  
  protected function formCount($pocet, $nula, $jeden, $malo, $hodne) {
    if (!$pocet) return "0 ".$nula;
    elseif ($pocet==1) return $pocet." ".$jeden;
    elseif ($pocet<5) return $pocet." ".$malo;
    else return $pocet." ".$hodne;
  }
  protected function createComponentWorks($name)
  {
  //url as link,
            //title,
  
      $grid = new DataGrid;                     
      $grid->bindDataTable(
        //Model::worksSource
        dibi::getConnection()->dataSource(
          'SELECT
          url as link,
          authorUrl,
          title,
          award,
          year,',
          Model::sqlWorkClassName() . 'as workClass,',
          'type,                                     
          pages,
          [read]
          FROM [works]
          JOIN [authors] on author = authorId 
          WHERE authorUrl=%s', $this->id
          
        )
      );
      $grid->addColumn('title', 'Název')->addDefaultSorting('asc')->getCellPrototype()->addClass('first');;
      $grid->addNumericColumn('year', 'Rok')->getCellPrototype()->style('text-align: center;');
      $grid->addColumn('workClass', 'Třída')->getCellPrototype()->style('text-align: center;');
      $grid->addColumn('award', 'Ocenění');
      $grid->addColumn('type', 'Typ');
      $grid->addNumericColumn('pages', 'Stran')->getCellPrototype()->style('text-align: right');
      $grid->addColumn('read', 'Čtenost')->getCellPrototype()->style('text-align: right;');      
      
      /*
      $grid->addActionColumn('Akce');
      $grid['title']->addFilter();
      $grid['authorName']->addFilter();
      $grid['year']->addSelectboxFilter();
      $grid['award']->addSelectboxFilter();
      $grid['type']->addSelectboxFilter();
      $grid['pages']->addSelectboxFilter();  */
                                         
      $grid->itemsPerPage = 0;
      $grid->multiOrder = FALSE;
      
      $renderer = $grid->getRenderer();
      $renderer->paginatorFormat = '%label% %input% z %count%';
      $renderer->infoFormat = 'Práce %from% - %to% z %count% | Zobrazit: %selectbox% | %reset%';
      $renderer->onRowRender[]  = array($this, 'worksOnRowRendered');
      //$renderer->onCellRender[] = array($this, 'gridOnCellRendered');      
      //$grid->rememberState = TRUE;
      return $grid;
      //$this->addComponent($grid, $name);
  }
  public function worksOnRowRendered(Html $row, DibiRow $data){
    $this->placeLink($row, $data, "Work");
  } 
  public function placeLink(Html $row, DibiRow $data, $pres)
  {
      foreach($row->getChildren() as $cell){
        $inside = $cell->getText();
        $cell->setText('');
        $cell->add(Html::el('a')->href($this->link(":Front:$pres:", $data['link']))->setText($inside));        
      }                       
  }
 
}
      //
      //
