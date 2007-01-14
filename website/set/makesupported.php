<?
// create supported model list based on trunk module list
$file="../../trunk/libexec/modules.lst";
if(!is_file($file)) die("no such file\n");
$strings=file($file);
$out=array();
foreach($strings as $v){
    $v=trim($v);
    if($v!=''){
	list($lib,$models,$vendor)=explode(':',$v);
	$models_array=explode(',',$models);
	foreach($models_array as $model){
	    $out[$vendor][]=$model;
	}
    }

}
// creating "supported.php"
$text='';
ksort($out);
foreach($out as $vendor=>$models){
    asort($models);
    $text.='<li><u>'.$vendor.':</u> ';
    $text.=implode(', ',$models)."\n";
}
// <li><u>Alcatel:</u> 4200E
echo $text;
// print_r($out);
?>