<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .error-message { background: rgba(255,0,0,0.1); color: #ff6b6b; padding: 10px 15px; border-radius: 5px; margin: 15px 0; text-align: center; border: 1px solid rgba(255,0,0,0.2); }
        .success-message { background: rgba(0,255,0,0.1); color: #4CAF50; padding: 10px 15px; border-radius: 5px; margin: 15px 0; text-align: center; border: 1px solid rgba(0,255,0,0.2); }
    </style>
</head>
<body>
    <section>
        <form action="{{ route('password.forgot') }}" method="POST">
            @csrf
            <h1>Forgot Password</h1>
            <p style="color:#aaa; font-size:14px; margin-bottom:20px;">Enter your email and we'll send you a password reset link.</p>

            @if ($errors->any())
                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
            @endif

            @if (session('success'))
                <div class="success-message"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif

            <div class="inputbox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder=" " value="{{ old('email') }}" required autofocus>
                <label>Email</label>
            </div>

            <button type="submit">Send Reset Link</button>

            <div class="register" style="margin-top:15px;">
                <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a></p>
            </div>
        </form>
    </section>
</body>
</html>
