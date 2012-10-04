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
		    	<div class="overflow-hidden">
			    	<h3>$Title</h3>
	<% if Lightbox %>
					$Body
				</div>
				<% include WidgetLightBox %>
	<% else %>
					$Body
				</div>
				<% include WidgetLink %>
	<% end_if %>
				<a href="$LinkURL" class="arrow"></a>
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