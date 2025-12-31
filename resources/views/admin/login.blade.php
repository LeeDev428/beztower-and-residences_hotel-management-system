<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Beztower & Residences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .login-left {
            background: linear-gradient(135deg, #d4af37 0%, #f4e4c1 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #2c2c2c;
        }

        .logo-container {
            width: 100px;
            height: 100px;
            background: rgba(44, 44, 44, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            font-size: 3rem;
            color: #2c2c2c;
        }

        .login-left h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 1rem;
            font-family: 'Georgia', serif;
        }

        .login-left p {
            font-size: 1.1rem;
            opacity: 0.8;
            line-height: 1.6;
        }

        .login-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 2rem;
            color: #2c2c2c;
            margin-bottom: 0.5rem;
            font-weight: 300;
        }

        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #d4af37;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 2.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .error-message {
            background: #fee;
            border-left: 4px solid #e53e3e;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }

        .error-message p {
            color: #c53030;
            font-size: 0.9rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .demo-credentials {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
        }

        .demo-credentials h4 {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .demo-credentials p {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .demo-credentials strong {
            color: #d4af37;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #d4af37;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #c49d2f;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-left {
                padding: 2rem;
            }

            .login-left h1 {
                font-size: 2rem;
            }

            .login-right {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="logo-container">
                <i class="fas fa-gem logo-icon"></i>
            </div>
            <h1>BEZTOWER</h1>
            <p>Luxury Hotel Management System</p>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h2>Admin Login</h2>
                <p>Access the management dashboard</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <p><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            placeholder="Enter your email" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            placeholder="Enter your password">
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="demo-credentials">
                <h4>Demo Credentials</h4>
                <p><strong>Admin:</strong> admin@beztower.com / admin123</p>
                <p><strong>Manager:</strong> manager@beztower.com / manager123</p>
                <p><strong>Receptionist:</strong> receptionist@beztower.com / receptionist123</p>
            </div>

            <div class="back-link">
                <a href="{{ route('home') }}">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
