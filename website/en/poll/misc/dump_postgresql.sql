\connect - postgres
CREATE SEQUENCE "poll_comment_seq" start 2 increment 1 maxvalue 2147483647 minvalue 1  cache 1 ;
CREATE SEQUENCE "poll_index_seq" start 4 increment 1 maxvalue 2147483647 minvalue 1  cache 1 ;
CREATE SEQUENCE "poll_templateset_seq" start 5 increment 1 maxvalue 2147483647 minvalue 1  cache 1 ;
CREATE SEQUENCE "poll_data_seq" start 21 increment 1 maxvalue 2147483647 minvalue 1  cache 1 ;
CREATE SEQUENCE "poll_templates_seq" start 29 increment 1 maxvalue 2147483647 minvalue 1  cache 1 ;
CREATE TABLE "poll_config" (
	"config_id" int2 NOT NULL,
	"base_gif" character varying(60) NOT NULL,
	"lang" character varying(20) NOT NULL,
	"title" character varying(60) NOT NULL,
	"vote_button" character varying(30) NOT NULL,
	"result_text" character varying(40) NOT NULL,
	"total_text" character varying(40) NOT NULL,
	"voted" character varying(40) NOT NULL,
	"send_com" character varying(40) NOT NULL,
	"img_height" int2 DEFAULT '0' NOT NULL,
	"img_length" int2 DEFAULT '0' NOT NULL,
	"table_width" character varying(6) NOT NULL,
	"bgcolor_tab" character varying(7) NOT NULL,
	"bgcolor_fr" character varying(7) NOT NULL,
	"font_face" character varying(70) NOT NULL,
	"font_color" character varying(7) NOT NULL,
	"type" character varying(10) DEFAULT '0' NOT NULL,
	"check_ip" int2 DEFAULT '0' NOT NULL,
	"lock_timeout" int4 DEFAULT '0' NOT NULL,
	"time_offset" character varying(5) DEFAULT '0' NOT NULL,
	"entry_pp" int2 DEFAULT '0' NOT NULL,
	"poll_version" character varying(5) DEFAULT '0' NOT NULL,
	"base_url" character varying(100) NOT NULL,
	"result_order" character varying(20) NOT NULL,
	"def_options" int2 DEFAULT '0' NOT NULL,
	"polls_pp" int2 DEFAULT '0' NOT NULL
);
CREATE TABLE "poll_index" (
	"poll_id" int4 DEFAULT nextval('poll_index_seq'::text) NOT NULL,
	"question" character varying(100) NOT NULL,
	"timestamp" int4 DEFAULT '0' NOT NULL,
	"status" int2 DEFAULT '0' NOT NULL,
	"logging" int2 DEFAULT '0' NOT NULL,
	"exp_time" int4 DEFAULT '0' NOT NULL,
	"expire" int2 DEFAULT '0' NOT NULL,
	"comments" int2 DEFAULT '0' NOT NULL,
	PRIMARY KEY ("poll_id")
);
CREATE TABLE "poll_templates" (
	"tpl_id" int4 DEFAULT nextval('poll_templates_seq'::text) NOT NULL,
	"tplset_id" int4 NOT NULL,
	"title" character varying(100) NOT NULL,
	"template" text NOT NULL,
	PRIMARY KEY ("tpl_id")
);
CREATE TABLE "poll_data" (
	"id" int4 DEFAULT nextval('poll_data_seq'::text) NOT NULL,
	"poll_id" int4 NOT NULL,
	"option_id" int4 NOT NULL,
	"option_text" character varying(100) NOT NULL,
	"color" character varying(20) NOT NULL,
	"votes" int4 DEFAULT '0' NOT NULL,
	PRIMARY KEY ("id")
);
CREATE TABLE "poll_ip" (
	"poll_id" int4 DEFAULT '0' NOT NULL,
	"ip_addr" character varying(15) DEFAULT '' NOT NULL,
	"timestamp" int4 DEFAULT '0' NOT NULL
);
CREATE TABLE "poll_comment" (
	"com_id" int4 DEFAULT nextval('poll_comment_seq'::text) NOT NULL,
	"poll_id" int4 NOT NULL,
	"time" int4 NOT NULL,
	"host" character varying(70) NOT NULL,
	"browser" character varying(70) NOT NULL,
	"name" character varying(60) NOT NULL,
	"email" character varying(70) NOT NULL,
	"message" text NOT NULL,
	PRIMARY KEY ("com_id")
);
CREATE TABLE "poll_log" (
	"poll_id" int4 DEFAULT '0' NOT NULL,
	"option_id" int4 DEFAULT '0' NOT NULL,
	"timestamp" int4 DEFAULT '0' NOT NULL,
	"ip_addr" character varying(15) NOT NULL,
	"host" character varying(70) NOT NULL,
	"agent" character varying(80) NOT NULL
);
CREATE TABLE "poll_user" (
	"user_id" int2 NOT NULL,
	"username" character varying(30) NOT NULL,
	"userpass" character varying(32) NOT NULL,
	"session" character varying(32) NOT NULL,
	"last_visit" int4,
	PRIMARY KEY ("user_id")
);
CREATE TABLE "poll_templateset" (
	"tplset_id" int4 DEFAULT nextval('poll_templateset_seq'::text) NOT NULL,
	"tplset_name" character varying(50) NOT NULL,
	"created" character varying(20),
	PRIMARY KEY ("tplset_id")
);
COPY "poll_config" FROM stdin;
1	/pollphp/db/image	english.php	Advanced Poll	Vote	View results	Total votes	You have already voted!	Send comment	10	42	170	#FFFFFF	#666699	Verdana, Arial, Helvetica, sans-serif	#000000	percent	0	2	0	5	2.0	/pollphp/db	desc	10	12
\.
COPY "poll_index" FROM stdin;
1	Which OS is your Website running on?	1018815036	1	1	1019679036	1	1
2	Which database engine do you prefer?	1018815086	1	0	1019679036	0	1
3	What is your favourite scripting language?	1018815086	1	0	1019679036	0	1
\.
COPY "poll_templates" FROM stdin;
1	1	display_head	<table width="$pollvars[table_width]" border="0" cellspacing="0" cellpadding="1" bgcolor="$pollvars[bgcolor_fr]">
\
  <tr align="center">
