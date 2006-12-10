<HTML><BODY BACKGROUND=background.gif>
<CENTER>
<H1>Search Results</H1>
<HR>
<TABLE BORDER=0 width=i95% spacing=0 CELLPADDING=0 CELLSPACING=0>
<TR><TD background=topbg.gif><BR></TD></TR>
<?Php
$bgchange="FFFFFC";
$fchek=0;
$zombie="0";
$handle=opendir('.');
while (($file = readdir($handle))!==false){

if (eregi("[a-zA-Z0-p_-]*.html",$file) or eregi("[a-zA-Z0-p_-]*.htm",$file)){
$fchek=$fchek+1;

$file=trim($file);
$file=chop($file);
$filed=file($file);
$count = count($filed);
$i = $count;
$zt = 0;
$clt=0;
$found=0;
$stringer=0;

for($j=$zt;$j<$i;$j++){

$string=$filed[$j];
$stringer=$filed[$j];
$stringer=trim($stringer);
$string=trim($string);
$stringer=chop($stringer);
$string=chop($string);
$stringer=rtrim($stringer);
$string=rtrim($string);
$stringer=ltrim($stringer);
$string=ltrim($string);
$num = "EPLACED IT";

$string=strtolower($string);
$stringer=strtolower($stringer);


$string = ereg_replace($whatdoreplace, $num, $string);

if($string!=$stringer){

$found=$found+1;
$abby=$found;
$show[$found]=$stringer;

}

}

$dircount=count($file);
echo "<FONT SIZE=-1>";


if($found>0){
$zombie=$zombie+10;
#echo "<BR>";


if($file=="index.html"){
if($bgchange=="EEEEEE"){
$bgchange="FFFFFF";
echo "</TD></TR><TR><TD BGCOLOR=$bgchange><BR><A HREF=",$file," target=_new><FONT SIZE=+2>$file</A></FONT><BR><BR>This file containes <B><FONT COLOR=RED>$found</FONT></B> instances of <B><FONT COLOR=RED>$whatdoreplace</FONT></B> (shown below) <BR><UL>";
} else {
$bgchange="EEEEEE";
echo "</TD></TR><TR><TD BGCOLOR=$bgchange><BR><A HREF=",$file," target=_new><FONT SIZE=+2>$file</A></FONT><BR><BR>This file containes <B><FONT COLOR=RED>$found</FONT></B> instances of <B><FONT COLOR=RED>$whatdoreplace</FONT></B> (shown below) <BR><UL>";
}
} elseif($bgchange=="EEEEEE"){
$bgchange="FFFFFF";
echo "</TD></TR><TR><TD BGCOLOR=$bgchange><BR><A HREF=",$file,"><FONT SIZE=+2>$file</A></FONT><BR><BR>This file containes <B><FONT COLOR=RED>$found</FONT></B> instances of <B><FONT COLOR=RED>$whatdoreplace</FONT></B> (shown below) <BR><UL>";
} else {
$bgchange="EEEEEE";
echo "</TD></TR><TR><TD BGCOLOR=$bgchange><BR><A HREF=",$file,"><FONT SIZE=+2>$file</A></FONT><BR><BR>This file containes <B><FONT COLOR=RED>$found</FONT></B> instances of <B><FONT COLOR=RED>$whatdoreplace</FONT></B> (shown below) <BR><UL>";
}



for($new=1;$new<=$found;$new++){

$show[$new] = ereg_replace("</"," ",$show[$new]);
$show[$new] = ereg_replace("<"," ",$show[$new]);
$show[$new] = ereg_replace(">"," ",$show[$new]);
$show[$new] = ereg_replace("background"," ",$show[$new]);
$show[$new] = ereg_replace("body"," ",$show[$new]);
$show[$new] = ereg_replace("href"," ",$show[$new]);
$show[$new] = ereg_replace("="," ",$show[$new]);
$show[$new] = ereg_replace("mime"," ",$show[$new]);
$show[$new] = ereg_replace("meta"," ",$show[$new]);

$show[$new] = ereg_replace($whatdoreplace,"<FONT COLOR=RED><B>$whatdoreplace</FONT></B>",$show[$new]);
echo "<FONT SIZE=-1>";
print "<LI>$show[$new]";

}

echo "</UL></FONT></TD></TR>";
}

}
}
if($zombie==0){
echo "<BR></TD></TR><TR><TD><CENTER><B><FONT SIZE=+2 COLOR=RED>No Results Found!";
}
echo "<BR></TD></TR><TR><TD BGCOLOR=AAAAFF><CENTER><B>There where $fchek total file(s) searched.";
?>
<TD/></TD></TR></TABLE><TABLE>
<BR><BR><BR><BR><BR><BR>
<TABLE WIDTH=300><TR><TD><FONT SIZE=-3 COLOR=BLUE>
In case your interested, this search engine was written entirely by me, using the PhP 4 Scripting Languarge.  It uses nothing but itself, making it the easiest to setup and begin using for people who don't want to setup a database just to do searches.  Every site I checked said that PhP search engines do not work.  But I guess they do, when you try hard enough.
</TD></TR></TABLE>
</HTML>
