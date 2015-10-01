<?php

/**
 * DataGrid wrapper
 */
class AuthorForm extends Control implements ArrayAccess, INamingContainer
{
	public $advanced = false;
  
  public $where = array();
  
	protected $file;
	
	protected $setId;
	
	 

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
  /*public function link($destination, $array = array()){
    if(strpos($destination, ":")!==FALSE){
      $control = $this->lookup('Nette\Application\Presenter', TRUE);
      return $control->link($destination, $array);
    } else {
      return parent::link($destination, $array); 
    }
  }*/
  
  public function addClicked(SubmitButton $button)
  {
    $array = $button->getForm()->getValues();    
    $array = $this->convertNumberToClass($array);
    $array['authorUrl'] = Model::createAuthorUri($array['name'], $array['surname'], $array['class']);
    Model::add($array, 'authors');    
    $this->flashMessage($array['name']." ".$array['surname'] . ' byl přidán.');
    $this->redirect("this");
    
  }
  
  public function saveClicked(SubmitButton $button)
  {
    $array = $button->getForm()->getValues();    
    $array = $this->convertNumberToClass($array);
    $array['authorUrl'] = Model::createAuthorUri($array['name'], $array['surname'], $array['class'], $array['authorId']);
    Model::save($array, 'authorId', 'authors');    
    $this->flashMessage('Změny pro autora:' . $array['name']." ".$array['surname'] . ' byly uloženy.');
    $this->presenter->redirect(":Admin:Default:authors");
  }
  
  protected function convertNumberToClass($array){
    if($array['number'] != 0){
      if(isset($array['r']) && $array['r'] === TRUE){
        $num = 8;
      }else{
        $num = 4;
      }
      //$num = (isset($array['r']) && $array['r']) ? 8 : 4;
      //$num = 4; 
      $array['class'] = $num - min($num,$array['number']) + Model::getSchoolYear();            
    }
    unset($array['number']);
    //$array['name'] = ucwords(mb_strtolower($array['name'], 'UTF-8'));
    //$array['surname'] = ucwords(mb_strtolower($array['surname'], 'UTF-8'));
    $array['name'] = $this->mb_ucwords($array['name'], 'UTF-8');
    $array['surname'] = $this->mb_ucwords($array['surname'], 'UTF-8');
    return $array;
  }
  
  protected function mb_ucwords($text, $locale){
    $array = explode(' ', mb_strtolower($text, 'UTF-8'));
    $ret = '';
    foreach($array as $word){
      //$ret .= mb_strtoupper(substr($word, 0, 1), 'UTF-8').substr($word, 1)." ";
      $ret .= mb_strtoupper(mb_substr($word,0,1, 'UTF-8'), 'UTF-8').mb_substr($word,1, strlen($word)-1, 'UTF-8')." ";
    }
    return substr($ret, 0, -1);
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
	protected function createComponentForm($name)
	{
		$form = new AppForm;
    $form->addGroup('general'); 
    //$form->getElementPrototype()->id('editform');
    $form->addText('name', 'Křestí a příp. prostřední jméno')
      ->addRule(Form::FILLED, "Jméno je potřeba vyplnit");
    $form->addText('surname', 'Příjmení')
      ->addRule(Form::FILLED, "Příjmení je potřeba vyplnit");
    $form->addCheckbox('r', 'Osmileté gymnázium (R)');
    $form->addSelect('mark','Písmeno třídy',array("A"=>"A", "B"=>"B", "C"=>"C", "D"=>"D"));
      
    $form->addGroup('left');    
    $form->addText('class', 'Maturitní ročník', 4);      
    
    $form->addGroup('right');    
    $form->addSelect('number','Číslo třídy',array("?", "1", "2", "3", "4", "5", "6", "7", "8"));
  
    $form['class']->addConditionOn($form['number'], Form::EQUAL, 0)
        ->addRule(Form::FILLED, 'Pokud nevyberete číslo třídy, musíte zadat maturitní ročník')
        ->addRule(Form::NUMERIC, 'Ročník musí být číslo')
        ->addRule(Form::RANGE, 'Rok maturity musí být od %d do %d', array(1960, 2999));
    
    $form->addGroup('submit');
    $form->addSubmit('ok', 'Přidat autora')
        ->onClick[] = array($this, 'addClicked');
            
    if($this->setId != ''){
      $dataSource = dibi::query('SELECT * FROM [authors] WHERE authorId=%i',$this->setId)->fetch();	        
      $form->setDefaults($dataSource);
        
      $form['ok']->onClick = array(array($this, 'SaveClicked'));
      $form['ok']->caption = "Uložit autora";
      $form->addHidden("authorId")->setValue($this->setId);
    }
        
    $this->addComponent($form, $name);

		return;
	}
	public function setEdit($id){
	  $this->setId = $id;
	  
  }

	/**
	 * Returns data grid's form component.
	 * @param  bool   throw exception if form doesn't exist?
	 * @return AppForm
	 */
/*	public function getForm($need = TRUE)
	{
		return $this->getComponent('dataGrid', $need);
	}
*/
	/********************* backend *********************/
	public function __toString()
	{
		$template = $this->getTemplate();
		$template->setFile($this->file);
		$this->template->form = $this['form'];
		$this->template->schoolYear = Model::getSchoolYear();
		$s = $template->__toString(TRUE);    
		return mb_convert_encoding($s, 'HTML-ENTITIES', 'UTF-8');
	}
}