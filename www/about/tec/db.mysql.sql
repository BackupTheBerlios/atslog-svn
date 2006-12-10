CREATE TABLE calls (
    timeofcall datetime NOT NULL default '0000-00-00 00:00:00',
    forwarded char(3) default NULL,
    internally smallint(6) unsigned default NULL,
    co smallint(6) unsigned default NULL,
    way char(3) default NULL,
    number decimal(100,0) unsigned NOT NULL default '0',
    duration int(10) unsigned NOT NULL default '0',
    cost decimal(100,3) unsigned default NULL,
    KEY co (co),
    KEY internally (internally),
    KEY timeofcall (timeofcall),
    KEY cost (cost)
) COMMENT='www.ATSlog.dp.ua';
			