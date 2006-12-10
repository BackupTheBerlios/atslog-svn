-- MySQL dump 8.21
--
-- Host: localhost    Database: poll
---------------------------------------------------------
-- Server version	3.23.49

--
-- Table structure for table 'poll_comment'
--

CREATE TABLE poll_comment (
  com_id int(9) NOT NULL auto_increment,
  poll_id int(9) NOT NULL default '0',
  time int(11) NOT NULL default '0',
  host varchar(70) NOT NULL default '',
  browser varchar(70) NOT NULL default '',
  name varchar(60) NOT NULL default '',
  email varchar(70) NOT NULL default '',
  message text NOT NULL,
  PRIMARY KEY  (com_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_comment'
--


INSERT INTO poll_comment VALUES (1,1,1019467656,'localhost','Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0)','nobody','nobody@server.com','This is the first comment!');

--
-- Table structure for table 'poll_config'
--

CREATE TABLE poll_config (
  config_id smallint(5) unsigned NOT NULL auto_increment,
  base_gif varchar(60) NOT NULL default '',
  lang varchar(20) NOT NULL default '',
  title varchar(60) NOT NULL default '',
  vote_button varchar(30) NOT NULL default '',
  result_text varchar(40) NOT NULL default '',
  total_text varchar(40) NOT NULL default '',
  voted varchar(40) NOT NULL default '',
  send_com varchar(40) NOT NULL default '',
  img_height int(5) NOT NULL default '0',
  img_length int(5) NOT NULL default '0',
  table_width varchar(6) NOT NULL default '',
  bgcolor_tab varchar(7) NOT NULL default '',
  bgcolor_fr varchar(7) NOT NULL default '',
  font_face varchar(70) NOT NULL default '',
  font_color varchar(7) NOT NULL default '',
  type varchar(10) NOT NULL default '0',
  check_ip smallint(2) NOT NULL default '0',
  lock_timeout int(9) NOT NULL default '0',
  time_offset varchar(5) NOT NULL default '0',
  entry_pp int(4) unsigned NOT NULL default '0',
  poll_version varchar(5) NOT NULL default '0',
  base_url varchar(100) NOT NULL default '',
  result_order varchar(20) NOT NULL default '',
  def_options smallint(3) unsigned NOT NULL default '0',
  polls_pp int(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (config_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_config'
--


INSERT INTO poll_config VALUES (1,'/test/poll/db/image','english.php','Advanced Poll','Vote','View results','Total votes','You have already voted!','Send comment',10,42,'170','#FFFFFF','#666699','Verdana, Arial, Helvetica, sans-serif','#000000','percent',0,2,'0',5,'2.02','/test/poll/db','desc',10,12);

--
-- Table structure for table 'poll_data'
--

CREATE TABLE poll_data (
  id int(11) NOT NULL auto_increment,
  poll_id int(11) NOT NULL default '0',
  option_id int(11) NOT NULL default '0',
  option_text varchar(100) NOT NULL default '',
  color varchar(20) NOT NULL default '',
  votes int(14) NOT NULL default '0',
  PRIMARY KEY  (poll_id,option_id),
  KEY id (id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_data'
--


INSERT INTO poll_data VALUES (1,1,1,'Linux','blue',49);
INSERT INTO poll_data VALUES (2,1,2,'Solaris','yellow',12);
INSERT INTO poll_data VALUES (3,1,3,'FreeBSD','green',29);
INSERT INTO poll_data VALUES (4,1,4,'WindowsNT','brown',17);
INSERT INTO poll_data VALUES (5,1,5,'Unix','grey',10);
INSERT INTO poll_data VALUES (6,1,6,'BSD','red',15);
INSERT INTO poll_data VALUES (7,1,7,'other','purple',9);
INSERT INTO poll_data VALUES (8,2,5,'Sybase','orange',2);
INSERT INTO poll_data VALUES (9,2,4,'MS SQL','green',9);
INSERT INTO poll_data VALUES (10,2,3,'Oracle','blue',17);
INSERT INTO poll_data VALUES (11,2,2,'PostgreSQL','gold',6);
INSERT INTO poll_data VALUES (12,2,1,'MySQL','pink',23);
INSERT INTO poll_data VALUES (13,2,6,'other','brown',3);
INSERT INTO poll_data VALUES (14,2,7,'DB/2','grey',4);
INSERT INTO poll_data VALUES (15,3,1,'PHP','red',65);
INSERT INTO poll_data VALUES (16,3,2,'Perl','orange',34);
INSERT INTO poll_data VALUES (17,3,3,'ASP','green',17);
INSERT INTO poll_data VALUES (18,3,4,'JSP','purple',20);
INSERT INTO poll_data VALUES (19,3,5,'Python','gold',7);
INSERT INTO poll_data VALUES (20,3,6,'other','aqua',16);

--
-- Table structure for table 'poll_index'
--

CREATE TABLE poll_index (
  poll_id int(11) unsigned NOT NULL auto_increment,
  question varchar(100) NOT NULL default '',
  timestamp int(11) NOT NULL default '0',
  status smallint(2) NOT NULL default '0',
  logging smallint(2) NOT NULL default '0',
  exp_time int(11) NOT NULL default '0',
  expire smallint(2) NOT NULL default '0',
  comments smallint(2) NOT NULL default '0',
  PRIMARY KEY  (poll_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_index'
--


INSERT INTO poll_index VALUES (1,'Which OS is your Website running on?',1019467656,1,1,1020331656,1,1);
INSERT INTO poll_index VALUES (2,'Which database engine do you prefer?',1019467706,1,0,1020331656,0,1);
INSERT INTO poll_index VALUES (3,'What is your favourite scripting language?',1019467706,1,0,1020331656,0,1);

--
-- Table structure for table 'poll_ip'
--

CREATE TABLE poll_ip (
  ip_id int(11) NOT NULL auto_increment,
  poll_id int(11) NOT NULL default '0',
  ip_addr varchar(15) NOT NULL default '',
  timestamp int(11) NOT NULL default '0',
  PRIMARY KEY  (ip_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_ip'
--



--
-- Table structure for table 'poll_log'
--

CREATE TABLE poll_log (
  log_id int(11) unsigned NOT NULL auto_increment,
  poll_id int(11) NOT NULL default '0',
  option_id int(11) NOT NULL default '0',
  timestamp int(11) NOT NULL default '0',
  ip_addr varchar(15) NOT NULL default '',
  host varchar(70) NOT NULL default '',
  agent varchar(80) NOT NULL default '0',
  PRIMARY KEY  (log_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_log'
--



--
-- Table structure for table 'poll_templates'
--

CREATE TABLE poll_templates (
  tpl_id int(10) unsigned NOT NULL auto_increment,
  tplset_id int(10) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  template mediumtext NOT NULL,
  PRIMARY KEY  (tpl_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_templates'
--


INSERT INTO poll_templates VALUES (1,1,'display_head','<table width=\"$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"$pollvars[bgcolor_fr]\">\r\n  <tr align=\"center\">\r\n    <td>\r\n      <style type=\"text/css\">\r\n <!--\r\n  .input { font-family: $pollvars[font_face]; font-size: 8pt}\r\n -->\r\n</style>\r\n      <font face=\"$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>$pollvars[title]</b></font></td>\r\n  </tr>\r\n  <tr align=\"center\"> \r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"$pollvars[bgcolor_tab]\">\r\n        <tr> \r\n          <td height=\"40\" valign=\"middle\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><b>$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"right\" valign=\"top\"> \r\n          <td>\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n                <tr valign=\"top\" align=\"center\"> \r\n                  <td> \r\n                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n');
INSERT INTO poll_templates VALUES (2,1,'display_loop',' <tr> \r\n   <td width=\"15%\"><input type=\"radio\" name=\"option_id\" value=\"$data[option_id]\"></td>\r\n   <td width=\"85%\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"$pollvars[font_color]\">$data[option_text]</font></td>\r\n </tr>\r\n');
INSERT INTO poll_templates VALUES (3,1,'display_foot','                    </table>\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"$poll_id\">\r\n                    <input type=\"submit\" value=\"$pollvars[vote_button]\" class=\"input\">\r\n                    <br>\r\n                    <br>\r\n                    <font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><a href=\"$this->form_forward?action=results&amp;poll_ident=$poll_id\">$pollvars[result_text]</a></font>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n            <font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (4,1,'result_head','<table width=\"$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"$pollvars[bgcolor_fr]\">\r\n<tr align=\"center\">\r\n<td>\r\n<style type=\"text/css\">\r\n<!--\r\n .input { font-family: $pollvars[font_face]; font-size: 8pt}\r\n .links { font-family: $pollvars[font_face]; font-size: 7.5pt; color: $pollvars[font_color]}\r\n-->\r\n</style>\r\n<font face=\"$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>$pollvars[title]</b></font></td>\r\n</tr>\r\n<tr align=\"center\">\r\n <td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"$pollvars[bgcolor_tab]\">\r\n <tr valign=\"middle\">\r\n   <td height=\"40\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><b>$question</b></font></td>\r\n </tr>\r\n <tr align=\"right\" valign=\"bottom\">\r\n   <td>\r\n     <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" align=\"center\">\r\n       <tr valign=\"top\">\r\n        <td>\r\n         <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n');
INSERT INTO poll_templates VALUES (5,1,'result_loop','<tr>\r\n    <td height=\"22\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$option_text</font></td>\r\n    <td nowrap height=\"22\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><img src=\"$pollvars[base_gif]/$poll_color.gif\" width=\"$img_width\" height=\"$pollvars[img_height]\"> $vote_val</font></td>\r\n</tr>\r\n');
INSERT INTO poll_templates VALUES (6,1,'result_foot','       </table>\r\n       <font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><br>\r\n       $pollvars[total_text]: <font color=\"#CC0000\">$total_votes</font><br>\r\n       $VOTE<br><br><div align=\"center\">\r\n       $COMMENT&nbsp;</div></font>\r\n       </td></tr>\r\n      <tr><td height=\"2\">&nbsp;</td></tr>\r\n     </table>\r\n    <font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font></td>\r\n   </tr>\r\n </table>\r\n</td>\r\n</tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (7,1,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Send Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#FFFFFF\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (8,2,'display_head','<table width=\"$pollvars[table_width]\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" bgcolor=\"#F3F3F3\">\r\n  <tr valign=\"top\"> \r\n    <td valign=\"top\" align=\"right\">\r\n      <form method=\"post\" action=\"$this->form_forward\">\r\n        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\r\n          <tr bgcolor=\"$pollvars[bgcolor_fr]\"> \r\n            <td colspan=\"2\" height=\"30\"><font face=\"$pollvars[font_face]\" color=\"#FFFFFF\" size=\"1\"><b>\r\n              $question</b></font></td>\r\n          </tr>\r\n');
INSERT INTO poll_templates VALUES (9,2,'display_loop','<tr> \r\n    <td width=\"14%\"><input type=\"radio\" name=\"option_id\" value=\"$data[option_id]\"></td>\r\n    <td width=\"86%\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"$pollvars[font_color]\">$data[option_text]</font></td>\r\n</tr>\r\n');
INSERT INTO poll_templates VALUES (10,2,'display_foot','          <tr align=\"center\"> \r\n            <td colspan=\"2\"> \r\n              <input type=\"image\" border=\"0\" src=\"$pollvars[base_gif]/vote.gif\" width=\"110\" height=\"48\">\r\n              <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n              <input type=\"hidden\" name=\"poll_ident\" value=\"$poll_id\">\r\n              <br>\r\n              <font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><a href=\"$this->form_forward?action=results&amp;poll_ident=$poll_id\">$pollvars[result_text]</a>\r\n            </td>\r\n          </tr>\r\n        </table>\r\n      </form>\r\n      <font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font>\r\n     </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (11,2,'result_head','<table width=\"170\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F3F3F3\">\r\n  <tr> \r\n    <td colspan=\"2\" height=\"25\" bgcolor=\"$pollvars[bgcolor_fr]\"><font face=\"$pollvars[font_face]\" color=\"#FFFFFF\" size=\"1\"><b>$question</b></font></td>\r\n  </tr>\r\n\r\n');
INSERT INTO poll_templates VALUES (12,2,'result_loop','  <tr> \r\n    <td colspan=\"2\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$option_text</font></td>\r\n  </tr>\r\n  <tr> \r\n    <td width=\"52%\"><img src=\"$pollvars[base_gif]/greenbar.gif\" width=\"$img_width\" height=\"7\"></td>\r\n    <td width=\"48%\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$vote_val</font></td>\r\n  </tr>\r\n');
INSERT INTO poll_templates VALUES (13,2,'result_foot','  <tr> \r\n    <td colspan=\"2\" height=\"40\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"> \r\n      $pollvars[total_text]: <font color=\"#CC0000\">$total_votes</font>\r\n      <br>$VOTE</font><br><div align=\"center\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$COMMENT</font></div>\r\n    </td>\r\n  </tr>\r\n  <tr align=\"right\" valign=\"bottom\" height=\"30\"> \r\n    <td colspan=\"2\"><font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font></td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (14,2,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Send Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#F3F3F3\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (15,3,'display_head','<table width=\"$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"$pollvars[bgcolor_fr]\">\r\n  <tr align=\"center\">\r\n    <td>\r\n      <style type=\"text/css\">\r\n <!--\r\n  .input { font-family: $pollvars[font_face]; font-size: 8pt}\r\n -->\r\n</style>\r\n      <font face=\"$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>$pollvars[title]</b></font></td>\r\n  </tr>\r\n  <tr align=\"center\"> \r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"$pollvars[bgcolor_tab]\">\r\n        <tr> \r\n          <td height=\"40\" valign=\"middle\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><b>$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"right\" valign=\"top\"> \r\n          <td>\r\n            <form method=\"post\" name=\"poll_$poll_id\" onsubmit=\"return poll_results_$poll_id(\'vote\',\'$pollvars[base_url]/popup.php\',\'Poll\',\'500\',\'350\',\'toolbar=no,scrollbars=yes\');\">\r\n              <script language=\"JavaScript\">\r\n<!--\r\nfunction poll_results_$poll_id(action,theURL,winName,winWidth,winHeight,features) {      \r\n    var w = (screen.width - winWidth)/2;\r\n    var h = (screen.height - winHeight)/2 - 20;\r\n    features = features+\',width=\'+winWidth+\',height=\'+winHeight+\',top=\'+h+\',left=\'+w;\r\n    var poll_ident = self.document.poll_$poll_id.poll_ident.value;\r\n    option_id = \'\';\r\n    for (i=0; i<self.document.poll_$poll_id.option_id.length; i++) {\r\n        if(self.document.poll_$poll_id.option_id[i].checked == true) {\r\n            option_id = self.document.poll_$poll_id.option_id[i].value;\r\n            break;\r\n        }\r\n    }\r\n    option_id = (option_id != \'\') ? \'&option_id=\'+option_id : \'\';\r\n    if (action==\'results\' || (option_id != \'\' && action==\'vote\')) {\r\n        theURL = theURL+\'?action=\'+action+\'&poll_ident=\'+poll_ident+option_id;\r\n        poll_popup = window.open(theURL,winName,features);\r\n        poll_popup.focus();\r\n    }\r\n    return false;\r\n}\r\n//-->\r\n        </script>\r\n              <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n                <tr valign=\"top\" align=\"center\"> \r\n                  <td> \r\n                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n');
INSERT INTO poll_templates VALUES (16,3,'display_loop',' <tr> \r\n   <td width=\"15%\"><input type=\"radio\" name=\"option_id\" value=\"$data[option_id]\"></td>\r\n   <td width=\"85%\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"$pollvars[font_color]\">$data[option_text]</font></td>\r\n </tr>\r\n');
INSERT INTO poll_templates VALUES (17,3,'display_foot','                    </table>\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"$poll_id\">\r\n                    <input type=\"submit\" value=\"$pollvars[vote_button]\" class=\"input\">\r\n                    <br>\r\n                    <br>\r\n                    <font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><a href=\"javascript:void(poll_results_$poll_id(\'results\',\'$pollvars[base_url]/popup.php\',\'Poll\',\'500\',\'350\',\'toolbar=no,scrollbars=yes\'))\">$pollvars[result_text]</a><br>\r\n                    </font></td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n            <font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (18,3,'result_head','<table width=\"450\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCCCCC\">\r\n  <tr>\r\n    <td align=\"center\"> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F6F6F6\">\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" height=\"40\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"#000000\"><b>$question</b></font></td>\r\n        </tr>\r\n');
INSERT INTO poll_templates VALUES (19,3,'result_loop','        <tr>\r\n          <td width=\"3%\">&nbsp;</td>\r\n          <td><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$option_text</font></td>\r\n          <td><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><img src=\"$pollvars[base_gif]/$poll_color.gif\" width=\"$img_width\" height=\"$pollvars[img_height]\"> $vote_percent % ($vote_count)</font></td>\r\n        </tr>\r\n');
INSERT INTO poll_templates VALUES (20,3,'result_foot','        <tr> \r\n          <td colspan=\"3\" valign=\"bottom\" align=\"center\"><b><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$pollvars[total_text]: \r\n            $total_votes</font></b></td>\r\n        </tr>\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" valign=\"top\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$VOTE \r\n            <br>\r\n            $COMMENT</font></td>\r\n        </tr>\r\n        <tr align=\"right\"> \r\n          <td colspan=\"3\"><font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font></td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (21,3,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Submit Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#FFFFFF\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (22,4,'display_head','<table width=\"$pollvars[table_width]\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"$pollvars[bgcolor_fr]\">\r\n  <tr align=\"center\">\r\n    <td>\r\n      <style type=\"text/css\">\r\n <!--\r\n  .input { font-family: $pollvars[font_face]; font-size: 8pt}\r\n -->\r\n</style>\r\n      <font face=\"$pollvars[font_face]\" size=\"-1\" color=\"#FFFFFF\"><b>$pollvars[title]</b></font></td>\r\n  </tr>\r\n  <tr align=\"center\"> \r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" bgcolor=\"$pollvars[bgcolor_tab]\">\r\n        <tr> \r\n          <td height=\"40\" valign=\"middle\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><b>$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"right\" valign=\"top\"> \r\n          <td>\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n               <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n                <tr valign=\"top\" align=\"center\"> \r\n                  <td> \r\n                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">\r\n');
INSERT INTO poll_templates VALUES (23,4,'display_loop','<tr> \r\n   <td width=\"15%\"><input type=\"radio\" name=\"option_id\" value=\"$data[option_id]\"></td>\r\n   <td width=\"85%\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"$pollvars[font_color]\">$data[option_text]</font></td>\r\n </tr>\r\n');
INSERT INTO poll_templates VALUES (24,4,'display_foot','                    </table>\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"$poll_id\">\r\n                    <input type=\"submit\" value=\"$pollvars[vote_button]\" class=\"input\">\r\n                    <br>\r\n                    <br>\r\n                    <font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><a href=\"$this->form_forward?action=results&amp;poll_ident=$poll_id\">$pollvars[result_text]</a><br>\r\n                    </font></td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n            <font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (25,4,'result_head','<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"360\">\r\n  <tr> \r\n    <td colspan=\"2\"><font face=\"$pollvars[font_face]\" size=\"2\">$question</font></td>\r\n  </tr>\r\n  <tr> \r\n    <td><img src=\"$pollvars[base_url]/png.php?poll_id=$poll_id\"></td>\r\n    <td> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" bgcolor=\"#000000\">\r\n        <tr> \r\n          <td> \r\n            <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#EBEBEB\">');
INSERT INTO poll_templates VALUES (26,4,'result_loop','            <tr> \r\n                <td width=\"12\"><font size=\"1\" face=\"$pollvars[font_face]\"><img src=\"$pollvars[base_gif]/$poll_color.gif\" width=\"8\" height=\"10\"></font></td>\r\n                <td><font size=\"1\" face=\"$pollvars[font_face]\">$option_text -\r\n                  $vote_val</font></td>\r\n              </tr>');
INSERT INTO poll_templates VALUES (27,4,'result_foot','              <tr> \r\n                <td>&nbsp;</td>\r\n                <td><font face=\"$pollvars[font_face]\" size=\"1\">$pollvars[total_text]: \r\n                 <font color=\"#990000\">$total_votes</font><br>\r\n                 $COMMENT&nbsp;</font></td>\r\n              </tr>\r\n            </table>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>');
INSERT INTO poll_templates VALUES (28,4,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Submit Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#FFFFFF\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (29,5,'display_head','<table width=\"450\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCCCCC\">\r\n  <tr>\r\n    <td align=\"center\">\r\n     <style type=\"text/css\">\r\n       <!--\r\n        .input { font-family: $pollvars[font_face]; font-size: 9pt}\r\n       -->\r\n      </style> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F6F6F6\">\r\n        <tr align=\"center\"> \r\n          <td colspan=\"2\" height=\"40\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><b>$question</b></font></td>\r\n        </tr>\r\n        <tr align=\"center\"> \r\n          <td colspan=\"2\"> \r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">');
INSERT INTO poll_templates VALUES (30,5,'display_loop',' <tr> \r\n   <td width=\"11%\" align=\"center\"><input type=\"radio\" name=\"option_id\" value=\"$data[option_id]\"></td>\r\n   <td width=\"89%\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"$pollvars[font_color]\">$data[option_text]</font></td>\r\n </tr>');
INSERT INTO poll_templates VALUES (31,5,'display_foot','                <tr align=\"center\" valign=\"bottom\"> \r\n                  <td colspan=\"2\"> \r\n                    <input type=\"submit\" value=\"$pollvars[vote_button]\" class=\"input\" name=\"submit\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"vote\">\r\n                    <input type=\"hidden\" name=\"poll_ident\" value=\"$poll_id\">\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"bottom\"> \r\n                  <td colspan=\"2\" height=\"30\" align=\"center\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">[<a href=\"$this->form_forward?action=results&amp;poll_ident=$poll_id\">$pollvars[result_text]</a>]</font></td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (32,5,'result_head','<table width=\"450\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCCCCC\">\r\n  <tr>\r\n    <td align=\"center\"> \r\n      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F6F6F6\">\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" height=\"40\"><font face=\"$pollvars[font_face]\" size=\"1\" color=\"#000000\"><b>$question</b></font></td>\r\n        </tr>\r\n');
INSERT INTO poll_templates VALUES (33,5,'result_loop','        <tr>\r\n          <td width=\"3%\">&nbsp;</td>\r\n          <td><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$option_text</font></td>\r\n          <td><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\"><img src=\"$pollvars[base_gif]/$poll_color.gif\" width=\"$img_width\" height=\"$pollvars[img_height]\"> $vote_percent % ($vote_count)</font></td>\r\n        </tr>\r\n');
INSERT INTO poll_templates VALUES (34,5,'result_foot','        <tr> \r\n          <td colspan=\"3\" valign=\"bottom\" align=\"center\"><b><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$pollvars[total_text]: \r\n            $total_votes</font></b></td>\r\n        </tr>\r\n        <tr align=\"center\"> \r\n          <td colspan=\"3\" valign=\"top\"><font face=\"$pollvars[font_face]\" color=\"$pollvars[font_color]\" size=\"1\">$VOTE \r\n            <br>\r\n            $COMMENT</font></td>\r\n        </tr>\r\n        <tr align=\"right\"> \r\n          <td colspan=\"3\"><font face=\"$pollvars[font_face]\" size=\"1\"><a href=\"http://www.proxy2.de\" target=\"_blank\" title=\"Advanced Poll\">Version $pollvars[poll_version]</a></font></td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (35,5,'comment','<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\" bgcolor=\"#666699\">\r\n  <tr align=\"center\">\r\n    <td>\r\n     <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 195px}\r\n       .input {  width: 195px}\r\n      -->\r\n    </style><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><b>Send Your Comment</b></font></td>\r\n  </tr>\r\n  <tr>\r\n    <td>\r\n      <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#F3F3F3\" width=\"200\">\r\n        <tr>\r\n          <td width=\"149\">\r\n            <form method=\"post\" action=\"$this->form_forward\">\r\n              <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"\" align=\"center\">\r\n                <tr>\r\n                  <td class=\"td1\" height=\"40\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$question</font></b></td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n                    <input type=\"text\" name=\"name\" maxlength=\"25\" class=\"input\" size=\"23\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">e-mail:</font><br>\r\n                    <input type=\"text\" name=\"email\" size=\"23\" maxlength=\"50\" class=\"input\">\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment(*):</font><br>\r\n                    <font face=\"MS Sans Serif\" size=\"1\">\r\n                    <textarea name=\"message\" cols=\"19\" wrap=\"VIRTUAL\" rows=\"6\" class=\"textarea\"></textarea>\r\n                    </font>\r\n                  </td>\r\n                </tr>\r\n                <tr valign=\"top\">\r\n                  <td>\r\n                    <input type=\"submit\" value=\"Submit\" class=\"button\">\r\n                    <input type=\"reset\" value=\"Reset\" class=\"button\">\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\">\r\n                    <input type=\"hidden\" name=\"id\" value=\"$poll_id\">\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n            </form>\r\n          </td>\r\n        </tr>\r\n      </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (36,0,'poll_comment','<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\" width=\"450\">\r\n  <tr bgcolor=\"#E4E4E4\"> \r\n    <td bgcolor=\"#F2F2F2\"><b><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$data[name]</font></b> \r\n      <i><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$data[email]</font></i> \r\n      - <font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$data[time]</font><br>\r\n      <font size=\"1\" face=\"Arial, Helvetica, sans-serif\">$data[host] - $data[browser]</font> \r\n    </td>\r\n  </tr>\r\n  <tr>\r\n    <td><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$data[message]</font> \r\n    </td>\r\n  </tr>\r\n  <tr> \r\n    <td height=\"15\">&nbsp;</td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (37,0,'poll_list','<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"450\">\r\n  <tr> \r\n    <td width=\"80\" valign=\"top\"> &#0149; <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><i>$data[timestamp]</i></font></td>\r\n    <td width=\"354\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"$PHP_SELF?poll_id=$data[poll_id]\">$data[question]</a></font></td>\r\n  </tr>\r\n</table>\r\n');
INSERT INTO poll_templates VALUES (38,0,'poll_form','<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n  <tr> \r\n    <td> \r\n      <style type=\"text/css\">\r\n      <!--\r\n       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}\r\n       .poll_textarea {  font-family: \"MS Sans Serif\"; font-size: 9pt; width: 300px}\r\n       .poll_input {  width: 300px}\r\n      -->\r\n    </style>\r\n      <form method=\"post\" action=\"$this->form_forward\">\r\n        <table border=\"0\" cellspacing=\"0\" cellpadding=\"4\">\r\n          <tr>\r\n            <td class=\"td1\"><font color=\"#CC0000\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\"><b>$msg</b></font></td>\r\n          </tr>\r\n          <tr> \r\n            <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Name:</font><br>\r\n              <input type=\"text\" name=\"name\" value=\"$comment[name]\" maxlength=\"30\" class=\"poll_input\" size=\"25\">\r\n            </td>\r\n          </tr>\r\n          <tr> \r\n            <td class=\"td1\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">E-mail:</font><br>\r\n              <input type=\"text\" name=\"email\" value=\"$comment[email]\" size=\"25\" maxlength=\"50\" class=\"poll_input\">\r\n            </td>\r\n          </tr>\r\n          <tr> \r\n            <td class=\"td1\"> <font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Comment:</font><br>\r\n              <font face=\"MS Sans Serif\" size=\"1\"> \r\n              <textarea name=\"message\" cols=\"25\" wrap=\"VIRTUAL\" rows=\"8\" class=\"poll_textarea\">$comment[message]</textarea>\r\n              </font> </td>\r\n          </tr>\r\n          <tr valign=\"top\"> \r\n            <td> \r\n              <input type=\"submit\" value=\"Submit\" class=\"button\" name=\"submit\">\r\n              <input type=\"reset\" value=\"Reset\" class=\"button\" name=\"reset\">\r\n              <input type=\"hidden\" name=\"action\" value=\"add\">\r\n              <input type=\"hidden\" name=\"pcomment\" value=\"$poll_id\">\r\n            </td>\r\n          </tr>\r\n        </table>\r\n      </form>\r\n    </td>\r\n  </tr>\r\n</table>\r\n');

--
-- Table structure for table 'poll_templateset'
--

CREATE TABLE poll_templateset (
  tplset_id int(10) unsigned NOT NULL auto_increment,
  tplset_name varchar(50) NOT NULL default '',
  created datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (tplset_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_templateset'
--


INSERT INTO poll_templateset VALUES (1,'default','2002-04-22 11:27:36');
INSERT INTO poll_templateset VALUES (2,'simple','2002-04-22 11:27:36');
INSERT INTO poll_templateset VALUES (3,'popup','2002-04-22 11:27:36');
INSERT INTO poll_templateset VALUES (4,'graphic','2002-04-22 11:27:36');
INSERT INTO poll_templateset VALUES (5,'plain','2002-04-22 11:27:36');

--
-- Table structure for table 'poll_user'
--

CREATE TABLE poll_user (
  user_id smallint(5) NOT NULL auto_increment,
  username varchar(30) NOT NULL default '',
  userpass varchar(32) NOT NULL default '',
  session varchar(32) NOT NULL default '',
  last_visit int(11) NOT NULL default '0',
  PRIMARY KEY  (user_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'poll_user'
--


INSERT INTO poll_user VALUES (1,'admin','b0f6dfb42fa80caee6825bfecd30f094','df91295f21c367379bc342ecc93c40ee',1019479374);

