<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Beztower Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }
        .card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 12px;
            padding: 1.6rem;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 0.35rem;
            color: #2c2c2c;
        }
        p.note {
            color: #666;
            font-size: 0.94rem;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.45rem;
            color: #2c2c2c;
        }
        input[type="email"] {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem 0.85rem;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }
        .error {
            color: #c53030;
            font-size: 0.88rem;
            margin-bottom: 0.5rem;
        }
        .status {
            background: #ecfdf3;
            border-left: 4px solid #2f855a;
            color: #276749;
            border-radius: 6px;
            padding: 0.85rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .actions {
            display: flex;
            gap: 0.6rem;
            margin-top: 1rem;
        }
        .btn {
            display: inline-block;
            text-align: center;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.92rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            flex: 1;
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Forgot Password</h1>
        <p class="note">Enter your admin email. If your account exists, we will send a secure password reset link.</p>

        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}">
            @csrf
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="admin@beztower.com">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <div class="actions">
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                <a href="{{ route('admin.login') }}" class="btn btn-secondary">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>
