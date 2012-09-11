<% require css(pagewidget/css/PageWidgets.css) %>
<% if ContentWidgets %>
<div class="gridCn">
	<% control ContentWidgets %>
		$Widget
	<% end_control %>
</div>
<% end_if %>