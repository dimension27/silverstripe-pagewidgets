<% require themedCSS(result-set) %>
<ul>
	<% if Items.NotFirstPage %>
		<li class="previousPage"><a href="$Items.PrevLink" title="View the previous page of Items">Previous</a></li>
	<% else %>
		<li class="previousPage">Previous</span></li>
	<% end_if %>
	
	<% control Items.Pages %>
		<% if CurrentBool %>
			<li class="currentPage">$PageNum</li>
		<% else %>
			<li class="pageNumber"><a href="$Link" title="View page number $PageNum">$PageNum</a></li>
		<% end_if %>
	<% end_control %>
	
	<% if Items.NotLastPage %>
		<li class="nextPage"><a href="$Items.NextLink" title="View the next page of Items">Next</a></li>
	<% else %>
		<li class="nextPage">Next</span></li>
	<% end_if %>
</ol>
<p>Page $Items.CurrentPage of $Items.TotalPages</p>
