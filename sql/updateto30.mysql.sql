
USE @sqldatabase@;

ALTER TABLE @sqldatabase@.calls CHANGE TimeOfCall TimeOfCall datetime default NULL;
ALTER TABLE @sqldatabase@.calls CHANGE Forwarded Forwarded TINYINT(3) unsigned NOT NULL default '0';
ALTER TABLE @sqldatabase@.calls CHANGE Internally Internally smallint(6) unsigned default NULL;
ALTER TABLE @sqldatabase@.calls CHANGE CO CO smallint(6) unsigned default NULL;
ALTER TABLE @sqldatabase@.calls CHANGE Duration Duration smallint(5) unsigned default NULL;

