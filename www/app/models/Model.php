<?php



class Model extends /*Nette\*/Object
{




  public static function getValues($column, $table){
    return dibi::query(
          'SELECT DISTINCT
            %n', $column,
            'FROM
             %n', $table,
            'WHERE %n', $column,'!=%s','',
            'ORDER BY %n', $column

           )->fetchPairs();
  }

  public static function add($array, $table){
    dibi::query('INSERT INTO %n', $table, '%v', $array);
    return dibi::query('SELECT LAST_INSERT_ID()')->fetchSingle();
  }

  public static function save($array, $idCol, $table){
    dibi::query('UPDATE %n', $table, 'SET %a', $array, 'WHERE %n', $idCol, '=%i', $array[$idCol]);
  }

  public static function delete($id, $idCol, $table){
    dibi::query('DELETE FROM %n', $table,'WHERE %n', $idCol, '=%i', $id);
  }
  public static function increaseRead($id){
    $reader = Environment::getSession('reader');
    if($reader[$id] == NULL){
      dibi::query('UPDATE [works] SET [read] = [read] + 1 WHERE workId=%i', $id);
      $reader[$id] = TRUE;
    }
  }

  private static $schoolYear;

  public static function getSchoolYear(){
    if(isset(self::$schoolYear)){
      return self::$schoolYear;
    } else {
      return (date('n') < 9) ? date('Y') : date('Y')+1;
    }
  }

