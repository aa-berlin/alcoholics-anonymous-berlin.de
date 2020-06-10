jQuery(function ($) {
    $('<button id="aa-berlin-addons-gso-gb-chat">').css({
        display: 'block',
        position: 'fixed',
        zIndex: '100',
        right: '25px',
        bottom: '10px',
        border: '0',
        width: '75px',
        height: '75px',
        margin: '0',
        padding: '0',
        outline: '0',
        boxShadow: '0',
        background: 'transparent url(https://prod.purechatcdn.com/content/images/stockwidgetimages/flat/hexagon.webp) no-repeat',
        backgroundSize: '100% auto',
    }).attr({
        title: 'Alcoholics Anonymous Chat',
    }).appendTo('body').on('click', function () {
        location.assign('https://www.alcoholics-anonymous.org.uk/About-AA/Newcomers#chat-now');
    });
});
