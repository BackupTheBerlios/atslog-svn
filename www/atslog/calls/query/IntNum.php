<?
		if(empty($sortBy))	$sortBy="1";
		if($int != "")		$additionalReq.=" AND (calls.internally='".$int."')";

// Поготовим подзапрос для выяснения общего количества страниц 
// ------------------------------------------------------------------------------------------------------
//
		$qP1="CREATE TEMPORARY TABLE tmpNum_P (
		     number decimal(65,0) NULL default '0',
		     Count decimal(65,0) NULL default '0'
		)";


		$qP2="
		INSERT INTO tmpNum_P
		SELECT calls.number,count(*)
		from calls
		where ((calls.timeofcall>='".$from_date."')
		AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".$vectorReq."
		)
		GROUP BY calls.number";

                $qP3="DROP TABLE tmpNum_P";

		$qP="SELECT COUNT(*) FROM tmpNum_P";

// Выполним подзапрос для выяснения общего количества страниц
// ----------------------------------------------------------------------------

		$resP = $conn->Execute($qP1);
		$resP = $conn->Execute($qP2);
		$limitsP = getLimits($qP);
                $resP = $conn->Execute($qP3);
		if($debug) echo $qP1.";<br><br>".$qP2.";<br><br>".$qP3.";<br><br>";
// ----------------------------------------------------------------------------
		$q="SELECT calls.number,count(*),sum(calls.duration),calls.way,intphones.name,phonebook.description
		from calls
		LEFT JOIN intphones ON calls.internally = intphones.intnumber
		LEFT JOIN phonebook ON
		       calls.number LIKE phonebook.number AND phonebook.login = '".$_SERVER['PHP_AUTH_USER']."'
		    OR calls.number LIKE phonebook.number AND phonebook.login IS NULL
		where ((calls.timeofcall>='".$from_date."')
		AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".$vectorReq."
		)
		GROUP BY calls.number,calls.way,intphones.name,phonebook.description ORDER BY ".$sortBy." ".$order.$limitsP;
		if($debug) echo $q.";<br>";
		if($cacheflush) $res = $conn->CacheFlush($q);
		$res = $conn->CacheExecute($q);

// Напечатаем на странице все исходные параметры нашего запроса                
// ----------------------------------------------------------------------------
                if(!empty($res->fields[4])) $intPhoneDescription = $res->fields[4];

		if(empty($export)) pechat_ishodnyh();

