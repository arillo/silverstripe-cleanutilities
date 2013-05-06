<% if PageNavigation(Sort_ASC) %>
	<ul class="pageNavigation">
		<li class="pager">$CurrentPageNumber(Sort_ASC) / $NumberOfSiblings</li>
		<% if PrevPage(Sort_ASC) %><li class="control prevPage"><% control PrevPage(Sort_ASC) %><a href="$Link" title="<% _t('ProjectPage.PREVOIUS','Previous') %>: $Title"><% _t('PageNavigation.PREVIOUS_PAGE','Previous page') %></a><% end_control %></li><% end_if %>
		<% if NextPage(Sort_ASC) %><li class="control nextPage"><% control NextPage(Sort_ASC) %><a href="$Link" title="<% _t('ProjectPage.NEXT','Next') %>: $Title"><% _t('PageNavigation.NEXT_PAGE','Next page') %></a><% end_control %></li><% end_if %>
	</ul>
<% end_if %>