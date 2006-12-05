<?
if(empty($sortBy)) $sortBy="1";

// Формируем запрос к БД
// -----------------------------------------------------------------------------------------------------


// Временная таблица для вычисления внутрегородских звонков
// ------------------------------------------------------------------------------------------------------

// City table
$qA1="
CREATE TEMPORARY TABLE tmp_A (
co smallint NULL default '0',
duration decimal(65,0) NULL default '0'
)
";

$qA2="  
INSERT INTO tmp_A SELECT calls.co,SUM(duration)
from calls
where ((calls.timeofcall>='".$from_date."')
AND (calls.timeofcall<='".$to_date."')
".$additionalReq."
".VectorOfCall(MathVector(14))."
)
group by calls.co
";

// Временная таблица для вычисления междугородних звонков
// ------------------------------------------------------------------------------------------------------
//

// Trunk table
$qB1="
CREATE TEMPORARY TABLE tmp_B (
co smallint NULL default '0',
duration decimal(65,0) NULL default '0'
)
";

$qB2="
INSERT INTO tmp_B SELECT calls.co,SUM(duration)
from calls
where ((calls.timeofcall>='".$from_date."')
AND (calls.timeofcall<='".$to_date."')
".$additionalReq."
".VectorOfCall(MathVector(13))."
)
group by calls.co
";

// Временная таблица для вычисления звонков сотовой связи
// ------------------------------------------------------------------------------------------------------

// Mobile table
$qC1="
CREATE TEMPORARY TABLE tmp_C (
co smallint NULL default '0',
duration decimal(65,0) NULL default '0'
)
";

$qC2="
INSERT INTO tmp_C SELECT calls.co,SUM(duration)
from calls
where ((calls.timeofcall>='".$from_date."')
AND (calls.timeofcall<='".$to_date."')
".$additionalReq."
".VectorOfCall(MathVector(11))."
)
group by calls.co
";

// Временная таблица для вычисления международных звонков
// ------------------------------------------------------------------------------------------------------

// Long distance calls table
$qD1="
CREATE TEMPORARY TABLE tmp_D (
co smallint NULL default '0',
duration decimal(65,0) NULL default '0'
)
";

$qD2="
INSERT INTO tmp_D SELECT calls.co,SUM(duration)
from calls
where ((calls.timeofcall>='".$from_date."')
AND (calls.timeofcall<='".$to_date."')
".$additionalReq."
".VectorOfCall(MathVector(7))."
)
group by calls.co
";

// Поготовим подзапрос для выяснения общего количества страниц 
// ------------------------------------------------------------------------------------------------------
//

// For limits
$qP1="
CREATE TEMPORARY TABLE tmp_P (
 internally smallint NULL default '0',
 duration decimal(65,0) NULL default '0'
)
";

$qP2="
INSERT INTO tmp_P
SELECT calls.co,COUNT(co)
FROM calls
where ((calls.timeofcall>='".$from_date."')
AND (calls.timeofcall<='".$to_date."')
".$additionalReq."
".$vectorReq."

)
GROUP BY calls.co";

$qP="
SELECT COUNT(*) FROM tmp_P
";


$conn->Execute($qA1);
if(empty($CityLine)) $conn->Execute($qA2);

$conn->Execute($qB1);
if(empty($TrunkLine)) $conn->Execute($qB2);

$conn->Execute($qC1);
if(empty($MobLine)) $conn->Execute($qC2);

$conn->Execute($qD1);
if(empty($NationalLine)) $conn->Execute($qD2);

$conn->Execute($qP1);
$conn->Execute($qP2);

// Выполним подзапрос для выяснения общего количества страниц
// ----------------------------------------------------------------------------
$limitsP = getLimits($qP);
// ----------------------------------------------------------------------------

