// resources/js/supervisor/dashboard.js

// Import CSS dependencies
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './../supervisor/dashboard.css'; // Corrected path to CSS

// Import JS dependencies
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Initialize Toastr for notifications
import toastr from 'toastr';

// Ensure the DOM is fully loaded before executing scripts
document.addEventListener('DOMContentLoaded', () => {
    console.log('Supervisor Dashboard Loaded');

    // Initialize Toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showDuration": "300",
        "hideDuration": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Display Toastr notifications based on session messages
    const successMessage = document.querySelector('[data-success]');
    const errorMessage = document.querySelector('[data-error]');

    if (successMessage) {
        toastr.success(successMessage.getAttribute('data-success'));
    }

    if (errorMessage) {
        toastr.error(errorMessage.getAttribute('data-error'));
    }

    // IntersectionObserver for fade-in animations
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    });

    // Observe all elements with the 'fade-in' class
    document.querySelectorAll('.fade-in').forEach(element => {
        observer.observe(element);
    });

    // Smooth scroll to top functionality
    const scrollToTopButton = document.getElementById('scrollToTopButton');
    if (scrollToTopButton) {
        scrollToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Task Cards Click Handlers
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach(card => {
        card.addEventListener('click', () => {
            // Navigate based on data attribute
            const targetUrl = card.getAttribute('data-target-url');
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });
    });
    
    // Attach toggleDetails function to the window object for global access
    window.toggleDetails = function(id) {
        const details = document.getElementById(`details-${id}`);
        if (details) {
            // Toggle display
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'block';
            } else {
                details.style.display = 'none';
            }

            // Optional: Add smooth transition
            details.classList.toggle('hidden');
            details.classList.toggle('block');
        }
    };
});
