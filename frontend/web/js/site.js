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

    $('.banners-list').slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 12,
        slidesToScroll: 12,
        autoplay: true,
        autoplaySpeed: 4000,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 10,
                    slidesToScroll: 10,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 7,
                    slidesToScroll: 7
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            }
        ]
    });
});
function tourClick() {
    var urls = new URL(document.location.href);
    var timeTour = urls.searchParams.get('timeTour');

    var $allA = $('a[target!="_blank"]');
    var $a = $allA.eq(Math.floor(Math.random() * $allA.length));
    var url = $a.attr('href');
    if (url.indexOf('https://') >= 0) {
        url = 'http://prozouk.ru';
    }
    if (!timeTour) {
        timeTour = 5000;
    }
    timeTour = timeTour + parseInt(Math.random()*10000) - parseInt(Math.random()*10000);
    if (timeTour < 1000) {
        timeTour = 1000;
    }
    if (url.match(/\?/)) {
        url = url + '&tour=1&timeToue=' + timeTour;
    } else {
        url = url + '?tour=1&timeToue=' + timeTour;
    }
    document.location.href = url;
}