\
    <td><style type="text/css">
\
       <!--
\
        .input { font-family: $pollvars[font_face]; font-size: 8pt}
\
       -->
\
      </style><font face="$pollvars[font_face]" size="-1" color="#FFFFFF"><b>$pollvars[title]</b></font></td>
\
  </tr>
\
  <tr align="center">
\
    <td>
\
      <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="$pollvars[bgcolor_tab]">
\
        <tr>
\
          <td height="40" valign="middle"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><b>$question</b></font></td>
\
        </tr>
\
        <tr align="right" valign="top">
\
          <td>
\
           <form method="post" action="$this->form_forward">
\
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" height="190">
\
              <tr valign="top" align="center">
\
                <td>
\
                  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
\

2	1	display_loop	<tr>
\
    <td width="15%"><input type="radio" name="option_id" value="$data[option_id]"></td>
\
    <td width="85%"><font face="$pollvars[font_face]" size="1" color="$pollvars[font_color]">$data[option_text]</font></td>
\
</tr>
\

3	1	display_foot	     </table>
\
     <input type="hidden" name="action" value="vote">
\
     <input type="hidden" name="poll_ident" value="$poll_id">
\
     <input type="submit" value="$pollvars[vote_button]" class="input">
\
     <br><br>
\
     <font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><a href="$this->form_forward?action=results&amp;poll_ident=$poll_id">$pollvars[result_text]</a><br><br><br></td>
\
 </tr>
\
</table>
\
</form>
\
  <font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font></td>
\
</tr>
\
</table>
\
</td>
\
</tr>
\
</table>
\

4	1	result_head	<table width="$pollvars[table_width]" border="0" cellspacing="0" cellpadding="1" bgcolor="$pollvars[bgcolor_fr]">
\
<tr align="center">
\
<td>
\
<style type="text/css">
\
<!--
\
 .input { font-family: $pollvars[font_face]; font-size: 8pt}
\
 .links { font-family: $pollvars[font_face]; font-size: 7.5pt; color: $pollvars[font_color]}
\
-->
\
</style>
\
<font face="$pollvars[font_face]" size="-1" color="#FFFFFF"><b>$pollvars[title]</b></font></td>
\
</tr>
\
<tr align="center">
\
 <td><table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="$pollvars[bgcolor_tab]">
\
 <tr valign="middle">
\
   <td height="40"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><b>$question</b></font></td>
\
 </tr>
