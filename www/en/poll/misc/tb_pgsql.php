<?php

$tbl[] = "CREATE SEQUENCE poll_comment_seq start 2 increment 1 maxvalue 2147483647 minvalue 1  cache 1" ;
$tbl[] = "CREATE SEQUENCE poll_index_seq start 4 increment 1 maxvalue 2147483647 minvalue 1  cache 1" ;
$tbl[] = "CREATE SEQUENCE poll_templateset_seq start 5 increment 1 maxvalue 2147483647 minvalue 1  cache 1" ;
$tbl[] = "CREATE SEQUENCE poll_data_seq start 21 increment 1 maxvalue 2147483647 minvalue 1  cache 1" ;
$tbl[] = "CREATE SEQUENCE poll_templates_seq start 29 increment 1 maxvalue 2147483647 minvalue 1  cache 1" ;


$tbl[] = "CREATE TABLE poll_config (
	config_id int2 NOT NULL,
	base_gif character varying(60) NOT NULL,
	lang character varying(20) NOT NULL,
	title character varying(60) NOT NULL,
	vote_button character varying(30) NOT NULL,
	result_text character varying(40) NOT NULL,
	total_text character varying(40) NOT NULL,
	voted character varying(40) NOT NULL,
	send_com character varying(40) NOT NULL,
	img_height int2 DEFAULT '0' NOT NULL,
	img_length int2 DEFAULT '0' NOT NULL,
	table_width character varying(6) NOT NULL,
	bgcolor_tab character varying(7) NOT NULL,
	bgcolor_fr character varying(7) NOT NULL,
	font_face character varying(70) NOT NULL,
	font_color character varying(7) NOT NULL,
	type character varying(10) DEFAULT '0' NOT NULL,
	check_ip int2 DEFAULT '0' NOT NULL,
	lock_timeout int4 DEFAULT '0' NOT NULL,
	time_offset character varying(5) DEFAULT '0' NOT NULL,
	entry_pp int2 DEFAULT '0' NOT NULL,
	poll_version character varying(5) DEFAULT '0' NOT NULL,
	base_url character varying(100) NOT NULL,
	result_order character varying(20) NOT NULL,
	def_options int2 DEFAULT '0' NOT NULL,
	polls_pp int2 DEFAULT '0' NOT NULL
)";

$tbl[] = "CREATE TABLE poll_index (
	poll_id int4 DEFAULT nextval('poll_index_seq'::text) NOT NULL,
	question character varying(100) NOT NULL,
	timestamp int4 DEFAULT '0' NOT NULL,
	status int2 DEFAULT '0' NOT NULL,
	logging int2 DEFAULT '0' NOT NULL,
	exp_time int4 DEFAULT '0' NOT NULL,
	expire int2 DEFAULT '0' NOT NULL,
	comments int2 DEFAULT '0' NOT NULL,
	PRIMARY KEY (poll_id)
)";

$tbl[] = "CREATE TABLE poll_templates (
	tpl_id int4 DEFAULT nextval('poll_templates_seq'::text) NOT NULL,
	tplset_id int4 NOT NULL,
	title character varying(100) NOT NULL,
	template text NOT NULL,
	PRIMARY KEY (tpl_id)
)";

$tbl[] = "CREATE TABLE poll_data (
	id int4 DEFAULT nextval('poll_data_seq'::text) NOT NULL,
	poll_id int4 NOT NULL,
	option_id int4 NOT NULL,
	option_text character varying(100) NOT NULL,
	color character varying(20) NOT NULL,
	votes int4 DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
)";

$tbl[] = "CREATE TABLE poll_ip (
	poll_id int4 DEFAULT '0' NOT NULL,
	ip_addr character varying(15) DEFAULT '' NOT NULL,
	timestamp int4 DEFAULT '0' NOT NULL
)";

$tbl[] = "CREATE TABLE poll_comment (
	com_id int4 DEFAULT nextval('poll_comment_seq'::text) NOT NULL,
	poll_id int4 NOT NULL,
	time int4 NOT NULL,
	host character varying(70) NOT NULL,
	browser character varying(70) NOT NULL,
	name character varying(60) NOT NULL,
	email character varying(70) NOT NULL,
	message text NOT NULL,
	PRIMARY KEY (com_id)
)";

$tbl[] = "CREATE TABLE poll_log (
	poll_id int4 DEFAULT '0' NOT NULL,
	option_id int4 DEFAULT '0' NOT NULL,
	timestamp int4 DEFAULT '0' NOT NULL,
	ip_addr character varying(15) NOT NULL,
	host character varying(70) NOT NULL,
	agent character varying(80) NOT NULL
)";

$tbl[] = "CREATE TABLE poll_user (
	user_id int2 NOT NULL,
	username character varying(30) NOT NULL,
	userpass character varying(32) NOT NULL,
	session character varying(32) NOT NULL,
	last_visit int4,
	PRIMARY KEY (user_id)
)";

$tbl[] = "CREATE TABLE poll_templateset (
	tplset_id int4 DEFAULT nextval('poll_templateset_seq'::text) NOT NULL,
	tplset_name character varying(50) NOT NULL,
	created character varying(20),
	PRIMARY KEY (tplset_id)
)";

$tbl[] = "CREATE  INDEX tplset_id_poll_templates_key on poll_templates using btree ( tplset_id int4_ops )";
$tbl[] = "CREATE  INDEX poll_id_poll_data_key on poll_data using btree ( poll_id int4_ops )";
$tbl[] = "CREATE  INDEX poll_id_poll_ip_key on poll_ip using btree ( poll_id int4_ops )";
$tbl[] = "CREATE UNIQUE INDEX config_id_poll_config_ukey on poll_config using btree ( config_id int2_ops )";
$tbl[] = "CREATE  INDEX option_id_poll_log_key on poll_log using btree ( option_id int4_ops )";
$tbl[] = "CREATE  INDEX poll_id_poll_log_key on poll_log using btree ( poll_id int4_ops )";

?>