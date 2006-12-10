<?php
///////////////////////////////////////////////////
//  Advanced Poll 2.0.3 (PHP/MySQL)              //
//  Copyright (c)2001 Chi Kien Uong              //
//  URL: http://www.proxy2.de                    //
///////////////////////////////////////////////////

$version = "v2.03";
if (!isset($PHP_SELF)) {
    $PHP_SELF = $HTTP_SERVER_VARS["PHP_SELF"];
    if (isset($HTTP_GET_VARS)) {
        while (list($name, $value)=each($HTTP_GET_VARS)) {
            $$name=$value;
        }
    }
    if (isset($HTTP_POST_VARS)) {
        while (list($name, $value)=each($HTTP_POST_VARS)) {
            $$name=$value;
        }
    }
}

if (!file_exists("./install/cross.gif")) {
  $img_loc="http://www.proxy2.de/poll/install";
} else {
  $img_loc="install";
}
if (file_exists("./include/config.inc.php")) {
  include "./include/config.inc.php";
} else {
  abort("Cannot find configuration file <u>config.inc.php</u>.");
}

if (file_exists("./include/$POLLDB[class]")) {
  include "./include/$POLLDB[class]";
} else {
  abort("Cannot find database class <u>$POLLDB[class]</u>.");
}

function print_header($refresh,$stp) {
global $PHP_SELF, $version, $img_loc;
$SELF = basename($PHP_SELF);
?>
<html>
<head>
<title>Advanced Poll <?php echo $version; ?> Installation</title>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="-1">
<?php
if ($refresh != -1) {
?>
<meta http-equiv="refresh" content="<?php echo "$refresh;URL=$SELF?action=$stp"; ?>">
<?php } ?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.td1 {  font-family: "MS Sans Serif"; font-size: 9pt}
.textarea {  font-family: "MS Sans Serif"; width: 450px; background-color: #C6C3C6; height: 140px; clip:  rect(   ); font-size: 9pt}
-->
</style>
<script language="Javascript">
<!--
function abort() {
 if (window.confirm("Do you wish to cancel the installation?")) {
    window.location.href = "http://"+window.location.host+window.location.pathname+"?action=cancel"
 }
}
function go_back() {
  history.go(-1);
}
function trim(value) {
 startpos=0;
 while((value.charAt(startpos)==" ")&&(startpos<value.length)) {
   startpos++;
 }
 if(startpos==value.length) {
   value="";
 } else {
   value=value.substring(startpos,value.length);
   endpos=(value.length)-1;
   while(value.charAt(endpos)==" ") {
     endpos--;
   }
   value=value.substring(0,endpos+1);
 }
 return(value);
}
function check_data() {
  document.FormPwd.username.value = trim(document.FormPwd.username.value);
  document.FormPwd.password.value = trim(document.FormPwd.password.value);
  if (document.FormPwd.username.value == "") {
    alert("You forgot to fill in the username field!");
    document.FormPwd.username.focus();
    return false;
  } else if (document.FormPwd.password.value == "") {
    alert("You forgot to fill in the password field!");
    document.FormPwd.password.focus();
    return false;
  } else if (document.FormPwd.password.value != document.FormPwd.confirm.value) {
    alert("The passwords do not match!");
    return false;
  }
}
function set_focus() {
  document.FormPwd.username.focus();
}
// -->
</script>
</head>

<?php
}

function welcome() {
global $PHP_SELF, $version, $img_loc;
$SELF = basename($PHP_SELF);
?>
<body bgcolor="#3A6EA5">
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="500">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="500" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?> Installation</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0" usemap="#close"><map name="close"><area shape="rect" coords="1,1,14,12" href="javascript:abort()"></map>
          </td>
        </tr>
        <tr align="center">
          <td colspan="2">
            <form method="post" action="<?php echo $SELF; ?>">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr bgcolor="#FFFFFF">
                  <td class="td1" height="60">
                    <table border="0" cellspacing="0" cellpadding="0" width="500">
                      <tr>
                        <td width="190"><img src="<?php echo $img_loc; ?>/install.gif" width="164" height="300" alt="Installation"></td>
                        <td width="310" valign="top" class="td1"><br>
                          <b><br>
                          Welcome to the Installation Wizard for <br>Advanced Poll
                          <?php echo $version; ?></b><br>
                          <br>
                          <br>
                          <br>
                          Press 'Next' button to begin the installation.</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center" height="20">
                    <img src="<?php echo $img_loc; ?>/h_line.gif" height="18" width="490">
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6" align="right">
                  <td><img src="<?php echo $img_loc; ?>/disabled.gif" width="75" height="22" border="0" alt="Back"><img src="<?php echo $img_loc; ?>/next.gif" width="75" height="22" usemap="#Next" border="0" alt="Next"><map name="Next"><area shape="rect" coords="1,1,74,21" href="<?php echo $SELF; ?>?action=step_1"></map>
                    &nbsp;&nbsp;<img src="<?php echo $img_loc; ?>/cancel.gif" width="75" height="22" usemap="#Cancel" border="0" alt="Cancel"><map name="Cancel"><area shape="rect" coords="1,2,73,20" href="javascript:abort()"></map>
                    &nbsp;&nbsp; </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
}

function license() {
global $PHP_SELF, $version, $img_loc;
$SELF = basename($PHP_SELF);
?>
<body bgcolor="#3A6EA5">
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="500">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="500" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?> Installation</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0" usemap="#close"><map name="close"><area shape="rect" coords="1,1,14,12" href="javascript:abort()"></map>
          </td>
        </tr>
        <tr align="center">
          <td colspan="2">
            <form method="post" action="<?php echo $SELF; ?>">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr bgcolor="#FFFFFF">
                  <td class="td1" height="30"><b>&nbsp;&nbsp;&nbsp;End-User Software
                    License Agreement</b></td>
                </tr>
                <tr bgcolor="#FFFFFF" valign="top">
                  <td class="td1" height="30"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please
                    read the following agreement carefully.</td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center">
                    <table width="400" border="0" cellspacing="0" cellpadding="0" align="center">
                      <tr>
                        <td class="td1" height="40">Press the PAGE DOWN to see
                          the rest of the agreement</td>
                      </tr>
                      <tr>
                        <td>
                          <font face="MS sans serif" size="1">
<textarea name="textfield" cols="48" rows="10" wrap="VIRTUAL" class="textarea">
You should carefully read the following terms and conditions before using this software. Your use of this software indicates your acceptance of this license agreement. If you do not agree to all the terms of this agreement, do not use this software.

1. This script may be used and modified free of charge for personal, academic and non-profit use. Commercial use must register the script for a one time fee of $15 US or 15 EUR.
--> http://www.proxy2.de/register
Registered users receive support, free upgrades and also do not have to display the text link. 

2. You may copy and distribute verbatim copies of the Program's source code as 
you receive it, in any medium, provided that you conspicuously and appropriately 
publish on each copy an appropriate copyright notice. Keep intact all the notices
that refer to this License and give any other recipients of the Program a copy of this License along with the Program.

3. Selling the code for this program, or a program derived from Advanced Poll, without 
prior written consent is expressly forbidden.

THIS SOFTWARE AND THE ACCOMPANYING FILES ARE PROVIDED "AS IS" 
WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING 
BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS FOR A PARTICULAR PURPOSE. THE AUTHOR DOES NOT WARRANT 
THAT THE FUNCTIONS CONTAINED IN THE SOFTWARE WILL MEET YOUR 
REQUIREMENTS, OR THAT THE OPERATION OF THE SOFTWARE WILL BE 
UNINTERRUPTED OR ERROR-FREE, OR THAT ANY DEFECTS DISCOVERED IN 
THE SOFTWARE WILL BE CORRECTED. FURTHERMORE, THE AUTHOR DOES 
NOT WARRANT OR MAKE ANY REPRESENTATIONS REGARDING THE USE OR 
THE RESULTS OF THE USE OF THE SOFTWARE IN TERMS OF ITS CORRECTNESS,
ACCURACY, RELIABILITY, OR OTHERWISE. YOU ASSUME ALL RISKS IN 
USING THE SOFTWARE. NO ORAL OR WRITTEN INFORMATION OR ADVICE 
GIVEN BY THE AUTHOR OR ANY OF THEIR AUTHORIZED REPRESENTATIVES 
SHALL CREATE A WARRANTY OR IN ANY WAY INCREASE THE SCOPE OF THIS 
WARRANTY. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES.
</textarea></font>
                        </td>
                      </tr>
                      <tr>
                        <td class="td1" height="50">Do you accept all the terms
                          of the preceding Lisence Agrement? If you choose NO,
                          the setup will close. To install Advanced Poll <?php echo $version; ?>,
                          you must accept this agreement. </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center" height="20">
                    <img src="<?php echo $img_loc; ?>/h_line.gif" height="18" width="490">
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6" align="right">
                  <td> <img src="<?php echo $img_loc; ?>/back.gif" width="75" height="22" usemap="#Back" border="0" alt="Back"><map name="Back"><area shape="rect" coords="1,2,73,20" href="javascript:go_back()"></map><img src="<?php echo $img_loc; ?>/yes.gif" width="75" height="22" usemap="#Yes" border="0" alt="Yes"><map name="Yes"><area shape="rect" coords="2,2,73,20" href="<?php echo $SELF; ?>?action=step_2"></map>
                    &nbsp; <img src="<?php echo $img_loc; ?>/no.gif" width="75" height="22" usemap="#No" border="0" alt="No"><map name="No"><area shape="rect" coords="1,3,73,20" href="javascript:abort()"></map>&nbsp;&nbsp;
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
}

function install_process($msg,$stat,$stp) {
global $PHP_SELF, $version, $img_loc;
$SELF = basename($PHP_SELF);
?>
<body bgcolor="#3A6EA5">
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="500">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="500" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?> Installation</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0" usemap="#close"><map name="close"><area shape="rect" coords="1,1,14,12" href="javascript:abort()"></map>
          </td>
        </tr>
        <tr align="center">
          <td colspan="2">
            <form method="post" action="<?php echo $SELF; ?>">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr bgcolor="#FFFFFF" valign="bottom">
                  <td class="td1" height="30"><b>&nbsp;&nbsp;&nbsp;&nbsp;Installation
                    Process</b></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                  <td class="td1" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $msg; ?>.</td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td class="td1" align="center">
                    <table width="400" border="0" cellspacing="0" cellpadding="0" align="center">
                      <tr>
                        <td class="td1" height="75">Please wait while the Installation
                          Wizard creates the tables needed to install Advanced Poll
                          on your server. This may take a few moments.</td>
                      </tr>
                      <tr>
                        <td height="140" class="td1"> Please wait...
                          <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><img src="<?php echo $img_loc; ?>/status.gif" width="<?php echo $stat; ?>" height="16"></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center" height="20">
                    <img src="<?php echo $img_loc; ?>/h_line.gif" height="18" width="490">
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="right"><img src="<?php echo $img_loc; ?>/disabled.gif" width="75" height="22" border="0" alt="Back"><img src="<?php echo $img_loc; ?>/next.gif" width="75" height="22" border="0" usemap="#Next" alt="Next"><map name="Next"><area shape="rect" coords="1,1,73,20" href="<?php echo "$SELF?action=$stp"; ?>"></map>
                    &nbsp;&nbsp;<img src="<?php echo $img_loc; ?>/cancel.gif" width="75" height="22" usemap="#Cancel" border="0" alt="Cancel"><map name="Cancel"><area shape="rect" coords="1,1,73,20" href="javascript:abort()"></map>
                    &nbsp;&nbsp; </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
}

function create_account() {
global $PHP_SELF, $version, $img_loc;
$SELF = basename($PHP_SELF);
?>
<body bgcolor="#3A6EA5" onload="set_focus()">
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="500">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="500" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?> Installation</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0" usemap="#close"><map name="close"><area shape="rect" coords="1,1,14,12" href="javascript:abort()"></map>
          </td>
        </tr>
        <tr align="center">
          <td colspan="2">
            <form method="post" action="<?php echo $SELF; ?>" name="FormPwd" onsubmit="return check_data()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr bgcolor="#FFFFFF" valign="bottom">
                  <td class="td1" height="30"><b>&nbsp;&nbsp;&nbsp;&nbsp;Installation
                    Process</b></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                  <td class="td1" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    All tables were created successfully!</td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td class="td1" valign="top" align="center">
                    <table width="400" border="0" cellspacing="0" cellpadding="0" align="center">
                      <tr>
                        <td class="td1" height="75">To complete the installation enter a username
                          and password.</td>
                      </tr>
                      <tr>
                        <td height="140" class="td1">
                          <table width="100%" border="0" cellspacing="0" cellpadding="3">
                            <tr>
                              <td width="18%" class="td1">Username:</td>
                              <td width="82%">
                                <input type="text" name="username" size="25">
                              </td>
                            </tr>
                            <tr>
                              <td width="18%" class="td1">Password:</td>
                              <td width="82%">
                                <input type="password" name="password" size="25">
                              </td>
                            </tr>
                            <tr>
                              <td width="18%" class="td1">Confirm:</td>
                              <td width="82%">
                                <input type="password" name="confirm" size="25">
                                <input type="hidden" name="action" value="step_6">
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center" height="20">
                    <img src="<?php echo $img_loc; ?>/h_line.gif" height="18" width="490">
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="right"><img src="<?php echo $img_loc; ?>/disabled.gif" width="75" height="22" border="0" alt="Back"><input type="image" src="install/next.gif" width="75" height="22" border="0" alt="Next">
                    &nbsp;&nbsp;<img src="<?php echo $img_loc; ?>/cancel.gif" width="75" height="22" usemap="#Cancel" border="0" alt="Cancel"><map name="Cancel"><area shape="rect" coords="1,1,73,20" href="javascript:abort()"></map>
                    &nbsp;&nbsp; </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
}

function abort($msg) {
global $version, $img_loc, $PHP_SELF;
$SELF = basename($PHP_SELF);
?>
<html>
<head>
<title>Advanced Poll <?php echo $version; ?></title>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.td1 {  font-family: "MS Sans Serif"; font-size: 9pt}
-->
</style>
</head>
<body bgcolor="#3A6EA5">
<br><br><br><br><br><br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="300">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="300" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?></font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0">
          </td>
        </tr>
        <tr align="center">
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr bgcolor="#C6C3C6">
                <td align="center" height="75" width="20%"><img src="<?php echo $img_loc; ?>/info.gif" width="35" height="35"></td>
                <td align="left" height="75" width="80%" class="td1"><?php echo $msg; ?></td>
              </tr>
              <tr bgcolor="#C6C3C6" align="center">
                <td colspan="2" height="40"><img src="<?php echo $img_loc; ?>/disabled.gif" width="75" height="22" border="0" alt="Back">&nbsp;&nbsp;<img src="<?php echo $img_loc; ?>/ok.gif" width="75" height="22" border="0" alt="Ok"></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
exit();
}

function poll_error_msg($strg) {
global $PHP_SELF, $version, $img_loc;
$SELF = basename($PHP_SELF);
?>
<body bgcolor="#3A6EA5">
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="500">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="500" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?> Installation</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0" usemap="#close"><map name="close"><area shape="rect" coords="1,1,14,12" href="javascript:abort()"></map>
          </td>
        </tr>
        <tr align="center">
          <td colspan="2">
            <form method="post" action="<?php echo $SELF; ?>">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr bgcolor="#FFFFFF">
                  <td class="td1" height="60">
                    <table border="0" cellspacing="0" cellpadding="0" width="500">
                      <tr>
                        <td width="190"><img src="<?php echo $img_loc; ?>/install.gif" width="164" height="300"></td>
                        <td width="310" valign="top" class="td1"><br>
                          <b><br>
                          Welcome to the Installation Wizard for <br>Advanced Poll
                          <?php echo $version; ?></b><br>
                          <br>
                          <br>
                          <br>
                          <font color="#FF0000"><?php echo $strg; ?></font>
                          </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center" height="20">
                    <img src="<?php echo $img_loc; ?>/h_line.gif" height="18" width="490">
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="right"><img src="<?php echo $img_loc; ?>/disabled.gif" width="75" height="22" border="0" alt="Back"><img src="<?php echo $img_loc; ?>/next.gif" width="75" height="22" border="0" usemap="#Next" alt="Back"><map name="Next"><area shape="rect" coords="1,1,73,20" href="<?php echo $SELF; ?>"></map>
                    &nbsp;&nbsp;<img src="<?php echo $img_loc; ?>/cancel.gif" width="75" height="22" usemap="#Cancel" border="0" alt="Cancel"><map name="Cancel"><area shape="rect" coords="1,1,73,20" href="javascript:abort()"></map>
                    &nbsp;&nbsp; </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
}

function inst_complete() {
global $version, $img_loc, $PHP_SELF;
$SELF = basename($PHP_SELF);
?>
<body bgcolor="#3A6EA5">
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="500">
  <tr bgcolor="#C6C3C6">
    <td>
      <table width="500" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#000084"><b><font color="#FFFFFF">
            &nbsp;Advanced Poll <?php echo $version; ?> Installation</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#000084"><img src="<?php echo $img_loc; ?>/cross.gif" width="16" height="14" border="0">
          </td>
        </tr>
        <tr align="center">
          <td colspan="2">
            <form method="post" action="<?php echo $SELF; ?>">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr bgcolor="#FFFFFF" valign="bottom">
                  <td class="td1" height="30"><b>&nbsp;&nbsp;&nbsp;&nbsp;Installation
                    Process</b></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                  <td class="td1" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Advanced Poll has been installed successfully.</td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td class="td1" align="center">
                    <table width="400" border="0" cellspacing="0" cellpadding="0" align="center">
                      <tr>
                        <td class="td1" height="75">Thank you for using Advanced Poll.<br>
                        If you installed Advanced Poll for the first time, do
                        not forget to read the license.</td>
                      </tr>
                      <tr>
                        <td height="140" class="td1"> Installation complete...
                          <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><img src="<?php echo $img_loc; ?>/status.gif" width="395" height="16"></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="center" height="20">
                    <img src="<?php echo $img_loc; ?>/h_line.gif" height="18" width="490">
                  </td>
                </tr>
                <tr bgcolor="#C6C3C6">
                  <td align="right"><img src="<?php echo $img_loc; ?>/disabled.gif" width="75" height="22" border="0" alt="Back"><img src="<?php echo $img_loc; ?>/done.gif" width="75" height="22" border="0" usemap="#Next" alt="Done"><map name="Next"><area shape="rect" coords="1,1,73,20" href="demo_1.php"></map>
                    &nbsp;&nbsp;</td>
                </tr>
              </table>
             </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<br>
<br>
</body>
</html>
<?php
}

if (!isset($action)) {
  $action ='';
}

switch ($action) {

case "step_1":
  print_header(200,"step_2");
  license();
  break;

case "step_2":
  print_header(2,"step_3");
  install_process("Connecting to $POLLDB[host].",1,"step_3");
  break;

case "step_3":

  if(ereg("class_mysql",$POLLDB["class"])) {
    include "./misc/tb_mysql.php";
    $serverid  = mysql_connect($POLLDB["host"], $POLLDB["user"], $POLLDB["pass"]) or poll_error_msg("Cannot connect to mysql database");
    if(!@mysql_select_db($POLLDB["dbName"],$serverid)) {
        mysql_create_db($POLLDB["dbName"]);
    }
  } elseif (ereg("class_pgsql",$POLLDB["class"])) {
      include "./misc/tb_pgsql.php";
      $conn_strg = '';
      if ($POLLDB["user"]) {
        $conn_strg .= "user=$POLLDB[user] ";
      }
      if ($POLLDB["pass"]) {
        $conn_strg .= "password=$POLLDB[pass] ";
      }
      $serverid = @pg_connect("host=$POLLDB[host] port=5432 dbname=$POLLDB[dbName] $conn_strg");
      if(!$serverid) {
        $serverid = pg_connect("host=$POLLDB[host] port=5432 $conn_strg dbname=template1");
        pg_exec("CREATE DATABASE $POLLDB[dbName]");
      }
  } else {
      abort("<u>$POLLDB[class]</u> : Cannot new database object.");
  }
   
  $poll_db = new polldb_sql();
  $poll_db->connect();
  
  for ($i=0; $i<sizeof($tbl); $i++) {
    $poll_db->query($tbl[$i]);
  }
  
  print_header(2,"step_4");
  install_process("Connecting to '$POLLDB[host]' succeed. Creating tables...",150,"step_4");
  break;

case "step_4":
  $timestamp = time();
  $timestamp2 = $timestamp+50;
  $exp_time = time()+10*86400;
  if (eregi("php.exe",$PHP_SELF)) {
    if (isset($HTTP_SERVER_VARS["PATH_INFO"]) && !empty($HTTP_SERVER_VARS["PATH_INFO"])) {
        $GB_PG["base_url"] = dirname($HTTP_SERVER_VARS["PATH_INFO"]);
    } elseif (isset($HTTP_SERVER_VARS["REQUEST_URI"]) && !empty($HTTP_SERVER_VARS["REQUEST_URI"])) {
        $GB_PG["base_url"] = dirname($HTTP_SERVER_VARS["REQUEST_URI"]);
    } else {
        $GB_PG["base_url"] = dirname($HTTP_SERVER_VARS["SCRIPT_NAME"]);
    }
  } else {
      $image_url = dirname($PHP_SELF);
  }
  $base_url = $image_url;
  $image_url .= "/image";
  $now = date("Y-m-d H:i:s",time());
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_config] (config_id, base_gif, lang, title, vote_button, result_text, total_text, voted, send_com, img_height, img_length, table_width, bgcolor_tab, bgcolor_fr, font_face, font_color, type, check_ip, lock_timeout, time_offset, entry_pp, poll_version, base_url, result_order, def_options, polls_pp) VALUES (1, '$image_url', 'english.php', 'Advanced Poll', 'Vote', 'View results', 'Total votes', 'You have already voted!', 'Send comment', 10, 42, '170', '#FFFFFF', '#666699', 'Verdana, Arial, Helvetica, sans-serif', '#000000', 'percent', 0, 2, '0', 5, '2.03', '$base_url', 'desc', 10, 12)";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_index] (poll_id, question, timestamp, status, logging, exp_time, expire, comments) VALUES ( '1', 'Which OS is your Website running on?', '$timestamp', '1', '1', '$exp_time', '1', '1')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_index] (poll_id, question, timestamp, status, logging, exp_time, expire, comments) VALUES ( '2', 'Which database engine do you prefer?', '$timestamp2', '1', '0', '$exp_time', '0', '1')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_index] (poll_id, question, timestamp, status, logging, exp_time, expire, comments) VALUES (3, 'What is your favourite scripting language?', $timestamp2, 1, 0, $exp_time, 0, 1)";   
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '1', '1', '1', 'Linux', 'blue', '49')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '2', '1', '2', 'Solaris', 'yellow', '12')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '3', '1', '3', 'FreeBSD', 'green', '29')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '4', '1', '4', 'WindowsNT', 'brown', '17')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '5', '1', '5', 'Unix', 'grey', '10')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '6', '1', '6', 'BSD', 'red', '15')";
  $tbl_data[]  = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '7', '1', '7', 'other', 'purple', '9')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '8', '2', '5', 'Sybase', 'orange', '2')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '9', '2', '4', 'MS SQL', 'green', '9')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '10', '2', '3', 'Oracle', 'blue', '17')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '11', '2', '2', 'PostgreSQL', 'gold', '6')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '12', '2', '1', 'MySQL', 'pink', '23')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '13', '2', '6', 'other', 'brown', '3')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES ( '14', '2', '7', 'DB/2', 'grey', '4')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES (15, 3, 1, 'PHP', 'red', 65)";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES (16, 3, 2, 'Perl', 'orange', 34)";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES (17, 3, 3, 'ASP', 'green', 17)";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES (18, 3, 4, 'JSP', 'purple', 20)";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES (19, 3, 5, 'Python', 'gold', 7)";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_data] (id, poll_id, option_id, option_text, color, votes) VALUES (20, 3, 6, 'other', 'aqua', 16)";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_comment] (com_id, poll_id, time, host, browser, name, email, message) VALUES ( '1', '1', '$timestamp', 'localhost', 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0)', 'nobody', 'nobody@server.com', 'This is the first comment!')";
  
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (1,1,'display_head','<table width=\"\$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"\$pollvars[bgcolor_fr]\">\r\n  <tr align=\"center\">\r\n    <td>\r\n      <style type=\"text/css\">\r\n <!--\r\n  .input { font-family: \$pollvars[font_face]; font-size: 8pt}\r\n -->\r\n</style>\r\n      <font face=\"\$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>\$pollvars[title]</b></font></td>\r\n  </tr>\r\n  <tr align=\"center\"> \r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"\$pollvars[bgcolor_tab]\">\r\n        <tr> \r\n          <td height=\"40\" valign=\"middle\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><b>\$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"right\" valign=\"top\"> \r\n          <td>\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n                <tr valign=\"top\" align=\"center\"> \r\n                  <td> \r\n                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (2,1,'display_loop',' <tr> \r\n   <td width=\"15%\"><input type=\"radio\" name=\"option_id\" value=\"\$data[option_id]\"></td>\r\n   <td width=\"85%\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"\$pollvars[font_color]\">\$data[option_text]</font></td>\r\n </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (3,1,'display_foot','                    </table>\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"\$poll_id\">\r\n                    <input type=\"submit\" value=\"\$pollvars[vote_button]\" class=\"input\">\r\n                    <br>\r\n                    <br>\r\n                    <font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><a href=\"\$this->form_forward?action=results&amp;poll_ident=\$poll_id\">\$pollvars[result_text]</a></font>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n            <font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (4,1,'result_head','<table width=\"\$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"\$pollvars[bgcolor_fr]\">\r\n<tr align=\"center\">\r\n<td>\r\n<style type=\"text/css\">\r\n<!--\r\n .input { font-family: \$pollvars[font_face]; font-size: 8pt}\r\n .links { font-family: \$pollvars[font_face]; font-size: 7.5pt; color: \$pollvars[font_color]}\r\n-->\r\n</style>\r\n<font face=\"\$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>\$pollvars[title]</b></font></td>\r\n</tr>\r\n<tr align=\"center\">\r\n <td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"\$pollvars[bgcolor_tab]\">\r\n <tr valign=\"middle\">\r\n   <td height=\"40\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><b>\$question</b></font></td>\r\n </tr>\r\n <tr align=\"right\" valign=\"bottom\">\r\n   <td>\r\n     <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" align=\"center\">\r\n       <tr valign=\"top\">\r\n        <td>\r\n         <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (5,1,'result_loop','<tr>\r\n    <td height=\"22\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$option_text</font></td>\r\n    <td nowrap height=\"22\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><img src=\"\$pollvars[base_gif]/\$poll_color.gif\" width=\"\$img_width\" height=\"\$pollvars[img_height]\"> \$vote_val</font></td>\r\n</tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (6,1,'result_foot','       </table>\r\n       <font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><br>\r\n       \$pollvars[total_text]: <font color=\"#CC0000\">\$total_votes</font><br>\r\n       \$VOTE<br><br><div align=\"center\">\r\n       \$COMMENT&nbsp;</div></font>\r\n       </td></tr>\r\n      <tr><td height=\"2\">&nbsp;</td></tr>\r\n     </table>\r\n    <font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font></td>\r\n   </tr>\r\n </table>\r\n</td>\r\n</tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (7,1,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Send Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#FFFFFF\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">\$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"\$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (8,2,'display_head','<table width=\"\$pollvars[table_width]\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" bgcolor=\"#F3F3F3\">\r\n  <tr valign=\"top\"> \r\n    <td valign=\"top\" align=\"right\">\r\n      <form method=\"post\" action=\"\$this->form_forward\">\r\n        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\r\n          <tr bgcolor=\"\$pollvars[bgcolor_fr]\"> \r\n            <td colspan=\"2\" height=\"30\"><font face=\"\$pollvars[font_face]\" color=\"#FFFFFF\" size=\"1\"><b>\r\n              \$question</b></font></td>\r\n          </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (9,2,'display_loop','<tr> \r\n    <td width=\"14%\"><input type=\"radio\" name=\"option_id\" value=\"\$data[option_id]\"></td>\r\n    <td width=\"86%\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"\$pollvars[font_color]\">\$data[option_text]</font></td>\r\n</tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (10,2,'display_foot','          <tr align=\"center\"> \r\n            <td colspan=\"2\"> \r\n              <input type=\"image\" border=\"0\" src=\"\$pollvars[base_gif]/vote.gif\" width=\"110\" height=\"48\">\r\n              <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n              <input type=\"hidden\" name=\"poll_ident\" value=\"\$poll_id\">\r\n              <br>\r\n              <font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><a href=\"\$this->form_forward?action=results&amp;poll_ident=\$poll_id\">\$pollvars[result_text]</a>\r\n            </td>\r\n          </tr>\r\n        </table>\r\n      </form>\r\n      <font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font>\r\n     </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (11,2,'result_head','<table width=\"170\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F3F3F3\">\r\n  <tr> \r\n    <td colspan=\"2\" height=\"25\" bgcolor=\"\$pollvars[bgcolor_fr]\"><font face=\"\$pollvars[font_face]\" color=\"#FFFFFF\" size=\"1\"><b>\$question</b></font></td>\r\n  </tr>\r\n\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (12,2,'result_loop','  <tr> \r\n    <td colspan=\"2\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$option_text</font></td>\r\n  </tr>\r\n  <tr> \r\n    <td width=\"52%\"><img src=\"\$pollvars[base_gif]/greenbar.gif\" width=\"\$img_width\" height=\"7\"></td>\r\n    <td width=\"48%\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$vote_val</font></td>\r\n  </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (13,2,'result_foot','  <tr> \r\n    <td colspan=\"2\" height=\"40\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"> \r\n      \$pollvars[total_text]: <font color=\"#CC0000\">\$total_votes</font>\r\n      <br>\$VOTE</font><br><div align=\"center\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$COMMENT</font></div>\r\n    </td>\r\n  </tr>\r\n  <tr align=\"right\" valign=\"bottom\" height=\"30\"> \r\n    <td colspan=\"2\"><font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font></td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (14,2,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Send Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#F3F3F3\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">\$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"\$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (15,3,'display_head','<table width=\"\$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"\$pollvars[bgcolor_fr]\">\r\n  <tr align=\"center\">\r\n    <td>\r\n      <style type=\"text/css\">\r\n <!--\r\n  .input { font-family: \$pollvars[font_face]; font-size: 8pt}\r\n -->\r\n</style>\r\n      <font face=\"\$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>\$pollvars[title]</b></font></td>\r\n  </tr>\r\n  <tr align=\"center\"> \r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"\$pollvars[bgcolor_tab]\">\r\n        <tr> \r\n          <td height=\"40\" valign=\"middle\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><b>\$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"right\" valign=\"top\"> \r\n          <td>\r\n            <form method=\"post\" name=\"poll_\$poll_id\" onsubmit=\"return poll_results_\$poll_id(\'vote\',\'\$pollvars[base_url]/popup.php\',\'Poll\',\'500\',\'350\',\'toolbar=no,scrollbars=yes\');\">\r\n              <script language=\"JavaScript\">\r\n<!--\r\nfunction poll_results_\$poll_id(action,theURL,winName,winWidth,winHeight,features) {      \r\n    var w = (screen.width - winWidth)/2;\r\n    var h = (screen.height - winHeight)/2 - 20;\r\n    features = features+\',width=\'+winWidth+\',height=\'+winHeight+\',top=\'+h+\',left=\'+w;\r\n    var poll_ident = self.document.poll_\$poll_id.poll_ident.value;\r\n    option_id = \'\';\r\n    for (i=0; i<self.document.poll_\$poll_id.option_id.length; i++) {\r\n        if(self.document.poll_\$poll_id.option_id[i].checked == true) {\r\n            option_id = self.document.poll_\$poll_id.option_id[i].value;\r\n            break;\r\n        }\r\n    }\r\n    option_id = (option_id != \'\') ? \'&option_id=\'+option_id : \'\';\r\n    if (action==\'results\' || (option_id != \'\' && action==\'vote\')) {\r\n        theURL = theURL+\'?action=\'+action+\'&poll_ident=\'+poll_ident+option_id;\r\n        poll_popup = window.open(theURL,winName,features);\r\n        poll_popup.focus();\r\n    }\r\n    return false;\r\n}\r\n//-->\r\n        </script>\r\n              <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n                <tr valign=\"top\" align=\"center\"> \r\n                  <td> \r\n                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (16,3,'display_loop',' <tr> \r\n   <td width=\"15%\"><input type=\"radio\" name=\"option_id\" value=\"\$data[option_id]\"></td>\r\n   <td width=\"85%\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"\$pollvars[font_color]\">\$data[option_text]</font></td>\r\n </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (17,3,'display_foot','                    </table>\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"\$poll_id\">\r\n                    <input type=\"submit\" value=\"\$pollvars[vote_button]\" class=\"input\">\r\n                    <br>\r\n                    <br>\r\n                    <font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><a href=\"javascript:void(poll_results_\$poll_id(\'results\',\'\$pollvars[base_url]/popup.php\',\'Poll\',\'500\',\'350\',\'toolbar=no,scrollbars=yes\'))\">\$pollvars[result_text]</a><br>\r\n                    </font></td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n            <font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (18,3,'result_head','<table width=\"450\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCCCCC\">\r\n  <tr>\r\n    <td align=\"center\"> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F6F6F6\">\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" height=\"40\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"#000000\"><b>\$question</b></font></td>\r\n        </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (19,3,'result_loop','        <tr>\r\n          <td width=\"3%\">&nbsp;</td>\r\n          <td><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$option_text</font></td>\r\n          <td><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><img src=\"\$pollvars[base_gif]/\$poll_color.gif\" width=\"\$img_width\" height=\"\$pollvars[img_height]\"> \$vote_percent % (\$vote_count)</font></td>\r\n        </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (20,3,'result_foot','        <tr> \r\n          <td colspan=\"3\" valign=\"bottom\" align=\"center\"><b><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$pollvars[total_text]: \r\n            \$total_votes</font></b></td>\r\n        </tr>\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" valign=\"top\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$VOTE \r\n            <br>\r\n            \$COMMENT</font></td>\r\n        </tr>\r\n        <tr align=\"right\"> \r\n          <td colspan=\"3\"><font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font></td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (21,3,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Submit Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#FFFFFF\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">\$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"\$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (22,4,'display_head','<table width=\"\$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"\$pollvars[bgcolor_fr]\">\r\n  <tr align=\"center\">\r\n    <td>\r\n      <style type=\"text/css\">\r\n <!--\r\n  .input { font-family: \$pollvars[font_face]; font-size: 8pt}\r\n -->\r\n</style>\r\n      <font face=\"\$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>\$pollvars[title]</b></font></td>\r\n  </tr>\r\n  <tr align=\"center\"> \r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"\$pollvars[bgcolor_tab]\">\r\n        <tr> \r\n          <td height=\"40\" valign=\"middle\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><b>\$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"right\" valign=\"top\"> \r\n          <td>\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n               <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n                <tr valign=\"top\" align=\"center\"> \r\n                  <td> \r\n                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (23,4,'display_loop','<tr> \r\n   <td width=\"15%\"><input type=\"radio\" name=\"option_id\" value=\"\$data[option_id]\"></td>\r\n   <td width=\"85%\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"\$pollvars[font_color]\">\$data[option_text]</font></td>\r\n </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (24,4,'display_foot','                    </table>\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"\$poll_id\">\r\n                    <input type=\"submit\" value=\"\$pollvars[vote_button]\" class=\"input\">\r\n                    <br>\r\n                    <br>\r\n                    <font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><a href=\"\$this->form_forward?action=results&amp;poll_ident=\$poll_id\">\$pollvars[result_text]</a><br>\r\n                    </font></td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n            <font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (25,4,'result_head','<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"360\">\r\n  <tr> \r\n    <td colspan=\"2\"><font face=\"\$pollvars[font_face]\" size=\"2\">\$question</font></td>\r\n  </tr>\r\n  <tr> \r\n    <td><img src=\"\$pollvars[base_url]/png.php?poll_id=\$poll_id\"></td>\r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"#000000\">\r\n        <tr> \r\n          <td> \r\n            <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#EBEBEB\">')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (26,4,'result_loop','            <tr> \r\n                <td width=\"12\"><font size=\"1\" face=\"\$pollvars[font_face]\"><img src=\"\$pollvars[base_gif]/\$poll_color.gif\" width=\"8\" height=\"10\"></font></td>\r\n                <td><font size=\"1\" face=\"\$pollvars[font_face]\">\$option_text -\r\n                  \$vote_val</font></td>\r\n              </tr>')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (27,4,'result_foot','              <tr> \r\n                <td>&nbsp;</td>\r\n                <td><font face=\"\$pollvars[font_face]\" size=\"1\">\$pollvars[total_text]: \r\n                 <font color=\"#990000\">\$total_votes</font><br>\r\n                 \$COMMENT&nbsp;</font></td>\r\n              </tr>\r\n            </table>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (28,4,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Submit Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#FFFFFF\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">\$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"\$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (29,5,'display_head','<table width=\"450\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCCCCC\">\r\n  <tr>\r\n    <td align=\"center\">\r\n     <style type=\"text/css\">\r\n       <!--\r\n        .input { font-family: \$pollvars[font_face]; font-size: 9pt}\r\n       -->\r\n      </style> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F6F6F6\">\r\n        <tr align=\"center\"> \r\n          <td colspan=\"2\" height=\"40\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><b>\$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"center\"> \r\n          <td colspan=\"2\"> \r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (30,5,'display_loop',' <tr> \r\n   <td width=\"11%\" align=\"center\"><input type=\"radio\" name=\"option_id\" value=\"\$data[option_id]\"></td>\r\n   <td width=\"89%\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"\$pollvars[font_color]\">\$data[option_text]</font></td>\r\n </tr>')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (31,5,'display_foot','                <tr align=\"center\" valign=\"bottom\"> \r\n                  <td colspan=\"2\"> \r\n                    <input type=\"submit\" value=\"\$pollvars[vote_button]\" class=\"input\" name=\"submit\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"\$poll_id\">\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"bottom\"> \r\n                  <td colspan=\"2\" height=\"30\" align=\"center\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">[<a href=\"\$this->form_forward?action=results&amp;poll_ident=\$poll_id\">\$pollvars[result_text]</a>]</font></td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (32,5,'result_head','<table width=\"450\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCCCCC\">\r\n  <tr>\r\n    <td align=\"center\"> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F6F6F6\">\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" height=\"40\"><font face=\"\$pollvars[font_face]\" size=\"1\" color=\"#000000\"><b>\$question</b></font></td>\r\n        </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (33,5,'result_loop','        <tr>\r\n          <td width=\"3%\">&nbsp;</td>\r\n          <td><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$option_text</font></td>\r\n          <td><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\"><img src=\"\$pollvars[base_gif]/\$poll_color.gif\" width=\"\$img_width\" height=\"\$pollvars[img_height]\"> \$vote_percent % (\$vote_count)</font></td>\r\n        </tr>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (34,5,'result_foot','        <tr> \r\n          <td colspan=\"3\" valign=\"bottom\" align=\"center\"><b><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$pollvars[total_text]: \r\n            \$total_votes</font></b></td>\r\n        </tr>\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" valign=\"top\"><font face=\"\$pollvars[font_face]\" color=\"\$pollvars[font_color]\" size=\"1\">\$VOTE \r\n            <br>\r\n            \$COMMENT</font></td>\r\n        </tr>\r\n        <tr align=\"right\"> \r\n          <td colspan=\"3\"><font face=\"\$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version \$pollvars[poll_version]</a></font></td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (35,5,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Send Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#F3F3F3\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"\$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">\$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"\$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (36,0,'poll_comment','<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\" width=\"450\">\r\n  <tr bgcolor=\"#E4E4E4\"> \r\n    <td bgcolor=\"#F2F2F2\"><b><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">\$data[name]</font></b> \r\n      <i><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">\$data[email]</font></i> \r\n      - <font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">\$data[time]</font><br>\r\n      <font size=\"1\" face=\"Arial, Helvetica, sans-serif\">\$data[host] - \$data[browser]</font> \r\n    </td>\r\n  </tr>\r\n  <tr>\r\n    <td><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">\$data[message]</font> \r\n    </td>\r\n  </tr>\r\n  <tr> \r\n    <td height=\"15\">&nbsp;</td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (37,0,'poll_list','<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"450\">\r\n  <tr> \r\n    <td width=\"80\" valign=\"top\"> &#0149; <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><i>\$data[timestamp]</i></font></td>\r\n    <td width=\"354\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"\$PHP_SELF?poll_id=\$data[poll_id]\">\$data[question]</a></font></td>\r\n  </tr>\r\n</table>\r\n')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tpl] VALUES (38,0,'poll_form','<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n  <tr> \r\n    <td> \r\n      <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .poll_textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 300px}\r\n       .poll_input {  width: 300px}\r\n      -->\r\n    </style>\r\n      <form method=\"post\" action=\"\$this->form_forward\">\r\n        <table border=\"0\" cellspacing=\"0\" cellpadding=\"4\">\r\n          <tr>\r\n            <td class=\"td1\"><font color=\"#CC0000\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\"><b>\$msg</b></font></td>\r\n          </tr>\r\n          <tr> \r\n            <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n              <input type=\"text\" name=\"name\" value=\"\$comment[name]\" maxlength=\"30\" class=\"poll_input\" size=\"25\">\r\n            </td>\r\n          </tr>\r\n          <tr> \r\n            <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">E-mail:</font><br>\r\n              <input type=\"text\" name=\"email\" value=\"\$comment[email]\" size=\"25\" maxlength=\"50\" class=\"poll_input\">\r\n            </td>\r\n          </tr>\r\n          <tr> \r\n            <td class=\"td1\"> <font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment:</font><br>\r\n              <font face=\"MS Sans Serif\" size=\"1\"> \r\n              <textarea name=\"message\" cols=\"25\" wrap=\"VIRTUAL\" rows=\"8\" class=\"poll_textarea\">\$comment[message]</textarea>\r\n              </font> </td>\r\n          </tr>\r\n          <tr valign=\"top\"> \r\n            <td> \r\n              <input type=\"submit\" value=\"Submit\" class=\"button\" name=\"submit\">\r\n              <input type=\"reset\" value=\"Reset\" class=\"button\" name=\"reset\">\r\n              <input type=\"hidden\" name=\"action\" value=\"add\">\r\n              <input type=\"hidden\" name=\"pcomment\" value=\"\$poll_id\">\r\n            </td>\r\n          </tr>\r\n        </table>\r\n      </form>\r\n    </td>\r\n  </tr>\r\n</table>\r\n')";

  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tplset] (tplset_id, tplset_name, created) VALUES (1, 'default', '$now')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tplset] (tplset_id, tplset_name, created) VALUES (2, 'simple', '$now')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tplset] (tplset_id, tplset_name, created) VALUES (3, 'popup', '$now')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tplset] (tplset_id, tplset_name, created) VALUES (4, 'graphic', '$now')";
  $tbl_data[] = "INSERT INTO $POLLTBL[poll_tplset] (tplset_id, tplset_name, created) VALUES (5, 'plain', '$now')";
  
  $poll_db = new polldb_sql();
  $poll_db->connect();
  
  for ($i=0;$i<sizeof($tbl_data);$i++) {
    $poll_db->query($tbl_data[$i]);
  }
  print_header(2,"step_5");
  install_process("Creating tables...succeed. Insert values...",300,"step_5");
  break;