\
 <tr align="right" valign="bottom">
\
   <td>
\
     <table border="0" cellspacing="0" cellpadding="1" width="100%" align="center" height="210">
\
       <tr valign="top">
\
        <td>
\
         <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
\
         
5	1	result_loop	<tr>
\
    <td height="22"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$option_text</font></td>
\
    <td nowrap height="22"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><img src="$pollvars[base_gif]/$poll_color.gif" width="$img_width" height="$pollvars[img_height]"> $vote_val</font></td>
\
</tr>
\

6	1	result_foot	       </table>
\
       <font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><br>
\
       $pollvars[total_text]: <font color="#CC0000">$total_votes</font><br>
\
       $VOTE<br><br><div align="center">
\
       $COMMENT&nbsp;</div></font>
\
       </td></tr>
\
      <tr><td height="32">&nbsp;</td></tr>
\
     </table>
\
    <font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font></td>
\
   </tr>
\
 </table>
\
</td>
\
</tr>
\
</table>
\

7	1	comment	<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#666699">
\
  <tr align="center">
\
    <td>
\
     <style type="text/css">
\
      <!--
\
       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
\
       .textarea {  font-family: "MS Sans Serif"; font-size: 9pt; width: 195px}
\
       .input {  width: 195px}
\
      -->
\
    </style><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Send Your Comment</b></font></td>
\
  </tr>
\
  <tr>
\
    <td>
\
      <table border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#FFFFFF" width="200">
\
        <tr>
\
          <td width="149">
\
            <form method="post" action="$this->form_forward">
\
              <table border="0" cellspacing="0" cellpadding="2" bgcolor="" align="center">
\
                <tr>
\
                  <td class="td1" height="40"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="1">$question</font></b></td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Name:</font><br>
\
                    <input type="text" name="name" maxlength="25" class="input" size="23">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">e-mail:</font><br>
\
                    <input type="text" name="email" size="23" maxlength="50" class="input">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Comment(*):</font><br>
\
                    <font face="MS Sans Serif" size="1">
\
                    <textarea name="message" cols="19" wrap="VIRTUAL" rows="6" class="textarea"></textarea>
\
                    </font>
\
                  </td>
\
                </tr>
\
                <tr valign="top">
\
                  <td>
\
                    <input type="submit" value="Submit" class="button">
\
                    <input type="reset" value="Reset" class="button">
\
                    <input type="hidden" name="action" value="add">
\
                    <input type="hidden" name="id" value="$poll_id">
\
                  </td>
\
                </tr>
\
              </table>
\
            </form>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

8	2	display_head	<table width="$pollvars[table_width]" cellspacing="0" cellpadding="0" border="0" bgcolor="#F3F3F3">
\
  <tr valign="top"> 
\
    <td valign="top" align="right">
\
      <form method="post" action="$this->form_forward">
\
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
\
          <tr bgcolor="$pollvars[bgcolor_fr]"> 
\
            <td colspan="2" height="30"><font face="$pollvars[font_face]" color="#FFFFFF" size="1"><b>
\
              $question</b></font></td>
\
          </tr>
\

9	2	display_loop	<tr> 
\
    <td width="14%"><input type="radio" name="option_id" value="$data[option_id]"></td>
\
    <td width="86%"><font face="$pollvars[font_face]" size="1" color="$pollvars[font_color]">$data[option_text]</font></td>
\
</tr>
\

10	2	display_foot	          <tr align="center"> 
\
            <td colspan="2"> 
\
              <input type="image" border="0" src="$pollvars[base_gif]/vote.gif" width="110" height="48">
\
              <input type="hidden" name="action" value="vote">
\
              <input type="hidden" name="poll_ident" value="$poll_id">
\
              <br>
\
              <font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><a href="$this->form_forward?action=results&amp;poll_ident=$poll_id">$pollvars[result_text]</a>
\
            </td>
\
          </tr>
\
        </table>
\
      </form>
\
      <font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font>
\
     </td>
\
  </tr>
\
</table>
\

11	2	result_head	<table width="170" border="0" cellspacing="0" cellpadding="3" bgcolor="#F3F3F3">
\
  <tr> 
\
    <td colspan="2" height="25" bgcolor="$pollvars[bgcolor_fr]"><font face="$pollvars[font_face]" color="#FFFFFF" size="1"><b>$question</b></font></td>
\
  </tr>
\
\

