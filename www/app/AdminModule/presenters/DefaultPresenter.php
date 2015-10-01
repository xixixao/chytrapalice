<?php



class Admin_DefaultPresenter extends Admin_BasePresenter
{
	
	//|Nazev|Autor|Rocnik|Oceneni|Typ|Pocet stran
  protected function createComponentWorkList($name)
  {        
    $list = new WorkList;
    $list->advanced = true;    
    return $list;
      
     /* $grid->bindDataTable(
        dibi::getConnection()->dataSource(
          'SELECT
            workId as link,
            title,
            CONCAT_WS(" ", name, surname) as authorName,                         
            FROM [works] 
            join [authors] on author = authorId
          ')
      );*/
      
  }
  
//|Jméno|Příjmení|Maturita|Pocet praci  
  protected function createComponentAuthors($name)
  {
    $list = new AuthorList;
    $list->advanced = true;    
    return $list;
  }

}
