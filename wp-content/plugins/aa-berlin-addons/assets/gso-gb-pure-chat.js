jQuery(function ($) {
    const widget = $('#PureChatWidget');
    const sessionKey = 'pureChatSuperMinimize';

    const updateWidgetState = (minimize) => {
        widget.toggleClass('purechat-widget-collapsed', !minimize);
        widget.toggleClass('purechat-widget-super-collapsed', minimize);
    };

    widget.on('click', '[data-trigger="superMinimize"], [data-trigger="collapse"]', () => {
        const minimize = localStorage.getItem(sessionKey) === 'false';
        localStorage.setItem(sessionKey, String(minimize));

        updateWidgetState(minimize);
    });

    widget.on('click', '[data-trigger="expand"], .purechat-widget-title', (e) => {
        e.preventDefault();

        location.assign('https://www.alcoholics-anonymous.org.uk/Home#chat-now');
    });

    if (!localStorage.getItem(sessionKey)) {
        localStorage.setItem(sessionKey, 'true');
    }

    updateWidgetState(localStorage.getItem(sessionKey) === 'true');
});
