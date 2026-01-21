// Ensure instapaper_button_vars is defined (fallback if jsVars endpoint fails)
if (typeof instapaper_button_vars === 'undefined') {
    var instapaper_button_vars = {
        keyboard_shortcut: '',
        icons: {
            added_to_instapaper: ''
        },
        i18n: {
            added_article_to_instapaper: 'Article added to Instapaper',
            failed_to_add_article_to_instapaper: 'Failed to add article to Instapaper',
            ajax_request_failed: 'AJAX request failed',
            article_not_found: 'Article not found'
        }
    };
}

// Wait for FreshRSS to fully initialize before running our code
function initInstapaperButton() {
    // Wait for DOM and FreshRSS to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(documentReady, 100);
        }, false);
    } else {
        // DOM is ready, but wait a bit for FreshRSS initialization
        setTimeout(documentReady, 100);
    }
}

function documentReady() {
    // Check if the stream container exists
    var streamContainer = document.querySelector('#stream');
    if (!streamContainer) {
        return;
    }
    
    // Look for buttons
    var instapaperButtons = document.querySelectorAll('#stream .flux a.instapaperButton');
    
    for (var i = 0; i < instapaperButtons.length; i++) {
        let instapaperButton = instapaperButtons[i];
        instapaperButton.addEventListener('click', function(e) {
            if (!instapaperButton) {
                return;
            }

            var active = instapaperButton.closest(".flux");
            if (!active) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            add_to_instapaper(instapaperButton, active);
        }, false);
    }

    if (instapaper_button_vars && instapaper_button_vars.keyboard_shortcut) {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey || e.altKey || e.shiftKey || e.target.closest('input, textarea')) {
                return;
            }

            if (e.key === instapaper_button_vars.keyboard_shortcut) {
                var active = document.querySelector("#stream .flux.active");
                if (!active) {
                    return;
                }

                var instapaperButton = active.querySelector("a.instapaperButton");
                if (!instapaperButton) {
                    return;
                }

                add_to_instapaper(instapaperButton, active);
            }
        });
    }
}

function add_to_instapaper(instapaperButton, active) {
    if (!instapaperButton || !active) {
        return;
    }

    var url = instapaperButton.getAttribute("href");
    if (!url) {
        return;
    }

    let instapaperButtonImg = instapaperButton.querySelector("img");
    if (!instapaperButtonImg) {
        return;
    }
    instapaperButtonImg.classList.add("ib_disabled");

    let loadingAnimation = instapaperButton.querySelector(".ib_lds-dual-ring");
    if (!loadingAnimation) {
        instapaperButtonImg.classList.remove("ib_disabled");
        return;
    }
    loadingAnimation.classList.remove("ib_disabled");

    let activeId = active.getAttribute('id');
    if (!activeId) {
        instapaperButtonImg.classList.remove("ib_disabled");
        loadingAnimation.classList.add("ib_disabled");
        return;
    }

    if (typeof pending_entries === 'undefined') {
        window.pending_entries = {};
    }
    if (pending_entries[activeId]) {
        instapaperButtonImg.classList.remove("ib_disabled");
        loadingAnimation.classList.add("ib_disabled");
        return;
    }

    pending_entries[activeId] = true;

    let request = new XMLHttpRequest();

    request.open('POST', url, true);
    request.responseType = 'json';

    request.onload = function(e) {
        delete pending_entries[activeId];

        instapaperButtonImg.classList.remove("ib_disabled");
        loadingAnimation.classList.add("ib_disabled");

        if (this.status != 200) {
            return request.onerror(e);
        }

        let response = null;
        if (typeof xmlHttpRequestJson === 'function') {
            response = xmlHttpRequestJson(this);
        } else {
            try {
                response = JSON.parse(this.responseText);
            } catch (e) {
                return request.onerror(e);
            }
        }
        if (!response) {
            return request.onerror(e);
        }

        if (response.status === 200 || response.status === 201) {
            if (instapaper_button_vars && instapaper_button_vars.icons && instapaper_button_vars.icons.added_to_instapaper) {
                instapaperButtonImg.setAttribute("src", instapaper_button_vars.icons.added_to_instapaper);
            }
            var message = instapaper_button_vars && instapaper_button_vars.i18n ? 
                instapaper_button_vars.i18n.added_article_to_instapaper.replace('%s', response.response && response.response.title ? response.response.title : '') : 
                'Article added to Instapaper';
            if (typeof openNotification === 'function') {
                openNotification(message, 'ib_good');
            }
        } else {
            if (response.status === 404) {
                var notFoundMsg = instapaper_button_vars && instapaper_button_vars.i18n ? 
                    instapaper_button_vars.i18n.article_not_found : 
                    'Article not found';
                if (typeof openNotification === 'function') {
                    openNotification(notFoundMsg, 'ib_bad');
                }
            } else {
                var failedMsg = instapaper_button_vars && instapaper_button_vars.i18n ? 
                    instapaper_button_vars.i18n.failed_to_add_article_to_instapaper.replace('%s', response.errorCode || response.status || '') : 
                    'Failed to add article to Instapaper';
                if (typeof openNotification === 'function') {
                    openNotification(failedMsg, 'ib_bad');
                }
            }
        }
    };

    request.onerror = function(e) {
        delete pending_entries[activeId];

        instapaperButtonImg.classList.remove("ib_disabled");
        loadingAnimation.classList.add("ib_disabled");

        if (typeof badAjax === 'function') {
            badAjax(this.status == 403);
        }
    };

    request.setRequestHeader('Content-Type', 'application/json');
    
    var csrf = '';
    if (typeof context !== 'undefined' && context.csrf) {
        csrf = context.csrf;
    }
    
    request.send(JSON.stringify({
        ajax: true,
        _csrf: csrf
    }));
}

// Initialize when script loads
initInstapaperButton();