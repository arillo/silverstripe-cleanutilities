<% if Videos %>
  <% loop Videos %>
    <h3>$Title</h3>
    $VideoEmbed(320, 200)
  <% end_loop %>
<% end_if %>