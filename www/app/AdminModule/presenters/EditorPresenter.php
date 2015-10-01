<?php



class Admin_EditorPresenter extends Admin_BasePresenter
{ 

  public function handleAddWork($id){
    $s = Environment::getSession('workform');
    $s->author = $id;    
    $this->redirect(':Admin:Editor:work');
  } 
	public function actionAuthor($id=''){
	  if($id != ''){
	    $this['authorForm']->setEdit($id);
	    
      $this->template->edit = true;
      $this->template->url = dibi::query('SELECT authorUrl FROM authors WHERE authorId=%i',$id)->fetchSingle();
      $this['workList']->where['authorId']=$id;      
    }	  
  }
  public function actionWork($id=''){
	  if($id != ''){
	    $this['workForm']->setEdit($id);
	    $in = $this['workForm']->getForm();
      $in['ok']->onClick[] = array($this, 'saveClicked');
	    
	    //$this->template->id = $id 
      $this->template->edit = true;
      $this->template->url = dibi::query('SELECT url FROM works WHERE workId=%i',$id)->fetchSingle();
    }  
  }
	
	protected function createComponentAuthorForm($name)
  {        
    $form = new AuthorForm;    
    return $form;
  }
  
  protected function createComponentWorkForm($name)
  {        
    $form = new WorkForm;            
    return $form;
  }
  
  protected function createComponentWorkList($name)
  {        
    $list = new WorkList;
    $list->advanced = false;    
    return $list;
  }  
  
  protected function createComponentHomeForm($name)
  {        
    $form = new AppForm;
    $form->addTextArea('text', 'Text')
      ->addRule(Form::FILLED, "Bez textu zůstane úvodní strana prázdná")->getControlPrototype()->style = 'width: 685px; height: 370px';
    $form->addHidden('id');                    
    $dataSource = dibi::query('SELECT id, text FROM [options] WHERE [name]=%s', 'home')->fetch();
    if($dataSource != null){            
      $form->setDefaults($dataSource);
    }             
    $form->addSubmit('ok', 'Uložit')
        ->onClick[] = array($this, 'saveClicked');     
            
    return $form;
  }
  
  public function saveClicked(SubmitButton $button)
  {
    $array = $button->getForm()->getValues();
    if($array['id']!=''){
      Model::save($array, 'id', 'options');
    } else {
      $array['name'] = 'home'; 
      Model::add($array, 'options');
    }    
    $this->flashMessage('Text uložen.', 'info');
    $this->presenter->redirect("this");
  }
} 
  

