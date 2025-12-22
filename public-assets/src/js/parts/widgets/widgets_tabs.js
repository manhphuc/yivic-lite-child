(() => {
    "use strict";

    const ROOT_SELECTOR = "[data-yivic-lite-tabs]";
    const INIT_FLAG = "yivicLiteTabsInited";

    function activate(root, tab) {
        const tabs = Array.from(root.querySelectorAll(".yivic-lite-tabs__tab[role='tab']"));
        const panels = Array.from(root.querySelectorAll(".yivic-lite-tabs__panel[role='tabpanel']"));

        const targetId = tab.getAttribute("aria-controls");
        if (!targetId) return;

        const targetPanel = root.querySelector(`#${CSS.escape(targetId)}`);
        if (!targetPanel) return;

        tabs.forEach((t) => {
            const isActive = t === tab;
            t.classList.toggle("is-active", isActive);
            t.setAttribute("aria-selected", isActive ? "true" : "false");
            t.tabIndex = isActive ? 0 : -1;
        });

        panels.forEach((p) => {
            const isActive = p === targetPanel;
            p.classList.toggle("is-active", isActive);
            if (isActive) p.removeAttribute("hidden");
            else p.setAttribute("hidden", "");
        });
    }

    function initOne(root) {
        if (!root || root.dataset[INIT_FLAG] === "1") return;

        const tablist = root.querySelector(".yivic-lite-tabs__nav");
        const tabs = Array.from(root.querySelectorAll(".yivic-lite-tabs__tab[role='tab']"));
        const panels = Array.from(root.querySelectorAll(".yivic-lite-tabs__panel[role='tabpanel']"));

        if (!tablist || tabs.length === 0 || panels.length === 0) return;

        // Mark as inited (prevents double-binding when Customizer re-renders).
        root.dataset[INIT_FLAG] = "1";

        // Click
        tabs.forEach((tab) => {
            tab.addEventListener("click", (e) => {
                e.preventDefault();
                activate(root, tab);
            });
        });

        // Keyboard
        tablist.addEventListener("keydown", (e) => {
            const currentIndex = tabs.findIndex((t) => t.getAttribute("aria-selected") === "true");
            if (currentIndex < 0) return;

            let nextIndex = currentIndex;

            switch (e.key) {
                case "ArrowLeft":
                    nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
                    e.preventDefault();
                    tabs[nextIndex].focus();
                    break;
                case "ArrowRight":
                    nextIndex = (currentIndex + 1) % tabs.length;
                    e.preventDefault();
                    tabs[nextIndex].focus();
                    break;
                case "Home":
                    e.preventDefault();
                    tabs[0].focus();
                    break;
                case "End":
                    e.preventDefault();
                    tabs[tabs.length - 1].focus();
                    break;
                case "Enter":
                case " ":
                    e.preventDefault();
                    if (document.activeElement && tabs.includes(document.activeElement)) {
                        activate(root, document.activeElement);
                    }
                    break;
            }
        });

        // Ensure a valid initial state.
        const preActive = tabs.find((t) => t.classList.contains("is-active")) || tabs[0];
        activate(root, preActive);
    }

    function bootAll() {
        document.querySelectorAll(ROOT_SELECTOR).forEach(initOne);
    }

    // Re-init when widgets are updated (Customizer / Widgets screen)
    function bindWidgetEvents() {
        // Classic events used by WP widgets UI/customizer in many cases
        document.addEventListener("widget-added", bootAll);
        document.addEventListener("widget-updated", bootAll);
    }

    // Observe DOM mutations (covers async sidebar refresh in Customizer)
    function observe() {
        const mo = new MutationObserver(() => bootAll());
        mo.observe(document.documentElement, { childList: true, subtree: true });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", () => {
            bootAll();
            bindWidgetEvents();
            observe();
        });
    } else {
        bootAll();
        bindWidgetEvents();
        observe();
    }
})();
