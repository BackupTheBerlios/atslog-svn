<?php
echo ("
<table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr>
    <td align=left><a href=\"http://www.atslog.dp.ua/\" title=\"Анализ и учет звонков различных моделей мини-АТС\"><img src=\"/img/atslog.logo.gif\" width=223 height=84 border=0 alt=\"Анализ и учет звонков различных моделей мини-АТС\"></a></td>
    <td valign=\"top\">
		<table cellspacing=\"5\" cellpadding=\"5\" border=\"0\">
		<tr valign=\"top\">
			<td nowrap><a href=\"/about/\" title=\"возможности, модель работы, планы развития\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;о программе</a></td>
			<td nowrap><a href=\"/news/\" title=\"последние изменения в ATSlog\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;новости</a></td>
			<td nowrap><a href=\"/autor/\" title=\"авторы программы, благодарности участникам проекта\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;об авторах</a></td>
			<td nowrap><a href=\"/search/\" title=\"поиск по сайту\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;поиск</a></td>
		</tr>
		<tr valign=\"top\">
			<td nowrap><a href=\"/get/\" title=\"все версии программы\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;скачать</a></td>
			<td nowrap><a href=\"/faq/\" title=\"Часто задаваемые Вопросы и Ответы\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;чаво</a></td>
			<td nowrap><a href=\"/links/\" title=\"cсылки на тематические ресурсы\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;ссылки</a></td>
			<td nowrap><a href=\"/forum/\" title=\"форумы на около-АТСные темы\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"\">&nbsp;форум</a></td>
		</tr>
		<tr valign=\"top\">
		    <td><a href=\"/screenshots/\" title=\"реальный пример использования\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;ознакомиться</a></td>
			<td nowrap>
");

if (isset($menu) && $menu=="doc")
{
	echo("
			<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td nowrap valign=bottom><img src=\"/img/menu.arrowhead.right.gif\" width=6 height=10 hspace=0 vspace=0 border=0  alt=\"\"></td>
					<td nowrap>&nbsp;<a href=\"/doc/\" title=\"документация по установке и настройке\">документация</a></td>
				</tr>
				<tr>
					<td nowrap><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/installing/\" title=\"Монтирование АТС и подключение к компьютеру\">монтирование и подключение</a></td>
				</tr>
				<tr>
					<td nowrap><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/solder-up/\" title=\"Схемы распайки кабеля для подключения АТС к компьютеру\">схемы распайки кабеля</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/setup/\" title=\"установка и настройка программы ATSlog\">установка и настройка программы</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/updating/\" title=\"обновление с предыдущих версий программы\">обновление предыдущих версий</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsCenterCorner.gif\" width=8 height=20 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/using/\" title=\"использование ATSlog\">использование ATSlog</a></td>
				</tr>
				<tr>
					<td nowrap valign=top><img src=\"/img/menu.dotsLeftCorner.gif\" width=8 height=12 hspace=0 vspace=0 border=0 alt=\"\"></td>
					<td nowrap>&nbsp;&nbsp;<a href=\"/doc/deleting/\" title=\"удаление ATSlog\">удаление программы</a></td>
				</tr>
			</table>
");
}else{
echo("
				<a href=\"/doc/\" title=\"документация по установке и настройке\"><img src=\"/img/menu.arrowhead.right.gif\" width=6 height=10 hspace=0 vspace=0 border=0  alt=\"\">&nbsp;документация</a>
");
}

echo ("
			</td>
			<td nowrap><a href=\"/map/\" title=\"быстрый просмотр содержимого сайта\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;карта сайта</a></td>
			<td nowrap><a href=\"/en/about/\" title=\"site in english\"><img src=\"/img/menu.arrowhead.right.gif\" width=\"6\" height=\"10\" hspace=\"0\" vspace=\"0\" border=\"0\"  alt=\"\">&nbsp;english</a></td>
		</tr>
		</table>
	</td>
</tr>
</table>

");

?>
