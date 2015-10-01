<?php
header('content-type:text/css');
header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT"); 


//widths:
//page
$page = 1000;

//right bar
$right = 270;

//home
$home = 550;

//searchbox
$search = 330;

//span sizes
$shadow = 5;
$small = 10;
$big = 20;

//font colors
$background = "#21232d";//#21232d
$normal = "#525b37";
$link = "#5ea81d";//1d78b8


//line-height: 20px;

$content = $page - $right;

$fileTypes=array("doc", "docx", "pdf", "txt");

$css= "

/*general*/

body {
	font: 12px helvetica, arial, sans-serif;
	margin: 0px;
	padding: 0px;
	color: $normal;
	background-color: $background;//f5f8ff
}
#main{
  margin: 0px auto;
  width: $page px;
}

#header{
  float: left;
  background: url('../images/header.png') no-repeat;
  margin-top: $big px;   
  width: $page px;
  height: 100px;
}
#page{
  float: left;  
  width: $page px;
  margin-top: " . ($big - 2 * $shadow) . "px;     
}
#footer{
  float: left;  
  width: $page px;
  text-align: center;
  color: #444;     
}
#footer a {
  color: #777;     
}

a{
  text-decoration: none;
}  
a:hover{
  text-decoration: underline;
}

/*header*/
/*--home--*/
#home{
  float: left;
}
#home a{
  display: block;
  width: $home px;
  height: 100px;
  color: #ccc;
}

#home a span{
  display: none;
}
/*--searchbox--*/
#searchbox{
  float: left;
  padding: " .($pad = $big + $shadow) . "px;
  margin-left: " . ($page - $home - $search) ."px;
  width: ".($search - 2*$pad)." px;  
  font-size: 16px;
  color: $background;
}
#searchbox form{
  background: #e1eaeb;
}
#searchbox input, #searchbox a {
  margin-top: $small px;
  float: left;
}
#searchbox input.text{
  width: " .($search - 2*$pad - 20 - 6 - $small)."px;
  margin-right: $small px;
}
#searchbox a.searchBtn{
  width: 20px;
  height: 20px;
  background: url('../images/search.png') no-repeat;
  display: block;
}

/*page*/
#page div.top{
  background: url('../images/pagetop.png') no-repeat;
  float: left;
  width: $page px;
  height: ".($shadow + $small)."px;
}
#page div.bottom{
  background: url('../images/pagebottom.png') no-repeat;
  float: left;
  width: $page px;
  height: ".($shadow + $small)."px;
}
#page div.body{
  background: url('../images/pagebody.png') repeat-y;
  float: left;
  width: $page px;
}

#content{
  background: #f5f8ff;
  float: left;
  width: " . ($inside = $content - 2*$big - $shadow) ."px;
  margin: $small px 0px $small px ".($big + $shadow)."px;
  height: 100%;
}
#content a{
  color: $link;
  text-decoration: none;
}
#content a:hover{
  text-decoration: underline;
}

