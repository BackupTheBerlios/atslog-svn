USE @sqldatabase@;

ALTER TABLE @sqldatabase@.calls CHANGE Duration Duration MEDIUMINT( 8 ) UNSIGNED DEFAULT NULL;
