<?php

//  Параметры из внешнего файла
// ----------------------------------------------------------------------------
include("../include/set/conf.inc.php");

// Функции, описанные во внешнем файле
// ----------------------------------------------------------------------------
include('../include/set/functions.php');

// Общие данные и переменные
// ----------------------------------------------------------------------------
include('../include/set/commonData.php');


    $DiagramArr=array();
    $allDay=array();
    $allDays[]=array('0',$a,$a);
    $DateArr=array();
    //$Months=array('Янв', 'Февр', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Ноя', 'Дек');
    $Months=array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des');

    $q="SELECT timeofcall,duration,way
    FROM calls
    where ((calls.timeofcall>='".$from_date."')               
    AND (calls.timeofcall<='".$to_date."')
    ".$additionalReq."
    ".$vectorReq."
    )";
    if($debug) echo $q."<br>";                   
    if($cacheflush) $res = $conn->CacheFlush($q);
    $rs = $conn->CacheExecute($q);

    //print_r($rs);
    if ($rs && $rs->RecordCount() > 0){
	while ($arr = $rs->FetchRow()) {
	    $day=split(" ", $arr[0], 2);
	    $preMkDay=split("-",$day[0],3);
	    //echo $preMkDay[2],"<br>";
	    $mkday=gmmktime(0, 0, 0, $preMkDay[1], $preMkDay[2], $preMkDay[0]);
	    $res[0]=$mkday;
	    $res[1]=$arr[1];
	    $res[2]=$preMkDay[1];
	    $res[3]=$preMkDay[2];
	    $res[4]=$preMkDay[0];
	    $res[5]=ereg_replace(" ", "",$arr[2]);
	    $DateArr[]=$res;
	}
    }

    $prevDay = -1;
    $allDaysKey=0;
    $prevDOfMonth=32;
    $printDate=0;
    $inc=0;
    $outg=0;
    while (list($key, $val) = each($DateArr)) {
	$DayOfMonth=$val[3];
	$mkday=$val[0];
	if($val[1] > 0) $odin=1; else $odin=0;
	//echo "$key => $mkday $val[1] $val[2] $DayOfMonth $val[4]<br>\n";
	if($prevDay==$mkday){
	    
	    if($val[5]=='1'){
		if ($incoming!='2'){
		    if($incoming=='3'){
			$allDays[$allDaysKey - 1][1]+=$odin;
		    }else{
			$allDays[$allDaysKey - 1][3]+=$odin;
		    }
		    
		}
	    }elseif($val[5]=='2'){
		if ($incoming!='3'){
		    if($incoming=='2'){
			$allDays[$allDaysKey - 1][1]+=$odin;
		    }else{
			$allDays[$allDaysKey - 1][2]+=$odin;
		    }
		}
	    }
	    if ($incoming!='2' && $incoming!='3'){
		$allDays[$allDaysKey - 1][1]+=$odin;
	    }
	    //print"$prevDay=$mkday<br>";
	}else{
	    $printMonth=$val[0];
	    if($val[5]=='1'){
		$inc=$odin;
	    }elseif($val[5]=='2'){
		$outg=$odin;
	    }
	    if ($incoming=='2'){
		$prevData=array($printMonth,$outg);
	    }elseif($incoming=='3'){
		$prevData=array($printMonth,$inc);
	    }else{
		$prevData=array($printMonth,1,$outg,$inc);
	    }
    
	    $allDays[$allDaysKey]=$prevData;
	    $allDaysKey++;
	    //print"<font color=red>$prevDay=$mkday</font><br>";
	}
	$prevDay=$mkday;
	$prevDOfMonth=$DayOfMonth;
    }

    $Columns = sizeof($allDays);
    if($Columns > 30){                             
	$delta = ceil($Columns/30);                
                                                   
	$giveDelta=0;                              
	while (list($key, $val) = each($allDays)) {
    	    if($giveDelta == $key){                
        	$giveDelta+=$delta;                
    	    }else{                                 
        	$allDays[$key][0]='';              
    	    }                                      
    	    if($val[1] > $maxValue){               
        	$maxValue=$val[1];                 
    	    }
	}                                          
    }

if($debug) print_r($allDays);
if($debug) print("I".$maxValue."I");

include("../include/phplot/phplot.php");
$graph = new PHPlot(600,300);
$graph->SetDataType("text-data");
$graph->SetDataValues($allDays);
$graph->SetYTickIncrement();
$graph->y_tick_increment=ceil(@$graph->y_tick_increment);
$graph->SetXLabelType("time");
$graph->SetXTimeFormat("%b %d");
if ($incoming=='2'){
    $graph->SetLegend(array("Outgoing"));
    $graph->SetDataColors(array('green'));
}elseif ($incoming=='3'){
    $graph->SetLegend(array("Incoming"));
    $graph->SetDataColors(array('orange'));
}else{
    $graph->SetLegend(array("All","Outgoing","Incoming"));
}
$graph->SetYTitle("Quantity of calls");
$graph->SetPlotType("bars");
$graph->SetXLabelAngle(90);
// Turn off X tick labels and ticks because they don't apply here:
$graph->SetXTickLabelPos('none');
$graph->SetXTickPos('none');
$graph->DrawGraph();

?>
