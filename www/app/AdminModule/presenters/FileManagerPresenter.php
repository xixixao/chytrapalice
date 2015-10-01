<?php



class Admin_FileManagerPresenter extends Admin_BasePresenter
{   
  private $arr;  
  
	public function deleteClicked(SubmitButton $button)
  {
    $arr=$button->getForm()->getValues();
    $files[] = $arr['file'][0];   
    $miniature = WWW_DIR . "/attachments/mini/" . basename($files[0]);    	    
    if(is_file($miniature)){
      $files[] = $miniature;        
    }          
    $this->fileModel->deleteFiles($files);
		$this->arr = $files;
	}  
	public function renderDefault()
	{ 
	
	/*
{if(count($deletedFiles)>0)}
<p>
  {foreach $deletedFiles as $file}
    [php echo preg_replace('~.*attachments/~','',$file,1); ] byl smazán<br>
  {/foreach}
</p>
{/if}
*/
	
	
    //$this->template->deletedFiles = $this->arr;               
    // retrieving data
		$this->template->files = $this->fileModel->getAllFiles();
		$this->template->form = $this['deleteForm'];				
	}
  protected function createComponentDeleteForm()
  {    
    $array = $this->fileModel->getAllFiles();
  
    $form = new AppForm;
    $form->addCheckBoxList('file','files:',$array['ids']);
    $form->addSubmit('delete', 'Smazat soubory')
        ->onClick[] = array($this, 'deleteClicked');
    return $form;
  }	
}
