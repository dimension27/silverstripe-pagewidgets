<div class="gridCell $CSSClasses" id="Carousel$ID">
	<div class="carousel-inner">
		<% control Items %>
			<div class="item<% if First %> active<% end_if %>">
				<% if Lightbox %>
					<a href="#read-more-journal-$iteratorPos" class="lightbox hash-only">
						$SizedImage
					</a>
				<% else %>
					$SizedImage
				<% end_if %>
				<div class="bd">
					<% if Lightbox %>
						<div class="overflow-hidden">
							<h3>$Title</h3>
							$Body
						</div>
						<% include WidgetLightBox %>
					<% else %>
						<div class="overflow-hidden">
							<h3>$Title</h3>
							$Body
						</div>
						<% include WidgetLink %>
					<% end_if %>
				</div>
			</div>
		<% end_control %>
	</div>
	<a class="carousel-control left" href="#Carousel$ID" data-slide="prev">
		<span class="hide-text">&lsaquo;</span></a>
	<a class="carousel-control right" href="#Carousel$ID" data-slide="next">
		<span class="hide-text">&rsaquo;</span></a>
</div>