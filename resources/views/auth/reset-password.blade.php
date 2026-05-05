<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .error-message { background: rgba(255,0,0,0.1); color: #ff6b6b; padding: 10px 15px; border-radius: 5px; margin: 15px 0; text-align: center; border: 1px solid rgba(255,0,0,0.2); }
        .inputbox { position: relative; margin: 30px 0; width: 100%; border-bottom: 2px solid #fff; }
        .inputbox input { width: 100%; height: 30px; background: transparent; border: none; outline: none; font-size: 1rem; color: #fff; padding-right: 30px; }
        .inputbox label { position: absolute; top: 50%; left: 5px; transform: translateY(-50%); color: #fff; font-size: 1rem; pointer-events: none; transition: all 0.3s; }
        .inputbox input:focus ~ label,
        .inputbox input:valid ~ label,
        .inputbox input:not(:placeholder-shown) ~ label { top: -10px; font-size: 0.85rem; }
        .toggle-eye { position: absolute; right: 8px; top: 5px; cursor: pointer; color: #fff; font-size: 1rem; z-index: 10; }
        .toggle-eye:hover { color: #e94560; }
    </style>
</head>
<body>
    <section>
        <form action="{{ route('password.reset') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <h1>Reset Password</h1>

            @if ($errors->any())
                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
            @endif

            <div class="inputbox">
                <input type="password" name="password" id="password" placeholder=" " required minlength="6">
                <label>New Password</label>
                <span class="toggle-eye" onclick="togglePass('password', this)"><i class="fas fa-eye"></i></span>
            </div>

            <div class="inputbox">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" " required minlength="6">
                <label>Confirm Password</label>
                <span class="toggle-eye" onclick="togglePass('password_confirmation', this)"><i class="fas fa-eye"></i></span>
            </div>

            <button type="submit">Reset Password</button>

            <div class="register" style="margin-top:15px;">
                <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a></p>
            </div>
        </form>
    </section>

    <script>
        function togglePass(id, el) {
            const input = document.getElementById(id);
            const icon = el.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
