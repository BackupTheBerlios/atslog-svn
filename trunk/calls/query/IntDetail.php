<?
		if(empty($sortBy))	$sortBy="1";
		if($int != "")		$additionalReq .= " AND (calls.Internally='".$int."')";

// Подзапрос для выяснения общего количества страниц
// ----------------------------------------------------------------------------
		$qP="SELECT COUNT(*) from calls where (calls.TimeOfCall>='".$from_date."' AND (calls.TimeOfCall<='".$to_date."')".$additionalReq.")";
		$limitsP = getLimits($qP);
		$q="SELECT calls.TimeOfCall,calls.CO,calls.Number,calls.Duration,calls.Way,co.Name,internally.Name from calls LEFT JOIN co ON calls.CO = co.CO LEFT JOIN internally ON calls.Internally = internally.Internally where (calls.TimeOfCall>='".$from_date."' AND (calls.TimeOfCall<='".$to_date."')".$additionalReq.") ORDER BY ".$sortBy." ".$order.$limitsP;
// ----------------------------------------------------------------------------
		
		if($debug) echo $q."<br>";
		if($cacheflush) $res = $conn->CacheFlush($q);
		$res = $conn->CacheExecute($q);

// Напечатаем на странице все исходные параметры нашего запроса
// ----------------------------------------------------------------------------
		if(!empty($res->fields[6])) $telephone = $res->fields[6];
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
			AddTableHeader("2",$GUI_LANG['ExternalLine'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("3",$GUI_LANG['Number'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("4",$GUI_LANG['Duration'],$toprint);
			echo("</td>
			</tr>");
		    }else{
			$expor_excel->MontaConteudo(0, 0,$GUI_LANG['DateOfCall']);
			$expor_excel->MontaConteudo(0, 1,$GUI_LANG['ExternalLine']);
			$expor_excel->MontaConteudo(0, 2,$GUI_LANG['Number']);
			$expor_excel->MontaConteudo(0, 3,$GUI_LANG['Duration']);
			$expor_excel->MontaConteudo(1, 0, "   ");
			$expor_excel->mid_sqlparaexcel();
		    }

			$anyDigit=1;
			$linha=0;
			while (!$res->EOF) {
				$row = $res->fields;
				
				if(!empty($row[5])){
				    $NamedLine="($row[5])";
				}else{
				    $NamedLine="";
				}
				
				if($row[2] == "0"){
					if($row[4] == "1"){
							// Будем выводить комментарий к номеру в зависимости от
							// того, имеет АТС АОН или нет.
							$row[2] = $IfAON;
					}
				}

// Разные цвета для внутригородских, междугородних и международных звонков
// ------------------------------------------------------------------------------------------------------
//
				$FontColor='';
				$FontColorEnd='';

				if(empty($toprint)){
				    if($row[4] == "1" && $incoming!="3"){
					$FontColor="<font ".$COLORS['IncomingCalls'].">";
					$FontColorEng="</font>";
				    }elseif(ereg($MobileCallsR, $row[2])){
	                                $FontColor="<font ".$COLORS['MobileCalls'].">";
	                                $FontColorEng="</font>";
                                    }elseif(ereg($InternationalCalls, $row[2])){
					$FontColor="<font ".$COLORS['InternationalCalls'].">";
					$FontColorEng="</font>";
				    }elseif(ereg($LongDistanceCalls, $row[2])){
					$FontColor="<font ".$COLORS['LongDistanceCalls'].">";
					$FontColorEng="</font>";
				    }
				}
				if(empty($export)){
				    echo"<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmouseout=\"setPointer(this, $anyDigit, 'out', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmousedown=\"setPointer(this, $anyDigit, 'click', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\">";
				    echo"<td nowrap>${FontColor}$row[0]${FontColorEnd}</td>";
				    echo"<td><a href=\"".complitLink($local_type="IntCoDetail",$local_co="$row[1]")."\" title=\"".$GUI_LANG['CallsFromInternalPhone']." $telephone, ".$GUI_LANG['ThroughAnExternalLine']." $row[1] $NamedLine\">$row[1]</a> $NamedLine${FontColorEnd}</td>";
				    echo"<td align=right>${FontColor}$row[2]${FontColorEnd}</td>";
				    echo"<td>${FontColor}".sumTotal($row[3],0)."${FontColorEnd}</td>";
				    echo "</TR>\n";
				}else{
				    $expor_excel->MontaConteudo($linha+2, 0, $row[0]);
				    $expor_excel->MontaConteudo($linha+2, 1, $row[1]." ".$NamedLine);
				    $expor_excel->MontaConteudo($linha+2, 2, $row[2]);
				    $expor_excel->MontaConteudo($linha+2, 3, sumTotal($row[3],0));
				}
				array($InAll);
				if(!empty($row[0])) $InAll[0] ++;
				if(!empty($row[3])) $InAll[3] += $row[3];
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
	    if(!empty($InAll[3])) echo $GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[3],1);
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
	    $TTF[3]=$GUI_LANG['GeneralDuration'].": ".sumTotal($InAll[3],2);

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