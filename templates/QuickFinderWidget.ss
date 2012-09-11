<div class="gridCell $CSSClasses">
	<div class="bd">
		<div class="bullet"></div><h3>$Title</h3>
		<label>$SearchLabel</label>
		$SearchForm
		<label for="quickLinks">$LinksLabel</label>
		<select onChange="QuickFinderWidget.goToLink(this)" id="quickLinks">
		<% if Items %>
			<option>Please select...</option>
			<% control Items %>
		    <option value="$LinkURL">$LinkLabel</option>
			<% end_control %>
		<% else %>
			<option>(No items)</option>
		<% end_if %>
		</select>
	</div>
</div>
