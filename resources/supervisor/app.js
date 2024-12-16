// resources/js/supervisor/app.js

// Import CSS dependencies
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './app.css'; // Corrected path to app.css

// Import JS dependencies
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import toastr from 'toastr';

// Ensure the DOM is fully loaded before executing scripts
document.addEventListener('DOMContentLoaded', () => {
    console.log('Supervisor App Loaded');

    // Initialize Toastr options if needed
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
    };
});
