all:	koi utf8

#utf8:	
#	iconv -f WINDOWS-1251 -t UTF-8 ru_1251.php > ru_UTF8.php.tmp
#	sed -e "s/Russian 1251/Russian UTF-8/g" ru_UTF8.php.tmp > ru_UTF8.php
#	rm ru_UTF8.php.tmp
koi:	
	iconv -f WINDOWS-1251 -t KOI8-R ru_1251.php > ru_KOI8-R.php.tmp
	sed -e "s/Russian 1251/Russian KOI8-R/g" ru_KOI8-R.php.tmp > ru_KOI8-R.php
	rm ru_KOI8-R.php.tmp
1251:	
	iconv -f KOI8-R -t WINDOWS-1251 ru_KOI8-R.php > ru_1251.php.tmp
	sed -e "s/Russian KOI8-R/Russian 1251/g" ru_1251.php.tmp > ru_1251.php
	rm ru_1251.php.tmp
