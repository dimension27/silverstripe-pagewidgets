<% if LinkURL %>
<div class="widget-link">
	<a href="$LinkURL" class="$LinkClass" target="$LinkWindowTarget"<% if LinkRel %> rel="$LinkRel"<% end_if %>>$LinkLabel $LinkSuffix</a>
</div>
<% end_if %>