
USE @sqldatabase@;

DROP TABLE IF EXISTS @sqldatabase@.tarif;

DELETE FROM mysql.user WHERE User = 'atslog1' AND Host = '@sqlfromhost@';
DELETE FROM mysql.db WHERE User = 'atslog1' AND Host = '@sqlfromhost@';
DELETE FROM mysql.tables_priv WHERE User = 'atslog1' AND Host = '@sqlfromhost@';
DELETE FROM mysql.columns_priv WHERE User = 'atslog1' AND Host = '@sqlfromhost@';
FLUSH PRIVILEGES ;

ALTER TABLE @sqldatabase@.calls COMMENT = 'www.ATSlog.dp.ua';
ALTER TABLE @sqldatabase@.calls CHANGE TimeOfCall timeofcall DATETIME NOT NULL;
ALTER TABLE @sqldatabase@.calls CHANGE Forwarded forwarded CHAR(3);
ALTER TABLE @sqldatabase@.calls CHANGE Internally internally SMALLINT(6) UNSIGNED;
ALTER TABLE @sqldatabase@.calls CHANGE CO co SMALLINT(6) UNSIGNED;
ALTER TABLE @sqldatabase@.calls CHANGE Way way CHAR(3);
ALTER TABLE @sqldatabase@.calls CHANGE Number number DECIMAL(65, 0) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.calls CHANGE Duration duration INT(10) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.calls CHANGE cost cost DECIMAL(65, 3) UNSIGNED DEFAULT '0';
ALTER TABLE @sqldatabase@.calls DROP INDEX Internally;
ALTER TABLE @sqldatabase@.calls DROP INDEX CO;
ALTER TABLE @sqldatabase@.calls DROP INDEX TimeOfCall;
ALTER TABLE @sqldatabase@.calls DROP INDEX Cost;
ALTER TABLE @sqldatabase@.calls ADD INDEX (co);
ALTER TABLE @sqldatabase@.calls ADD INDEX (internally);
ALTER TABLE @sqldatabase@.calls ADD INDEX (timeofcall);
ALTER TABLE @sqldatabase@.calls ADD INDEX (cost);
UPDATE @sqldatabase@.calls SET way = '1' WHERE way = 'in';
UPDATE @sqldatabase@.calls SET way = '2' WHERE way = 'out';

ALTER TABLE @sqldatabase@.co RENAME extlines;
ALTER TABLE @sqldatabase@.extlines CHANGE CO line VARCHAR(25) DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.extlines CHANGE Name name VARCHAR(25) NOT NULL;
ALTER TABLE @sqldatabase@.extlines DROP INDEX CO;
ALTER TABLE @sqldatabase@.extlines DROP INDEX Name;
ALTER TABLE @sqldatabase@.extlines ADD UNIQUE (line);
ALTER TABLE @sqldatabase@.extlines ADD INDEX (name);
ALTER TABLE @sqldatabase@.extlines COMMENT = 'Names of external lines';

ALTER TABLE @sqldatabase@.country CHANGE ID id INT(4) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.country CHANGE Name name VARCHAR(50) NOT NULL;
ALTER TABLE @sqldatabase@.country DROP PRIMARY KEY, ADD PRIMARY KEY (id);

ALTER TABLE @sqldatabase@.internally RENAME intphones;
ALTER TABLE @sqldatabase@.intphones CHANGE Internally intnumber VARCHAR(25) DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.intphones CHANGE Name name VARCHAR(25) NOT NULL;
ALTER TABLE @sqldatabase@.intphones DROP INDEX Internally;
ALTER TABLE @sqldatabase@.intphones DROP INDEX Name;
ALTER TABLE @sqldatabase@.intphones ADD UNIQUE (intnumber);
ALTER TABLE @sqldatabase@.intphones ADD INDEX (name);
ALTER TABLE @sqldatabase@.intphones COMMENT = 'Names of internally phones';

#
# Структура таблицы `phonebook`
#

CREATE TABLE @sqldatabase@.phonebook (
    login varchar(25) default NULL,
    number decimal(65,0) unsigned default '0',
    description varchar(255) default NULL,
    UNIQUE KEY number (number),
    KEY login (login),
    KEY description (description)
) COMMENT='Phone book';

ALTER TABLE @sqldatabase@.towns CHANGE ID id INT(6) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.towns CHANGE ID_Country id_country SMALLINT(4) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.towns CHANGE Name name VARCHAR(50) NOT NULL;
ALTER TABLE @sqldatabase@.towns DROP INDEX ID;
ALTER TABLE @sqldatabase@.towns DROP INDEX ID_Country;
ALTER TABLE @sqldatabase@.towns ADD INDEX (id);
ALTER TABLE @sqldatabase@.towns ADD INDEX (id_country);

ALTER TABLE @sqldatabase@.users CHANGE Internally internally VARCHAR(25) DEFAULT '0' NOT NULL;
ALTER TABLE @sqldatabase@.users CHANGE Login login VARCHAR(25) NOT NULL;
ALTER TABLE @sqldatabase@.users CHANGE `Password` `password` VARCHAR(100);
ALTER TABLE @sqldatabase@.users CHANGE Firstname firstname VARCHAR(25);
ALTER TABLE @sqldatabase@.users CHANGE Secondname secondname VARCHAR(25);
ALTER TABLE @sqldatabase@.users CHANGE Lastname lastname VARCHAR(25);
ALTER TABLE @sqldatabase@.users DROP PRIMARY KEY,ADD PRIMARY KEY (internally,login);

DELETE FROM @sqldatabase@.users WHERE login = 'atslog';
INSERT INTO @sqldatabase@.users VALUES ('atslog', 'atslog',MD5('atslog'), 'by', 'default', 'Administrator');

ALTER TABLE @sqldatabase@.usersgroups CHANGE Login login VARCHAR(25) NOT NULL;
ALTER TABLE @sqldatabase@.usersgroups CHANGE Groups groups VARCHAR(25) NOT NULL;
ALTER TABLE @sqldatabase@.usersgroups DROP INDEX Login;
ALTER TABLE @sqldatabase@.usersgroups DROP INDEX Groups;
ALTER TABLE @sqldatabase@.usersgroups ADD INDEX (login);
ALTER TABLE @sqldatabase@.usersgroups ADD INDEX (groups);