// Выборка из временных таблиц
// ------------------------------------------------------------------------------------------------------
//
$q="SELECT calls.co,extlines.name,COUNT(*),
tmp_A.duration,tmp_B.duration,tmp_C.duration,tmp_D.duration
from calls
LEFT JOIN extlines ON calls.co = extlines.line
LEFT JOIN tmp_A ON calls.co=tmp_A.co
LEFT JOIN tmp_B ON calls.co=tmp_B.co
LEFT JOIN tmp_C ON calls.co=tmp_C.co
LEFT JOIN tmp_D ON calls.co=tmp_D.co
where (calls.timeofcall>='".$from_date."'
AND (calls.timeofcall<='".$to_date."')
".$additionalReq."
".$vectorReq."
)
group by calls.co,extlines.name,tmp_A.duration,tmp_B.duration,tmp_C.duration,tmp_D.duration
ORDER BY ".$sortBy." ".$order.$limitsP;

// Выполним основной запрос используя заранее подготовленный LIMIT
if($cacheflush) $res = $conn->CacheFlush($q);
$res = $conn->CacheExecute($q);

$qDrop="DROP TABLE tmp_A,tmp_B,tmp_C,tmp_D,tmp_P";

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
	AddTableHeader("1",$GUI_LANG['ExternalLine'],$toprint);
	echo ("</td>");

	echo ("<td>");
	AddTableHeader("3",$GUI_LANG['QuantityOfCalls'],$toprint);
	echo ("</td>");

	if(empty($CityLine)){
	    echo ("<td>");
	    AddTableHeader("4",$GUI_LANG['DurationOfCityCalls'],$toprint);
	    echo("</td>");
	}

	if(empty($TrunkLine)){
	    echo("<td>");
	    AddTableHeader("5",$GUI_LANG['DurationOfTrunkCalls'],$toprint);
	    echo("</td>");
	}
	if(empty($MobLine)){
	    echo("<td>");
	    AddTableHeader("6",$GUI_LANG['DurationOfCellularCalls'],$toprint);
	    echo("</td>");
	}

	if(empty($NationalLine)){
	    echo("<td>");
	    AddTableHeader("7",$GUI_LANG['DurationOfLongDistanceCalls'],$toprint);
	    echo("</td>");
	}

	echo("</tr>");
    }else{
	$expor_excel->MontaConteudo(0, 0,$GUI_LANG['ExternalLine']);
	$expor_excel->MontaConteudo(0, 1,$GUI_LANG['QuantityOfCalls']);
	if(empty($CityLine)){
	    $expor_excel->MontaConteudo(0, 2,$GUI_LANG['DurationOfCityCalls']);
	}
	if(empty($TrunkLine)){
	    $expor_excel->MontaConteudo(0, 3,$GUI_LANG['DurationOfTrunkCalls']);
	}
	if(empty($MobLine)){
	    $expor_excel->MontaConteudo(0, 4,$GUI_LANG['DurationOfCellularCalls']);
	}
	if(empty($NationalLine)){
	    $expor_excel->MontaConteudo(0, 5,$GUI_LANG['DurationOfLongDistanceCalls']);
	}
	$expor_excel->MontaConteudo(1, 0, "   ");
	$expor_excel->MontaConteudo(1, 1, "   ");
	$expor_excel->mid_sqlparaexcel();
    }

		$anyDigit=1;
		$linha=0;
		while ($row = $res->FetchRow()) {
			if(!empty($row[1])) {
			    $intPhoneDescription="($row[1])";
			}else{
			    $intPhoneDescription="";
			}
			if(empty($export)){
			    echo ("<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over');\" onmouseout=\"setPointer(this, $anyDigit, 'out');\" onmousedown=\"setPointer(this, $anyDigit, 'click');\">
				    <td nowrap>
					<a href=\"".complitLink($local_co="$row[0]",$local_type="CoDetail")."\" title=\"".$GUI_LANG['InDetails'].". ".$GUI_LANG['CallsThroughAnExternalLine']." $row[0] $intPhoneDescription.\">$row[0]</a>
					$intPhoneDescription
				    </td>
				    <td>
				        <a href=\"".complitLink($local_co="$row[0]",$local_type="CoNum")."\" title=\"".$GUI_LANG['NumbersThroughAnExternalLine']." $row[0] $intPhoneDescription\">$row[2]</a>
				    </td>");
			}else{
			    $expor_excel->MontaConteudo($linha+2, 0, $row[0]." ".$intPhoneDescription);
			    $expor_excel->MontaConteudo($linha+2, 1, $row[2]);
			}
			if(empty($CityLine)){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[3],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 2, sumTotal($row[3],0));
			    }
			}
			if(empty($TrunkLine)){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[4],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 3, sumTotal($row[4],0));
			    }
			}
			if(empty($MobLine)){
			    if(empty($export)){
				echo ("<td>".sumTotal($row[5],0)."</td>");
			    }else{
				$expor_excel->MontaConteudo($linha+2, 4, sumTotal($row[5],0));
			    }
			}
			if(empty($NationalLine)){
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
	echo ("<tr ".$COLORS['AltogetherTrBgcolor']."><td>".$GUI_LANG['Total'].": <b>".$InAll[0]."</b></td>");
	echo ("<td nowrap><b>".$InAll[2]."</b></td>");
	if(empty($CityLine))echo ("<td nowrap>".sumTotal($InAll[3],1)."</td>");
	if(empty($TrunkLine))echo ("<td nowrap>".sumTotal($InAll[4],1)."</td>");
	if(empty($MobLine))echo ("<td nowrap>".sumTotal($InAll[5],1)."</td>");
	if(empty($NationalLine))echo ("<td nowrap>".sumTotal($InAll[6],1)."</td>");
	print ("</tr>\n");
	if($pages > 1 or $debug){
	    echo ("<tr ".$COLORS['TotalTrBgcolor']."><td>".$GUI_LANG['Altogether'].":</td>");
	    echo ("<td nowrap><b>".totalTableFooter('4',1)."</b></td>");
	    if(empty($CityLine))echo ("<td nowrap>".sumTotal(totalTableFooter('5',1),1)."</td>");
	    if(empty($TrunkLine))echo ("<td nowrap>".sumTotal(totalTableFooter('6',1),1)."</td>");
	    if(empty($MobLine))echo ("<td nowrap>".sumTotal(totalTableFooter('7',1),1)."</td>");
	    if(empty($NationalLine))echo ("<td nowrap>".sumTotal(totalTableFooter('8',1),1)."</td>");
	    print ("</tr>\n");
	}
	print ("</table>\n\n </td></tr></table>");
    }else{
	$expor_excel->MontaConteudo($linha+3, 0, $GUI_LANG['Total'].": ".$InAll[0]);
	$expor_excel->MontaConteudo($linha+3, 1, $InAll[2]);
	if(empty($CityLine)) $expor_excel->MontaConteudo($linha+3, 2, sumTotal($InAll[3],0));
	if(empty($TrunkLine)) $expor_excel->MontaConteudo($linha+3, 3, sumTotal($InAll[4],0));
	if(empty($MobLine)) $expor_excel->MontaConteudo($linha+3, 4, sumTotal($InAll[5],0));
	if(empty($NationalLine)) $expor_excel->MontaConteudo($linha+3, 5, sumTotal($InAll[6],0));

	if($pages > 1 or $debug){
	    $expor_excel->MontaConteudo($linha+4, 0, $GUI_LANG['Altogether'].":");
	    $expor_excel->MontaConteudo($linha+4, 1, totalTableFooter('4',1));
	    if(empty($CityLine)) $expor_excel->MontaConteudo($linha+4, 2, sumTotal(totalTableFooter('5',1),0));
	    if(empty($TrunkLine)) $expor_excel->MontaConteudo($linha+4, 3, sumTotal(totalTableFooter('6',1),0));
	    if(empty($MobLine)) $expor_excel->MontaConteudo($linha+4, 4, sumTotal(totalTableFooter('7',1),0));
	    if(empty($NationalLine)) $expor_excel->MontaConteudo($linha+4, 5, sumTotal(totalTableFooter('8',1),0));
	}
    }

    if(!empty($export)) $expor_excel->GeraArquivo();

// ------------------------------------------------------------------------------------------------------

}else{
	echo "<font size=+1>".$GUI_LANG['NoSuchData']."</font>";
}
?>