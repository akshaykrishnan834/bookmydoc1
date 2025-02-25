<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Signup</title>
    <style>
        /* General Styles */
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        /* Form Container */
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 450px;
            max-width: 90%;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Section */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .logo-container h2 {
            margin: 0;
            font-size: 1.8rem;
            color: #0077b6;
        }

        .logo-container h5 {
            font-size: 1rem;
            color: #666;
            margin-top: 5px;
        }

        h3 {
            color: #444;
            margin-bottom: 20px;
        }

        /* Floating Label Form Group */
        .form-group {
            position: relative;
            margin-bottom: 15px;
            text-align: left;
        }

        input {
            display: block;
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
            margin: 0 auto;
        }

        input:focus {
            border-color: #0077b6;
            box-shadow: 0 0 8px rgba(0, 119, 182, 0.5);
        }

        input.valid {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }

        input.invalid {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        label {
            position: absolute;
            top: 10px;
            left: 10px;
            color: #777;
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        input:focus + label,
        input:not(:placeholder-shown) + label {
            top: -15px;
            left: 5px;
            font-size: 0.9rem;
            color: rgb(0, 0, 0);
        }

        input.valid + label {
            color: #28a745;
        }

        input.invalid + label {
            color: #dc3545;
        }

        .error {
            color: #ff4d4f;
            font-size: 0.9em;
            margin-bottom: 15px;
            padding: 8px;
            background-color: #fff2f0;
            border-radius: 5px;
            border: 1px solid #ffccc7;
            text-align: center;
        }

        /* Buttons */
        button {
            width: 100%;
            padding: 12px;
            background-color: #0077b6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #005f99;
        }

        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .already-account {
            margin-top: 15px;
            font-size: 0.9em;
            text-align: center;
        }

        .already-account a {
            color: #0077b6;
            text-decoration: none;
        }

        .already-account a:hover {
            text-decoration: underline;
        }

        /* Google Sign Up Button */
        .google-btn {
            width: 100%;
            padding: 12px;
            background-color: #db4437;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }

        .google-btn img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .google-btn:hover {
            background-color: #c1351d;
        }
        .back-to-home a {
            display: inline-flex;
            align-items: center;
            padding: 15px 9px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #0077b6, #1e90ff);
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .back-to-home a:hover {
            background: linear-gradient(135deg, #005f8d, #1565c0);
            transform: scale(1.05);
        }

        .home-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
            <div>
                <h2>BookMyDoc</h2>
                <h5>Online Doctor Appointment Booking</h5>
            </div>
        </div>

        <h3>Create Your Account As Doctor</h3>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
            <div class="error">
                <strong>Email already exists!</strong> Please use a different email or login with your existing account.
            </div>
        <?php endif; ?>

        <form method="post" action="Doctorconnect.php" id="signupForm" novalidate>
            <div class="form-group">
                <input type="text" id="name" name="name" placeholder=" " required>
                <label for="name">&nbsp Name:</label>
            </div>

            <div class="form-group">
                <input type="text" id="phone" name="phone" placeholder=" " required>
                <label for="phone">&nbsp Contact Number:</label>
            </div>

            <div class="form-group">
                <input type="email" id="email" name="email" placeholder=" " required>
                <label for="email">&nbsp Email:</label>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">&nbsp Password:</label>
            </div>

            <div class="form-group">
                <input type="password" id="confirm_password" name="confirm_password" placeholder=" " required>
                <label for="confirm_password">&nbsp Confirm Password:</label>
            </div>

            <button type="submit" id="submitBtn">Create Account</button>

            <div class="already-account">
                <p>Already have an account? <a href="doctorlog.php">Log in</a></p>
            </div>
            <div class="back-to-home">
                <a href="home.php">
                &nbsp<img src="images/home_738822.png" alt="Home" class="home-icon">Home</a>
                <a href="role.php">
                &nbsp<img src="images/staff.png" alt="Home" class="home-icon">Role</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('signupForm');
            const nameInput = document.getElementById('name');
            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const submitBtn = document.getElementById('submitBtn');

            // Validation patterns
            const patterns = {
                name: /^[a-zA-Z\s]{3,30}$/,
                phone: /^[0-9]{10}$/,
                email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
            };

            // Function to validate a single input
            function validateInput(input, pattern, isConfirmPassword = false) {
                let isValid = false;
                
                if (isConfirmPassword) {
                    isValid = input.value === passwordInput.value && input.value !== '';
                } else {
                    isValid = pattern.test(input.value);
                }
                
                if (isValid) {
                    input.classList.add('valid');
                    input.classList.remove('invalid');
                } else {
                    input.classList.add('invalid');
                    input.classList.remove('valid');
                }
                
                return isValid;
            }

            // Function to check if all inputs are valid
            function checkFormValidity() {
                const nameValid = validateInput(nameInput, patterns.name);
                const phoneValid = validateInput(phoneInput, patterns.phone);
                const emailValid = validateInput(emailInput, patterns.email);
                const passwordValid = validateInput(passwordInput, patterns.password);
                const confirmPasswordValid = validateInput(confirmPasswordInput, null, true);
                
                // Enable/disable submit button based on form validity
                submitBtn.disabled = !(nameValid && phoneValid && emailValid && passwordValid && confirmPasswordValid);
            }

            // Add event listeners for real-time validation
            nameInput.addEventListener('input', function() {
                validateInput(nameInput, patterns.name);
                checkFormValidity();
            });
            
            phoneInput.addEventListener('input', function() {
                validateInput(phoneInput, patterns.phone);
                checkFormValidity();
            });
            
            emailInput.addEventListener('input', function() {
                validateInput(emailInput, patterns.email);
                checkFormValidity();
            });
            
            passwordInput.addEventListener('input', function() {
                validateInput(passwordInput, patterns.password);
                // Also validate confirm password if it has a value
                if (confirmPasswordInput.value !== '') {
                    validateInput(confirmPasswordInput, null, true);
                }
                checkFormValidity();
            });
            
            confirmPasswordInput.addEventListener('input', function() {
                validateInput(confirmPasswordInput, null, true);
                checkFormValidity();
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                // Validate all fields before submission
                const nameValid = validateInput(nameInput, patterns.name);
                const phoneValid = validateInput(phoneInput, patterns.phone);
                const emailValid = validateInput(emailInput, patterns.email);
                const passwordValid = validateInput(passwordInput, patterns.password);
                const confirmPasswordValid = validateInput(confirmPasswordInput, null, true);
                
                // If any field is invalid, prevent form submission
                if (!(nameValid && phoneValid && emailValid && passwordValid && confirmPasswordValid)) {
                    e.preventDefault();
                }
            });

            // Initial form check
            checkFormValidity();
        });
    </script>
</body>
</html>