<?
		if(empty($sortBy))	$sortBy="1";
		if($num != "")		$additionalReq.=" AND (calls.Number = '".$num."')";

// ��������� ��� ��������� ������ ���������� �������
// ----------------------------------------------------------------------------
		$qP="SELECT COUNT(*) from calls where ((calls.TimeOfCall>='".$from_date."') AND (calls.TimeOfCall<='".$to_date."') ".$additionalReq.")";
		$limitsP = getLimits($qP);
		$q="SELECT calls.TimeOfCall,calls.Number,calls.Duration,calls.Way,co.Name,internally.Name,calls.CO,calls.Internally from calls LEFT JOIN co ON calls.CO = co.CO LEFT JOIN internally ON calls.Internally = internally.Internally where ((calls.TimeOfCall>='".$from_date."') AND (calls.TimeOfCall<='".$to_date."') ".$additionalReq.") ORDER BY ".$sortBy." ".$order.$limitsP;
// ----------------------------------------------------------------------------
		if($debug) echo $q."<br>";
		if($cacheflush) $res = $conn->CacheFlush($q);
		$res = $conn->CacheExecute($q);

// ���������� �� �������� ��� �������� ��������� ������ �������
// ----------------------------------------------------------------------------
		if(!empty($res->fields[4])) $NamedLine = $res->fields[4];
		if(!empty($res->fields[5])) $telephone = $res->fields[5];
		if(empty($export)) pechat_ishodnyh();

// ���� �� ������� ������� ������ � ������� 
// ----------------------------------------------------------------------------
		if ($res && $res->RecordCount() > 0) { 
		    if(empty($export)){
			print("<table cellspacing=0 cellpadding=1 border=0><tr ".$COLORS['BaseBorderTableBgcolor']."><td><table cellspacing=1 cellpadding=4 border=0>");
			print ("<tr ".$COLORS['BaseTablesBgcolor']." align=center>
			<td>");
			AddTableHeader("1",$GUI_LANG['DateOfCall'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("7",$GUI_LANG['ExternalLine'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("8",$GUI_LANG['InternalPhone'],$toprint);
			echo("</td>
			<td>");
			AddTableHeader("3",$GUI_LANG['Duration'],$toprint);
			echo("</td>
			</tr>");
		    }else{                                                             
		        $expor_excel->MontaConteudo(0, 0,$GUI_LANG['DateOfCall']);
			$expor_excel->MontaConteudo(0, 1,$GUI_LANG['ExternalLine']);
			$expor_excel->MontaConteudo(0, 2,$GUI_LANG['InternalPhone']);
			$expor_excel->MontaConteudo(0, 3,$GUI_LANG['Duration']);
			$expor_excel->MontaConteudo(1, 0, "   ");
			$expor_excel->mid_sqlparaexcel();
		    }
			
			$anyDigit=1;
			$linha=0;
			while (!$res->EOF) {
				$row = $res->fields;
				if($row[1] == "0"){
					if($row[3] == "1"){
							// ����� �������� ����������� � ������ � ����������� ��
							// ����, ����� ��� ��� ��� ���.
							$row[1] = $IfAON;
					}
				}
				if(!empty($row[4]) && !$noAbonents) {
                            	    $NamedLine=" ($row[4])";
                                }else{
                            	    $NamedLine="";
	                        }
				if(!empty($row[5]) && !$noAbonents) {
                            	    $telephone=" ($row[5])";
                                }else{
                            	    $telephone="";
	                        }

				if(empty($export)){
				    echo"<tr ".$COLORS['BaseTrBgcolor']." onmouseover=\"setPointer(this, $anyDigit, 'over', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmouseout=\"setPointer(this, $anyDigit, 'out', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\" onmousedown=\"setPointer(this, $anyDigit, 'click', '".$COLORS['TrOnmouseOne']."', '".$COLORS['TrOnmouseTwo']."', '".$COLORS['TrOnmouseThree']."');\">";
				    echo"<td nowrap>$row[0]</td>";
				    echo"<td>$row[6] $NamedLine</td>";
				    echo"<td>$row[7] $telephone</td>";
				    echo"<TD>".sumTotal($row[2],0)."</TD>";
				    echo "</TR>\n";
				}else{
				    $expor_excel->MontaConteudo($linha+2, 0, $row[0]);
				    $expor_excel->MontaConteudo($linha+2, 1, $row[6]." ".$NamedLine);
				    $expor_excel->MontaConteudo($linha+2, 2, $row[7]." ".$telephone);
				    $expor_excel->MontaConteudo($linha+2, 3, sumTotal($row[2],0));
				}
				array($InAll);                           
				if(!empty($row[0])) $InAll[0] ++;        
				if(!empty($row[2])) $InAll[2] += $row[2];
				
				$res->MoveNext();
				$anyDigit++;
				$linha++;
			};
// ������� totalTableFooter() ������� �������� ��������. � �������� ��������� ��������� ����� SQL �������
// ������� sumTotal() ����������� ����� ������ � ���������� �����,����� � ������.
// ------------------------------------------------------------------------------------------------------

	if(empty($export)){
	    echo("<tr ".$COLORS['AltogetherTrBgcolor']."><td>".$GUI_LANG['Altogether'].": <b>".$InAll[0]."</b></td>");
    	    echo("<td colspan=3>");
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


		}
		else
		{
			print("<br><font size=+1>".$GUI_LANG['NoSuchData']."</font>");
		};

?>