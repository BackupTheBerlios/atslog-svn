<?
		$CI="Internally";
		$Ci="Internally";
		$ci="internally";
		if(empty($sortBy))	$sortBy="1";
		if($int != "")		$additionalReq.=" AND (calls.${CI}='".$int."')";

// Поготовим подзапрос для выяснения общего количества страниц 
// ------------------------------------------------------------------------------------------------------
//
		$qP1="CREATE TEMPORARY TABLE tmp${Ci}Num_P (
		     Number decimal(100,0) unsigned NOT NULL default '0',
		     Count decimal(100,0) unsigned NOT NULL default '0',
		     KEY Number (Number),
		     KEY Count (Count)
		    )";


		$qP2="
		INSERT INTO tmp${Ci}Num_P
		SELECT calls.Number,count(*)
		from calls
		where ((calls.TimeOfCall>='".$from_date."')
		AND (calls.TimeOfCall<='".$to_date."') ".$additionalReq.")
		GROUP BY calls.Number";

                $qP3="DROP TABLE tmp${Ci}Num_P";

		$qP="SELECT COUNT(*) FROM tmp${Ci}Num_P";

// Выполним подзапрос для выяснения общего количества страниц
// ----------------------------------------------------------------------------

		$resP = $conn->Execute($qP1);
		$resP = $conn->Execute($qP2);
		$limitsP = getLimits($qP);
                $resP = $conn->Execute($qP3);
		if($debug) echo $qP1.";<br><br>".$qP2.";<br><br>".$qP3.";<br><br>";
// ----------------------------------------------------------------------------
		$q="SELECT calls.Number,count(*),sum(calls.Duration),calls.Way,${ci}.Name from calls LEFT JOIN ${ci} ON calls.${CI} = ${ci}.${CI} where ((calls.TimeOfCall>='".$from_date."') AND (calls.TimeOfCall<='".$to_date."') ".$additionalReq.") GROUP BY calls.Number ORDER BY ".$sortBy." ".$order.$limitsP;
		if($debug) echo $q.";<br>";
		if($cacheflush) $res = $conn->CacheFlush($q);
		$res = $conn->CacheExecute($q);

// Напечатаем на странице все исходные параметры нашего запроса                
// ----------------------------------------------------------------------------
                if(!empty($res->fields[4])) $telephone = $res->fields[4];

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
				$numberName = $row[0];
				if($row[0] == "0"){
				    if($row[3] == "1"){
					// Будем выводить комментарий к номеру в зависимости от
					// того, имеет АТС АОН или нет.
					$numberName = $IfAON;
				    }
				}
// Разные цвета для внутригородских, междугородних и международных звонков
// ------------------------------------------------------------------------------------------------------
//
				$FontColor='';
				$FontColorEnd='';

				if(empty($toprint)){
				    if($row[3] == "1" && $incoming!="3"){
					$FontColor="<font ".$COLORS['IncomingCalls'].">";
					$FontColorEng="</font>";
				    }elseif(ereg($MobileCallsR, $row[0])){
                                        $FontColor="<font ".$COLORS['MobileCalls'].">";
                                        $FontColorEng="</font>";
                                    }elseif(ereg($InternationalCalls, $row[0])){
					$FontColor="<font ".$COLORS['InternationalCalls'].">";
					$FontColorEng="</font>";
				    }elseif(ereg($LongDistanceCalls, $row[0])){
					$FontColor="<font ".$COLORS['LongDistanceCalls'].">";
					$FontColorEng="</font>";
				    }
				}
				if(empty($export)){
				    echo"<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmouseout=\"setPointer(this, $anyDigit, 'out', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmousedown=\"setPointer(this, $anyDigit, 'click', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\">";
				    echo"<td nowrap>${FontColor}$numberName${FontColorEnd}</td>";
				    echo"<td><a href=\"".complitLink($local_type="IntNumDetail",$local_num="$row[0]")."\" title=\"".$GUI_LANG['Number'].": $numberName, ".$GUI_LANG['FromInternalPhone'].": $telephone\">$row[1]</a></td>";
				    echo"<TD>${FontColor}".sumTotal($row[2],0)."${FontColorEnd}</TD>";
				    echo "</TR>\n";
				}else{
				    $expor_excel->MontaConteudo($linha+2, 0, $numberName);
				    $expor_excel->MontaConteudo($linha+2, 1, $row[1]);
				    $expor_excel->MontaConteudo($linha+2, 2, sumTotal($row[2],0));
				}
				array($InAll);
				if(!empty($numberName)) $InAll[0] ++;
				if(!empty($row[1])) $InAll[1] += $row[1];
				if(!empty($row[2])) $InAll[2] += $row[2];
// ------------------------------------------------------------------------------------------------------
				$res->MoveNext();
				$anyDigit++;
				$linha++;
			};

// Функция totalTableFooter() выводит итоговые сведения. В качестве аргумента выступает номер SQL запроса
// Функция sumTotal() преобразует число секунд в количество часов,минут и секунд.
// ------------------------------------------------------------------------------------------------------
	if(empty($export)){
	    echo("<tr ".$COLORS['AltogetherTrBgcolor']."><td>".$GUI_LANG['Altogether'].": <b>".$InAll[0]."</b></td>");
    	    echo("<td colspan=3>");
	    if(!empty($InAll[1])) echo $GUI_LANG['QuantityOfCalls'].": <b>".$InAll[1]."</b><br>";
	    if(!empty($InAll[2])) echo $GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[2],1);
	    echo("&nbsp;</td>");
	    print ("</tr>\n");

	    echo("<tr ".$COLORS['TotalTrBgcolor']."><td>".$GUI_LANG['Total'].":&nbsp;&nbsp;</td>");
    	    echo("<td colspan=3>");
	    echo totalTableFooter('5',0);
	    echo totalTableFooter('6',0);
	    echo totalTableFooter('7',0);
	    echo totalTableFooter('8',0);
	    echo("</td>");
	    print ("</tr>\n");

	    print ("</table>\n\n </td></tr></table>");
	}else{
	    array($TTF);
	    $TTF[1]=$GUI_LANG['QuantityOfCalls'].": ".$InAll[1];
	    $TTF[2]=$GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[2],2);

	    array($TTFa);
	    $TTFa[5]=totalTableFooter('5',2);
	    $TTFa[6]=totalTableFooter('6',2);
	    $TTFa[7]=totalTableFooter('7',2);
	    $TTFa[8]=totalTableFooter('8',2);

	    TTFprint();
	}
	if(!empty($export)) $expor_excel->GeraArquivo();

// ------------------------------------------------------------------------------------------------------

		}else{
			print("<br><font size=+1>".$GUI_LANG['NoSuchData']."</font>");
		};

?>