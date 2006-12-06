<p>&nbsp;</p>
<?php
//print "I".IMG_GIF."I";
//.IMG_JPG.IMG_JPEG.IMG_PNG.IMG_WBMP;
$gif_enable=IMG_GIF;
$jpg_enable=IMG_JPG;
$jpeg_enable=IMG_JPEG;
$png_enable=IMG_PNG;
$wbmp_enable=IMG_WBMP;
if ($gif_enable==1 or $jpg_enable==1 or $jpeg_enable==1 or $png_enable==1 or $wbmp_enable==1) {
    if($toprint!="yes"){
        if($diatype!='all'){
	    print("<a href='".complitLink($local_diatype='all')."'>".$GUI_LANG['AllSchedules']."</a>");
	}

	if(!empty($diatype) && $diatype!='pie' && $diatype!='all'){
	    print("<br><a href='".complitLink($local_diatype='pie')."'>".$GUI_LANG['RelativeQuantityOfCalls']."</a>");
	}

	if($diatype!='bars' && $diatype!='all'){
	    print("<br><a href='".complitLink($local_diatype='bars')."'>".$GUI_LANG['QuantityOfCalls']."</a>");
	}
	if($diatype!='dur' && $diatype!='all'){
	    print("<br><a href='".complitLink($local_diatype='dur')."'>".$GUI_LANG['DurationOfCalls']."</a>");
	}
    }

?>
<div align=center>
<?php
    if(empty($diatype) or $diatype=='pie' or $diatype=='all'){
	print "<br><img src=\"../diagram/diagram_pie.php".complitLink()."\" width=600 height=300 alt='".$GUI_LANG['RelativeQuantityOfCalls']."'>";
    }
    if($diatype=='all' or $diatype=='bars'){
	print "<br><img src=\"../diagram/diagram_bars.php".complitLink()."\" width=600 height=300 alt='".$GUI_LANG['QuantityOfCalls']."'>";
    }
    if($diatype=='all' or $diatype=='dur'){
	print "<br><img src=\"../diagram/diagram_bars_dur.php".complitLink()."\" width=600 height=300 alt='".$GUI_LANG['DurationOfCalls']."'>";
    }
}else{
?>
<div align=center>
<?php
    echo $GUI_LANG['GDSupportIsDisabled'];
}

?>
</div>
<p>&nbsp;</p>
