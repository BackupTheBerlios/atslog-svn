<?
		if(empty($sortBy))	$sortBy="1";
		if($int != "")		$additionalReq.=" AND (calls.internally = '".$int."')";
		if($num != "")		$additionalReq.=" AND (calls.number = '".$num."')";

// Подзапрос для выяснения общего количества страниц
// ----------------------------------------------------------------------------
		$qP="SELECT COUNT(*) from calls where ((calls.timeofcall>='".$from_date."') AND (calls.timeofcall<='".$to_date."') ".$additionalReq.")";
		$limitsP = getLimits($qP);
// ----------------------------------------------------------------------------
		$q="SELECT calls.timeofcall,calls.internally,calls.co,calls.number,calls.duration,extlines.name,intphones.name,phonebook.description
		from calls
		LEFT JOIN extlines ON calls.co = extlines.line
		LEFT JOIN intphones ON calls.internally = intphones.intnumber
		LEFT JOIN phonebook ON
		    calls.number LIKE phonebook.number AND phonebook.login = '".$_SERVER['PHP_AUTH_USER']."'
		    OR calls.number LIKE phonebook.number AND phonebook.login IS NULL
		where ((calls.timeofcall>='".$from_date."')
		AND (calls.timeofcall<='".$to_date."') ".$additionalReq.")
		ORDER BY ".$sortBy." ".$order.$limitsP;
		if($debug) echo $q."<br>";
		if($cacheflush) $res = $conn->CacheFlush($q);
		$res = $conn->CacheExecute($q);

// Напечатаем на странице все исходные параметры нашего запроса
// ----------------------------------------------------------------------------
		if(!empty($res->fields[5])) $coLineDescription = $res->fields[5];
		if(!empty($res->fields[6])) $intPhoneDescription = $res->fields[6];
		if(!empty($res->fields[7])) $NumberDescription = $res->fields[7];
		if(empty($export)) pechat_ishodnyh();

// если по запросу найдены записи в таблице 
// ----------------------------------------------------------------------------
		if ($res && $res->RecordCount() > 0) { 
		    if(empty($export)){
			print("<table cellspacing=0 cellpadding=1 border=0><tr ".$COLORS['BaseBorderTableBgcolor']."><td><table cellspacing=1 cellpadding=4 border=0>");
			print ("<tr ".$COLORS['BaseTablesBgcolor']." align=center>
			<td>");
			AddTableHeader("1",$GUI_LANG['DateOfCall'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("3",$GUI_LANG['ExternalLine'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("5",$GUI_LANG['Duration'],$toprint);
			echo("</td>
			</tr>");
		    }else{
			$expor_excel->MontaConteudo(0, 0,$GUI_LANG['DateOfCall']);
			$expor_excel->MontaConteudo(0, 1,$GUI_LANG['ExternalLine']);
			$expor_excel->MontaConteudo(0, 2,$GUI_LANG['Duration']);
			$expor_excel->MontaConteudo(1, 0, "   ");
			$expor_excel->mid_sqlparaexcel();
		    }
// Да, это действительно экспорт!
// -----------------------------------------------------------------------------
			
			$anyDigit=1;
			$linha=0;
			while (!$res->EOF) {
				$row = $res->fields;

				$timeOfCall=$row[0];
				$intPhone=$row[1];
				$coLine=$row[2];
				$CallNumber=$row[3];
				$Duration=$row[4];
				$NamedLine=$row[5];
				$NamedPhone=$row[6];
				$PhonebookDescription=$row[7];
				
				$NumberIs=setNumberIs();
				$intPhoneDescription=setPhoneDescription();
				$NumberDescription=SetNumberDescription();
				$coLineDescription=SetLineDescription();
				setCollColor();

				if(empty($export)){
				    echo"<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over');\" onmouseout=\"setPointer(this, $anyDigit, 'out');\" onmousedown=\"setPointer(this, $anyDigit, 'click');\">";
				    echo"<td nowrap>$timeOfCall</td>";
				    echo"<td>$coLine $coLineDescription</td>";
				    echo"<td>".sumTotal($Duration,0)."</td>\n";
				    echo"</tr>\n";
				}else{
				    if(!empty($coLineDescription)) $coLineDescription=" ".$coLineDescription;
				    $expor_excel->MontaConteudo($linha+2, 0, $timeOfCall);
				    $expor_excel->MontaConteudo($linha+2, 1, $coLine.$coLineDescription);
				    $expor_excel->MontaConteudo($linha+2, 2, sumTotal($Duration,0));
				}
				
				array($InAll);
				if(!empty($timeOfCall)) $InAll[0] ++;
				if(!empty($Duration)) $InAll[4] += $Duration;
				
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
	    if(!empty($InAll[4])) echo $GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[4],1);
	    echo("&nbsp;</td>");
	    print ("</tr>\n");

	    echo("<tr ".$COLORS['TotalTrBgcolor']."><td>".$GUI_LANG['Altogether'].":&nbsp;&nbsp;</td>");
    	    echo("<td colspan=2>");
	    echo totalTableFooter('5',0);
	    echo totalTableFooter('6',0);
	    echo totalTableFooter('7',0);
	    echo totalTableFooter('8',0);
	    echo("&nbsp;</td>");
	    print ("</tr>\n");

	    print ("</table>\n\n </td></tr></table>");
	}else{
	    array($TTF);
	    $TTF[4]=$GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[4],2);

	    array($TTFa);
	    $TTFa[5]=totalTableFooter('5',2);
	    $TTFa[6]=totalTableFooter('6',2);
	    $TTFa[7]=totalTableFooter('7',2);
	    $TTFa[8]=totalTableFooter('8',2);

	    TTFprint();
	}

	if(!empty($export)) $expor_excel->GeraArquivo();

// ------------------------------------------------------------------------------------------------------

		}
		else
		{
			print("<br><font size=+1>".$GUI_LANG['NoSuchData']."</font>");
		};
?>
