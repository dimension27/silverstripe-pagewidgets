<div class="gridCell $WidgetCSSClasses">
	<div class="carousel-inner">
		<% control Items %>
			<div class="item<% if First %> active<% end_if %>"
				<% if Lightbox %>
					<a href="#read-more-journal-$iteratorPos" class="lightbox hash-only">
				<% end_if %>
				$SizedImage
				<% if Lightbox %>
					</a>
				<% end_if %>	
			</div>
		<% end_control %>
	</div>
</div>