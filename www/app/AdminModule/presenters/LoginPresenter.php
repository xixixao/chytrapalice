<?php



class Admin_LoginPresenter extends BasePresenter
{
  /** @persistent */
  public $error="";
  
	public function okClicked($button){
	  $arr = $button->getForm()->getValues();
	  $username = $arr['userName'];
	  $password = $arr['password'];
	  
    $user = Environment::getUser();
    // zaregistrujeme autentizační handler
    $credits = (array)Environment::getConfig('admin');
    $user->setAuthenticationHandler(new SimpleAuthenticator(array($credits['username']=>$credits['password'])));
    try {
            // pokusíme se přihlásit uživatele...
            $user->authenticate($username, $password);
            $this->redirect(':Admin:Default:');
    } catch (AuthenticationException $e) {
            $this->error =         $e->getMessage();
            $this->redirect('this');
    } 
  }
  public function handleLogout(){    
    $user = Environment::getUser();
    
    // odhlášení
    $user->signOut();
    $this->redirect(':Front:Default:'); 
  }
	public function renderDefault()
	{
    $this->template->form = $this['loginForm'];
    $this->template->error = $this->error;         
	}
	
	protected function createComponentLoginForm()
  {    
    $form = new AppForm;
    $form->addText('userName', 'Uživatelské jméno:')
      ->addRule(Form::FILLED, "Uživatelské jméno musí být zadáno");
    $form->addPassword('password', 'Heslo:')
      ->addRule(Form::FILLED, "Heslo musí být zadáno");
    $form->addSubmit('ok', 'Přihlásit')
        ->onClick[] = array($this, 'okClicked');
    return $form;
  }
   
} 
  


