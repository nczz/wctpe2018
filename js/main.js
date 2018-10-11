jQuery(document).ready(function() {
    jQuery('#FBShare').click(function() {
        FB.ui({
            method: 'share',
            hashtag: '#WordCampTaipei2018',
            //quote: '',
            display: 'popup',
            href: location.href,
        }, function(response) { /*console.log(response);*/ });
    });
    jQuery('#TwitterShare').click(function() {
        var shareURL = 'http://twitter.com/share?';
        var params = {
            url: location.href,
            text: 'Hey! I am here in WordCamp Taipei 2018 now!',
            via: 'WordCampTaipei',
            hashtags: 'WCTPE,WordCampTaipei2018'
        }
        for (var prop in params) shareURL += '&' + prop + '=' + encodeURIComponent(params[prop]);
        window.open(shareURL, '', 'left=0,top=0,width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
    });
});
