<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            border: 1px solid rgba(255, 0, 0, 0.2);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-message {
            background: rgba(0, 255, 0, 0.1);
            color: #4CAF50;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            border: 1px solid rgba(0, 255, 0, 0.2);
        }
    </style>
</head>

<body>
    <section>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <h1>Login</h1>

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="inputbox">
                <i class="fas fa-user-circle"></i>
                <input type="text" name="login" placeholder=" " value="{{ old('login') }}" required>
                <label for="">Email or Username</label>
            </div>

            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder=" " required>
                <label for="">Password</label>
            </div>

            <div class="forget">
                <label for="remember">
                    <input type="checkbox" id="remember" name="remember"> Remember Me
                </label>
                <a href="{{ route('password.forgot.form') }}">Forgot Password?</a>
            </div>

            <button type="submit">Login</button>

            <div class="social-login" style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 15px;">
                <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Or login with</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <a href="{{ route('login.google') }}" style="background: #db4437; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">
                        <i class="fab fa-google"></i> Google
                    </a>
                    <a href="{{ route('login.facebook') }}" style="background: #4267B2; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                </div>
            </div>

            <div class="register">
                <p>Don't have an account? <a href="{{ route('register') }}">Register Now</a></p>
                <!-- Back to Home -->
                <p style="margin-top: 10px;"><a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </form>
    </section>
</body>

</html>
