<?
if(empty($sortBy)) $sortBy="1";
$CI="Internally";
$Ci="Internally";
$ci="internally";

// Формируем запрос к БД
// -----------------------------------------------------------------------------------------------------


// Временная таблица для вычисления внутрегородских звонков
// ------------------------------------------------------------------------------------------------------
$qA1="
CREATE TEMPORARY TABLE tmp${Ci}_A (
${CI} smallint(6) unsigned NOT NULL default '0',
Duration decimal(100,0) unsigned NOT NULL default '0',
KEY ${CI} (${CI}),
KEY Duration (Duration)
) COMMENT='City'
";

$qA2="  
INSERT INTO tmp${Ci}_A SELECT calls.${CI},SUM(Duration)
from calls
where ((calls.TimeOfCall>='".$from_date."')
AND (calls.TimeOfCall<='".$to_date."')
AND (calls.Number REGEXP '".$LocalCalls."')
".$additionalReq."
)
group by calls.${CI}
";

// Временная таблица для вычисления междугородних звонков
// ------------------------------------------------------------------------------------------------------
//
$qB1="
CREATE TEMPORARY TABLE tmp${Ci}_B (
${CI} smallint(6) unsigned NOT NULL default '0',
Duration decimal(100,0) unsigned NOT NULL default '0',
KEY ${CI} (${CI}),
KEY Duration (Duration)
) COMMENT='Trunk'
";

$qB2="
INSERT INTO tmp${Ci}_B SELECT calls.${CI},SUM(Duration)
from calls
where ((calls.TimeOfCall>='".$from_date."')
AND (calls.TimeOfCall<='".$to_date."')
AND (calls.Number REGEXP '".$LongDistanceCalls."' AND calls.Number NOT REGEXP '".$MobileCallsR."')
".$additionalReq."
)
group by calls.${CI}
";

// Временная таблица для вычисления звонков сотовой связи
// ------------------------------------------------------------------------------------------------------
$qC1="
CREATE TEMPORARY TABLE tmp${Ci}_C (
${CI} smallint(6) unsigned NOT NULL default '0',
Duration decimal(100,0) unsigned NOT NULL default '0',
KEY ${CI} (${CI}),
KEY Duration (Duration)
) COMMENT='Mobile'
";

$qC2="
INSERT INTO tmp${Ci}_C SELECT calls.${CI},SUM(Duration)
from calls
where ((calls.TimeOfCall>='".$from_date."')
AND (calls.TimeOfCall<='".$to_date."')
AND (calls.Number REGEXP '".$MobileCallsR."')
".$additionalReq."
)
group by calls.${CI}
";

// Временная таблица для вычисления международных звонков
// ------------------------------------------------------------------------------------------------------
$qD1="
CREATE TEMPORARY TABLE tmp${Ci}_D (
${CI} smallint(6) unsigned NOT NULL default '0',
Duration decimal(100,0) unsigned NOT NULL default '0',
KEY ${CI} (${CI}),
KEY Duration (Duration)
) COMMENT='Long distance'
";

$qD2="
INSERT INTO tmp${Ci}_D SELECT calls.${CI},SUM(Duration)
from calls
where ((calls.TimeOfCall>='".$from_date."')
AND (calls.TimeOfCall<='".$to_date."')
AND (calls.Number REGEXP '".$InternationalCalls."')
".$additionalReq."
)
group by calls.${CI}
";

// Поготовим подзапрос для выяснения общего количества страниц 
// ------------------------------------------------------------------------------------------------------
//
$qP1="
CREATE TEMPORARY TABLE tmp${Ci}_P (
 Internally smallint(6) unsigned NOT NULL default '0',
 Duration decimal(100,0) unsigned NOT NULL default '0',
 KEY Internally (Internally),
 KEY Duration (Duration)
) COMMENT='For limits'
";

