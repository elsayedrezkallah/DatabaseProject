// Function to toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
}

// Function to toggle confirm password visibility
function toggleConfirmPassword() {
    const confirmPasswordInput = document.getElementById('confirmPassword');
    confirmPasswordInput.type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
}

// Function to show error message
function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    document.getElementById('successMessage').style.display = 'none';
}

// Function to show success message
function showSuccess(message) {
    const successDiv = document.getElementById('successMessage');
    successDiv.textContent = message;
    successDiv.style.display = 'block';
    document.getElementById('errorMessage').style.display = 'none';
}

// Function to validate form
function validateForm(formData) {
    console.log('Validating form data:', {
        name: formData.get('name'),
        email: formData.get('email'),
        password: formData.get('password').length > 0 ? '(password provided)' : '(no password)'
    });
    
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirmPassword');

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address');
        return false;
    }

    // Password validation
    if (password.length < 6) {
        showError('Password must be at least 6 characters long');
        return false;
    }

    // Confirm password validation
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return false;
    }

    return true;
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    const form = document.getElementById('signupForm');
    
    if (form) {
        console.log('Found signup form, attaching event listener');
        
        form.addEventListener('submit', async function(e) {
            console.log('Form submit event triggered');
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            if (!validateForm(formData)) {
                console.log('Form validation failed');
                return;
            }
            
            console.log('Form validation passed, attempting to submit to signup_debug.php');
            
            try {
                // Use the correct relative path
                const response = await fetch('signup_debug.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response received:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    showSuccess('Account created successfully! Redirecting to login...');
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showError(data.message || 'An error occurred during signup');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showError('An error occurred. Please try again later.');
            }
        });
    } else {
        console.error('Could not find signup form!');
    }
}); 
