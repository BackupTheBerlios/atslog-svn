<?php

$tbl[] = "CREATE TABLE $POLLTBL[poll_config] (
   config_id smallint(5) unsigned NOT NULL auto_increment,
   base_gif varchar(60) NOT NULL,
   lang varchar(20) NOT NULL,
   title varchar(60) NOT NULL,
   vote_button varchar(30) NOT NULL,
   result_text varchar(40) NOT NULL,
   total_text varchar(40) NOT NULL,
   voted varchar(40) NOT NULL,
   send_com varchar(40) NOT NULL,
   img_height int(5) DEFAULT '0' NOT NULL,
   img_length int(5) DEFAULT '0' NOT NULL,
   table_width varchar(6) NOT NULL,
   bgcolor_tab varchar(7) NOT NULL,
   bgcolor_fr varchar(7) NOT NULL,
   font_face varchar(70) NOT NULL,
   font_color varchar(7) NOT NULL,
   type varchar(10) DEFAULT '0' NOT NULL,
   check_ip smallint(2) DEFAULT '0' NOT NULL,
   lock_timeout int(9) DEFAULT '0' NOT NULL,
   time_offset varchar(5) DEFAULT '0' NOT NULL,
   entry_pp int(4) unsigned DEFAULT '0' NOT NULL,
   poll_version varchar(5) NOT NULL default '0',
   base_url varchar(100) NOT NULL default '',
   result_order varchar(20) NOT NULL default '',
   def_options smallint(3) unsigned NOT NULL default '0',
   polls_pp int(5) unsigned NOT NULL default '0',
   PRIMARY KEY (config_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_data] (
   id int(11) NOT NULL auto_increment,
   poll_id int(11) DEFAULT '0' NOT NULL,
   option_id int(11) DEFAULT '0' NOT NULL,
   option_text varchar(100) NOT NULL,
   color varchar(20) NOT NULL,
   votes int(14) DEFAULT '0' NOT NULL,
   PRIMARY KEY (poll_id, option_id),
   KEY id (id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_index] (
   poll_id int(11) unsigned NOT NULL auto_increment,
   question varchar(100) NOT NULL,
   timestamp int(11) DEFAULT '0' NOT NULL,
   status smallint(2) DEFAULT '0' NOT NULL,
   logging smallint(2) DEFAULT '0' NOT NULL,
   exp_time int(11) DEFAULT '0' NOT NULL,
   expire smallint(2) DEFAULT '0' NOT NULL,
   comments smallint(2) DEFAULT '0' NOT NULL,
   PRIMARY KEY (poll_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_ip] (
   ip_id int(11) NOT NULL auto_increment,
   poll_id int(11) DEFAULT '0' NOT NULL,
   ip_addr varchar(15) NOT NULL,
   timestamp int(11) DEFAULT '0' NOT NULL,
   PRIMARY KEY (ip_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_log] (
   log_id int(11) unsigned NOT NULL auto_increment,
   poll_id int(11) DEFAULT '0' NOT NULL,
   option_id int(11) DEFAULT '0' NOT NULL,
   timestamp int(11) DEFAULT '0' NOT NULL,
   ip_addr varchar(15) NOT NULL,
   host varchar(70) NOT NULL,
   agent varchar(80) DEFAULT '0' NOT NULL,
   PRIMARY KEY (log_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_user] (
   user_id smallint(5) NOT NULL auto_increment,
   username varchar(30) NOT NULL,
   userpass varchar(32) NOT NULL,
   session varchar(32) NOT NULL,
   last_visit int(11) NOT NULL,
   PRIMARY KEY (user_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_comment] (
   com_id int(9) NOT NULL auto_increment,
   poll_id int(9) DEFAULT '0' NOT NULL,
   time int(11) DEFAULT '0' NOT NULL,
   host varchar(70) NOT NULL,
   browser varchar(70) NOT NULL,
   name varchar(60) NOT NULL,
   email varchar(70) NOT NULL,
   message text NOT NULL,
   PRIMARY KEY (com_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_tpl] (
  tpl_id int(10) unsigned NOT NULL auto_increment,
  tplset_id int(10) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  template mediumtext NOT NULL,
  PRIMARY KEY  (tpl_id)
)";

$tbl[] = "CREATE TABLE $POLLTBL[poll_tplset] (
  tplset_id int(10) unsigned NOT NULL auto_increment,
  tplset_name varchar(50) NOT NULL default '',
  created datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (tplset_id)
)";

?>