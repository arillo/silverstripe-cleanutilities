<% if Videos %>
  <% loop Videos %>
    $Title
    $VideoEmbed(320, 200, false, true, true, 'auto')
  <% end_loop %>
<% end_if %>