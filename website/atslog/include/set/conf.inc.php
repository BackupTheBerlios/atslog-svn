<?php
error_reporting(E_ERROR);
//error_reporting(E_ALL);
 /*
 
 
   ATSlog web interface settings
                                              
 */

// hostname of the SQL server 
$sqlhost='db.berlios.de';
// Atslog Database
$sqldatabase='atslog';
// atslog SQL user password
$sqlmaspasswd='p1w0DEDxDpy1XjT';
// atslog SQL user name
$sqlmasteruser='atslog';
// database type
$sqltype='mysql'; // PostgreSQL or MySQL
// PBX model
$model='DEMO';
// SQL cache directory.
$ADODB_CACHE_DIR = '/tmp/adodb';
$ADODB_CACHE_TTL = 60*60; // 1 hour. SQL query cache lifetime.
$debugMode=FALSE; // Debug mode.
$demoMode=TRUE; // Demo mode.
?>
