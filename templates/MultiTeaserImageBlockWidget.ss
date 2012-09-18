<div class="gridCell $WidgetCSSClasses">
	<% if Header %>
	<h2>$Header</h2>
	<% end_if %>
	<% if Items.MoreThanOnePage %>
	<div class="pagination top">
		<% include MultiTeaserBlockPagination %>
	</div>
	<% end_if %>
	<ul>
	<% control Items %>
    <li>
    	<div class="image">
    		<% if Lightbox %>
				<a href="#read-more-journal-$iteratorPos" class="lightbox hash-only">
				<% end_if %>
				$SizedImage
				<% if Lightbox %>
				</a>
				<% end_if %>	
			</div>
    	<div class="bd">
	    	<h3>$Title</h3>
				<% if Layout = FourGridCells %>
				$SizedImage(350, 170)
				<% else %>
				$SizedImage
				<% end_if %>
				<% if Lightbox %>
				$Body
				<% include WidgetLightBox %>
				<% else %>
				$Body
				<% include WidgetLink %>
				<% end_if %>
				<span class="arrow"></span>
			</div>
		</li>
	<% end_control %>
	</ul>
	<% if Items.MoreThanOnePage %>
	<div class="pagination bottom">
		<% include MultiTeaserBlockPagination %>
	</div>
	<% end_if %>
</div>