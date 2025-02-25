<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 450px;
            max-width: 90%;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #3498db;
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }
        
        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            background-color: #fff;
        }
        
        /* Validation styles */
        input.valid {
            border-color: #2ecc71;
            background-color: rgba(46, 204, 113, 0.05);
        }
        
        input.invalid {
            border-color: #e74c3c;
            background-color: rgba(231, 76, 60, 0.05);
        }
        
        .validation-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            width: 20px;
            height: 20px;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: none;
            font-weight: 500;
        }
        
        .password-strength {
            height: 5px;
            margin-top: 8px;
            border-radius: 3px;
            background-color: #ecf0f1;
            position: relative;
            overflow: hidden;
        }
        
        .password-strength-meter {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .password-feedback {
            font-size: 12px;
            margin-top: 5px;
            color: #7f8c8d;
        }
        
        .file-input-wrapper {
            position: relative;
            margin-top: 5px;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 15px;
            background-color: #f0f0f0;
            color: #555;
            border-radius: 8px;
            cursor: pointer;
            font-weight: normal;
            border: 1px dashed #ccc;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            background-color: #e3e3e3;
            border-color: #3498db;
        }
        
        .file-input-label span {
            margin-left: 8px;
        }
        
        .file-name {
            font-size: 13px;
            margin-top: 5px;
            color: #3498db;
            word-break: break-all;
        }
        
        input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        
        button, .back-button {
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        button:hover, .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        
        button:active, .back-button:active {
            transform: translateY(1px);
        }
        
        button.submit-btn {
            background: linear-gradient(to right,rgb(65, 156, 216), #2980b9);
            color: white;
        }
        
        button.submit-btn:hover {
            background: linear-gradient(to right, #2980b9, #2980b9);
        }
        
        button.submit-btn:disabled {
            background: linear-gradient(to right,rgb(65, 156, 216), #2980b9);
            cursor: not-allowed;
            transform: none;
            opacity: 0.7;
        }
        
        .back-button {
            background: linear-gradient(to right, #f6f9fc, #e9f0f7);
            color: #2c3e50;
            border: 1px solid #e1e8ed;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .back-button:hover {
            background: linear-gradient(to right, #e9f0f7, #d5e1ed);
            color: #1a5276;
        }
        
        .back-button .back-icon {
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        
        .back-button:hover .back-icon {
            transform: translateX(-3px);
        }
        
        .back-button::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: -100%;
            background: linear-gradient(90deg, 
                rgba(255,255,255,0) 0%, 
                rgba(255,255,255,0.2) 50%, 
                rgba(255,255,255,0) 100%);
            transition: left 0.7s ease;
        }
        
        .back-button:hover::after {
            left: 100%;
        }
        
        .form-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Profile Information</h2>
        <form id="profileForm" action="updatebase_profile.php" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
                <svg class="validation-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>
                <div class="error-message" id="email-error">Please enter a valid email address</div>
            </div>
            
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter Your Current Password" required>
                <svg class="validation-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>
                <div class="error-message" id="current-password-error">Please enter your current password</div>
            </div>
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Your New Password" required>
                <svg class="validation-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>
                <div class="password-strength">
                    <div class="password-strength-meter" id="password-meter"></div>
                </div>
                <div class="password-feedback" id="password-feedback">Password strength: Enter at least 8 characters</div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter Your Phone Number" required>
                <svg class="validation-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>
                <div class="error-message" id="phone-error">Please enter a valid phone number</div>
            </div>
            
            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <div class="file-input-wrapper">
                    <label class="file-input-label" for="profile_photo">
                        <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span>Choose a photo</span>
                    </label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                </div>
                <div class="file-name" id="file-name"></div>
            </div>
            
            <div class="button-group">
                <button type="submit" id="submitBtn" class="submit-btn">Update Profile</button>
                <a href="doctorac.php" class="back-button">
                    <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Profile
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const emailInput = document.getElementById('email');
            const currentPasswordInput = document.getElementById('current_password');
            const passwordInput = document.getElementById('password');
            const phoneInput = document.getElementById('phone');
            const fileInput = document.getElementById('profile_photo');
            const fileName = document.getElementById('file-name');
            const submitBtn = document.getElementById('submitBtn');
            const passwordMeter = document.getElementById('password-meter');
            const passwordFeedback = document.getElementById('password-feedback');
            
            // Validation patterns
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phonePattern = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/;
            
            // Form validation state
            let formValid = {
                email: false,
                currentPassword: false,
                password: false,
                phone: false
            };
            
            // Update submit button state
            function updateSubmitButton() {
                if (formValid.email && formValid.currentPassword && formValid.password && formValid.phone) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
            
            // Set validation status
            function setValidationStatus(element, isValid, errorElement) {
                const iconElement = element.nextElementSibling;
                
                if (isValid) {
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                    iconElement.style.display = 'block';
                    iconElement.innerHTML = '<circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path>';
                    iconElement.style.stroke = '#2ecc71';
                    if (errorElement) errorElement.style.display = 'none';
                } else {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                    iconElement.style.display = 'block';
                    iconElement.innerHTML = '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>';
                    iconElement.style.stroke = '#e74c3c';
                    if (errorElement && element.value !== '') errorElement.style.display = 'block';
                }
            }
            
            // Validate email
            function validateEmail() {
                const isValid = emailPattern.test(emailInput.value);
                formValid.email = isValid;
                setValidationStatus(emailInput, isValid, document.getElementById('email-error'));
                updateSubmitButton();
            }
            
            // Validate current password
            function validateCurrentPassword() {
                const isValid = currentPasswordInput.value.length > 0;
                formValid.currentPassword = isValid;
                setValidationStatus(currentPasswordInput, isValid, document.getElementById('current-password-error'));
                updateSubmitButton();
                
                // If new password is same as current password, mark new password as invalid
                if (passwordInput.value && currentPasswordInput.value === passwordInput.value) {
                    formValid.password = false;
                    setValidationStatus(passwordInput, false);
                    passwordFeedback.textContent = 'New password must be different from current password';
                    passwordFeedback.style.color = '#e74c3c';
                    passwordMeter.style.width = '25%';
                    passwordMeter.style.backgroundColor = '#e74c3c';
                } else {
                    // Re-validate new password
                    validatePassword();
                }
            }
            
            // Validate new password
            function validatePassword() {
                const password = passwordInput.value;
                const currentPassword = currentPasswordInput.value;
                
                // Check if new password is the same as current password
                if (currentPassword && password === currentPassword) {
                    formValid.password = false;
                    setValidationStatus(passwordInput, false);
                    passwordFeedback.textContent = 'New password must be different from current password';
                    passwordFeedback.style.color = '#e74c3c';
                    passwordMeter.style.width = '25%';
                    passwordMeter.style.backgroundColor = '#e74c3c';
                    updateSubmitButton();
                    return;
                }
                
                let strength = 0;
                let feedback = '';
                
                if (password.length >= 8) {
                    strength += 25;
                }
                
                if (password.match(/[A-Z]/)) {
                    strength += 25;
                }
                
                if (password.match(/[0-9]/)) {
                    strength += 25;
                }
                
                if (password.match(/[^A-Za-z0-9]/)) {
                    strength += 25;
                }
                
                // Update password meter
                passwordMeter.style.width = strength + '%';
                
                // Set color based on strength
                if (strength < 25) {
                    passwordMeter.style.backgroundColor = '#e74c3c';
                    feedback = 'Very weak - Use at least 8 characters';
                } else if (strength < 50) {
                    passwordMeter.style.backgroundColor = '#e67e22';
                    feedback = 'Weak - Add uppercase letters';
                } else if (strength < 75) {
                    passwordMeter.style.backgroundColor = '#f1c40f';
                    feedback = 'Medium - Add numbers';
                } else if (strength < 100) {
                    passwordMeter.style.backgroundColor = '#2ecc71';
                    feedback = 'Strong - Add special characters';
                } else {
                    passwordMeter.style.backgroundColor = '#27ae60';
                    feedback = 'Very strong password!';
                }
                
                passwordFeedback.textContent = 'Password strength: ' + feedback;
                passwordFeedback.style.color = '#7f8c8d';
                
                formValid.password = strength >= 50; // Consider password valid if medium strength or better
                setValidationStatus(passwordInput, formValid.password);
                updateSubmitButton();
            }
            
            // Validate phone
            function validatePhone() {
                const isValid = phonePattern.test(phoneInput.value);
                formValid.phone = isValid;
                setValidationStatus(phoneInput, isValid, document.getElementById('phone-error'));
                updateSubmitButton();
            }
            
            // Handle file selection
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    fileName.textContent = fileInput.files[0].name;
                    
                    // Basic image validation
                    const file = fileInput.files[0];
                    const fileType = file.type;
                    const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    
                    if (!validImageTypes.includes(fileType)) {
                        fileName.textContent = 'Invalid file type! Please select an image.';
                        fileName.style.color = '#e74c3c';
                    } else if (file.size > 5000000) { // 5MB limit
                        fileName.textContent = 'File is too large! Max size is 5MB.';
                        fileName.style.color = '#e74c3c';
                    } else {
                        fileName.style.color = '#3498db';
                    }
                } else {
                    fileName.textContent = '';
                }
            });
            
            // Add input event listeners
            emailInput.addEventListener('input', validateEmail);
            currentPasswordInput.addEventListener('input', validateCurrentPassword);
            passwordInput.addEventListener('input', validatePassword);
            phoneInput.addEventListener('input', validatePhone);
            
            // Add blur event listeners for better UX
            emailInput.addEventListener('blur', validateEmail);
            currentPasswordInput.addEventListener('blur', validateCurrentPassword);
            passwordInput.addEventListener('blur', validatePassword);
            phoneInput.addEventListener('blur', validatePhone);
            
            // Disable submit button initially
            submitBtn.disabled = true;
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                // Perform one final validation before submission
                validateEmail();
                validateCurrentPassword();
                validatePassword();
                validatePhone();
                
                if (!formValid.email || !formValid.currentPassword || !formValid.password || !formValid.phone) {
                    e.preventDefault();
                    // Highlight all invalid fields
                    if (!formValid.email) {
                        emailInput.focus();
                    } else if (!formValid.currentPassword) {
                        currentPasswordInput.focus();
                    } else if (!formValid.password) {
                        passwordInput.focus();
                    } else if (!formValid.phone) {
                        phoneInput.focus();
                    }
                }
            });
        });
    </script>
</body>
</html>