$qP2="
INSERT INTO tmp${Ci}_P
SELECT calls.${Ci},COUNT(${Ci})
FROM calls
where ((calls.TimeOfCall>='".$from_date."')
AND (calls.TimeOfCall<='".$to_date."')
".$additionalReq."
)
GROUP BY calls.${CI}";

$qP="
SELECT COUNT(*) FROM tmp${Ci}_P
";



$conn->Execute($qA1);
if($CityOnly!=2){
    $conn->Execute($qA2);
}

$conn->Execute($qB1);
if($CityOnly!=1){
    $conn->Execute($qB2);
}

$conn->Execute($qC1);
if(empty($noMobLine) && $CityOnly!=1){
    $conn->Execute($qC2);
}

$conn->Execute($qD1);
if(empty($noNationalLine) && $CityOnly!=1){
    $conn->Execute($qD2);
}

$conn->Execute($qP1);
$conn->Execute($qP2);

// Выполним подзапрос для выяснения общего количества страниц
// ----------------------------------------------------------------------------
$limitsP = getLimits($qP);
// ----------------------------------------------------------------------------

// Выборка из временных таблиц
// ------------------------------------------------------------------------------------------------------
//
$q="SELECT calls.${CI},${ci}.Name,COUNT(*),
tmp${Ci}_A.Duration,tmp${Ci}_B.Duration,tmp${Ci}_C.Duration,tmp${Ci}_D.Duration
from calls
LEFT JOIN ${ci} ON calls.${CI} = ${ci}.${CI}
LEFT JOIN tmp${Ci}_A ON calls.${CI}=tmp${Ci}_A.${CI}
LEFT JOIN tmp${Ci}_B ON calls.${CI}=tmp${Ci}_B.${CI}
LEFT JOIN tmp${Ci}_C ON calls.${CI}=tmp${Ci}_C.${CI}
LEFT JOIN tmp${Ci}_D ON calls.${CI}=tmp${Ci}_D.${CI}
where (calls.TimeOfCall>='".$from_date."'
AND (calls.TimeOfCall<='".$to_date."')
".$additionalReq."
)
group by calls.${CI}
ORDER BY ".$sortBy." ".$order.$limitsP;

// Выполним основной запрос използуя заранее подготовленный LIMIT
if($cacheflush) $res = $conn->CacheFlush($q);
$res = $conn->CacheExecute($q);

$qDrop="DROP TABLE tmp${Ci}_A,tmp${Ci}_B,tmp${Ci}_C,tmp${Ci}_D,tmp${Ci}_P";

$qDebug="$qA1;<br><br>$qA2;<br><br><br>$qB1;<br><br>$qB2;<br><br><br>$qC1;<br><br>$qC2;<br><br><br>$qD1;<br><br>$qD2;<br><br><br>$qP1;<br><br>$qP2;<br><br><br>$q;<br><br><br>$qDrop;";

if($debug) echo $qDebug."<br>";

$conn->Execute($qDrop);

// Напечатаем на странице все исходные параметры нашего запроса
// ----------------------------------------------------------------------------

if(empty($export)) pechat_ishodnyh();


