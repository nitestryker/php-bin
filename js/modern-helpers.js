
/**
 * Modern Compatibility Layer
 * 
 * This file provides compatibility with older JavaScript libraries
 * that may be used in the application.
 */

// Prototype.js Event.observe compatibility
if (typeof Event === 'undefined') {
    var Event = {};
}

if (typeof Event.observe !== 'function') {
    Event.observe = function(element, eventName, handler) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        if (element) {
            element.addEventListener(eventName, handler, false);
        }
    };
}

// Fix other potential compatibility issues
document.observe = function(eventName, handler) {
    document.addEventListener(eventName, handler, false);
};

Element.prototype.observe = function(eventName, handler) {
    this.addEventListener(eventName, handler, false);
};

// Add any $ function compatibility if jQuery is loaded
if (typeof jQuery !== 'undefined' && typeof $ === 'undefined') {
    window.$ = function(id) {
        if (typeof id === 'string') {
            return document.getElementById(id);
        }
        return id;
    };
}
