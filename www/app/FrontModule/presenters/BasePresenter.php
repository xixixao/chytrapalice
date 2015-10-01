<?php
class Front_BasePresenter extends BasePresenter
{
  public function beforeRender(){
  
    $filters = $this->getUniqueValues(array('works'=>array('year', 'award', 'type'), 'authors'=>array('class')));
    $temp = $filters['authors'];
    unset($filters['authors']);    
    //$filters['works']['category'] = Model::palicky();// + array(5=>'palice');    // 'category'=>'Kategorie',
    $filters['works']['grade'] = Model::rocniky();
    $filters['palicka']['category'] = Model::palicky();    
    $filters['authors'] = $temp;//{link :Front:Default:works "category"=>palicka}
    $this->template->filters = $filters;     
    $this->template->schoolYear = Model::getSchoolYear();
        
    $newest = dibi::query('SELECT
          url,
          title,
          CONCAT_WS(" ", name, surname) as authorName          
          FROM [works] 
          join [authors] on author = authorId',
          'ORDER BY %by', array('added'=>'desc'),
          'LIMIT %i', 2)->fetchAll();
          
    $this->template->newest = $newest;
    $mostread = dibi::query('SELECT
          url,
          title,
          CONCAT_WS(" ", name, surname) as authorName          
          FROM [works] 
          join [authors] on author = authorId',
          'ORDER BY %by', array('read'=>'desc'),
          'LIMIT %i', 3)->fetchAll();
    $this->template->mostread = $mostread;
    
    $user = Environment::getUser();
    if ($user->isLoggedIn()) {
        $this->template->adminMode = true;
    }
    
  }
  protected function getUniqueValues($array){
    $ret = array();
    foreach($array as $table=>$cols){
      foreach($cols as $col){
        $ret[$table][$col] = Model::getValues($col, $table);        
      }  
    }
    return $ret;
  }
}
?>






