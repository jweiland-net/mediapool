$(document).ready(function() {
  scrollToActiveVideo();
});

function scrollToActiveVideo() {
  $(".mediapool-playlist").scrollTop($(".playlist-item-active").position().top);
}
