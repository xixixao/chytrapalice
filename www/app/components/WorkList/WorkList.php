<?php

/**
 * DataGrid wrapper
 */
class WorkList extends Control implements ArrayAccess, INamingContainer
{
	public $advanced = false;
  
  public $where = array();
  
	protected $file;

	/**
	 * List constructor.
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
    
		$this->file = dirname(__FILE__) . '/list.phtml';		
	}

  /********************* signals *********************/
  
  public function handleDelete($id)
	{
		Model::delete($id, "workId", "works");
    FileModel::deleteFiles($id);	
		$this->redirect('this');
	}

  public function link($destination, $array = array()){
    if(strpos($destination, ":")!==FALSE){
      $control = $this->lookup('Nette\Application\Presenter', TRUE);
      return $control->link($destination, $array);
    } else {
      return parent::link($destination, $array); 
    }
  }

	/********************* rendering *********************/

	/**
	 * Renders data grid.
	 * @return void
	 */
	public function render()
	{		
		/*$template = $this->getTemplate();
		$template->setFile($this->file);
		$s = $template->__toString(TRUE);
		echo mb_convert_encoding($s, 'HTML-ENTITIES', 'UTF-8');*/
    echo $this->__toString();						
	}

	/********************* components handling *********************/

	/**
	 * Component factory.
	 * @see Nette/ComponentContainer#createComponent()
	 */
	protected function createComponentDataGrid($name)
	{
		$grid = new DataGrid;                         
    $grid->bindDataTable(      
      dibi::getConnection()->dataSource(
        'SELECT
        workId as id,        
        title,
        CONCAT_WS(" ", name, surname) as authorName,
        award,
        year,
        type,
        pages,
        added,
        edited
        FROM [works] 
        join [authors] on author = authorId',
        'WHERE %and', $this->where
      )
    );
    $grid->addActionColumn('Akce');
    $grid->addColumn('title', 'Název', 50)->addDefaultSorting('asc');
    if(!isset($this->where['authorId'])) $grid->addColumn('authorName', 'Autor');
    $grid->addNumericColumn('year', 'Ročník')->getCellPrototype()->style('min-width: 75px;');;
    $grid->addDateColumn('added', 'Vytvořena', '%d.%m.%Y');
    $grid->addDateColumn('edited', 'Upravena', '%H:%M:%S %d.%m.%Y')->getCellPrototype()->style('white-space: nowrap;');
    $grid->addColumn('award', 'Cena');                                    
    $grid->addColumn('type', 'Typ');
    $grid->addNumericColumn('pages', 'Stran')->getCellPrototype()->style('text-align: right;')->class = 'pages';
          
    
    $grid->keyName = 'id';
    $grid->addAction('Smazat', 'delete!', NULL, FALSE, DataGridAction::WITH_KEY);    
    
    
    if($this->advanced){          
      $grid['title']->addFilter();
      $grid['authorName']->addFilter();
      $grid['year']->addSelectboxFilter();
      $grid['award']->addSelectboxFilter();
      $grid['type']->addSelectboxFilter(); 
      $grid['type']->addSelectboxFilter();
      $grid['added']->addDateFilter();
      $grid['edited']->addDateFilter();
      if(isset($grid['pages'])) $grid['pages']->addSelectboxFilter();
    }
                                       
    //$grid->itemsPerPage = ;
    $grid->multiOrder = FALSE;
    $grid->itemsPerPage = $this->getCookiesItemsCount(15);
    
    $renderer = $grid->getRenderer();
    $renderer->paginatorFormat = '%label% %input% z %count%';
    $renderer->infoFormat = 'Práce %from% - %to% z %count% | Zobrazit: %selectbox% | %reset%';
    $renderer->onRowRender[]  = array($this, 'worksOnRowRendered');
    if(!$grid->paginator->itemCount) $renderer->wrappers['form']['.class'] .= " hidden";    
          
    //$grid->rememberState = TRUE;
    
    $this->addComponent($grid, $name);

		return;
	}

  public function worksOnRowRendered(Html $row, DibiRow $data){
    $this->placeLink($row, $data, "work");
  }
  
  private function getCookiesItemsCount($default){    
      if(isset($_COOKIE['itemsPerPage'])){
        return $_COOKIE['itemsPerPage'];      
      }else{
        return $default;
      }
  }
  
  
  public function placeLink(Html $row, DibiRow $data, $pres)
  {      
      $control = $this->lookup('Nette\Application\Presenter', TRUE);
      $i = 0;
      foreach($row->getChildren() as $cell){ 
        $i++;     
        if($i!=1){
          $inside = $cell->getText();
          $cell->setText('')->style.="padding: 0px;white-space: nowrap;";
          $cell->add(Html::el('a')->href($this->presenter->link(":Admin:Editor:$pres", $data['id']))->setText($inside));
        }
      }                       
  }

	/**
	 * Returns data grid's form component.
	 * @param  bool   throw exception if form doesn't exist?
	 * @return AppForm
	 */
	public function getDataGrid($need = TRUE)
	{
		return $this->getComponent('dataGrid', $need);
	}

	/********************* backend *********************/
	public function __toString()
	{
		$template = $this->getTemplate();
		$template->setFile($this->file);
		$s = $template->__toString(TRUE);    
		return mb_convert_encoding($s, 'HTML-ENTITIES', 'UTF-8');
	}
}