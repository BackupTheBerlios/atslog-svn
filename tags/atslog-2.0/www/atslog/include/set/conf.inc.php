<?php
error_reporting(E_ERROR);
 /*
 
   ATSlog web interface settings
                                              
 */

// hostname of the SQL server 
$sqlhost='';
// Atslog Database
$sqldatabase='';
// atslog SQL user password
$sqlmaspasswd='';
// atslog SQL user name
$sqlmasteruser='';
// database type
$sqltype=''; // PostgreSQL or MySQL
// PBX model
$model='';
// SQL cache directory.
$ADODB_CACHE_DIR = '/tmp/adodb';
$ADODB_CACHE_TTL = 60*60; // 1 hour. SQL query cache lifetime.
$debugMode=FALSE; // Debug mode.
$demoMode=FALSE; // Demo mode.

?>
