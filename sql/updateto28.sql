USE @sqldatabase@;

ALTER TABLE @sqldatabase@.calls ADD Way enum('in','out') default NULL AFTER CO;
ALTER TABLE @sqldatabase@.calls CHANGE Outgoing Number decimal(100,0) unsigned NOT NULL default '0';
ALTER TABLE @sqldatabase@.calls DROP COLUMN Incoming;