#rightbar{
  float: left;
  width: ".($tab = $right - $shadow + $big)."px;
}
/*--glossy label--*/
#rightbar ul.topmenu li a, #rightbar div.name a, #rightbar ul.submenu li td.label{
  float: left;
  height: 19px;
  padding: $small px 0px;
  margin-top: $small px;
  font-size: 16px;
  line-height: 16px;
  color: white; 
  text-shadow: black 0px 0px 5px;  
}
#rightbar div.name a{
  background: url('../images/rightname.png') no-repeat;
  margin-left: ".($small + $shadow)."px;
  padding-left: 28px;
  width: ".($tab- $big - 28 + $shadow)."px;  
}
#rightbar div.block{
  background: #e1eaeb;
  margin-left: ".($small + $shadow + 1)."px;
  padding: $shadow px $small px 0px;
  float: left;
  width: ".($tab- $big - 2*$small + $shadow - 1)."px;
}
#rightbar div.block a{
  color: $link;
}
#rightbar div.block p{
  margin-top: $small px;
  margin-bottom: 0px;
}
#rightbar div.block p.twolines{
  max-height: 30px;
  overflow: hidden;
}
div.schoolyear{
  margin-left: $big px;
  float: left;
  padding: $big px $small px $small px;
  text-align: right;
  width: 245px;
}
/*--fist level--*/
#rightbar ul{
  margin: 0px;
  padding: 0px;
  list-style: none;
  float: left;
}
#rightbar ul.topmenu li{  
  float: left;
  position: relative;
}
#rightbar ul.topmenu li a.list{
  background: url('../images/rightlink.png') no-repeat;
  padding-left: ".($big + $big)."px;
  margin-left: 3px;
  display: block;
  width: ".($tab - 2*$big)."px;
  text-decoration: none;
}
#rightbar ul.topmenu li a.list.hover{
  background: url('../images/rightlinkhover.png') no-repeat scroll -$small px 0px;
  padding-left: ".($big + $small)."px;
  margin-left: ".(3 + $small)."px;
  text-decoration: underline;
}
/*--second level--*/
#rightbar ul.submenu{
  list-style: none;  
  position: absolute;  
  right: 275px;
  top: $small px;  
  display: none; 
}
#rightbar ul.submenu li{
  float: left;
  position: relative;
  width: 100%;
  padding-bottom: 2px;  
}
#rightbar ul.submenu table{
  border-collapse: collapse;
  border: none;
}
#rightbar ul.submenu table, #rightbar ul.submenu tr{
  width: 100%;
} 
#rightbar ul.submenu li td.label{
  background: url('../images/submenu.png') no-repeat;  
  padding: 8px $small px 12px;
  cursor: default;
  float: none;
  white-space: nowrap;
  width: 100%;
  height: 39px;
}
#rightbar ul.submenu li td.end{
  padding: 0px; 
  height: 39px;
  width: 3px;
  display: block;
  background: url('../images/submenuend.png') no-repeat;
}
/*--third level--*/
#rightbar ul.optionsmenu{
  list-style: none;  
  position: absolute;
  top: 1px;  
  display: none;
  padding-right: 2px;
}
#rightbar ul.optionsmenu td.tl{
  background: url('../images/optionstop.png') no-repeat left top;    
  height: 10px;
  width: 100%;  
}
#rightbar ul.optionsmenu td.bl{
  background-image: url('../images/optionsbottom.png');   
  height: 10px;
  width: 100%;
}
#rightbar ul.optionsmenu td.tr{
  background: url('../images/optionstopend.png') no-repeat right top;  
  height: 10px;
  width: 10px;
  display:block;  
  padding: 0px;  
}
#rightbar ul.optionsmenu td.br{  
  background: url('../images/optionsbottomend.png') no-repeat right top;  
  height: 10px;
  width: 10px;
  display:block;
  padding: 0px; 
}
#rightbar ul.optionsmenu td.l{  
  background: url('../images/optionsbody.png') repeat-y;  
  width: 10px; 
}
#rightbar ul.optionsmenu td.r{  
  background: url('../images/optionsbody.png') repeat-y right top;  
  width: 10px; 
}
#rightbar li ul.optionsmenu td.label{
  background: #859ba6;
  padding: 0px;
} 
#rightbar ul.optionsmenu li a{
  display: block;  
  width: 100%;
  padding: 0px 0px 0px 8px;
  margin: 0px;
  text-decoration: none;
}
#rightbar ul.optionsmenu li a:hover{  
  text-decoration: underline;
}


/*--preloading--*/

div.preload{
  height: 0px;
}
div.preload0{
  background: url('../images/rightlinkhover.png');
}
div.preload1{
  background: url('../images/submenu.png') no-repeat;
}
div.preload2{
  background: url('../images/submenuend.png') no-repeat;
}
div.preload3{
  background: url('../images/optionsbottomend.png') no-repeat right top;
}
div.preload4{
  background: url('../images/optionstopend.png') no-repeat right top;
}
div.preload5{
  background-image: url('../images/optionsbottom.png');
}
div.preload6{
  background: url('../images/optionstop.png') no-repeat left top;
}
div.preload7{
  background: url('../images/optionsbody.png') repeat-y right top; 
}

           
/*div.work-text{
  width: $inside px;
} */
div.files{
  float: right;
  margin: 0px $big px $big px;
  text-align: center;
  max-width: 350px;
}
div.files h4{
  margin: 0px 0px 2px;
}
div.files .links{
  background: #fcfcfc;
  padding-right: 8px;
  float: right;
  -moz-border-radius: $small px;
  -webkit-border-radius: $small px;
  -khtml-border-radius: $small px;
  border-radius: $small px;


}

