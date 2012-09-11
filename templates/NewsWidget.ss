<div class="gridCell $CSSClasses">
	<div class="bd">
		<h2>$Heading</h2>
		<% if FeaturedNewsItems %>
		<ul>
		<% control FeaturedNewsItems %>
		<li>
			<a href="$Link">$ShortTeaser</a>
			<div class="date">$Date.Format(M. j Y)</div>
			<a class="readMore internal" href="$Link" title="Read more about &quot;{$Title}&quot;">Read more...</a>
		</li>
		<% end_control %>
		</ul>
		<% include WidgetLink %>
		<% else %>
		<p>There are no news items at this time.</p>
		<% end_if %>
	</div>
</div>