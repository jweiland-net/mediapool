$(document).ready(function() {
  resizePlaylist();
  scrollToActiveVideo();
  
  $(window).resize(function() {
    resizePlaylist();
  });
});

function scrollToActiveVideo() {
  $(".mediapool-playlist").scrollTop($(".playlist-item-active").position().top);
}

function resizePlaylist() {
  var playerHeight = $('.video-embed').css('height');
  $(".mediapool-playlist").each(function () {
    $(this).css('height', playerHeight);
  });
}
