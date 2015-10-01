<?php

/**
 * DataGrid wrapper
 */
class AuthorList extends Control implements ArrayAccess, INamingContainer
{
	public $advanced = false;

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
		Model::delete($id, "authorId", "authors");
		$array = dibi::query('SELECT workId FROM works WHERE author=%i', $id)->fetchAssoc();
		foreach($array as $val){
      FileModel::deleteFiles($val['workId']);  
    }
    Model::delete($id, "author", "works");
    
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
          authorId as id,
          name,
          surname,
          class,',
          Model::sqlClassName(Model::getSchoolYear()) . 'as classMark,
          (SELECT COUNT(workId) FROM [works] WHERE author=authorId) as sum                        
          FROM [authors]
        ')
    );
    $grid->addColumn('name', 'Jméno');
    $grid->addColumn('surname', 'Příjmení')->addDefaultSorting('asc');
    $grid->addColumn('class', 'Maturita')->getCellPrototype()->style('text-align: center;');;
    $grid->addColumn('classMark', 'Třída')->getCellPrototype()->style('text-align: right;');    
    $grid->addColumn('sum', 'Počet prací')->getCellPrototype()->style('text-align: right;')->class('pages');    
    $grid->addActionColumn('Akce');
    $grid->keyName = 'id';
    $grid->addAction('Smazat', 'delete!', NULL, FALSE, DataGridAction::WITH_KEY);
              
    $grid['name']->addFilter();
    $grid['surname']->addFilter();
    $grid['class']->addSelectboxFilter();
    $grid['classMark']->addSelectboxFilter();
    $grid['sum']->addSelectboxFilter();
    
    $grid->multiOrder = FALSE;
    $grid->itemsPerPage = $this->getCookiesItemsCount(15);
    
    $renderer = $grid->getRenderer();
    $renderer->paginatorFormat = '%label% %input% z %count%';
    $renderer->infoFormat = 'Autoři %from%. - %to%. z %count% | Zobrazit: %selectbox% | %reset%';
    $renderer->onRowRender[]  = array($this, 'OnRowRendered');
          
    //$grid->rememberState = TRUE;
    
    $this->addComponent($grid, $name);

		return;
	}

  public function OnRowRendered(Html $row, DibiRow $data){
    $this->placeLink($row, $data, "author");
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
        if($i!=count($row->getChildren())){
          $inside = $cell->getText();
          $cell->setText('')->style.="padding: 0px;";
          $cell->add(Html::el('a')->href($control->link(":Admin:Editor:$pres", $data['id']))->setText($inside));
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