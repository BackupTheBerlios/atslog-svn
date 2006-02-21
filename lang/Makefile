all:	utf8 cp1251

utf8:	
	iconv -f KOI8-R -t UTF-8 ru_RU.KOI8-R > ru_RU.UTF-8.tmp
	sed -e "s/KOI8-R/UTF-8/g" ru_RU.UTF-8.tmp > ru_RU.UTF-8
	rm ru_RU.UTF-8.tmp

cp1251:	
	iconv -f KOI8-R -t CP1251 ru_RU.KOI8-R > ru_RU.CP1251.tmp
	sed -e "s/KOI8-R/CP1251/g" ru_RU.CP1251.tmp > ru_RU.CP1251
	rm ru_RU.CP1251.tmp