  public static function sqlClassName($schoolYear){
    return
    'CONCAT(
      IF(r, "R", ""),
      IF(class - '.$schoolYear.' < 0,
        " ",
        CONCAT(
          IF(r, 8, 4) - class + '.$schoolYear.',
          ".")
        ),
      mark
      )';
  }

  public static function sqlWorkClassName(){
    return
    'CONCAT(
      IF(r, "R", ""),
      IF(class - year < 0,
        class,
        CONCAT(
          IF(r, 8, 4) - class + year,
          ".")
        ),
      mark)';
  }

  public static function sqlSumWorks(){
    return '(SELECT COUNT([workId]) FROM [works] WHERE [author]=[authorId])';
  }
  public static function sqlSumReads(){
    return '(SELECT SUM([read]) FROM [works]  WHERE [author]=[authorId] GROUP BY [author])';
  }
  public static function sqlCategory(){
    return '(IF((0 <= class - [year] - 4) && (class - [year] - 4 < 4), 3 - (class - [year] - 4), "palice"))';
  }

  public static function formValue(){
    $array = func_get_args();
    $text = $array[0];
    unset($array[0]);
    $single = $array[count($array)] === TRUE;
    if($single) unset($array[count($array)]);
    $count = 0;
    for($i=1; $i < count($array); $i += 2){
      $text = preg_replace($array[$i],$array[$i+1],$text, 1, $count);
      if($count==1 && $single === TRUE) break;
    }
    return $text;
  }

  public static function palicky($number=NULL){
    static $a = array('prima', 'sekunda', 'tercie', 'kvarta');
    return ($number==NULL) ? $a : $a[$number];
  }

  public static function rocniky(){
    static $a = array('první', 'druhý', 'třetí', 'čtvrtý');
    return $a;
  }

  public static function createAuthorUri($name, $surname, $class, $id=NULL){
    $name .= " $surname";
    $uri = self::formUri($name);
    $classUsed = FALSE;
    $i = 1;
    $suffix = "";
    while(count(dibi::query('SELECT `authorUrl` FROM [authors] WHERE `authorUrl`=%s', $uri.$suffix, '%if',($id!=NULL),'AND `authorId`!=%i',$id))>0){
      if(!$classUsed){
        $suffix = "-$class";
        $classUsed = TRUE;
      } else {
        $i++;
        $suffix = "-$class-$i";
      }
    }
    return $uri.$suffix;
  }
  public static function createUri($title, $author, $id=NULL){
    echo $title . " a " . $author;
    if(strlen($title) > 50){
      $words = explode(" ", $title);
      $short = "";
      for($i = 0; strlen($short) < 50; $i++){
        $short .= $words[$i]." ";
      }
      $title = substr($short, 0, -1);
    }
    $title .= " " . dibi::query('SELECT CONCAT_WS(" ", name, surname) FROM [authors] WHERE `authorId`=%i', $author)->fetchSingle();
    $uri = self::formUri($title);

    $i = 1;
    $suffix = "";
    while(count(dibi::query('SELECT `url` FROM [works] WHERE `url`=%s', $uri.$suffix, '%if',($id!=NULL),'AND `workId`!=%i',$id))>0){
      $i++;
      $suffix = "-$i";
    }
    return $uri.$suffix;
  }

  public static function formUri($string){
    return preg_replace('/-+$/','',preg_replace('/(\W+|-+)/','-',strtolower(self::getEnglishAlpha($string))));
  }
  public static function getEnglishAlpha($string){
    static $tbl = array(
      "\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z");
    return strtr($string, $tbl);
  }

 /*

	public function getPageNames($lang)
	{
		return $this->db->dataSource('SELECT `id`, `parent`, `title`, `uri` FROM [hcdata] WHERE `checked`=%b', true, 'AND `lang`=%s', $lang ,'ORDER BY `priority`')->fetchAssoc('parent,#');
	}
	public function getSimplePageNames()
	{
		return $this->db->dataSource('SELECT `id`, `title` FROM [hcdata]')->fetchAssoc('id');
	}

	public function deleteNote($id)
	{
	  //to really delete
    $this->db->query('DELETE FROM [hcdata] WHERE id=%i', $id);
    //$this->db->query('UPDATE [hcdata] SET `show` = NOT `show` WHERE id=%i', $id);
  }
  public function checkNote($id)
	{
    $this->db->query('UPDATE [hcdata] SET `checked` = NOT `checked` WHERE id=%i', $id);
  }
  public function saveNote($arr, $id)
  {
    unset($arr['image']);unset($arr['imageSelect']);
    $arr['uri'] = $this->createUri($arr['title'],$arr['lang'],$id);
    $this->db->query('UPDATE [hcdata] SET', $arr, 'WHERE id=%i', $id);
    return $arr['uri'];
  }
  public function addNote($arr)
  {
    unset($arr['image']);unset($arr['imageSelect']);
    $arr['priority'] = new DibiVariable("(SELECT MAX(`priority`) FROM (SELECT `priority` FROM [hcdata] WHERE `lang`='".$arr['lang']."') as x) +1", 'sql');
    //throwing exception if already taken
    $arr['uri'] = $this->createUri($arr['title'],$arr['lang']);
    $this->db->query('INSERT INTO [hcdata]', $arr);
    return $this->db->query('SELECT LAST_INSERT_ID()')->fetchSingle();
  }
  public function moveUp($old_position, $lang){
    $new_position = $this->db->query("SELECT `priority` FROM [hcdata] WHERE `lang`=%s",$lang," AND `priority` < $old_position  ORDER BY `priority` DESC LIMIT 1")->fetchSingle();

    $this->db->query(
    "UPDATE [hcdata] SET `priority` = IF( `priority` = $old_position, $new_position, $old_position ) WHERE `priority` IN ( $old_position, $new_position )");
  }
  public function moveDown($old_position, $lang){
    $new_position = $this->db->query("SELECT `priority` FROM [hcdata] WHERE `lang`=%s",$lang," AND `priority` > $old_position ORDER BY `priority` LIMIT 1")->fetchSingle();

    $this->db->query(
    "UPDATE [hcdata] SET `priority` = IF( `priority` = $old_position, $new_position, $old_position ) WHERE `priority` IN ( $old_position, $new_position )");
  }
  public function getData($lang){
  //return $this->db->dataSource(
    //  'SELECT `id`, `date`, `title`, `text`,'.(($normalView)?'':'`show`, `added`, `modified`,').' `checked` FROM [hcdata] WHERE `user`=%i',$id,($normalView)?'AND `show`=1':'');
    return $this->db->dataSource('SELECT `id`, `priority`, `title`, `text`, `checked` FROM [hcdata] WHERE `lang`=%s',$lang,' ORDER BY `priority`');
  }// WHERE `user`=%i',$id

  public function getPage($id,$lang){
    if($id==''){
      //return $this->db->query('SELECT `id`,`uri`,`title`,`text` FROM [hcdata] ORDER BY `priority` LIMIT 0,1')->fetchAll();
      return $this->db->query('SELECT `id`,`uri`,`title`,`text` FROM [hcdata] WHERE `priority`=',0,' AND `lang`=%s',$lang,'AND `checked`=%b', true)->fetchAll();
    }else{
      return $this->db->query('SELECT `id`,`uri`,`title`,`text` FROM [hcdata] WHERE `uri`=%s',$id)->fetchAll();
    }
  }
  protected function createUri($title, $lang, $id=NULL){
    static $except = array('\\', '/', ':', '*', '?', '"', '<', '>', '|', ' ');
    static $tbl = array(

      "\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"a","\xc3\x84"=>"a","\xc4\x8c"=>"c","\xc4\x8e"=>"d","\xc3\x89"=>"e","\xc4\x9a"=>"e","\xc3\x8d"=>"i","\xc4\xbd"=>"l","\xc4\xb9"=>"l","\xc5\x87"=>"n","\xc3\x93"=>"o","\xc3\x96"=>"o","\xc5\x90"=>"o","\xc3\x94"=>"o","\xc5\x98"=>"r","\xc5\x94"=>"r","\xc5\xa0"=>"s","\xc5\xa4"=>"t","\xc3\x9a"=>"u","\xc5\xae"=>"u","\xc3\x9c"=>"u","\xc5\xb0"=>"u","\xc3\x9d"=>"y","\xc5\xbd"=>"z");
    $uri = preg_replace('~-+($|-)~','-',strtolower(strtr(str_replace($except, '-', $title), $tbl)));
    if(count($this->db->query('SELECT `uri` FROM [hcdata] WHERE `uri`=%s', $uri, 'AND `lang`=%s',$lang,'%if',($id!=NULL),'AND `id`!=%i',$id))>0){
      throw new Exception('Uri already occupied');
    } else {
      return $uri;
    }
  } */
}

//"\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z");