case "step_5":
  print_header(-1,"stp");
  create_account();
  break;
  
case "step_6":
  if (empty($username) || empty($password)) {
    print_header(-1,"stp");
    install_process("step_5","Please enter a username and password...",300,"step_6");
    break;
  }
  if (get_magic_quotes_gpc()) {
    $password = stripslashes($password);    
  }
  $password = md5($password);
  $timestamp = time();
  srand((double)microtime()*1000000);
  $session = md5 (uniqid (rand()));
  
  $poll_db = new polldb_sql();
  $poll_db->connect();
  
  $sql_query = "INSERT INTO $POLLTBL[poll_user] (user_id, username, userpass, session, last_visit) VALUES ( '1', '$username', '$password', '$session','$timestamp')";
  
  $poll_db->query($sql_query);
  print_header(-1,"stp");
  inst_complete();
  break;

case "cancel":
  abort("Installation aborted!");
  break;

default:
  print_header(-1,"stp");
  if (empty($POLLDB["dbName"]) || empty($POLLDB["host"])) {
    poll_error_msg("Please be sure you have setup your mySQL settings in the configuration file <u>config.inc.php</u> before running Advanced Poll Setup.");
  } elseif (empty($POLLTBL["poll_index"]) || empty($POLLTBL["poll_data"]) || empty($POLLTBL["poll_config"]) || empty($POLLTBL["poll_ip"]) || empty($POLLTBL["poll_log"]) || empty($POLLTBL["poll_user"]) || empty($POLLTBL["poll_comment"])) {
    poll_error_msg("Some tables are not defined. Please check the configuration file.");
  } else {  
    welcome();
  }
  break;
}

?>