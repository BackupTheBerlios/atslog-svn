CREATE DATABASE IF NOT EXISTS @sqldatabase@;

USE @sqldatabase@;

GRANT USAGE ON *.* TO '@sqlmasteruser@'@'@sqlfromhost@' IDENTIFIED BY '@sqlmaspasswd@';
GRANT ALL PRIVILEGES ON @sqldatabase@.* TO '@sqlmasteruser@'@'@sqlfromhost@';


# --------------------------------------------------------

#
# Структура таблицы `calls`
#

CREATE TABLE `calls` (
  `timeofcall` datetime NOT NULL default '0000-00-00 00:00:00',
  `forwarded` char(3) default NULL,
  `internally` smallint(6) unsigned default NULL,
  `co` smallint(6) unsigned default NULL,
  `way` char(3) default NULL,
  `number` decimal(65,0) unsigned NOT NULL default '0',
  `duration` int(10) unsigned NOT NULL default '0',
  `cost` decimal(65,3) unsigned default '0.000',
  KEY `co` (`co`),
  KEY `internally` (`internally`),
  KEY `timeofcall` (`timeofcall`),
  KEY `cost` (`cost`)
) COMMENT='www.ATSlog.dp.ua';

#
# Дамп данных таблицы `calls`
#


# --------------------------------------------------------

#
# Структура таблицы `country`
#

CREATE TABLE `country` (
  `id` int(4) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) COMMENT='List of countries with tel codes';


#
# Структура таблицы `unauth`
#

CREATE TABLE `unauth` (
  `username` varchar(64) NOT NULL default '',
  `pass` varchar(64) NOT NULL default '',
  `ip` varchar(64) default NULL,
  `logintime` timestamp(14) NOT NULL,
  `x_forwardeded_for` varchar(64) default NULL,
  KEY `username` (`username`),
  KEY `pass` (`pass`),
  KEY `logintime` (`logintime`),
  KEY `ip` (`ip`)
) COMMENT='Attempts of authentifications';

#
# Дамп данных таблицы `unauth`
#


# --------------------------------------------------------

#
# Структура таблицы `users`
#

CREATE TABLE `users` (
  `internally` varchar(25) NOT NULL default '0',
  `login` varchar(25) NOT NULL default '',
  `password` varchar(100) default NULL,
  `firstname` varchar(25) default NULL,
  `secondname` varchar(25) default NULL,
  `lastname` varchar(25) default NULL,
  PRIMARY KEY  (`internally`,`login`)
) COMMENT='Personnels';

# --------------------------------------------------------

#
# Структура таблицы `usersgroups`
#

CREATE TABLE `usersgroups` (
  `login` varchar(25) NOT NULL default '',
  `groups` varchar(25) NOT NULL default '',
  KEY `login` (`login`),
  KEY `groups` (`groups`)
) COMMENT='Permissions';

#
# Структура таблицы `extlines`
#

CREATE TABLE `extlines` (
  `line` varchar(25) NOT NULL default '0',
  `name` varchar(25) NOT NULL default '',
  UNIQUE KEY `line` (`line`),
  KEY `name` (`name`)
) COMMENT='Names of external lines';

#
# Дамп данных таблицы `extlines`
#


# --------------------------------------------------------

#
# Структура таблицы `intphones`
#

CREATE TABLE `intphones` (
  `intnumber` varchar(25) NOT NULL default '0',
  `name` varchar(25) NOT NULL default '',
  UNIQUE KEY `intnumber` (`intnumber`),
  KEY `name` (`name`)
) COMMENT='Names of internally phones';

#
# Дамп данных таблицы `intphones`
#


# --------------------------------------------------------

#
# Структура таблицы `phonebook`
#

CREATE TABLE `phonebook` (
  `login` varchar(25) default NULL,
  `number` decimal(65,0) unsigned default '0',
  `description` varchar(255) default NULL,
  UNIQUE KEY `number` (`number`),
  KEY `login` (`login`),
  KEY `description` (`description`)
) COMMENT='Phone book';

#
# Дамп данных таблицы `phonebook`
#


# --------------------------------------------------------

#
# Дамп данных таблицы `tarif`
#


# --------------------------------------------------------

#
# Структура таблицы `towns`
#

CREATE TABLE `towns` (
  `id` int(6) unsigned NOT NULL default '0',
  `id_country` smallint(4) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  KEY `id` (`id`),
  KEY `id_country` (`id_country`)
) COMMENT='Towns codes with the links to country';

