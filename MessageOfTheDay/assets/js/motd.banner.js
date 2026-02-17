/**
 * Message of the Day — Banner Script
 * Zabbix 6.4 Frontend Module
 *
 * Fetches the banner config from the module's config endpoint and
 * injects a dismissible banner at the top of every page.
 */
(function () {
    'use strict';

    const DISMISS_KEY = 'motd_dismissed';
    const ICON_SVG = {
        info: '<svg class="motd-icon" viewBox="0 0 20 20"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9V9h2v6zm0-8H9V5h2v2z"/></svg>',
        warning: '<svg class="motd-icon" viewBox="0 0 20 20"><path d="M10 0L0 18h20L10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/></svg>',
        danger: '<svg class="motd-icon" viewBox="0 0 20 20"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V5h2v6z"/></svg>',
        success: '<svg class="motd-icon" viewBox="0 0 20 20"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm-1 14.59L4.41 10 5.83 8.58 9 11.75l6.17-6.17 1.42 1.42L9 14.59z"/></svg>'
    };

    function getDismissedMessages() {
        try {
            return JSON.parse(sessionStorage.getItem(DISMISS_KEY) || '[]');
        } catch (e) {
            return [];
        }
    }

    function markDismissed(message) {
        try {
            var dismissed = getDismissedMessages();
            var hash = btoa(encodeURIComponent(message)).substring(0, 32);
            if (dismissed.indexOf(hash) === -1) {
                dismissed.push(hash);
                sessionStorage.setItem(DISMISS_KEY, JSON.stringify(dismissed));
            }
        } catch (e) {}
    }

    function isMessageDismissed(message) {
        try {
            var dismissed = getDismissedMessages();
            var hash = btoa(encodeURIComponent(message)).substring(0, 32);
            return dismissed.indexOf(hash) !== -1;
        } catch (e) {
            return false;
        }
    }

    function createBanner(config) {
        var banner = document.createElement('div');
        banner.id = 'motd-banner';
        banner.className = 'motd-' + config.type;

        var icon = ICON_SVG[config.type] || ICON_SVG.info;

        var messageEl = document.createElement('span');
        messageEl.className = 'motd-message';
        messageEl.textContent = config.message;

        banner.innerHTML = icon;
        banner.appendChild(messageEl);

        if (config.dismissible) {
            var dismissBtn = document.createElement('button');
            dismissBtn.className = 'motd-dismiss';
            dismissBtn.setAttribute('aria-label', 'Dismiss message');
            dismissBtn.setAttribute('title', 'Dismiss');
            dismissBtn.innerHTML = '&times;';
            dismissBtn.addEventListener('click', function () {
                markDismissed(config.message);
                banner.classList.add('motd-hidden');
            });
            banner.appendChild(dismissBtn);
        }

        return banner;
    }

    function injectBanner(config) {
        // Already shown?
        if (document.getElementById('motd-banner')) {
            return;
        }

        // Check if this session has dismissed this exact message
        if (config.dismissible && isMessageDismissed(config.message)) {
            return;
        }

        var banner = createBanner(config);

        // Inject right after the main header / top navigation bar
        // Zabbix 6.4 wraps content in .wrapper > .content-header or .header-navigation
        var insertTarget = (
            document.querySelector('.header-navigation') ||
            document.querySelector('.wrapper') ||
            document.querySelector('header') ||
            document.body
        );

        if (insertTarget && insertTarget.parentNode) {
            insertTarget.parentNode.insertBefore(banner, insertTarget.nextSibling);
        } else {
            document.body.insertBefore(banner, document.body.firstChild);
        }
    }

    function loadAndRender() {
        // Resolve base URL for the AJAX call
        var scriptEl = document.querySelector('script[src*="motd.banner.js"]');
        var baseUrl = '';
        if (scriptEl) {
            // e.g. /zabbix/modules/MessageOfTheDay/assets/js/motd.banner.js
            var src = scriptEl.getAttribute('src');
            var idx = src.indexOf('/modules/');
            if (idx !== -1) {
                baseUrl = src.substring(0, idx);
            }
        }

        var configUrl = baseUrl + '/zabbix.php?action=messageoftheday.config';

        fetch(configUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) {
            if (!response.ok) { return null; }
            return response.json();
        })
        .then(function (config) {
            if (!config || !config.enabled || !config.message) {
                return;
            }

            // Respect "show_to" setting
            // user_type: 1=User, 2=Admin, 3=SuperAdmin
            if (config.show_to === 'admins' && config.user_type < 2) {
                return;
            }

            injectBanner(config);
        })
        .catch(function () {
            // Silently fail — never break the main UI
        });
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAndRender);
    } else {
        loadAndRender();
    }
}());
