<% if HaveMessages %>
	<div class="notifications">
		<% control SessionMessages %>
			<p class="notification $Mode">$Msg</p>
		<% end_control %>
	</div>
<% end_if %>