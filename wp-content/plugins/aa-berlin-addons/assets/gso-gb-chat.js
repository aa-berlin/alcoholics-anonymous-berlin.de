jQuery(function ($) {
    var button = $('<button class="aa-berlin-addons-gso-gb-chat">').on('click', function () {
        location.assign('https://www.alcoholics-anonymous.org.uk/Home/Newcomers');
    }).hover(function () {
        button.addClass('hovering');
    }, function () {
        button.removeClass('hovering');
    });

    $('<span class="aa-berlin-addons-text"></span>').html('Chat with us at Alcoholics Anonymous Great Britain').appendTo(button);
    $('<svg class="aa-berlin-addons-icon-bg" xmlns="http://www.w3.org/2000/svg" width="70" height="61" viewbox="0 0 70 60.6217782649107"><path fill="#fff" d="M0 30.31088913245535L17.5 0L52.5 0L70 30.31088913245535L52.5 60.6217782649107L17.5 60.6217782649107Z"></path></svg>').appendTo(button);
    $('<span class="aa-berlin-addons-icon fa fa-comment"></span>').appendTo(button);

    button.appendTo('body');
});
