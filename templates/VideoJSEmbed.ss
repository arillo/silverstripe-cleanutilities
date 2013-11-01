<% if $MP4File || $WebmFile || $OGVFile %>
  <video id="example_video_$ID" class="video-js vjs-default-skin"
      controls preload="none"
      width="$Width"
      height="$Height"
      <% if $PreviewImage %>poster="$PreviewImage.CroppedImage(380,235).URL"<% end_if %>
      data-setup='$Setup'
      >
    <% if $MP4File %>
      <source src="$MP4File.URL" type='video/mp4' />
    <% end_if %>

    <% if $WebmFile %>
      <source src="$WebmFile.URL" type='video/webm' />
    <% end_if %>

    <% if $OGVFile %>
      <source src="$OGVFile.URL" type='video/ogg' />
    <% end_if %>
  </video>
<% end_if %>
