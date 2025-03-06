
/**
 * Modern JavaScript Helper Functions
 * 
 * @package PHP-Bin
 * @version 2.0.0
 */

// Document ready function compatible with modern jQuery
jQuery(document).ready(function($) {
    // Initialize tooltips if Bootstrap is available
    if (typeof $.fn.tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    } else {
        console.log("Bootstrap tooltip function not available");
    }

    // Handle text area auto-resize
    $('.auto-resize').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Copy to clipboard functionality
    $('.copy-btn').on('click', function() {
        const textToCopy = $(this).data('clipboard-text');
        if (textToCopy) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    // Success message
                    const btn = $(this);
                    const originalText = btn.text();
                    btn.text('Copied!');
                    setTimeout(function() {
                        btn.text(originalText);
                    }, 2000);
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                });
            } else {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = textToCopy;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    const btn = $(this);
                    const originalText = btn.text();
                    btn.text('Copied!');
                    setTimeout(function() {
                        btn.text(originalText);
                    }, 2000);
                } catch (err) {
                    console.error('Fallback: Could not copy text: ', err);
                }
                document.body.removeChild(textarea);
            }
        }
    });

    // Handle form submission with AJAX
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                if (form.data('success-message')) {
                    showMessage(form.data('success-message'), 'success');
                }
                if (form.data('redirect')) {
                    window.location.href = form.data('redirect');
                }
            },
            error: function(xhr, status, error) {
                showMessage('An error occurred: ' + error, 'error');
            }
        });
    });
});

// Helper function to show messages
function showMessage(message, type = 'info') {
    let alertClass = 'alert-info';
    if (type === 'error') alertClass = 'alert-error';
    if (type === 'success') alertClass = 'alert-success';
    
    const alertHtml = `
        <div class="alert ${alertClass} fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ${message}
        </div>
    `;
    
    jQuery('#message-container').html(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        jQuery('#message-container .alert').alert('close');
    }, 5000);
}

// Modern alternative to the old resizeIt function
function resizeTextArea(element) {
    element.style.height = 'auto';
    element.style.height = (element.scrollHeight) + 'px';
}

// Safe polyfill for older browsers to get URL parameters
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Legacy support for older code using $ function
if (typeof $ !== 'function') {
    function $(id) {
        return document.getElementById(id);
    }
}

// Legacy support for Event.observe
if (typeof Event === 'undefined' || typeof Event.observe !== 'function') {
    var Event = Event || {};
    Event.observe = function(element, eventName, callback) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        if (element) {
            element.addEventListener(eventName, callback, false);
        }
    };
}
