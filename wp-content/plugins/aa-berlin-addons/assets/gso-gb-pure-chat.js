jQuery(function ($) {
    const widget = $('#PureChatWidget');
    const sessionKey = 'pureChatSuperMinimize';

    const updateWidgetState = (minimize) => {
        widget.toggleClass('purechat-widget-collapsed', !minimize);
        widget.toggleClass('purechat-widget-super-collapsed', minimize);
    };

    widget.on('click', '[data-trigger]', () => {
        const minimize = localStorage.getItem(sessionKey) === 'false';
        localStorage.setItem(sessionKey, String(minimize));

        updateWidgetState(minimize);
    });

    if (!localStorage.getItem(sessionKey)) {
        localStorage.setItem(sessionKey, 'true');
    }

    updateWidgetState(localStorage.getItem(sessionKey) === 'true');
});
