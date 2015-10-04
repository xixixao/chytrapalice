<?php



class FileModel extends /*Nette\*/Object
{

  public static function getFiles($id){
    $files = array();

    $workDirectoryName = WWW_DIR ."/files/".$id;
    if(is_dir($workDirectoryName)){
      $workDir = opendir($workDirectoryName);
      while($entryName = readdir($workDir)) {

      	if($entryName!="." &&  $entryName!=".." && !is_dir("$workDirectoryName/$entryName")){

      	  $files[] = array(
      	   //title="$file['name']" href="$file['link']" class="$file['type']"><div></div>({$file['size']})</a>
            'name'=>"$entryName",
            'link'=>"/files/$id/$entryName",
            'type'=>substr($entryName, strpos($entryName, '.')+1),
            'size'=>self::getFileSize("$workDirectoryName/$entryName")
          );
        }
      }
    }
    return $files;

  }
  public static function deleteFiles($work){
    self::delTree(WWW_DIR ."/files/$work");

  }
  public static function delete($work, $name){
    $file = WWW_DIR ."/files/$work/$name";
    if(is_file($file)){
      unlink( $file );
    }
  }
  public function add($work, $file){
    $baseName = basename($file['name']);
    $trimName = substr($baseName, 0, strrpos($baseName, '.'));
    $suffix = substr($baseName, strrpos($baseName, '.'));

    $trimName = Model::formUri($trimName);
    $trimName = (strlen($trimName)==0) ? "noname" : $trimName;

    $path = WWW_DIR ."/files/$work/";
    $i='';
    if(!is_dir($path)){
      mkdir($path);
    }
    while(file_exists($path.($fileName = $trimName.$i.$suffix))){
      $i++;
    }
    move_uploaded_file($file['tmp_name'], $path.$fileName);

    return $fileName;
  }
	public static function getFileSize($file){
	  $size = filesize($file);
	  static $t = array('B', 'kB', 'MB', 'GB');
	  $l = 0;
	  while($size>999){
      $size /= 1000;
      $l++;
    }
    return round($size).' '.$t[$l];

  }
	/*public function getAllFiles()
	{

       $model = new Model;
  	   $this->files=array();
  	   $i=0;
  	   $mainDirectoryName = WWW_DIR ."/attachments";

      $pageDir = opendir($mainDirectoryName);
      $name = array();
      $size = array();
      $ids = array();
      while($entryName = readdir($pageDir)) {
      	if($entryName!="." &&  $entryName!=".." && !is_dir($mainDirectoryName."/".$entryName)){
      	  //$this->files['ids']["$mainDirectoryName/$entryName"] = "";

          $ids[$i] = "$mainDirectoryName/$entryName";
          $name[$i]  = $entryName;
          $size[$i] = filesize("$mainDirectoryName/$entryName");
          $i++;
        }
       }
        array_multisort($name, $size, $ids);

        for ($i=0; $i< count($name); $i++) {
            $this->files['ids'][$ids[$i]] = "";
            $this->files[$i] = array(
            'name'=>$name[$i],
            'size'=>$size[$i]
          );
        }

    return $this->files;
	}

	public function addFile($file){
    $baseName = basename($file['name']);
    $trimName = substr($baseName, 0, strrpos($baseName, '.'));
    $suffix = substr($baseName, strrpos($baseName, '.'));

    $except = array('\\', '/', ':', '*', '?', '"', '<', '>', '|', ' ', '.');
    $trimName = str_replace($except, '', $trimName);
    $trimName = (strlen($trimName)==0) ? "noname" : $trimName;

    $path = WWW_DIR .'/attachments/';
    $i='';
    if(!is_dir($path)){
      mkdir($path);
    }
    while(file_exists($path.($fileName = $trimName.$i.$suffix))){
      $i++;
    }
    move_uploaded_file($file['tmp_name'], $path.$fileName);

    if(getimagesize($path.$fileName)){
      $this->addMiniature($file, $fileName, $path);
    }
    return $fileName;
  }

	public function deleteFiles($array)
	{
	  foreach($array as $file){
      if(is_file($file)){
        unlink( $file );
      }
	  }
  }

  public function addMiniature($file, $fileName, $path){
	  $dimensions = getimagesize($path.$fileName);
    switch ($dimensions[2]) {
      case 1: //GIF
      break;
      case 2: //JPEG
      $srcImage = imageCreateFromJpeg($path.$fileName);
      break;
      case 3: //PNG
      break;
      default:
      return false;
      break;
    }
    $miniDest = $path.'mini/';
    if(!is_dir($miniDest)){
      mkdir($miniDest);
    }
    $width = min(self::IMAGE_WIDTH,$dimensions[0]);
    $height =  $width*$dimensions[1]/$dimensions[0];
    $dst_img = imageCreateTrueColor($width,$height);
    imageCopyResampled($dst_img,$srcImage,0,0,0,0,$width,$height,$dimensions[0],$dimensions[1]);
    switch ($dimensions[2]) {
      case 1:
      break;
      case 2:
      imageJpeg($dst_img, $miniDest.$fileName, 100);
      break;
      case 3:
      break;
    }
  }   */
  /*public function deleteNoteAttachments($what) {
    $this->delTree('attachments/'.$what.'/');
  } */
  protected static function delTree($dir){
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file ){
        if( substr( $file, -1 ) == '/' || substr( $file, -1 ) == '\\' ) {
            self::delTree( $file );
        } else {
            unlink( $file );
        }
    }

    if (is_dir($dir)) rmdir( $dir );
  }

}