// если по запросу найдены записи в таблице
// ----------------------------------------------------------------------------
		if ($res && $res->RecordCount() > 0) { 
// Проверим, может это экспорт? 
// -----------------------------------------------------------------------------
		    if(empty($export)){
			print("<table cellspacing=0 cellpadding=1 border=0><tr ".$COLORS['BaseBorderTableBgcolor']."><td><table cellspacing=1 cellpadding=4 border=0>");
			print ("<tr ".$COLORS['BaseTablesBgcolor']." align=center>
			<td>");
			AddTableHeader("1",$GUI_LANG['Number'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("2",$GUI_LANG['QuantityOfCalls'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("3",$GUI_LANG['GeneralDuration'],$toprint);
			echo("</td>
			</tr>");
		    }else{
// Да, это действительно экспорт!
// -----------------------------------------------------------------------------
			$expor_excel->MontaConteudo(0, 0,$GUI_LANG['Number']);
			$expor_excel->MontaConteudo(0, 1,$GUI_LANG['QuantityOfCalls']);
			$expor_excel->MontaConteudo(0, 2,$GUI_LANG['GeneralDuration']);
			$expor_excel->MontaConteudo(1, 0, "   ");
			$expor_excel->mid_sqlparaexcel();
		    }

			$anyDigit=1;
			$linha=0;
			while (!$res->EOF) {
				$row = $res->fields;

				$CallNumber=$row[0];
				$Duration=$row[2];
				$CallWay=$row[3];
				$NamedPhone=$row[4];
				$PhonebookDescription=$row[5];
				
				$NumberIs=setNumberIs();
				$intPhoneDescription=setPhoneDescription();
				$NumberDescription=SetNumberDescription();
				$coLineDescription=SetLineDescription();
				setCollColor();

				if(empty($export)){
				    echo"<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over');\" onmouseout=\"setPointer(this, $anyDigit, 'out');\" onmousedown=\"setPointer(this, $anyDigit, 'click');\">";
				    echo"<td>${FontColor}$NumberIs${FontColorEnd}</td></td>";
				    echo"<td><table width=100%>
					    <tr>
						<td width=1%><a href=\"".complitLink($local_type="IntNumDetail",$local_num="$CallNumber")."\" title=\"".$GUI_LANG['Number'].": $NumberIs, ".$GUI_LANG['FromInternalPhone'].": $int $intPhoneDescription\">$row[1]</a>&nbsp;</td>
						${NumberDescription}
					    </tr>
					</table></td>";
				    echo"<TD>${FontColor}".sumTotal($Duration,0)."${FontColorEnd}</TD>";
				    echo "</TR>\n";
				}else{
				    if(!empty($PhonebookDescription)) $PhonebookDescription=" ".$PhonebookDescription;
				    $expor_excel->MontaConteudo($linha+2, 0, $NumberIs);
				    $expor_excel->MontaConteudo($linha+2, 1, $row[1].$PhonebookDescription);
				    $expor_excel->MontaConteudo($linha+2, 2, sumTotal($Duration,0));
				}
				array($InAll);
				if(!empty($row[1])) $InAll[0] ++;
				if(!empty($row[1])) $InAll[1] += $row[1];
				if(!empty($Duration)) $InAll[2] += $Duration;
// ------------------------------------------------------------------------------------------------------
				$res->MoveNext();
				$anyDigit++;
				$linha++;
			};

// Функция totalTableFooter() выводит итоговые сведения. В качестве аргумента выступает номер SQL запроса
// Функция sumTotal() преобразует число секунд в количество часов,минут и секунд.
// ------------------------------------------------------------------------------------------------------
	if(empty($export)){
	    echo("<tr ".$COLORS['AltogetherTrBgcolor']."><td>".$GUI_LANG['Total'].": <b>".$InAll[0]."</b></td>");
    	    echo("<td colspan=2>");
	    if(!empty($InAll[1])) echo $GUI_LANG['QuantityOfCalls'].": <b>".$InAll[1]."</b><br>";
	    if(!empty($InAll[2])) echo $GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[2],1);
	    echo("&nbsp;</td>");
	    print ("</tr>\n");

	    if($pages > 1 or $debug){
		echo("<tr ".$COLORS['TotalTrBgcolor']."><td>".$GUI_LANG['Altogether'].":&nbsp;&nbsp;</td>");
    		echo("<td colspan=2>");
		echo totalTableFooter('5',0);
		echo totalTableFooter('6',0);
		echo totalTableFooter('7',0);
		echo totalTableFooter('8',0);
		echo("&nbsp;</td>");
		print ("</tr>\n");
	    }
	    print ("</table>\n\n </td></tr></table>");
	}else{
	    array($TTF);
	    $TTF[1]=$GUI_LANG['QuantityOfCalls'].": ".$InAll[1];
	    $TTF[2]=$GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[2],2);

	    if($pages > 1 or $debug){
		array($TTFa);
		$TTFa[5]=totalTableFooter('5',2);
		$TTFa[6]=totalTableFooter('6',2);
		$TTFa[7]=totalTableFooter('7',2);
		$TTFa[8]=totalTableFooter('8',2);
	    }
	    TTFprint();
	}
	if(!empty($export)) $expor_excel->GeraArquivo();

// ------------------------------------------------------------------------------------------------------

		}else{
			print("<br><font size=+1>".$GUI_LANG['NoSuchData']."</font>");
		};

?>
