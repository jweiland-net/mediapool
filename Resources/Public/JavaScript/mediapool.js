$(document).ready(function() {
    resizePlaylist();
    scrollToActiveVideo();

    $(window).resize(function() {
        resizePlaylist();
    });
});

function scrollToActiveVideo() {
    let $mediapoolPlaylistItems = $('.mediapool-playlist-items');
    let $activeItem = $('.playlist-item-active');

    if ($activeItem.length) {
        $mediapoolPlaylistItems.scrollTop($mediapoolPlaylistItems.scrollTop() + $activeItem.position().top);
    }
}

function resizePlaylist() {
    let height = $('.video-embed').outerHeight() - $('.mediapool-playlist-header').outerHeight();
    $('.mediapool-playlist-items').each(function () {
        $(this).css('height', height + 'px');
    }, height);
}