// если по запросу найдены записи в таблице 
// ----------------------------------------------------------------------------
if ($res && $res->RecordCount() > 0) { 
    if(empty($export)){
	echo("<table cellspacing=0 cellpadding=1 border=0>
			<tr ".$COLORS['BaseBorderTableBgcolor'].">
				<td>
					<table cellspacing=1 cellpadding=4 border=0>");
	echo ("<tr ".$COLORS['BaseTablesBgcolor']." align=center valign=top>
	<td nowrap>");
	AddTableHeader("1",$GUI_LANG['InternalPhone'],$toprint);
	echo ("</td>");

	echo ("<td>");
	AddTableHeader("3",$GUI_LANG['QuantityOfCalls'],$toprint);
	echo ("</td>");

	if($CityOnly!=2){
	    echo ("<td>");
	    AddTableHeader("4",$GUI_LANG['DurationOfCityCalls'],$toprint);
	    echo("</td>");
	}

	if($CityOnly!=1){
	    echo("<td>");
	    AddTableHeader("5",$GUI_LANG['DurationOfTrunkCalls'],$toprint);
	    echo("</td>");
	}
	if(empty($noMobLine) && $CityOnly!=1){
	    echo("<td>");
	    AddTableHeader("6",$GUI_LANG['DurationOfCellularCalls'],$toprint);
	    echo("</td>");
	}

	if(empty($noNationalLine) && $CityOnly!=1){
	    echo("<td>");
	    AddTableHeader("7",$GUI_LANG['DurationOfLongDistanceCalls'],$toprint);
	    echo("</td>");
	}

	echo("</tr>");
    }else{
	$expor_excel->MontaConteudo(0, 0,$GUI_LANG['InternalPhone']);
	$expor_excel->MontaConteudo(0, 1,$GUI_LANG['QuantityOfCalls']);
	if($CityOnly!=2){
	    $expor_excel->MontaConteudo(0, 2,$GUI_LANG['DurationOfCityCalls']);
	}
	if($CityOnly!=1){
	    $expor_excel->MontaConteudo(0, 3,$GUI_LANG['DurationOfTrunkCalls']);
	}
	if(empty($noMobLine) && $CityOnly!=1){
	    $expor_excel->MontaConteudo(0, 4,$GUI_LANG['DurationOfCellularCalls']);
	}
	if(empty($noNationalLine) && $CityOnly!=1){
	    $expor_excel->MontaConteudo(0, 5,$GUI_LANG['DurationOfLongDistanceCalls']);
	}
	$expor_excel->MontaConteudo(1, 0, "   ");
	$expor_excel->MontaConteudo(1, 1, "   ");
	$expor_excel->mid_sqlparaexcel();
    }

		$anyDigit=1;
		$linha=0;
		while ($row = $res->FetchRow()) {
			if(!empty($row[1]) && !$noAbonents) {
			    $telephone="($row[1])";
			}else{
			    $telephone="";
			}
			if(empty($export)){
			    echo ("<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmouseout=\"setPointer(this, $anyDigit, 'out', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmousedown=\"setPointer(this, $anyDigit, 'click', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\">
				    <td nowrap>
					<a href=\"".complitLink($local_int="$row[0]",$local_type="IntDetail")."\" title=\"".$GUI_LANG['InDetails'].". ".$GUI_LANG['CallsFromInternalPhone']." $row[0] $telephone\">$row[0]</a>
					$telephone
				    </td>
				    <td>
					<a href=\"".complitLink($local_int="$row[0]",$local_type="IntNum")."\" title=\"".$GUI_LANG['NumbersFromInternalPhone']." $row[0] $telephone\">$row[2]</a>
				    </td>");
			}else{
			    $expor_excel->MontaConteudo($linha+2, 0, $row[0]." ".$telephone);
			    $expor_excel->MontaConteudo($linha+2, 1, $row[2]);
			}
			if($CityOnly!=2){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[3],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 2, sumTotal($row[3],0));
			    }
			}
			if($CityOnly!=1){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[4],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 3, sumTotal($row[4],0));
			    }
			}
			if(empty($noMobLine) && $CityOnly!=1){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[5],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 4, sumTotal($row[5],0));
			    }
			}
			if(empty($noNationalLine) && $CityOnly!=1){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[6],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 5, sumTotal($row[6],0));
			    }
			}
		    if(empty($export)){
    			echo ("</tr>\n");
		    }

		    array($InAll);
		    if(!empty($row[0])) $InAll[0] ++;
		    if(!empty($row[2])) $InAll[2] += $row[2];
		    if(!empty($row[3])) $InAll[3] += $row[3];
		    if(!empty($row[4])) $InAll[4] += $row[4];
		    if(!empty($row[5])) $InAll[5] += $row[5];
		    if(!empty($row[6])) $InAll[6] += $row[6];

		    $linha++;
		    $anyDigit++;
		}

