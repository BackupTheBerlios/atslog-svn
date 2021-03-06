<?php

// ������ ������ ������������ �� ��������� �������
// ----------------------------------------------------------------------------
$start_time = microtime();

//  ��������� �� �������� �����
// ----------------------------------------------------------------------------
include("../include/config.inc.php");

// �������, ��������� �� ������� �����
// ----------------------------------------------------------------------------
include('../include/set/functions.php');

// ����� ������ � ����������
// ----------------------------------------------------------------------------
include('../include/set/commonData.php');

// ������ ����� HTML
// ----------------------------------------------------------------------------

$title=strtr($GUI_LANG['TheAccountOfPhoneCalls'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']);;
if(empty($export)) include("../include/set/header.html");

// ---------------------------------------------------------------------------

switch($type){
	case "IntAll":
	case "IntCoDetail":
	case "IntDetail":
	case "IntNum":
	case "IntNumDetail":
		$int_echo = $GUI_LANG['ByInternalPhones'];
		$anothType = 1;
		break;
	case "diagram";
		$int_echo = $GUI_LANG['ReportInTheSchedules'];
		$anothType = 3;
		break;
	case "CoIntDetail":
	case "CoDetail":
	case "CoAll":
	case "CoNum":
	case "CoNumDetail":
		$int_echo = $GUI_LANG['ByExternalLines'];
		$anothType = 2;
		break;
	case "NumAll":
	case "NumDetail":
		$int_echo = $GUI_LANG['ByCallingNumber'];
		$anothType = 4;
		break;
	case "AllCalls":
		$int_echo = $GUI_LANG['ByAll'];
		$anothType = 5;
		break;
	default:
		$int_echo = $int;
}

$CurrentIP[$anothType]=" SELECTED";
$incomingCheck[$incoming] = " SELECTED";
$CityLineCheck[1] = " checked";
$TrunkLineCheck[2] = " checked";
$MobLineCheck[4] = " checked";
$NationalLineCheck[8] = " checked";
$CurrentDebug[$debug] = " SELECTED";

if (!isset($toprint) || $toprint!="yes"){
    $thisMenu="calls";
    if(empty($export)) include("../include/set/menuTable.html");
}

switch ($type){

	case "IntAll":
	/* 
	   ������ ���������� ��������� � ����� ������������� �����������.
	*/
		include("query/IntAll.php");
		break;	

	case "IntDetail":
	/*
		��������� ������ ������� � ������������ ����������� ��������.
	*/
		include("query/IntDetail.php");
		break;

	case "IntCoDetail":
	/*
	   ���������������� ������ ������� � ������������ ����������� ��������
	   �� ���������� ������� �����.
	*/
		include("query/IntCoDetail.php");
		break;
	
	case "diagram";
	/*
	  �������� ��� ����������� ������������� ����������.

	*/
		include("query/diagram.php");
		break;

	case "CoDetail":
	/*
	   ���������������� ������ ������� �� ����������� ������� �����.
	*/
		include("query/CoDetail.php");
		break;

	case "NumDetail":
	/*
	   ���������������� ������ ���� ������� �� ����������� �����.
	*/
		include("query/NumDetail.php");
		break;

	case "CoNum":
	/*
	   �������������� ������ ��������� ������� �� ����������� �����.
	*/
		include("query/CoNum.php");
		break;
	
	case "CoNumDetail":
	/*
	   ���������������� ������ ������� �� ����������� ������ �� ����������� �����.
	*/
		include("query/CoNumDetail.php");
		break;
	
	case "IntNumDetail":
	/*
	   ���������������� ������ ������� �� ����������� ������ �� ����������� ��������.
	*/
		include("query/IntNumDetail.php");
		break;
	
	case "IntNum":
	/*
	   �������������� ������ ��������� ������� �� ����������� ���������� ��������.
	*/
		include("query/IntNum.php");
		break;
	
	case "NumAll":
	/*
	   �������������� ������ ���� ��������� �������.
	*/
		include("query/NumAll.php");
		break;

	case "CoIntDetail":
	/*
	   ���������������� ������ ������� � ������������ ����������� ��������
	   �� ���������� ������� �����.
	*/
		include("query/CoIntDetail.php");
		break;

	case "CoDetail":
	/*
	   ���������������� ������ ������� �� ����������� ������� �����.
	*/
		include("query/CoDetail.php");
		break;

	case "AllCalls":
	/*
	   ���������������� ������ ���� �������, � ����� ��������� ������.
	*/
		include("query/AllCalls.php");
		break;

   default:
	/* 
	   ������ ������� ����� � ����� ������������� �����������.
	*/
		include("query/CoAll.php");
		break;
}
$duration = microtime_diff($start_time, microtime());
$duration = sprintf("%0.3f", $duration);

if(empty($export)) {

    // �������� ������ �������
	if(!isset($pages)) $pages=0;
    if(empty($export)) pagesNavigator($pages,$page);

    include("../include/set/printfooter.html");

    include("../include/set/footer.html");

}

?>
