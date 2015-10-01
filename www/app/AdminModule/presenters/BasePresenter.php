<?php



class Admin_BasePresenter extends BasePresenter
{
  /** @var fileModel */
	protected $fileModel;
	
	/** @var model */
	protected $model;
   
  protected function startup()
	{
		parent::startup();               
		$this->model = new Model;
		$this->fileModel = new FileModel;
		
		$user = Environment::getUser();

    if (!$user->isLoggedIn()) { // je uživatel přihlášen?
          $this->redirect(':Admin:Login:');
    } 
	} 
   
} 
  


