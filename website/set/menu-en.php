<?php
echo ("
<table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr>
    <td align=left><a href=\"http://www.atslog.dp.ua/\" title=\"Анализ и учет звонков различных моделей мини-АТС\"><img src=\"/img/atslog.logo.gif\" width=223 height=84 border=0 alt=\"Анализ и учет звонков различных моделей мини-АТС\"></a></td>
    <td valign=\"top\">
		<table cellspacing=\"5\" cellpadding=\"5\" border=\"0\">
		<tr valign=\"top\">
			<td nowrap><a href=\"/en/about/\" title=\"features, model, todo\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;about</a></td>
			<td nowrap><a href=\"/en/news/\" title=\"Latest ATSlog news\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;news</a></td>
			<td nowrap><a href=\"/en/autor/\" title=\"ATSlog developers and contributors\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;authors</a></td>
			<td nowrap><a href=\"/en/search/\" title=\"site search\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;search</a></td>
		</tr>
		<tr valign=\"top\">
			<td nowrap><a href=\"/en/get/\" title=\"All program versions\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;download</a></td>
			<td nowrap><a href=\"/en/faq/\" title=\"Frequently Asked Questions for ATSlog\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;faq</a></td>
			<td nowrap><a href=\"/en/links/\" title=\"Related resources\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;links</a></td>
			<td nowrap><a href=\"/forum/\" title=\"forum about ATSlog and not only\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;forum</a></td>
		</tr>
		<tr valign=\"top\">
		    <td><a href=\"/en/screenshots/\" title=\"live demo\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;demo</a></td>
			<td nowrap>
");

if (isset($menu) && $menu=="doc")
{
	echo("
			<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td nowrap valign=bottom><img src=\"/img/menu.arrowhead.right.gif\" width=6 height=10 hspace=0 vspace=0 border=0  alt=\"\"></td>
					<td nowrap>&nbsp;<a href=\"/en/doc/\" title=\"Documentation\">документация</a></td>
				</tr>
				<tr>
					<td nowrap><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/en/doc/installing/\" title=\"Монтирование АТС и подключение к компьютеру\">монтирование и подключение</a></td>
				</tr>
				<tr>
					<td nowrap><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/en/doc/solder-up/\" title=\"Схемы распайки кабеля для подключения АТС к компьютеру\">схемы распайки кабеля</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/en/doc/setup/\" title=\"установка и настройка программы ATSlog\">установка и настройка программы</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/en/doc/updating/\" title=\"обновление с предыдущих версий программы\">обновление предыдущих версий</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/en/doc/using/\" title=\"использование ATSlog\">использование ATSlog</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsLeftCorner.gif\" width=8 height=12 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/en/doc/deleting/\" title=\"удаление ATSlog\">удаление программы</a></td>
				</tr>
			</table>
");
}else{
echo("
				<a href=\"/en/doc/\" title=\"ATSlog Documentation\"><img src=\"/img/menu.arrowhead.right.gif\" width=6 height=10 hspace=0 vspace=0 border=0  alt=\"\">&nbsp;documentation</a>
");
}

echo ("
			</td>
			<td nowrap><a href=\"/map/\" title=\"map of the site\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;site map</a></td>
			<td nowrap><a href=\"/about/\" title=\"site in russian\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;russian</a></td>
		</tr>
		</table>
	</td>
</tr>
</table>

");

?>
