<?php
// error_reporting(E_ERROR);
 /*
 
   ATSlog web interface settings
                                              
 */
// hostname of the SQL server 
$sqlhost='localhost';
// Atslog Database
$sqldatabase='atslog';
// atslog SQL user password
$sqlmaspasswd='';
// atslog SQL user name
$sqlmasteruser='atslog';
// database type
$sqltype='mysql'; // PostgreSQL or MySQL
// PBX model
$model='';
// SQL cache directory.
$ADODB_CACHE_DIR = '/tmp/adodb';
$ADODB_CACHE_TTL = 60*60; // 1 hour. SQL query cache lifetime.
$debugMode=FALSE; // Debug mode.
$demoMode=FALSE; // Demo mode.

// Here you can define codes of your local mobile operators.
// here is example for the ukrainian mobile networks.
// Simply put your local operator codes to this array and uncomment this string
// $MobileCodes=array("050", "095", "066", "099", "067", "098", "068", "097", "096","039","063", "093", "094", "092");

?>