div.files a{
  display: block;
  width: 100px;
  height: 120px;
  margin: $small px;
  margin-right: 2px;
  float: left;
  background: #fcfcfc url('../images/fileicons/default.png') no-repeat;
  text-align: center;
}
div.files a div{
  height: 104px;  
}
#content div.files a:hover{
  border: 1px solid #eaedf4;
  margin: ".($small - 1)." px;
  margin-right: 1px;
  text-decoration: none;
}
";

foreach($fileTypes as $type){
  $css.="
div.files a.$type{
  background-image: url('../images/fileicons/".$type.".png');
}
  
  ";  
}

$css.="


/********************** DataGrid *********************/
table.datagrid tr.filters, table.datagrid th.actions, table.datagrid td.actions {
  display: none;
}

table.datagrid {
	padding: 0;
	margin: 0;
	border-collapse: collapse;
	max-width: $inside px;
	line-height: 16px;
}
table.datagrid tr {
  color: #525b37;
	
}
table.datagrid tbody tr.even {
	background: #eaedf4;
}
table.datagrid tbody tr:hover {
	background: #859ba6 !important;
	color: white !important;
}
table.datagrid tbody tr.selected, table.datagrid tbody tr.selected td {
	background: #B3F76F !important;
}

table.datagrid tr.header{
  background: url('../images/tableheader.png') no-repeat 0% 85%;
  
}
table.datagrid tr.header th{
padding-bottom: 4px;
}
table.datagrid tr.footer{
  background: url('../images/tablefooter.png') no-repeat 0px 4px;
}

table.datagrid tr.header th p{
  margin: 0px;
}
#content table.datagrid td a.advanced{
  color: $link;
  display: inline;
  display: none;
}
#content table.datagrid td a.advanced:hover{
  text-decoration: underline;
}

table.datagrid input[type=text], table.datagrid select {
	color: #525B37;
	font-family: Arial,sans-serif;
	border: 1px #CCCCCC solid;
	padding: 2px 0px;
	margin: 0 auto;
}
table.datagrid tr.filters input[type=text], table.datagrid tr.filters select {
	width: 100%;
}
table.datagrid select {
	padding: 1px 0px;
}
table.datagrid input[type=text]:hover, table.datagrid input[type=text]:focus {
	background-color: #F6F7FA;
}
table.datagrid input[type=checkbox], table.datagrid input[type=checkbox]:hover {
	border: none;
	*background-color: #C6DBFF;
	display: block;
	margin: 0 auto;
}
table.datagrid input[type=submit] {
	border: 0px;
	color: #525b37;
	font-family: Arial,sans-serif;
	cursor: pointer;
	padding-right: 18px;
	=padding-right: 10px;
	background: transparent no-repeat right center;
}
table.datagrid input[name=filterSubmit] {
	background: url('../images/search.png') no-repeat 2px 1px !important;
	font-size: 0px;
	height: 20px;
	width: 6px;
}
table.datagrid input[name=operationSubmit] {
	background-image: url('../images/datagrid/icons/accept.png') !important;
}
table.datagrid input[name=pageSubmit], table.datagrid input[name=itemsSubmit] {
	background-image: url('../images/datagrid/icons/arrow_rotate_clockwise.png') !important;
}
table.datagrid input[name=resetSubmit] {
	background-image: url('../images/datagrid/icons/arrow_refresh_small.png') !important;
}
table.datagrid input.datepicker {
	background: white url('../images/datagrid/calendar.gif') no-repeat right !important;
}
table.datagrid th {
		
	padding-left: 5px;
	padding-right: 5px;
}
#content table.datagrid td a {
	color: #525b37;
	display: block;
	padding: 2px $small px;
	text-decoration: none;
	height: 100%;
	overflow: hidden;
}

table.datagrid td{
  vertical-align: top;
  white-space: nowrap;
  border-left: 1px solid #eaedf4;
  height: 32px;
  
}
table.datagrid td.first, table.datagrid tr.footer td{
  white-space: normal;
  border-left: none;
}


