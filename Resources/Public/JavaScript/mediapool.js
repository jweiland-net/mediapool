$(document).ready(function() {
  resizePlaylist();
  scrollToActiveVideo();
  
  $(window).resize(function() {
    resizePlaylist();
  });
});

function scrollToActiveVideo() {
  var $mediapoolPlaylistItems = $('.mediapool-playlist-items');
  var $activeItem = $('.playlist-item-active');
  $mediapoolPlaylistItems.scrollTop($mediapoolPlaylistItems.scrollTop() + $activeItem.position().top);
}

function resizePlaylist() {
  var height = $('.video-embed').outerHeight() - $('.mediapool-playlist-header').outerHeight();
  $('.mediapool-playlist-items').each(function () {
    $(this).css('height', height + 'px');
  }, height);
}
