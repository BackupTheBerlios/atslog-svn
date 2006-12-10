Advanced Poll v2.03 (PHP/MySQL)
Copyright (c)2001 Chi Kien Uong
URL: http://www.proxy2.de

Requirements:

  - MySQL 3.2x or PostgreSQL 7.x
  - PHP3 or PHP4

Installation:

1. Open the configuration file 'config.inc.php' with a text editor
   and set up your database settings.

   $POLLDB["dbName"] = "YourDBName";
   $POLLDB["host"]   = "localhost";
   $POLLDB["user"]   = "YourUsername";
   $POLLDB["pass"]   = "YourPassword";

2. Call the install script from your browser -> http://www.yourURL.com/poll/install.php

