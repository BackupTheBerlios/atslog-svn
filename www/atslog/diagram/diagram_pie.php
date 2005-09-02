<?

//  Параметры из внешнего файла
// ----------------------------------------------------------------------------
include("../include/set/conf.inc");

// Функции, описанные во внешнем файле
// ----------------------------------------------------------------------------
include('../include/set/functions.php');

// Общие данные и переменные
// ----------------------------------------------------------------------------
include('../include/set/commonData.php');

    $DiagramArr=array();
    $allDay=array();
    //$Months=array('Янв', 'Февр', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Ноя', 'Дек');
    $Months=array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des');

    $additionalReq = ereg_replace(" AND \(calls.way = '2'\)","",$additionalReq);
    $additionalReq = ereg_replace(" AND \(calls.way = '1'\)","",$additionalReq);
    $q="SELECT COUNT(*),way
    FROM calls
    where ((calls.timeofcall>='".$from_date."')               
    AND (calls.timeofcall<='".$to_date."')
    ".$additionalReq."
    ".$vectorReq."
    )
    GROUP BY way";
    if($debug) echo $q."<br>";                   
    if($cacheflush) $res = $conn->CacheFlush($q);
    $rs = $conn->CacheExecute($q);

    //print_r($rs);
    if ($rs && $rs->RecordCount() > 0){
	while ($arr = $rs->FetchRow()) {
	    if($arr[1] == 1){
		$all[0]=$arr[0];
	    }else{
		$all[1]=$arr[0];
	    }
	}
    }
    $allDays[]=array('0',$all[1],$all[0]);
    //print_r($allDays);

include("../include/phplot/phplot.php");
$graph = new PHPlot(600,300);
$graph->SetDataType("text-data");
$graph->SetDataValues($allDays);
$graph->SetLegend(array("Outgoing","Incomming"));
$graph->SetYTitle("Relative quantity");
$graph->SetDataColors(array('green', 'orange'));
$graph->SetPlotType("pie");
$graph->DrawGraph();

?>
