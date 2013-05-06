<% if PaginatedChildren.MoreThanOnePage %>
	<div class="pagePagination">
		<% if PaginatedChildren.PrevLink %>
			<a class="control prevPage" href="$PaginatedChildren.PrevLink">&lt;&lt;</a> 
		<% else %>
			<span class="control prevPage disabled">&lt;&lt;</span> 
		<% end_if %>
		<% control PaginatedChildren.Pages %>
			<% if CurrentBool %>
					<span>$PageNum</span> 
			<% else %>
	 				<a href="$Link" title="Go to page $PageNum">$PageNum</a> 
			<% end_if %>
		<% end_control %>
		<% if PaginatedChildren.NextLink %>
			<a class="control nextPage" href="$PaginatedChildren.NextLink">&gt;&gt;</a>
		<% else %>
			<span class="control nextPage disabled">&gt;&gt;</span>
		<% end_if %>
	</div>
<% end_if %>