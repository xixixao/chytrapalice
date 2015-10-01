<?php



class Front_WorkPresenter extends Front_BasePresenter
{
  public function renderDefault($url){
    $res = dibi::query('
      SELECT
            workId,
            url as file,
            title,
            text,',
            Model::sqlCategory().' as category,
            authorUrl,
            CONCAT_WS(" ", name, surname) as authorName,
            year,', 
            Model::sqlWorkClassName() . 'as workClass,',
            'award,
            type, 
            pages,
            words,
            characters,
            [read],
            added,
            edited             
            FROM [works] 
            join [authors] on author = authorId
            WHERE `url`=%s',$url
    )->fetchAll();
    
    //$res[0]['award'] = ($res[0]['award'] != 99) ? $res[0]['award'] . ". místo" : "nominaci"; 
                                      //2010-08-19 22:43:25
    $res[0]['added'] = preg_replace('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', '$3.$2.$1 $4:$5', $res[0]['added']);
    $res[0]['edited'] = preg_replace('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', '$3.$2.$1 $4:$5', $res[0]['edited']);
    $this->template->data = $res[0];    
  
    $this->template->files = FileModel::getFiles($res[0]['workId']);
    
    Model::increaseRead($res[0]['workId']);    
    
    
    
  }
 
}
     
