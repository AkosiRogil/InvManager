<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register - SB Admin 2</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-gradient: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            min-height: 600px;
        }

        .register-image {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .register-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(78, 115, 223, 0.8), rgba(34, 74, 190, 0.8));
        }

        .register-image-content {
            text-align: center;
            color: white;
            z-index: 1;
            position: relative;
        }

        .register-image-content i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .register-image-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .register-image-content p {
            font-size: 0.9rem;
            opacity: 0.8;
            max-width: 200px;
        }

        .register-form {
            flex: 1;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-title p {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background-color: #f8f9fc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            background-color: white;
        }

        .btn-register {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .btn-register:hover {
            background: linear-gradient(180deg, #2e59d9 10%, #1b4aaa 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .form-links {
            text-align: center;
        }

        .form-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-links a:hover {
            color: #224abe;
            text-decoration: underline;
        }

        .form-links .divider {
            margin: 1rem 0;
            position: relative;
            text-align: center;
        }

        .form-links .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e3e6f0;
        }

        .form-links .divider span {
            background: white;
            padding: 0 1rem;
            color: var(--secondary-color);
            font-size: 0.8rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                max-width: 400px;
                min-height: auto;
            }

            .register-image {
                min-height: 200px;
            }

            .register-image-content h3 {
                font-size: 1.25rem;
            }

            .register-image-content i {
                font-size: 3rem;
            }

            .register-form {
                padding: 2rem 1.5rem;
            }
        }

        /* Form validation styles */
        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .form-control.is-valid {
            border-color: var(--success-color);
        }

        .password-strength {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .password-weak { color: var(--danger-color); }
        .password-medium { color: var(--warning-color); }
        .password-strong { color: var(--success-color); }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Left side image -->
        <div class="register-image d-none d-md-flex">
            <div class="register-image-content">
                <i class="fas fa-warehouse"></i>
                <h3>INVMANAGER</h3>
            </div>
        </div>

        <!-- Right side form -->
        <div class="register-form">
            <div class="form-title">
                <h1>Create Account</h1>
                <p>Fill in the details below to get started</p>
            </div>

            <form action="server/api/register.php" method="POST" id="registerForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" name="user_name" placeholder="User Name" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="password" class="form-control" name="repeat_password" id="repeatPassword" placeholder="Confirm Password" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Account
                </button>
            </form>

            <div class="form-links">
                <div class="divider">
                    <span>OR</span>
                </div>
                <div class="mb-2">
                </div>
                <div>
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Already have an account? Login!
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    feedback = 'Weak password';
                    return { strength: 'weak', feedback };
                case 2:
                    feedback = 'Medium password';
                    return { strength: 'medium', feedback };
                case 3:
                case 4:
                    feedback = 'Strong password';
                    return { strength: 'strong', feedback };
            }
        }

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const repeatPassword = document.getElementById('repeatPassword').value;
            
            if (password !== repeatPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length > 0) {
                const result = checkPasswordStrength(password);
                strengthDiv.textContent = result.feedback;
                strengthDiv.className = `password-strength password-${result.strength}`;
            } else {
                strengthDiv.textContent = '';
                strengthDiv.className = 'password-strength';
            }
        });

        // Confirm password validation
        document.getElementById('repeatPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const repeatPassword = this.value;
            
            if (repeatPassword.length > 0) {
                if (password === repeatPassword) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    </script>
</body>
</html>