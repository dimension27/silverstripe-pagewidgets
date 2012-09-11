<% require themedCSS(PageWidgets) %>
<div class="gridCn top">
	<% control GridWidgetRows(top) %>
		<div class="gridRow $CSSClasses">
		<% control Widgets %>
			$Widget
		<% end_control %>
		</div>
	<% end_control %>
</div>
