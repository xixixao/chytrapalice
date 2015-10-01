<?php

/**
 * DataGrid wrapper
 */
class WorkForm extends Control implements ArrayAccess, INamingContainer
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
  public function handleDeleteFile($name)
	{
		FileModel::delete($this->setId, $name);
    $this->flashMessage('Soubor odstraněn.', 'info');		
		$this->redirect('this');
	}
  public function fileSent(SubmitButton $button)
  {
    $array = $button->getForm()->getValues();
    FileModel::add($this->setId, $_FILES['file']);
    $this->flashMessage('Soubor byl přidán.', 'info');
    
    //$this->flashMessage($array['name']." ".$array['surname'] . ' byl přidán.');
    $this->redirect("this");
  }
  public function addClicked(SubmitButton $button)
  {  
    $array = $button->getForm()->getValues();
    if($array['authorId']=='true'){
      $array['author'] = dibi::query('SELECT authorId FROM authors ORDER BY authorId DESC LIMIT 1')->fetchSingle();
    }elseif($array['author']==0){
      $this->flashMessage('Je potřaba vybrat autora!', 'error');
      return;
    }
    unset($array['authorId']);
    try{
      $array['url'] = Model::createUri($array['title'], $array['author']);
    } catch (Exception $e) {      
      $this->flashMessage('U tohoto autora byla již vložena práce se stejným jménem', 'error');
      return;
    }
    $array['added%sql']='NOW()';
    $array['edited%sql']='NOW()';
        
    $array = $this->fixValues($array);
    $file = $array['file'];
    unset($array['file']);        
    $id = Model::add($array, 'works');
    if($file != '') FileModel::add($id, $_FILES['file']);
    
    $s = Environment::getSession('workform');
    $s->author = $array['author'];
    $s->award = $array['award'];
    $s->year = $array['year'];
    $s->type = $array['type'];
    
    $this->flashMessage('Práce byla přidána.', 'info');    
    $this->redirect("this");
  }
  
  public function saveClicked(SubmitButton $button)
  {
    $array = $button->getForm()->getValues();
    unset($array['authorId']);    
    try{
      $array['url'] = Model::createUri($array['title'], $array['author'],$array['workId']);
    } catch (Exception $e) {      
      $this->flashMessage('U tohoto autora byla již vložena práce se stejným jménem', 'error');
      return;
    } 
    $array = $this->fixValues($array);
    Model::save($array, 'workId', 'works');
    $this->flashMessage('Práce uložena.', 'info');
    $this->presenter->redirect(":Admin:Default:works");
  }
  
  protected function fixValues($array){  
    if($array['award'] == "0"){       
      $array['award'] = $array['newAward'];
    }
    if($array['type'] == "0"){       
      $array['type'] = $array['newType'];
    }
    unset($array['newAward']);
    unset($array['newType']);
    //$array['text'] = $this->sanitizeFromWord($array['text']);
    //$array['characters'] = strlen($array['text']);
    $array['characters'] = strlen(Model::getEnglishAlpha($array['text']));
    $array['pages'] = ceil($array['characters'] / 1800);
    //$array['words'] = str_word_count($array['text']);    
    $array['words'] = count( preg_split('/[\s,]+/', $array['text']));
    return $array;    
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

  
  protected function generateYears(){
    $years = array();
    for($i = Model::getSchoolYear()+1; $i >= 2000; $i--){
      $years[$i] = $i; 
    }
    return $years;
  }
  protected function setKeys($array){    
    $withKeys = array();
    foreach($array as $value){      
        $withKeys[$value] = $value;      
    }
    return $withKeys;
  }
	protected function createComponentForm($name)
	{		  	  
		$form = new AppForm;
    //$form->getElementPrototype()->id('editform');
    $form->addText('title', 'Název', 50)
      ->addRule(Form::FILLED, "Jméno je potřeba vyplnit");
                    
    $items = dibi::query('SELECT
        authorId,
        CONCAT_WS(" ", name, surname,', Model::sqlClassName(Model::getSchoolYear()),') as authorName
        FROM [authors]
        ORDER BY surname, class desc')->fetchPairs();           
            
    $keys = array_keys($items);        
    $form->addSelect('author', 'Autor', array("0"=>'Nový') + $items)->setValue($keys[0]);  
    $form->addTextArea('text', 'Text')->getControlPrototype()->style = 'width: 100%';
      
    $form->addSelect('award', 'Ocenění', array("0"=>'Nové') + $this->setKeys(Model::getValues('award', 'works')));      
    $form->addText('newAward', '');
    
    $form->addSelect('year', 'Ročník ChP',$this->generateYears())
      ->setDefaultValue(Model::getSchoolYear());
    
    $form->addSelect('type', 'Typ', array("0"=>'Nový') + $this->setKeys(Model::getValues('type', 'works')));
    $form->addText('newType', '');
    if($this->setId == '') $form->addFile('file',"Soubor:");      
    
    $form['newAward']->addConditionOn($form['award'], Form::EQUAL, 0)
        ->addRule(Form::FILLED, 'Pokud nevyberete ocenění, musíte zadat nové');        
    
    $form['newType']->addConditionOn($form['type'], Form::EQUAL, 0)
        ->addRule(Form::FILLED, 'Pokud nevyberete typ, musíte zadat nový');
        
    $form->addSubmit('ok', 'Přidat práci')
        ->onClick[] = array($this, 'addClicked');     
    
    $form->addHidden("authorId")->setValue('false');        
            
    if($this->setId != ''){
      $dataSource = dibi::query('SELECT * FROM [works] WHERE workId=%i',$this->setId)->fetch();	        
      $form->setDefaults($dataSource);
        
      $form['ok']->onClick = array(array($this, 'saveClicked'));
      $form['ok']->caption = "Uložit práci";
      $form->addHidden("workId")->setValue($this->setId);      
    }else{
      $s = Environment::getSession('workform');
      if(isset($s->author) && array_search($s->author, $keys) === FALSE){
        $s->author = $keys[0];
      }
      if(isset($s->author)){
        $form->setDefaults($s);
      }
    }
        
    $this->addComponent($form, $name);
    $form->getRenderer()->wrappers['control']['container'] .= ' class="wide"';    
    
		return;
	}
	
	protected function createComponentFileLoader($name)
	{		  	  
		if($this->setId != ''){
      $form = new AppForm;    
      $form->addFile('file',"Přidat soubor:");
      $form->addHidden("workId")->setValue($this->setId);          
      $form->addSubmit('fileSubmit', 'Přidat soubor')
          ->onClick[] = array($this, 'fileSent');
      $this->addComponent($form, $name);                            
    }        
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
	public function getForm($need = TRUE)
	{
		return $this->getComponent('form', $need);
	}

	/********************* backend *********************/
	public function __toString()
	{
		$template = $this->getTemplate();
		$template->setFile($this->file);
		$this->template->form = $this['form'];
		$this->template->schoolYear = Model::getSchoolYear();
		if($this->setId != ''){
		  $this->template->files = FileModel::getFiles($this->setId);
		  $this->template->loadLoader = TRUE;
		}
		$s = $template->__toString(TRUE);    
		return mb_convert_encoding($s, 'HTML-ENTITIES', 'UTF-8');		
	}
}