table.datagrid th {
	color: #505767;
	text-align: left;
}
table.datagrid th.checker, table.datagrid td.checker, table.datagrid td.actions {
	text-align: center;
	padding-left: 6px;
}
table.datagrid tr.header th a span {
	color: #7D9DC9;
	font-size: 90%;
	padding-left: 6px;
}
table.datagrid tr.filters, table.datagrid tr.filters td {
	background: #e1eaeb; 
	padding:5px 6px 4px;
	border: none;  	
}
table.datagrid tr.header th span.link {
	color: #165CA3;
}
#content table.datagrid th a, table.datagrid tr.filters a.filter {
	color: $link;
	text-decoration: none;
	line-height: 30px;
	float: left;
	font-weight: normal;
}
#content table.datagrid th a:hover{
  text-decoration: underline;
}
table.datagrid tr.filters a.filter {
	background: url('../images/datagrid/icons/find.png') no-repeat right center;
	padding-right: 25px;
	padding-left: 5px;
	font-weight: bold;
	text-align: left !important;
	line-height: 10px !important;
}
table.datagrid th p {
	text-decoration: none;
	line-height: 30px;
	float: left;
}
table.datagrid tr.filters td.actions {
	text-align: left;
}
table.datagrid tr.footer td {
	color: #505767;
	text-align: left;
	padding-top: 10px;
}
table.datagrid span.positioner {
	width: 22px;
	height: 16px;
	display: block;
	float: left;
	margin-right: 2px;
}
table.datagrid th span.positioner {

	padding-top: 7px;
	display: none;
}
table.datagrid span.positioner a {
	line-height: 0px;
}
table.datagrid span.positioner a span {
	border: none;
	display: block;
	float: left;
	margin: 2px;
	width: 21px;
	height: 4px;
	opacity: 0.40;
	=filter: alpha(opacity=40);
	padding-left: 0px !important;
}
table.datagrid span.positioner a span.up {
	background: url('../images/datagrid/asc.gif') no-repeat right center;
}
table.datagrid span.positioner a span.down {
	background: url('../images/datagrid/desc.gif') no-repeat right center;
}
table.datagrid span.positioner a span.down:hover, table.datagrid span.positioner a span.up:hover {
	opacity: 1;
	=filter: alpha(opacity=100);
}
table.datagrid span.positioner a.active span, table.datagrid span.positioner a.active span:hover {
	opacity: 1;
	=filter: alpha(opacity=100);
	cursor: default;
}
table.datagrid span.positioner a.inactive span, table.datagrid span.positioner a.inactive span:hover {
	background: none !important;
	cursor: default;
}
table.datagrid tr.footer select, table.datagrid tr.footer input[type=text] {
	margin: 0 0.2em;
	padding: 0 0.2em !important;
}
table.datagrid tr.footer input {
	text-align: center;
}


/** footer **/
table.datagrid .paginator, table.datagrid .operations, table.datagrid .grid-info {
	padding-right: 10px;
	float: left;
}
table.datagrid .grid-info {
	float: right;
}
table.datagrid .paginator .paginator-controls {
	float: left;
	padding: 0em;
}
table.datagrid .paginator a {
	float: left !important;
	padding: 0em !important;
}
table.datagrid .paginator .paginator-first, table.datagrid .paginator .paginator-prev, 
table.datagrid .paginator .paginator-next,  table.datagrid .paginator .paginator-last {
	margin: 0em 0.2em;
	float: left;
	margin: 1px 4px;
	height: 16px;
	width: 16px;
	background-repeat: no-repeat;
	display: block;
	overflow: hidden;
	text-indent: -99999px;
}
table.datagrid .paginator .paginator-first {
	background-image: url('../images/datagrid/icons/control_start_blue.png');
}
table.datagrid .paginator .paginator-prev {
	background-image: url('../images/datagrid/icons/control_left_blue.png');
}
table.datagrid .paginator .paginator-next {
	background-image: url('../images/datagrid/icons/control_right_blue.png');
}
table.datagrid .paginator .paginator-last {
	background-image: url('../images/datagrid/icons/control_end_blue.png');
}
table.datagrid .inactive {
	opacity: 0.40;
	=filter: alpha(opacity=40);
}


";
echo str_replace(" px", "px", $css);

?>