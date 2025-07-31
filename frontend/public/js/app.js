// CareConnect JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navAuth = document.querySelector('.nav-auth');

    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navAuth.classList.toggle('active');
        });
    }

    // User dropdown menu
    const userMenuBtn = document.querySelector('.user-menu-btn');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            userDropdown.classList.remove('active');
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    showError(field, 'This field is required');
                } else {
                    field.classList.remove('error');
                    removeError(field);
                }
            });

            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('error');
                    showError(field, 'Please enter a valid email address');
                }
            });

            // Password confirmation validation
            const passwordField = form.querySelector('input[name="password"]');
            const confirmPasswordField = form.querySelector('input[name="password_confirmation"]');
            
            if (passwordField && confirmPasswordField) {
                if (passwordField.value !== confirmPasswordField.value) {
                    isValid = false;
                    confirmPasswordField.classList.add('error');
                    showError(confirmPasswordField, 'Passwords do not match');
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Real-time form validation
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });

    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (isValidEmail(email)) {
                showNotification('Thank you for subscribing to our newsletter!', 'success');
                this.reset();
            } else {
                showNotification('Please enter a valid email address.', 'error');
            }
        });
    }

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-hide notifications
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    });

    // Hospital search functionality
    const hospitalSearch = document.getElementById('hospital-search');
    if (hospitalSearch) {
        hospitalSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const hospitalCards = document.querySelectorAll('.hospital-card');
            
            hospitalCards.forEach(card => {
                const hospitalName = card.querySelector('.hospital-name').textContent.toLowerCase();
                const hospitalLocation = card.querySelector('.hospital-location').textContent.toLowerCase();
                
                if (hospitalName.includes(searchTerm) || hospitalLocation.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Appointment date picker
    const appointmentDate = document.getElementById('appointment_date');
    if (appointmentDate) {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        appointmentDate.setAttribute('min', today);
    }

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.style.maxWidth = '200px';
                    preview.style.marginTop = '10px';
                    
                    const container = input.parentElement;
                    const existingPreview = container.querySelector('img');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    container.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

// Utility functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateField(field) {
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        field.classList.add('error');
        showError(field, 'This field is required');
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        field.classList.add('error');
        showError(field, 'Please enter a valid email address');
        return false;
    }
    
    if (field.type === 'tel' && value && !isValidPhone(value)) {
        field.classList.add('error');
        showError(field, 'Please enter a valid phone number');
        return false;
    }
    
    field.classList.remove('error');
    removeError(field);
    return true;
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function showError(field, message) {
    removeError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.color = 'var(--danger-color)';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorDiv);
}

function removeError(field) {
    const errorDiv = field.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '1rem 1.5rem';
    notification.style.borderRadius = '0.375rem';
    notification.style.color = 'white';
    notification.style.fontWeight = '500';
    notification.style.zIndex = '9999';
    notification.style.transform = 'translateX(100%)';
    notification.style.transition = 'transform 0.3s ease';
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.style.backgroundColor = 'var(--success-color)';
            break;
        case 'error':
            notification.style.backgroundColor = 'var(--danger-color)';
            break;
        case 'warning':
            notification.style.backgroundColor = 'var(--warning-color)';
            break;
        default:
            notification.style.backgroundColor = 'var(--primary-color)';
    }
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// AJAX helper function
function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    resolve(xhr.responseText);
                }
            } else {
                reject(xhr.statusText);
            }
        };
        
        xhr.onerror = function() {
            reject(xhr.statusText);
        };
        
        if (data) {
            xhr.send(JSON.stringify(data));
        } else {
            xhr.send();
        }
    });
}

// Export functions for global use
window.CareConnect = {
    showNotification,
    ajaxRequest,
    validateField
}; 