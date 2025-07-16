
<?php
if (isset($_GET['success'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if ($_GET['success'] == 0): ?>
            alert("❌ Wrong Credentials!");
        <?php endif; ?>
    });
</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Login</title>

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
            --google-color: #dd4b39;
            --facebook-color: #3b5998;
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

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            min-height: 600px;
        }

        .login-image {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(78, 115, 223, 0.8), rgba(34, 74, 190, 0.8));
        }

        .login-image-content {
            text-align: center;
            color: white;
            z-index: 1;
            position: relative;
        }

        .login-image-content i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .login-image-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-image-content p {
            font-size: 0.9rem;
            opacity: 0.8;
            max-width: 200px;
        }

        .login-form {
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

        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-container input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-right: 0.5rem;
            accent-color: var(--primary-color);
        }

        .checkbox-container label {
            color: var(--secondary-color);
            font-size: 0.85rem;
            cursor: pointer;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #224abe;
            text-decoration: underline;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            background: linear-gradient(180deg, #2e59d9 10%, #1b4aaa 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .btn-social {
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-google {
            background: var(--google-color);
        }

        .btn-google:hover {
            background: #c23321;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(221, 75, 57, 0.3);
        }

        .btn-facebook {
            background: var(--facebook-color);
        }

        .btn-facebook:hover {
            background: #2d4373;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 89, 152, 0.3);
        }

        .btn-social i {
            margin-right: 0.5rem;
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
            .login-container {
                flex-direction: column;
                max-width: 400px;
                min-height: auto;
            }

            .login-image {
                min-height: 200px;
            }

            .login-image-content h3 {
                font-size: 1.25rem;
            }

            .login-image-content i {
                font-size: 3rem;
            }

            .login-form {
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
    </style>
</head> 
<body>
    <div class="login-container">
        <!-- Left side image -->
        <div class="login-image d-none d-md-flex">
            <div class="login-image-content">
                <i class="fas fa-sign-in-alt"></i>
                <h3>Welcome Back!</h3>
                <p>Sign in to your account and continue your journey.</p>
            </div>
        </div>

        <!-- Right side form -->
        <div class="login-form">
            <div class="form-title">
                <h1>Welcome Back!</h1>
                <p>Please sign in to your account</p>
                
                <!-- Error message display -->
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Success message from registration -->
                <?php if (isset($_SESSION['registration_success'])): ?>
                    <div class="alert alert-success" role="alert">
                        Registration successful! Please log in.
                    </div>
                    <?php unset($_SESSION['registration_success']); ?>
                <?php endif; ?>
            </div>

            <form id="loginForm" action="server/api/login.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="user" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>

                <div class="remember-me">
                    <div class="checkbox-container">
                        <input type="checkbox" id="rememberMe" name="remember">
                        <label for="rememberMe">Remember Me</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>
            </form>

            <div class="form-links">
                <div class="divider">
                    <span></span>
                </div>
       
                <div>
                    <a href="register.php">Create an Account!</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="user"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields!');
                return false;
            }
        });
    </script>
</body>
</html>