12	2	result_loop	  <tr> 
\
    <td colspan="2"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$option_text</font></td>
\
  </tr>
\
  <tr> 
\
    <td width="52%"><img src="$pollvars[base_gif]/greenbar.gif" width="$img_width" height="7"></td>
\
    <td width="48%"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$vote_val</font></td>
\
  </tr>
\

13	2	result_foot	  <tr> 
\
    <td colspan="2" height="40"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"> 
\
      $pollvars[total_text]: <font color="#CC0000">$total_votes</font>
\
      <br>$VOTE</font><br><div align="center"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$COMMENT</font></div>
\
    </td>
\
  </tr>
\
  <tr align="right" valign="bottom" height="30"> 
\
    <td colspan="2"><font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font></td>
\
  </tr>
\
</table>
\

14	2	comment	<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#666699">
\
  <tr align="center">
\
    <td>
\
     <style type="text/css">
\
      <!--
\
       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
\
       .textarea {  font-family: "MS Sans Serif"; font-size: 9pt; width: 195px}
\
       .input {  width: 195px}
\
      -->
\
    </style><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Send Your Comment</b></font></td>
\
  </tr>
\
  <tr>
\
    <td>
\
      <table border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#F3F3F3" width="200">
\
        <tr>
\
          <td width="149">
\
            <form method="post" action="$this->form_forward">
\
              <table border="0" cellspacing="0" cellpadding="2" bgcolor="" align="center">
\
                <tr>
\
                  <td class="td1" height="40"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="1">$question</font></b></td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Name:</font><br>
\
                    <input type="text" name="name" maxlength="25" class="input" size="23">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">e-mail:</font><br>
\
                    <input type="text" name="email" size="23" maxlength="50" class="input">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Comment(*):</font><br>
\
                    <font face="MS Sans Serif" size="1">
\
                    <textarea name="message" cols="19" wrap="VIRTUAL" rows="6" class="textarea"></textarea>
\
                    </font>
\
                  </td>
\
                </tr>
\
                <tr valign="top">
\
                  <td>
\
                    <input type="submit" value="Submit" class="button">
\
                    <input type="reset" value="Reset" class="button">
\
                    <input type="hidden" name="action" value="add">
\
                    <input type="hidden" name="id" value="$poll_id">
\
                  </td>
\
                </tr>
\
              </table>
\
            </form>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

15	3	display_head	<table width="$pollvars[table_width]" border="0" cellspacing="0" cellpadding="1" bgcolor="$pollvars[bgcolor_fr]">
\
  <tr align="center">
\
    <td>
\
      <style type="text/css">
\
 <!--
\
  .input { font-family: $pollvars[font_face]; font-size: 8pt}
\
 -->
\
</style>
\
      <font face="$pollvars[font_face]" size="-1" color="#FFFFFF"><b>$pollvars[title]</b></font></td>
\
  </tr>
\
  <tr align="center"> 
\
    <td> 
\
      <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="$pollvars[bgcolor_tab]">
\
        <tr> 
\
          <td height="40" valign="middle"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><b>$question</b></font></td>
\
        </tr>
\
        <tr align="right" valign="top"> 
\
          <td>
\
            <form method="post" name="poll_$poll_id" onsubmit="return poll_results_$poll_id('vote','$pollvars[base_url]/popup.php','Poll','500','350','toolbar=no,scrollbars=yes');">
\
              <script language="JavaScript">
\
<!--
\
function poll_results_$poll_id(action,theURL,winName,winWidth,winHeight,features) {      
\
    var w = (screen.width - winWidth)/2;
\
    var h = (screen.height - winHeight)/2 - 20;
\
    features = features+',width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
\
    var poll_ident = self.document.poll_$poll_id.poll_ident.value;
\
    option_id = '';
\
    for (i=0; i<self.document.poll_$poll_id.option_id.length; i++) {
\
        if(self.document.poll_$poll_id.option_id[i].checked == true) {
\
            option_id = self.document.poll_$poll_id.option_id[i].value;
\
            break;
\
        }
\
    }
\
    option_id = (option_id != '') ? '&option_id='+option_id : '';
\
    if (action=='results' || (option_id != '' && action=='vote')) {
\
        theURL = theURL+'?action='+action+'&poll_ident='+poll_ident+option_id;
\
        poll_popup = window.open(theURL,winName,features);
\
        poll_popup.focus();
\
    }
\
    return false;
\
}
\
//-->
\
        </script>
