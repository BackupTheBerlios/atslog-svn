all:	
	diff CoAll.php IntAll.php > CoToInt.diff

pat:	
	mv IntAll.php IntAll.php.old
	cp CoAll.php IntAll.php
	patch IntAll.php < CoToInt.diff