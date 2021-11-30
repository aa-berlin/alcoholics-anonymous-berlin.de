jQuery(function ($) {
    var button = $('<button class="aa-berlin-addons-gso-gb-chat">').on('click', function () {
        location.assign('https://www.alcoholics-anonymous.org.uk/Home/Newcomers');
    }).hover(function () {
        button.addClass('hovering');
    }, function () {
        button.removeClass('hovering');
    });

    $('<span class="aa-berlin-addons-text"></span>').html('Chat with us at Alcoholics Anonymous Great Britain').appendTo(button);
    $('<svg xmlns="http://www.w3.org/2000/svg" class="aa-berlin-addons-icon" viewBox="0 0 74.515 65.314"><path d="M19.711.202c-2.232.115-2.648 2.76-3.776 4.24C10.647 13.661 5.25 22.826.03 32.081c-.473 1.962 1.368 3.457 2.072 5.142 5.308 9.133 10.514 18.333 15.885 27.423 1.464 1.39 3.678.544 5.49.777 10.563-.03 21.133.061 31.691-.046 1.937-.572 2.31-2.913 3.418-4.365 5.255-9.163 10.62-18.272 15.806-27.468.473-1.964-1.367-3.458-2.071-5.144C67.013 19.27 61.807 10.07 56.435.98c-1.463-1.391-3.678-.544-5.49-.777H19.712z" fill="#337ab7"/><path d="M37.663 13.425a20.684 20.684 0 00-1.128.01c-5.995.212-11.892 3.044-15.294 8.398-7.506 10.22-1.972 26.082 10.259 29.42 4.759 1.646 9.76 1.061 14.037-1.03 3.284 1.307 5.12 1.037 5.12 1.037.721.102 1.863-.206 1.251-.6 0 0-1.137-.737-2.483-2.116-.14-.143-.048-.089-.191-.246l.029-.426c.3-.23.332-.265.62-.512 4.175-3.58 6.909-8.824 6.697-14.643 0-4.32-1.485-8.58-4.172-11.964-3.535-4.799-9.113-7.219-14.745-7.327z" fill="#cddfdf"/></svg>').appendTo(button);

    button.appendTo('body');
});
