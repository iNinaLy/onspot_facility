// Import CSS file
import '../css/app.css';

// Import any JavaScript libraries you need (like Alpine.js)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import 'alpinejs';

// Your JavaScript code...
console.log('Main Application Loaded');
// resources/js/app.js

import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // Bootstrap JS
import toastr from 'toastr'; // Toastr JS
import 'toastr/build/toastr.min.css'; // Toastr CSS
import $ from 'jquery'; // jQuery

// Initialize Toastr Configuration
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right", // Position of the toast
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000", // Duration the toast is displayed
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$(document).ready(function() {
    // Initialize Bootstrap Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle Search Input Redirection for Multiple Forms
    $('.search-input').on('input', function () {
        const currentInput = $(this);
        const currentValue = currentInput.val().trim();
        const redirectRoute = currentInput.data('route');

        if (currentValue === '') {
            window.location.href = redirectRoute;
        }
    });

    // Toastr Notifications
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    @if(session('warning'))
        toastr.warning("{{ session('warning') }}");
    @endif

    @if(session('info'))
        toastr.info("{{ session('info') }}");
    @endif
});

// Debounce function to limit the rate of function calls
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Handle Search Input Redirection with Debounce
$('.search-input').on('input', debounce(function () {
    const currentInput = $(this);
    const currentValue = currentInput.val().trim();
    const redirectRoute = currentInput.data('route');

    if (currentValue === '') {
        window.location.href = redirectRoute;
    }
}, 300));
