function Toggler(storage) {
    "use strict";

    var STORAGE_KEY = 'sf_toggle_data',
            states = {},
            isCollapsed = function(button) {
                return Sfjs.hasClass(button, 'closed');
            },
            isExpanded = function(button) {
                return !isCollapsed(button);
            },
            expand = function(button) {
                var targetId = button.dataset.toggleTargetId,
                        target = document.getElementById(targetId);

                if (!target) {
                    throw "Toggle target " + targetId + " does not exist";
                }

                if (isCollapsed(button)) {
                    Sfjs.removeClass(button, 'closed');
                    Sfjs.removeClass(target, 'hidden');

                    states[targetId] = 1;
                    storage.setItem(STORAGE_KEY, states);
                }
            },
            collapse = function(button) {
                var targetId = button.dataset.toggleTargetId,
                        target = document.getElementById(targetId);

                if (!target) {
                    throw "Toggle target " + targetId + " does not exist";
                }

                if (isExpanded(button)) {
                    Sfjs.addClass(button, 'closed');
                    Sfjs.addClass(target, 'hidden');

                    states[targetId] = 0;
                    storage.setItem(STORAGE_KEY, states);
                }
            },
            toggle = function(button) {
                if (Sfjs.hasClass(button, 'closed')) {
                    expand(button);
                } else {
                    collapse(button);
                }
            },
            initButtons = function(buttons) {
                states = storage.getItem(STORAGE_KEY, {});

                // must be an object, not an array or anything else
                // `typeof` returns "object" also for arrays, so the following
                // check must be done
                // see http://stackoverflow.com/questions/4775722/check-if-object-is-array
                if ('[object Object]' !== Object.prototype.toString.call(states)) {
                    states = {};
                }

                for (var i = 0, l = buttons.length; i < l; ++i) {
                    var targetId = buttons[i].dataset.toggleTargetId,
                            target = document.getElementById(targetId);

                    if (!target) {
                        throw "Toggle target " + targetId + " does not exist";
                    }

                    // correct the initial state of the button
                    if (Sfjs.hasClass(target, 'hidden')) {
                        Sfjs.addClass(buttons[i], 'closed');
                    }

                    // attach listener for expanding/collapsing the target
                    clickHandler(buttons[i], toggle);

                    if (states.hasOwnProperty(targetId)) {
                        // open or collapse based on stored data
                        if (0 === states[targetId]) {
                            collapse(buttons[i]);
                        } else {
                            expand(buttons[i]);
                        }
                    }
                }
            };

    return {
        initButtons: initButtons,
        toggle: toggle,
        isExpanded: isExpanded,
        isCollapsed: isCollapsed,
        expand: expand,
        collapse: collapse
    };
}

function JsonStorage(storage) {
    var setItem = function(key, data) {
        storage.setItem(key, JSON.stringify(data));
    },
            getItem = function(key, defaultValue) {
                var data = storage.getItem(key);

                if (null !== data) {
                    try {
                        return JSON.parse(data);
                    } catch (e) {
                    }
                }

                return defaultValue;
            };

    return {
        setItem: setItem,
        getItem: getItem
    };
}

function TabView() {
    "use strict";

    var activeTab = null,
            activeTarget = null,
            select = function(tab) {
                var targetId = tab.dataset.tabTargetId,
                        target = document.getElementById(targetId);

                if (!target) {
                    throw "Tab target " + targetId + " does not exist";
                }

                if (activeTab) {
                    Sfjs.removeClass(activeTab, 'active');
                }

                if (activeTarget) {
                    Sfjs.addClass(activeTarget, 'hidden');
                }

                Sfjs.addClass(tab, 'active');
                Sfjs.removeClass(target, 'hidden');

                activeTab = tab;
                activeTarget = target;
            },
            initTabs = function(tabs) {
                for (var i = 0, l = tabs.length; i < l; ++i) {
                    var targetId = tabs[i].dataset.tabTargetId,
                            target = document.getElementById(targetId);

                    if (!target) {
                        throw "Tab target " + targetId + " does not exist";
                    }

                    clickHandler(tabs[i], select);

                    Sfjs.addClass(target, 'hidden');
                }

                if (tabs.length > 0) {
                    select(tabs[0]);
                }
            };

    return {
        initTabs: initTabs,
        select: select
    };
}

var tabTarget = new TabView(),
        toggler = new Toggler(new JsonStorage(sessionStorage)),
        clickHandler = function(element, callback) {
            Sfjs.addEventListener(element, 'click', function(e) {
                if (!e) {
                    e = window.event;
                }

                callback(this);

                if (e.preventDefault) {
                    e.preventDefault();
                } else {
                    e.returnValue = false;
                }

                e.stopPropagation();

                return false;
            });
        };

tabTarget.initTabs(document.querySelectorAll('.tree .tree-inner'));
toggler.initButtons(document.querySelectorAll('a.toggle-button'));
