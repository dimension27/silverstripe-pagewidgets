<div class="gridCell $CSSClasses">
	<% if Header %>
	$Header
	<% end_if %>
	<% if Items.MoreThanOnePage %>
		<% include MultiTeaserBlockPagination %>
	<% end_if %>
	<ul>
		<% control Items %>
		    <li><h3>$Title</h3>
				$Body
				<% include WidgetLink %>
			</li>
		<% end_control %>
	</ul>
	<% if Items.MoreThanOnePage %>
		<% include MultiTeaserBlockPagination %>
	<% end_if %>
</div>