\
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
\
                <tr valign="top" align="center"> 
\
                  <td> 
\
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
\

16	3	display_loop	 <tr> 
\
   <td width="15%"><input type="radio" name="option_id" value="$data[option_id]"></td>
\
   <td width="85%"><font face="$pollvars[font_face]" size="1" color="$pollvars[font_color]">$data[option_text]</font></td>
\
 </tr>
\

17	3	display_foot	                    </table>
\
                    <input type="hidden" name="action" value="vote">
\
                    <input type="hidden" name="poll_ident" value="$poll_id">
\
                    <input type="submit" value="$pollvars[vote_button]" class="input">
\
                    <br>
\
                    <br>
\
                    <font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><a href="javascript:void(poll_results_$poll_id('results','$pollvars[base_url]/popup.php','Poll','500','350','toolbar=no,scrollbars=yes'))">$pollvars[result_text]</a><br>
\
                    </font></td>
\
                </tr>
\
              </table>
\
            </form>
\
            <font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

18	3	result_head	  <table width="400" border="0" cellspacing="0" cellpadding="1" bgcolor="$pollvars[bgcolor_fr]">
\
    <tr align="center"> 
\
      <td> 
\
        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
\
          <tr valign="middle"> 
\
            <td height="25" align="center" bgcolor="$pollvars[bgcolor_fr]">
\
              <style type="text/css">
\
<!--
\
 .input { font-family: $pollvars[font_face]; font-size: 8pt}
