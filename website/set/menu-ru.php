<?php
echo ("
<table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr>
    <td align=left><a href=\"http://www.atslog.dp.ua/\" title=\"������ � ���� ������� ��������� ������� ����-���\"><img src=\"/img/atslog.logo.gif\" width=223 height=84 border=0 alt=\"������ � ���� ������� ��������� ������� ����-���\"></a></td>
    <td valign=\"top\">
		<table cellspacing=\"5\" cellpadding=\"5\" border=\"0\">
		<tr valign=\"top\">
			<td nowrap><a href=\"/about/\" title=\"�����������, ������ ������, ����� ��������\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;� ���������</a></td>
			<td nowrap><a href=\"/news/\" title=\"��������� ��������� � ATSlog\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;�������</a></td>
			<td nowrap><a href=\"/autor/\" title=\"������ ���������, ������������� ���������� �������\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;�� �������</a></td>
			<td nowrap><a href=\"/search/\" title=\"����� �� �����\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;�����</a></td>
		</tr>
		<tr valign=\"top\">
			<td nowrap><a href=\"/get/\" title=\"��� ������ ���������\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;�������</a></td>
			<td nowrap><a href=\"/faq/\" title=\"����� ���������� ������� � ������\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;����</a></td>
			<td nowrap><a href=\"/links/\" title=\"c����� �� ������������ �������\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;������</a></td>
			<td nowrap><a href=\"/forum/\" title=\"������ �� �����-������ ����\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;�����</a></td>
		</tr>
		<tr valign=\"top\">
		    <td><a href=\"/screenshots/\" title=\"�������� ������ �������������\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;������������</a></td>
			<td nowrap>
");

if (isset($menu) && $menu=="doc")
{
	echo("
			<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td nowrap valign=bottom><img src=\"/img/menu.arrowhead.right.gif\" width=6 height=10 hspace=0 vspace=0 border=0  alt=\"\"></td>
					<td nowrap>&nbsp;<a href=\"/doc/\" title=\"������������ �� ��������� � ���������\">������������</a></td>
				</tr>
				<tr>
					<td nowrap><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/installing/\" title=\"������������ ��� � ����������� � ����������\">������������ � �����������</a></td>
				</tr>
				<tr>
					<td nowrap><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/solder-up/\" title=\"����� �������� ������ ��� ����������� ��� � ����������\">����� �������� ������</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/setup/\" title=\"��������� � ��������� ��������� ATSlog\">��������� � ��������� ���������</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/updating/\" title=\"���������� � ���������� ������ ���������\">���������� ���������� ������</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/using/\" title=\"������������� ATSlog\">������������� ATSlog</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsLeftCorner.gif\" width=8 height=12 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/deleting/\" title=\"�������� ATSlog\">�������� ���������</a></td>
				</tr>
			</table>
");
}else{
echo("
				<a href=\"/doc/\" title=\"������������ �� ��������� � ���������\"><img src=\"/img/menu.arrowhead.right.gif\" width=6 height=10 hspace=0 vspace=0 border=0  alt=\"\">&nbsp;������������</a>
");
}

echo ("
			</td>
			<td nowrap><a href=\"/map/\" title=\"������� �������� ����������� �����\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;����� �����</a></td>
			<td nowrap><a href=\"/en/about/\" title=\"site in english\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;english</a></td>
		</tr>
		</table>
	</td>
</tr>
</table>

");

?>
