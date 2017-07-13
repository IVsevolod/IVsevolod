$(function() {
    var url = new URL(document.location.href);
    var isTour = url.searchParams.get('tour');
    if (isTour) {
        var timeTour = url.searchParams.get('timeTour');
        if (!timeTour) {
            timeTour = 5000;
        }
        setTimeout('tourClick()', timeTour);
    }
});
function tourClick() {
    var urls = new URL(document.location.href);
    var timeTour = urls.searchParams.get('timeTour');

    var $allA = $('a[target!="_blank"]');
    var $a = $allA.eq(Math.floor(Math.random() * $allA.length));
    var url = $a.attr('href');
    if (!timeTour) {
        timeTour = 5000;
    }
    if (url.match(/\?/)) {
        url = url + '&tour=1&timeToue=' + timeTour;
    } else {
        url = url + '?tour=1&timeToue=' + timeTour;
    }
    $a.attr('href', url);
    console.log($a);
    $a.click();
}