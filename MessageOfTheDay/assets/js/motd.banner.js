/**
 * Message of the Day — Banner Script
 * Zabbix 6.4 Frontend Module
 */
(function () {
    'use strict';

    var DISMISS_KEY = 'motd_dismissed';

    var ICON_SVG = {
        info:    '<svg class="motd-icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9V9h2v6zm0-8H9V5h2v2z"/></svg>',
        warning: '<svg class="motd-icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 0L0 18h20L10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/></svg>',
        danger:  '<svg class="motd-icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V5h2v6z"/></svg>',
        success: '<svg class="motd-icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm-1 14.59L4.41 10 5.83 8.58 9 11.75l6.17-6.17 1.42 1.42L9 14.59z"/></svg>'
    };

    function getMessageHash(message) {
        var hash = 0;
        for (var i = 0; i < message.length; i++) {
            hash = ((hash << 5) - hash) + message.charCodeAt(i);
            hash |= 0;
        }
        return DISMISS_KEY + '_' + Math.abs(hash);
    }

    function isDismissed(message) {
        try {
            return sessionStorage.getItem(getMessageHash(message)) === '1';
        } catch (e) {
            return false;
        }
    }

    function markDismissed(message) {
        try {
            sessionStorage.setItem(getMessageHash(message), '1');
        } catch (e) {}
    }

    function createBanner(config) {
        var type = config.type || 'info';

        var banner = document.createElement('div');
        banner.id = 'motd-banner';
        banner.className = 'motd-' + type;
        banner.setAttribute('role', 'alert');

        // Icon
        var iconWrap = document.createElement('span');
        iconWrap.className = 'motd-icon-wrap';
        iconWrap.innerHTML = ICON_SVG[type] || ICON_SVG.info;
        banner.appendChild(iconWrap);

        // Message text
        var msgEl = document.createElement('span');
        msgEl.className = 'motd-message';
        msgEl.textContent = config.message;
        banner.appendChild(msgEl);

        // Optional link
        if (config.link_url && config.link_url.trim() !== '') {
            var link = document.createElement('a');
            link.className = 'motd-link';
            link.href = config.link_url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.textContent = config.link_label || 'Read more';
            banner.appendChild(link);
        }

        // Dismiss button
        if (config.dismissible) {
            var btn = document.createElement('button');
            btn.className = 'motd-dismiss';
            btn.setAttribute('aria-label', 'Dismiss');
            btn.setAttribute('title', 'Dismiss');
            btn.innerHTML = '&times;';
            btn.addEventListener('click', function () {
                markDismissed(config.message);
                banner.style.transition = 'opacity 0.2s';
                banner.style.opacity = '0';
                setTimeout(function () {
                    if (banner.parentNode) {
                        banner.parentNode.removeChild(banner);
                    }
                }, 200);
            });
            banner.appendChild(btn);
        }

        return banner;
    }

    function injectBanner(config) {
        if (document.getElementById('motd-banner')) {
            return;
        }
        if (config.dismissible && isDismissed(config.message)) {
            return;
        }

        var banner = createBanner(config);

        var anchor = (
            document.querySelector('.header-navigation') ||
            document.querySelector('.top-subnav-container') ||
            document.querySelector('.content-header') ||
            document.querySelector('.wrapper') ||
            document.body.firstElementChild
        );

        if (anchor && anchor.parentNode) {
            anchor.parentNode.insertBefore(banner, anchor);
        } else {
            document.body.insertBefore(banner, document.body.firstChild);
        }
    }

    function run() {
        if (typeof window.MOTD_CONFIG === 'undefined') {
            return;
        }
        var config = window.MOTD_CONFIG;
        if (!config.enabled || !config.message) {
            return;
        }
        injectBanner(config);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
}());
