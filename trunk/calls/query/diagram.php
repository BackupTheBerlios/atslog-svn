<?

		$secondsInDay = 60*60*24;
		$diagramDate = mktime (0,0,0,$mon,$day,$year);
		$diagramDate2 = mktime (0,0,0,$mon2,$day2,$year2);
		$daysInTheTable = ceil(($diagramDate2-$diagramDate)/$secondsInDay);
		$heightOfTable = 200;
		//echo "$diagramDate $diagramDate2 $daysInTheTable $additionalReq<br><br>";

		for($i=0;$i<=$daysInTheTable;$i++){
			$secondsInYseterDay = (($i*$secondsInDay)+$diagramDate)-$secondsInDay;
			$secondsInTomorrowDay = (($i*$secondsInDay)+$diagramDate)+$secondsInDay;
			$yesterDay=date ("Y-m-d",$secondsInYseterDay);
			$tomorrowDay=date ("Y-m-d",$secondsInTomorrowDay);
			$q="SELECT SUM(Duration) FROM `calls` where TimeOfCall < '".$tomorrowDay."' AND TimeOfCall > '".$yesterDay."'".$additionalReq;
			if($debug) echo $q."<br>";

			$error=1;
			$rs = $conn->Execute($q);

			if ($rs){
				while ($row = $rs->FetchRow()) {

					//echo "$yesterDay $tomorrowDay $secondsInYseterDay $secondsInTomorrowDay<br>";
					//echo "$row[0]<br>";
					$dinamicInDays[] = $row[0];
					if ($maxnumber < $row[0]) $maxnumber=$row[0];
					$error=0;
				}
			}
		}
		if ($error){
			echo "<font size=+1>".$GUI_LANG['NoSuchData']."</font>";
		}else{
			$onePersentOfHeight = $maxnumber/$heightOfTable;
			$sizeOfDinamicInDays = sizeof($dinamicInDays);
			$COLS=$sizeOfDinamicInDays+2;

			if ($sizeOfDinamicInDays < 32) {
				$widthOfCell = 5;
				// Если захватываем один месяц, то ширина столбца:
			}elseif($sizeOfDinamicInDays < 62){
				// Если захватываем два месяца, то ширина столбца:
				$widthOfCell = 4;
			}elseif($sizeOfDinamicInDays < 93){
				// Если захватываем три месяца, то ширина столбца:
				$widthOfCell = 3;
			}elseif($sizeOfDinamicInDays < 125){
				$widthOfCell = 2;
			}else{
				$widthOfCell = 1;
			}
			$widthOfX = $sizeOfDinamicInDays*$widthOfCell;
			$widthOfY = $heightOfTable+20;

			echo "
				<table border=0 cols=1 cellpadding=1 cellspacing=0 bgcolor=\"#F0F0F0\" align=left width=$widthOfX>
					<tr>
						<td>
							<table COLS=$COLS border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td align=center><img src=\"../include/img/arrow.y.gif\" width=5 height=3 border=0 hspace=0 vspace=0 align=center><br><img src=\"../include/img/dot.colorblack.gif\" width=1 height=$widthOfY border=0 hspace=0 vspace=0 align=center></td>
									";
			while (list ($key, $val) = each ($dinamicInDays)) {
				if ($onePersentOfHeight) $colHeight = round($val/$onePersentOfHeight);
				if ($colHeight < 1) $colHeight = 1;
				if ($val == 0){
					$diagrImg="<img src=\"../include/img/dot.free.gif\" width=$widthOfCell height=1 border=0 hspace=0 vspace=0>";
				}else{
					$diagrImg="<img src=\"../include/img/dot.colortwo.gif\" width=$widthOfCell height=$colHeight border=0 hspace=0 vspace=0>";
				}
				echo "<td valign=bottom>$diagrImg</td>\n";
			}
			echo "					<td><img src=\"../include/img/dot.free.gif\" width=1 height=1 border=0 hspace=0 vspace=0></td>
								</tr>
								<tr><td valign=middle><img src=\"../include/img/arrow.0.gif\" width=5 height=5 border=0 hspace=0 vspace=0></td><td colspan=$widthOfX valign=middle><img src=\"../include/img/dot.colorblack.gif\" width=$widthOfX height=1 border=0 hspace=0 vspace=0 align=left></td><td><img src=\"../include/img/arrow.x.gif\" width=3 height=5 border=0 hspace=0 vspace=0></td></tr>
							</table>
						</td>
					</tr>
				</table>
			";

		}

?>
