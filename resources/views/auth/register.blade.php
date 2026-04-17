<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .forget {
            margin-bottom: 15px;
        }
        
        .forget label {
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <section>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <h1>Register</h1>
            
            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            <div class="inputbox">
                <i class="fas fa-id-badge"></i>
                <input type="text" name="username" placeholder=" " required value="{{ old('username') }}">
                <label for="">Username</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" placeholder=" " required value="{{ old('full_name') }}">
                <label for="">Full Name</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder=" " required value="{{ old('email') }}">
                <label for="">Email</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder=" " required>
                <label for="">Password</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="password_confirmation" placeholder=" " required>
                <label for="">Confirm Password</label>
            </div>
            
            <div class="forget">
                <label>
                    <input type="checkbox" name="agree_terms" required>
                    I agree to the Terms & Conditions
                </label>
            </div>
            
            <button type="submit">Register</button>
            
            <div class="register">
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
                <!-- Back to Home -->
                <p style="margin-top: 10px;"><a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </form>
    </section>

    <script>
        document.querySelector('input[name="full_name"]').focus();
    </script>
</body>
</html>
