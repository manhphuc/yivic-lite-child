/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./public-assets/src/js/parts/widgets/widgets_tabs.js":
/*!************************************************************!*\
  !*** ./public-assets/src/js/parts/widgets/widgets_tabs.js ***!
  \************************************************************/
/***/ (function() {

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
    tabs.forEach(t => {
      const isActive = t === tab;
      t.classList.toggle("is-active", isActive);
      t.setAttribute("aria-selected", isActive ? "true" : "false");
      t.tabIndex = isActive ? 0 : -1;
    });
    panels.forEach(p => {
      const isActive = p === targetPanel;
      p.classList.toggle("is-active", isActive);
      if (isActive) p.removeAttribute("hidden");else p.setAttribute("hidden", "");
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
    tabs.forEach(tab => {
      tab.addEventListener("click", e => {
        e.preventDefault();
        activate(root, tab);
      });
    });

    // Keyboard
    tablist.addEventListener("keydown", e => {
      const currentIndex = tabs.findIndex(t => t.getAttribute("aria-selected") === "true");
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
    const preActive = tabs.find(t => t.classList.contains("is-active")) || tabs[0];
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
    mo.observe(document.documentElement, {
      childList: true,
      subtree: true
    });
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

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Check if module exists (development only)
/******/ 		if (__webpack_modules__[moduleId] === undefined) {
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";
var __webpack_exports__ = {};
/*!******************************************!*\
  !*** ./public-assets/src/scss/main.scss ***!
  \******************************************/
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin

}();
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";
/*!**************************************!*\
  !*** ./public-assets/src/js/main.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _parts_widgets_widgets_tabs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./parts/widgets/widgets_tabs */ "./public-assets/src/js/parts/widgets/widgets_tabs.js");
/* harmony import */ var _parts_widgets_widgets_tabs__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_parts_widgets_widgets_tabs__WEBPACK_IMPORTED_MODULE_0__);

}();
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUFFLE1BQU07TUFDaERjLE9BQU8sQ0FBQyxDQUFDO01BQ1RDLGdCQUFnQixDQUFDLENBQUM7TUFDbEJDLE9BQU8sQ0FBQyxDQUFDO0lBQ2IsQ0FBQyxDQUFDO0VBQ04sQ0FBQyxNQUFNO0lBQ0hGLE9BQU8sQ0FBQyxDQUFDO0lBQ1RDLGdCQUFnQixDQUFDLENBQUM7SUFDbEJDLE9BQU8sQ0FBQyxDQUFDO0VBQ2I7QUFDSixDQUFDLEVBQUUsQ0FBQyxDOzs7Ozs7VUN4SEo7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7Ozs7V0M1QkE7V0FDQTtXQUNBO1dBQ0EsZUFBZSw0QkFBNEI7V0FDM0MsZUFBZTtXQUNmLGlDQUFpQyxXQUFXO1dBQzVDO1dBQ0EsRTs7Ozs7V0NQQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHlDQUF5Qyx3Q0FBd0M7V0FDakY7V0FDQTtXQUNBLEU7Ozs7O1dDUEEsOENBQThDLHlEOzs7OztXQ0E5QztXQUNBO1dBQ0E7V0FDQSx1REFBdUQsaUJBQWlCO1dBQ3hFO1dBQ0EsZ0RBQWdELGFBQWE7V0FDN0QsRTs7Ozs7Ozs7Ozs7OztBQ05BIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8veWl2aWMtbGl0ZS1jaGlsZC8uL3B1YmxpYy1hc3NldHMvc3JjL2pzL3BhcnRzL3dpZGdldHMvd2lkZ2V0c190YWJzLmpzIiwid2VicGFjazovL3lpdmljLWxpdGUtY2hpbGQvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8veWl2aWMtbGl0ZS1jaGlsZC93ZWJwYWNrL3J1bnRpbWUvY29tcGF0IGdldCBkZWZhdWx0IGV4cG9ydCIsIndlYnBhY2s6Ly95aXZpYy1saXRlLWNoaWxkL3dlYnBhY2svcnVudGltZS9kZWZpbmUgcHJvcGVydHkgZ2V0dGVycyIsIndlYnBhY2s6Ly95aXZpYy1saXRlLWNoaWxkL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8veWl2aWMtbGl0ZS1jaGlsZC93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovL3lpdmljLWxpdGUtY2hpbGQvLi9wdWJsaWMtYXNzZXRzL3NyYy9zY3NzL21haW4uc2Nzcz9jMzgyIiwid2VicGFjazovL3lpdmljLWxpdGUtY2hpbGQvLi9wdWJsaWMtYXNzZXRzL3NyYy9qcy9tYWluLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIigoKSA9PiB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICBjb25zdCBST09UX1NFTEVDVE9SID0gXCJbZGF0YS15aXZpYy1saXRlLXRhYnNdXCI7XG4gICAgY29uc3QgSU5JVF9GTEFHID0gXCJ5aXZpY0xpdGVUYWJzSW5pdGVkXCI7XG5cbiAgICBmdW5jdGlvbiBhY3RpdmF0ZShyb290LCB0YWIpIHtcbiAgICAgICAgY29uc3QgdGFicyA9IEFycmF5LmZyb20ocm9vdC5xdWVyeVNlbGVjdG9yQWxsKFwiLnlpdmljLWxpdGUtdGFic19fdGFiW3JvbGU9J3RhYiddXCIpKTtcbiAgICAgICAgY29uc3QgcGFuZWxzID0gQXJyYXkuZnJvbShyb290LnF1ZXJ5U2VsZWN0b3JBbGwoXCIueWl2aWMtbGl0ZS10YWJzX19wYW5lbFtyb2xlPSd0YWJwYW5lbCddXCIpKTtcblxuICAgICAgICBjb25zdCB0YXJnZXRJZCA9IHRhYi5nZXRBdHRyaWJ1dGUoXCJhcmlhLWNvbnRyb2xzXCIpO1xuICAgICAgICBpZiAoIXRhcmdldElkKSByZXR1cm47XG5cbiAgICAgICAgY29uc3QgdGFyZ2V0UGFuZWwgPSByb290LnF1ZXJ5U2VsZWN0b3IoYCMke0NTUy5lc2NhcGUodGFyZ2V0SWQpfWApO1xuICAgICAgICBpZiAoIXRhcmdldFBhbmVsKSByZXR1cm47XG5cbiAgICAgICAgdGFicy5mb3JFYWNoKCh0KSA9PiB7XG4gICAgICAgICAgICBjb25zdCBpc0FjdGl2ZSA9IHQgPT09IHRhYjtcbiAgICAgICAgICAgIHQuY2xhc3NMaXN0LnRvZ2dsZShcImlzLWFjdGl2ZVwiLCBpc0FjdGl2ZSk7XG4gICAgICAgICAgICB0LnNldEF0dHJpYnV0ZShcImFyaWEtc2VsZWN0ZWRcIiwgaXNBY3RpdmUgPyBcInRydWVcIiA6IFwiZmFsc2VcIik7XG4gICAgICAgICAgICB0LnRhYkluZGV4ID0gaXNBY3RpdmUgPyAwIDogLTE7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHBhbmVscy5mb3JFYWNoKChwKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBpc0FjdGl2ZSA9IHAgPT09IHRhcmdldFBhbmVsO1xuICAgICAgICAgICAgcC5jbGFzc0xpc3QudG9nZ2xlKFwiaXMtYWN0aXZlXCIsIGlzQWN0aXZlKTtcbiAgICAgICAgICAgIGlmIChpc0FjdGl2ZSkgcC5yZW1vdmVBdHRyaWJ1dGUoXCJoaWRkZW5cIik7XG4gICAgICAgICAgICBlbHNlIHAuc2V0QXR0cmlidXRlKFwiaGlkZGVuXCIsIFwiXCIpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBpbml0T25lKHJvb3QpIHtcbiAgICAgICAgaWYgKCFyb290IHx8IHJvb3QuZGF0YXNldFtJTklUX0ZMQUddID09PSBcIjFcIikgcmV0dXJuO1xuXG4gICAgICAgIGNvbnN0IHRhYmxpc3QgPSByb290LnF1ZXJ5U2VsZWN0b3IoXCIueWl2aWMtbGl0ZS10YWJzX19uYXZcIik7XG4gICAgICAgIGNvbnN0IHRhYnMgPSBBcnJheS5mcm9tKHJvb3QucXVlcnlTZWxlY3RvckFsbChcIi55aXZpYy1saXRlLXRhYnNfX3RhYltyb2xlPSd0YWInXVwiKSk7XG4gICAgICAgIGNvbnN0IHBhbmVscyA9IEFycmF5LmZyb20ocm9vdC5xdWVyeVNlbGVjdG9yQWxsKFwiLnlpdmljLWxpdGUtdGFic19fcGFuZWxbcm9sZT0ndGFicGFuZWwnXVwiKSk7XG5cbiAgICAgICAgaWYgKCF0YWJsaXN0IHx8IHRhYnMubGVuZ3RoID09PSAwIHx8IHBhbmVscy5sZW5ndGggPT09IDApIHJldHVybjtcblxuICAgICAgICAvLyBNYXJrIGFzIGluaXRlZCAocHJldmVudHMgZG91YmxlLWJpbmRpbmcgd2hlbiBDdXN0b21pemVyIHJlLXJlbmRlcnMpLlxuICAgICAgICByb290LmRhdGFzZXRbSU5JVF9GTEFHXSA9IFwiMVwiO1xuXG4gICAgICAgIC8vIENsaWNrXG4gICAgICAgIHRhYnMuZm9yRWFjaCgodGFiKSA9PiB7XG4gICAgICAgICAgICB0YWIuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIChlKSA9PiB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIGFjdGl2YXRlKHJvb3QsIHRhYik7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gS2V5Ym9hcmRcbiAgICAgICAgdGFibGlzdC5hZGRFdmVudExpc3RlbmVyKFwia2V5ZG93blwiLCAoZSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgY3VycmVudEluZGV4ID0gdGFicy5maW5kSW5kZXgoKHQpID0+IHQuZ2V0QXR0cmlidXRlKFwiYXJpYS1zZWxlY3RlZFwiKSA9PT0gXCJ0cnVlXCIpO1xuICAgICAgICAgICAgaWYgKGN1cnJlbnRJbmRleCA8IDApIHJldHVybjtcblxuICAgICAgICAgICAgbGV0IG5leHRJbmRleCA9IGN1cnJlbnRJbmRleDtcblxuICAgICAgICAgICAgc3dpdGNoIChlLmtleSkge1xuICAgICAgICAgICAgICAgIGNhc2UgXCJBcnJvd0xlZnRcIjpcbiAgICAgICAgICAgICAgICAgICAgbmV4dEluZGV4ID0gKGN1cnJlbnRJbmRleCAtIDEgKyB0YWJzLmxlbmd0aCkgJSB0YWJzLmxlbmd0aDtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICB0YWJzW25leHRJbmRleF0uZm9jdXMoKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSBcIkFycm93UmlnaHRcIjpcbiAgICAgICAgICAgICAgICAgICAgbmV4dEluZGV4ID0gKGN1cnJlbnRJbmRleCArIDEpICUgdGFicy5sZW5ndGg7XG4gICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgdGFic1tuZXh0SW5kZXhdLmZvY3VzKCk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgXCJIb21lXCI6XG4gICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgdGFic1swXS5mb2N1cygpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlIFwiRW5kXCI6XG4gICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgdGFic1t0YWJzLmxlbmd0aCAtIDFdLmZvY3VzKCk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgXCJFbnRlclwiOlxuICAgICAgICAgICAgICAgIGNhc2UgXCIgXCI6XG4gICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGRvY3VtZW50LmFjdGl2ZUVsZW1lbnQgJiYgdGFicy5pbmNsdWRlcyhkb2N1bWVudC5hY3RpdmVFbGVtZW50KSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aXZhdGUocm9vdCwgZG9jdW1lbnQuYWN0aXZlRWxlbWVudCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIEVuc3VyZSBhIHZhbGlkIGluaXRpYWwgc3RhdGUuXG4gICAgICAgIGNvbnN0IHByZUFjdGl2ZSA9IHRhYnMuZmluZCgodCkgPT4gdC5jbGFzc0xpc3QuY29udGFpbnMoXCJpcy1hY3RpdmVcIikpIHx8IHRhYnNbMF07XG4gICAgICAgIGFjdGl2YXRlKHJvb3QsIHByZUFjdGl2ZSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gYm9vdEFsbCgpIHtcbiAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbChST09UX1NFTEVDVE9SKS5mb3JFYWNoKGluaXRPbmUpO1xuICAgIH1cblxuICAgIC8vIFJlLWluaXQgd2hlbiB3aWRnZXRzIGFyZSB1cGRhdGVkIChDdXN0b21pemVyIC8gV2lkZ2V0cyBzY3JlZW4pXG4gICAgZnVuY3Rpb24gYmluZFdpZGdldEV2ZW50cygpIHtcbiAgICAgICAgLy8gQ2xhc3NpYyBldmVudHMgdXNlZCBieSBXUCB3aWRnZXRzIFVJL2N1c3RvbWl6ZXIgaW4gbWFueSBjYXNlc1xuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKFwid2lkZ2V0LWFkZGVkXCIsIGJvb3RBbGwpO1xuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKFwid2lkZ2V0LXVwZGF0ZWRcIiwgYm9vdEFsbCk7XG4gICAgfVxuXG4gICAgLy8gT2JzZXJ2ZSBET00gbXV0YXRpb25zIChjb3ZlcnMgYXN5bmMgc2lkZWJhciByZWZyZXNoIGluIEN1c3RvbWl6ZXIpXG4gICAgZnVuY3Rpb24gb2JzZXJ2ZSgpIHtcbiAgICAgICAgY29uc3QgbW8gPSBuZXcgTXV0YXRpb25PYnNlcnZlcigoKSA9PiBib290QWxsKCkpO1xuICAgICAgICBtby5vYnNlcnZlKGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCwgeyBjaGlsZExpc3Q6IHRydWUsIHN1YnRyZWU6IHRydWUgfSk7XG4gICAgfVxuXG4gICAgaWYgKGRvY3VtZW50LnJlYWR5U3RhdGUgPT09IFwibG9hZGluZ1wiKSB7XG4gICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJET01Db250ZW50TG9hZGVkXCIsICgpID0+IHtcbiAgICAgICAgICAgIGJvb3RBbGwoKTtcbiAgICAgICAgICAgIGJpbmRXaWRnZXRFdmVudHMoKTtcbiAgICAgICAgICAgIG9ic2VydmUoKTtcbiAgICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgYm9vdEFsbCgpO1xuICAgICAgICBiaW5kV2lkZ2V0RXZlbnRzKCk7XG4gICAgICAgIG9ic2VydmUoKTtcbiAgICB9XG59KSgpO1xuIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDaGVjayBpZiBtb2R1bGUgZXhpc3RzIChkZXZlbG9wbWVudCBvbmx5KVxuXHRpZiAoX193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0gPT09IHVuZGVmaW5lZCkge1xuXHRcdHZhciBlID0gbmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIiArIG1vZHVsZUlkICsgXCInXCIpO1xuXHRcdGUuY29kZSA9ICdNT0RVTEVfTk9UX0ZPVU5EJztcblx0XHR0aHJvdyBlO1xuXHR9XG5cdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG5cdHZhciBtb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdID0ge1xuXHRcdC8vIG5vIG1vZHVsZS5pZCBuZWVkZWRcblx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuXHRcdGV4cG9ydHM6IHt9XG5cdH07XG5cblx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG5cdF9fd2VicGFja19tb2R1bGVzX19bbW9kdWxlSWRdKG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG5cdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG5cdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn1cblxuIiwiLy8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbl9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuXHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cblx0XHRmdW5jdGlvbigpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcblx0XHRmdW5jdGlvbigpIHsgcmV0dXJuIG1vZHVsZTsgfTtcblx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgeyBhOiBnZXR0ZXIgfSk7XG5cdHJldHVybiBnZXR0ZXI7XG59OyIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIGRlZmluaXRpb24pIHtcblx0Zm9yKHZhciBrZXkgaW4gZGVmaW5pdGlvbikge1xuXHRcdGlmKF9fd2VicGFja19yZXF1aXJlX18ubyhkZWZpbml0aW9uLCBrZXkpICYmICFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywga2V5KSkge1xuXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIGtleSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGRlZmluaXRpb25ba2V5XSB9KTtcblx0XHR9XG5cdH1cbn07IiwiX193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqLCBwcm9wKSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKTsgfSIsIi8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG5cdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG5cdH1cblx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbn07IiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307IiwiaW1wb3J0ICcuL3BhcnRzL3dpZGdldHMvd2lkZ2V0c190YWJzJzsiXSwibmFtZXMiOlsiUk9PVF9TRUxFQ1RPUiIsIklOSVRfRkxBRyIsImFjdGl2YXRlIiwicm9vdCIsInRhYiIsInRhYnMiLCJBcnJheSIsImZyb20iLCJxdWVyeVNlbGVjdG9yQWxsIiwicGFuZWxzIiwidGFyZ2V0SWQiLCJnZXRBdHRyaWJ1dGUiLCJ0YXJnZXRQYW5lbCIsInF1ZXJ5U2VsZWN0b3IiLCJDU1MiLCJlc2NhcGUiLCJmb3JFYWNoIiwidCIsImlzQWN0aXZlIiwiY2xhc3NMaXN0IiwidG9nZ2xlIiwic2V0QXR0cmlidXRlIiwidGFiSW5kZXgiLCJwIiwicmVtb3ZlQXR0cmlidXRlIiwiaW5pdE9uZSIsImRhdGFzZXQiLCJ0YWJsaXN0IiwibGVuZ3RoIiwiYWRkRXZlbnRMaXN0ZW5lciIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImN1cnJlbnRJbmRleCIsImZpbmRJbmRleCIsIm5leHRJbmRleCIsImtleSIsImZvY3VzIiwiZG9jdW1lbnQiLCJhY3RpdmVFbGVtZW50IiwiaW5jbHVkZXMiLCJwcmVBY3RpdmUiLCJmaW5kIiwiY29udGFpbnMiLCJib290QWxsIiwiYmluZFdpZGdldEV2ZW50cyIsIm9ic2VydmUiLCJtbyIsIk11dGF0aW9uT2JzZXJ2ZXIiLCJkb2N1bWVudEVsZW1lbnQiLCJjaGlsZExpc3QiLCJzdWJ0cmVlIiwicmVhZHlTdGF0ZSJdLCJpZ25vcmVMaXN0IjpbXSwic291cmNlUm9vdCI6IiJ9