// Функция totalTableFooter() выводит итоговые сведения. В качестве аргумента выступает номер SQL запроса
// Функция sumTotal() преобразует число секунд в количество часов,минут и секунд.
// ------------------------------------------------------------------------------------------------------

    if(empty($export)){
	echo ("<tr ".$COLORS['AltogetherTrBgcolor']."><td>".$GUI_LANG['Altogether'].": <b>".$InAll[0]."</b></td>");
	echo ("<td nowrap><b>".$InAll[2]."</b></td>");
	if($CityOnly!=2)echo ("<td nowrap>".sumTotal($InAll[3],1)."</td>");
	if($CityOnly!=1)echo ("<td nowrap>".sumTotal($InAll[4],1)."</td>");
	if(empty($noMobLine) && $CityOnly!=1)echo ("<td nowrap>".sumTotal($InAll[5],1)."</td>");
	if(empty($noNationalLine) && $CityOnly!=1)echo ("<td nowrap>".sumTotal($InAll[6],1)."</td>");
	print ("</tr>\n");
	echo ("<tr ".$COLORS['TotalTrBgcolor']."><td>".$GUI_LANG['Total'].":</td>");
	echo ("<td nowrap><b>".totalTableFooter('4',1)."</b></td>");
	if($CityOnly!=2)echo ("<td nowrap>".sumTotal(totalTableFooter('5',1),1)."</td>");
	if($CityOnly!=1)echo ("<td nowrap>".sumTotal(totalTableFooter('6',1),1)."</td>");
	if(empty($noMobLine) && $CityOnly!=1)echo ("<td nowrap>".sumTotal(totalTableFooter('7',1),1)."</td>");
	if(empty($noNationalLine) && $CityOnly!=1)echo ("<td nowrap>".sumTotal(totalTableFooter('8',1),1)."</td>");
	print ("</tr>\n");
	print ("</table>\n\n </td></tr></table>");
    }else{
	$expor_excel->MontaConteudo($linha+3, 0, $GUI_LANG['Altogether'].": ".$InAll[0]);
	$expor_excel->MontaConteudo($linha+3, 1, $InAll[2]);
	if($CityOnly!=2) $expor_excel->MontaConteudo($linha+3, 2, sumTotal($InAll[3],0));
	if($CityOnly!=1) $expor_excel->MontaConteudo($linha+3, 3, sumTotal($InAll[4],0));
	if(empty($noMobLine) && $CityOnly!=1) $expor_excel->MontaConteudo($linha+3, 4, sumTotal($InAll[5],0));
	if(empty($noNationalLine) && $CityOnly!=1) $expor_excel->MontaConteudo($linha+3, 5, sumTotal($InAll[6],0));

	$expor_excel->MontaConteudo($linha+4, 0, $GUI_LANG['Total'].":");
	$expor_excel->MontaConteudo($linha+4, 1, totalTableFooter('4',1));
	if($CityOnly!=2) $expor_excel->MontaConteudo($linha+4, 2, sumTotal(totalTableFooter('5',1),0));
	if($CityOnly!=1) $expor_excel->MontaConteudo($linha+4, 3, sumTotal(totalTableFooter('6',1),0));
	if(empty($noMobLine) && $CityOnly!=1) $expor_excel->MontaConteudo($linha+4, 4, sumTotal(totalTableFooter('7',1),0));
	if(empty($noNationalLine) && $CityOnly!=1) $expor_excel->MontaConteudo($linha+4, 5, sumTotal(totalTableFooter('8',1),0));
    }

    if(!empty($export)) $expor_excel->GeraArquivo();

// ------------------------------------------------------------------------------------------------------

}else{
	echo "<font size=+1>".$GUI_LANG['NoSuchData']."</font>";
}
?>