\
 .links { font-family: $pollvars[font_face]; font-size: 7.5pt; color: #000000}
\
-->
\
</style>
\
\
            <font face="$pollvars[font_face]" color="#FFFFFF" size="1"><b>$question</b></font></td>
\
          </tr>
\
          <tr align="right" valign="bottom"> 
\
            <td> 
\
              <table border="0" cellspacing="0" cellpadding="1" width="100%" align="center">
\
                <tr valign="top"> 
\
                  <td> 
\
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
\

19	3	result_loop	<tr> 
\
   <td height="22" bgcolor="#EAEAEA"><font face="$pollvars[font_face]" color="#000000" size="1">$option_text</font></td>
\
   <td height="22" bgcolor="#D8D8EB"><font face="$pollvars[font_face]" color="#000000" size="1"><img src="$pollvars[base_gif]/$poll_color.gif" width="$img_width" height="$pollvars[img_height]"> $vote_val</font></td>
\
</tr>
\

20	3	result_foot	                  </table>
\
                  <font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><br>
\
                  $pollvars[total_text]: <font color="#CC0000">$total_votes</font><br>
\
                  $VOTE<br><div align="center">
\
                  $COMMENT&nbsp;</div></font></td>
\
              </tr>
\
              <tr> 
\
                <td>&nbsp;</td>
\
              </tr>
\
            </table>
\
            <font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

21	3	comment	<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#666699">
\
  <tr align="center">
\
    <td>
\
     <style type="text/css">
\
      <!--
\
       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
\
       .textarea {  font-family: "MS Sans Serif"; font-size: 9pt; width: 195px}
\
       .input {  width: 195px}
\
      -->
\
    </style><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Submit Your Comment</b></font></td>
\
  </tr>
\
  <tr>
\
    <td>
\
      <table border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#FFFFFF" width="200">
\
        <tr>
\
          <td width="149">
\
            <form method="post" action="$this->form_forward">
\
              <table border="0" cellspacing="0" cellpadding="2" bgcolor="" align="center">
\
                <tr>
\
                  <td class="td1" height="40"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="1">$question</font></b></td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Name:</font><br>
\
                    <input type="text" name="name" maxlength="25" class="input" size="23">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">e-mail:</font><br>
\
                    <input type="text" name="email" size="23" maxlength="50" class="input">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Comment(*):</font><br>
\
                    <font face="MS Sans Serif" size="1">
\
                    <textarea name="message" cols="19" wrap="VIRTUAL" rows="6" class="textarea"></textarea>
\
                    </font>
\
                  </td>
\
                </tr>
\
                <tr valign="top">
\
                  <td>
\
                    <input type="submit" value="Submit" class="button">
\
                    <input type="reset" value="Reset" class="button">
\
                    <input type="hidden" name="action" value="add">
\
                    <input type="hidden" name="id" value="$poll_id">
\
                  </td>
\
                </tr>
\
              </table>
\
            </form>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

22	4	display_head	<table width="$pollvars[table_width]" border="0" cellspacing="0" cellpadding="1" bgcolor="$pollvars[bgcolor_fr]">
\
  <tr align="center">
\
    <td>
\
      <style type="text/css">
\
 <!--
\
  .input { font-family: $pollvars[font_face]; font-size: 8pt}
\
 -->
\
</style>
\
      <font face="$pollvars[font_face]" size="-1" color="#FFFFFF"><b>$pollvars[title]</b></font></td>
\
  </tr>
\
  <tr align="center"> 
\
    <td> 
\
      <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="$pollvars[bgcolor_tab]">
\
        <tr> 
\
          <td height="40" valign="middle"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><b>$question</b></font></td>
\
        </tr>
\
        <tr align="right" valign="top"> 
\
          <td>
\
            <form method="post" action="$this->form_forward">
\
               <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
\
                <tr valign="top" align="center"> 
\
                  <td> 
\
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
\

23	4	display_loop	<tr> 
\
   <td width="15%"><input type="radio" name="option_id" value="$data[option_id]"></td>
\
   <td width="85%"><font face="$pollvars[font_face]" size="1" color="$pollvars[font_color]">$data[option_text]</font></td>
\
 </tr>
\

24	4	display_foot	                    </table>
\
                    <input type="hidden" name="action" value="vote">
\
                    <input type="hidden" name="poll_ident" value="$poll_id">
\
                    <input type="submit" value="$pollvars[vote_button]" class="input">
\
                    <br>
\
                    <br>
\
                    <font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><a href="$this->form_forward?action=results&amp;poll_ident=$poll_id">$pollvars[result_text]</a><br>
\
                    </font></td>
\
                </tr>
\
              </table>
\
            </form>
\
            <font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

25	4	result_head	<table border="0" cellspacing="0" cellpadding="2" width="360">
\
  <tr> 
\
    <td colspan="2"><font face="$pollvars[font_face]" size="2">$question</font></td>
\
  </tr>
\
  <tr> 
\
    <td><img src="$pollvars[base_url]/png.php?poll_id=$poll_id"></td>
\
    <td> 
\
      <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="#000000">
\
        <tr> 
\
          <td> 
\
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="#EBEBEB">
26	4	result_loop	            <tr> 
\
                <td width="12"><font size="1" face="$pollvars[font_face]"><img src="$pollvars[base_gif]/$poll_color.gif" width="8" height="10"></font></td>
\
                <td><font size="1" face="$pollvars[font_face]">$option_text -
\
                  $vote_val</font></td>
\
              </tr>
27	4	result_foot	              <tr> 
\
                <td>&nbsp;</td>
\
                <td><font face="$pollvars[font_face]" size="1">$pollvars[total_text]: 
\
                 <font color="#990000">$total_votes</font><br>
\
                 $COMMENT&nbsp;</font></td>
\
              </tr>
\
            </table>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
28	4	comment	<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#666699">
\
  <tr align="center">
\
    <td>
\
     <style type="text/css">
\
      <!--
\
       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
\
       .textarea {  font-family: "MS Sans Serif"; font-size: 9pt; width: 195px}
\
       .input {  width: 195px}
\
      -->
\
    </style><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Submit Your Comment</b></font></td>
\
  </tr>
\
  <tr>
\
    <td>
\
      <table border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#FFFFFF" width="200">
\
        <tr>
\
          <td width="149">
\
            <form method="post" action="$this->form_forward">
\
              <table border="0" cellspacing="0" cellpadding="2" bgcolor="" align="center">
\
                <tr>
\
                  <td class="td1" height="40"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="1">$question</font></b></td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Name:</font><br>
\
                    <input type="text" name="name" maxlength="25" class="input" size="23">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">e-mail:</font><br>
\
                    <input type="text" name="email" size="23" maxlength="50" class="input">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Comment(*):</font><br>
\
                    <font face="MS Sans Serif" size="1">
\
                    <textarea name="message" cols="19" wrap="VIRTUAL" rows="6" class="textarea"></textarea>
\
                    </font>
\
                  </td>
\
                </tr>
\
                <tr valign="top">
\
                  <td>
\
                    <input type="submit" value="Submit" class="button">
\
                    <input type="reset" value="Reset" class="button">
\
                    <input type="hidden" name="action" value="add">
\
                    <input type="hidden" name="id" value="$poll_id">
\
                  </td>
\
                </tr>
\
              </table>
\
            </form>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

29	5	display_head	<table width="450" border="0" cellspacing="0" cellpadding="2" bgcolor="#CCCCCC">
\
  <tr>
\
    <td align="center">
\
     <style type="text/css">
\
       <!--
\
        .input { font-family: $pollvars[font_face]; font-size: 9pt}
\
       -->
\
      </style> 
\
      <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="#F6F6F6">
\
        <tr align="center"> 
\
          <td colspan="2" height="40"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><b>$question</b></font></td>
\
        </tr>
\
        <tr align="center"> 
\
          <td colspan="2"> 
\
            <form method="post" action="$this->form_forward">
\
              <table width="100%" border="0" cellspacing="0" cellpadding="1">
30	5	display_loop	 <tr> 
\
   <td width="11%" align="center"><input type="radio" name="option_id" value="$data[option_id]"></td>
\
   <td width="89%"><font face="$pollvars[font_face]" size="1" color="$pollvars[font_color]">$data[option_text]</font></td>
\
 </tr>
31	5	display_foot	                <tr align="center" valign="bottom"> 
\
                  <td colspan="2"> 
\
                    <input type="submit" value="$pollvars[vote_button]" class="input" name="submit">
\
                    <input type="hidden" name="action" value="vote">
\
                    <input type="hidden" name="poll_ident" value="$poll_id">
\
                  </td>
\
                </tr>
\
                <tr valign="bottom"> 
\
                  <td colspan="2" height="30" align="center"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">[<a href="$this->form_forward?action=results&amp;poll_ident=$poll_id">$pollvars[result_text]</a>]</font></td>
\
                </tr>
\
              </table>
\
            </form>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

32	5	result_head	<table width="450" border="0" cellspacing="0" cellpadding="2" bgcolor="#CCCCCC">
\
  <tr>
\
    <td align="center"> 
\
      <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="#F6F6F6">
\
        <tr align="center"> 
\
          <td colspan="3" height="40"><font face="$pollvars[font_face]" size="1" color="#000000"><b>$question</b></font></td>
\
        </tr>
\

33	5	result_loop	        <tr>
\
          <td width="3%">&nbsp;</td>
\
          <td><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$option_text</font></td>
\
          <td><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1"><img src="$pollvars[base_gif]/$poll_color.gif" width="$img_width" height="$pollvars[img_height]"> $vote_percent % ($vote_count)</font></td>
\
        </tr>
\

34	5	result_foot	        <tr> 
\
          <td colspan="3" valign="bottom" align="center"><b><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$pollvars[total_text]: 
\
            $total_votes</font></b></td>
\
        </tr>
\
        <tr align="center"> 
\
          <td colspan="3" valign="top"><font face="$pollvars[font_face]" color="$pollvars[font_color]" size="1">$VOTE 
\
            <br>
\
            $COMMENT</font></td>
\
        </tr>
\
        <tr align="right"> 
\
          <td colspan="3"><font face="$pollvars[font_face]" size="1"><a href="http://www.proxy2.de" target="_blank" title="Advanced Poll">Version $pollvars[poll_version]</a></font></td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

35	5	comment	<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#666699">
\
  <tr align="center">
\
    <td>
\
     <style type="text/css">
\
      <!--
\
       .button {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
\
       .textarea {  font-family: "MS Sans Serif"; font-size: 9pt; width: 195px}
\
       .input {  width: 195px}
\
      -->
\
    </style><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Send Your Comment</b></font></td>
\
  </tr>
\
  <tr>
\
    <td>
\
      <table border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#F3F3F3" width="200">
\
        <tr>
\
          <td width="149">
\
            <form method="post" action="$this->form_forward">
\
              <table border="0" cellspacing="0" cellpadding="2" bgcolor="" align="center">
\
                <tr>
\
                  <td class="td1" height="40"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="1">$question</font></b></td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Name:</font><br>
\
                    <input type="text" name="name" maxlength="25" class="input" size="23">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">e-mail:</font><br>
\
                    <input type="text" name="email" size="23" maxlength="50" class="input">
\
                  </td>
\
                </tr>
\
                <tr>
\
                  <td class="td1"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Comment(*):</font><br>
\
                    <font face="MS Sans Serif" size="1">
\
                    <textarea name="message" cols="19" wrap="VIRTUAL" rows="6" class="textarea"></textarea>
\
                    </font>
\
                  </td>
\
                </tr>
\
                <tr valign="top">
\
                  <td>
\
                    <input type="submit" value="Submit" class="button">
\
                    <input type="reset" value="Reset" class="button">
\
                    <input type="hidden" name="action" value="add">
\
                    <input type="hidden" name="id" value="$poll_id">
\
                  </td>
\
                </tr>
\
              </table>
\
            </form>
\
          </td>
\
        </tr>
\
      </table>
\
    </td>
\
  </tr>
\
</table>
\

36	0	poll_comment	<table border="0" cellspacing="1" cellpadding="2" width="450">
\
  <tr bgcolor="#E4E4E4"> 
\
    <td bgcolor="#F2F2F2"><b><font size="1" face="Verdana, Arial, Helvetica, sans-serif">$data[name]</font></b> 
\
      <i><font size="1" face="Verdana, Arial, Helvetica, sans-serif">$data[email]</font></i> 
\
      - <font size="1" face="Verdana, Arial, Helvetica, sans-serif">$data[time]</font><br>
\
      <font size="1" face="Arial, Helvetica, sans-serif">$data[host] - $data[browser]</font> 
\
    </td>
\
  </tr>
\
  <tr>
\
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">$data[message]</font> 
\
    </td>
\
  </tr>
\
  <tr> 
\
    <td height="15">&nbsp;</td>
\
  </tr>
\
</table>
\

37	0	poll_list	<table border="0" cellspacing="0" cellpadding="4" width="450">
\
  <tr> 
\
    <td width="80" valign="top"> &#0149; <font size="2" face="Arial, Helvetica, sans-serif"><i>$data[timestamp]</i></font></td>
\
    <td width="354"><font face="Arial, Helvetica, sans-serif" size="2"><a href="$PHP_SELF?poll_id=$data[poll_id]">$data[question]</a></font></td>
\
  </tr>
\
</table>
\

\.
COPY "poll_data" FROM stdin;
1	1	1	Linux	blue	49
2	1	2	Solaris	yellow	12
3	1	3	FreeBSD	green	29
4	1	4	WindowsNT	brown	17
5	1	5	Unix	grey	10
6	1	6	BSD	red	15
7	1	7	other	purple	9
8	2	5	Sybase	orange	2
9	2	4	MS SQL	green	9
10	2	3	Oracle	blue	17
11	2	2	PostgreSQL	gold	6
12	2	1	MySQL	pink	23
13	2	6	other	brown	3
14	2	7	DB/2	grey	4
15	3	1	PHP	red	65
16	3	2	Perl	orange	34
17	3	3	ASP	green	17
18	3	4	JSP	purple	20
19	3	5	Python	gold	7
20	3	6	other	aqua	16
\.
COPY "poll_ip" FROM stdin;
\.
COPY "poll_comment" FROM stdin;
1	1	1018815036	localhost	Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0)	nobody	nobody@server.com	This is the first comment!
\.
COPY "poll_log" FROM stdin;
\.
COPY "poll_user" FROM stdin;
1	admin	b0f6dfb42fa80caee6825bfecd30f094	d8f826b3b6c938e6e6ff6d9c099ee2b3	1018815068
\.
COPY "poll_templateset" FROM stdin;
1	default	2002-04-14 22:10:36
2	simple	2002-04-14 22:10:36
3	popup	2002-04-14 22:10:36
4	graphic	2002-04-14 22:10:36
5	plain	2002-04-14 22:10:36
\.
CREATE  INDEX "tplset_id_poll_templates_key" on "poll_templates" using btree ( "tplset_id" "int4_ops" );
CREATE  INDEX "poll_id_poll_data_key" on "poll_data" using btree ( "poll_id" "int4_ops" );
CREATE  INDEX "poll_id_poll_ip_key" on "poll_ip" using btree ( "poll_id" "int4_ops" );
CREATE UNIQUE INDEX "config_id_poll_config_ukey" on "poll_config" using btree ( "config_id" "int2_ops" );
CREATE  INDEX "option_id_poll_log_key" on "poll_log" using btree ( "option_id" "int4_ops" );
CREATE  INDEX "poll_id_poll_log_key" on "poll_log" using btree ( "poll_id" "int4_ops" );
