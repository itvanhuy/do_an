<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .alert { padding: 12px; border-radius: 5px; margin: 15px 0; text-align: center; font-size: 14px; }
        .alert-error { background: rgba(255,0,0,0.1); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.2); }
        .alert-success { background: rgba(0,255,0,0.1); color: #4CAF50; border: 1px solid rgba(0,255,0,0.2); }
        .field-error { color: #ff6b6b; font-size: 12px; margin-top: 4px; display: block; }
        .inputbox { position: relative; }
        /* Đẩy input sang trái để không bị icon che */
        .inputbox input { padding-right: 30px !important; }
        /* Icon cố định bên phải giữa input */
        .inputbox > i { top: 5px !important; }
        /* Toggle password */
        .toggle-password {
            position: absolute;
            right: 8px;
            top: 5px;
            cursor: pointer;
            color: #fff;
            font-size: 1.2em;
            z-index: 10;
            line-height: 1;
        }
        .toggle-password:hover { color: #e94560; }
        /* Ẩn icon lock mặc định khi có toggle */
        .has-toggle > i { display: none; }
        .password-strength { margin-top: 6px; height: 4px; border-radius: 2px; background: #444; overflow: hidden; }
        .password-strength-bar { height: 100%; width: 0; transition: all 0.3s; border-radius: 2px; }
        .strength-text { font-size: 11px; margin-top: 3px; display: block; }
    </style>
</head>
<body>
    <section>
        <form action="{{ route('register') }}" method="POST" id="registerForm">
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
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" id="full_name" placeholder=" " required value="{{ old('full_name') }}" autofocus>
                <label>Full Name</label>
                @error('full_name')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="inputbox">
                <i class="fas fa-id-badge"></i>
                <input type="text" name="username" id="username" placeholder=" " required value="{{ old('username') }}">
                <label>Username</label>
                @error('username')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="inputbox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder=" " required value="{{ old('email') }}">
                <label>Email</label>
            </div>

            <div class="inputbox has-toggle">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label>Password</label>
                <span class="toggle-password" onclick="togglePassword('password', this)">
                    <i class="fas fa-eye"></i>
                </span>
                <div class="password-strength"><div class="password-strength-bar" id="strengthBar"></div></div>
                <span class="strength-text" id="strengthText"></span>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="inputbox has-toggle">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" " required>
                <label>Confirm Password</label>
                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                    <i class="fas fa-eye"></i>
                </span>
                <span class="field-error" id="confirmError"></span>
            </div>

            <div class="forget">
                <label>
                    <input type="checkbox" name="agree_terms" required>
                    I agree to the <a href="#" style="color:#e94560;">Terms & Conditions</a>
                </label>
                @error('agree_terms')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <button type="submit">Register</button>

            <div class="register">
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
                <p style="margin-top:10px;"><a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </form>
    </section>

    <script>
        function togglePassword(id, el) {
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

        // Password strength
        document.getElementById('password').addEventListener('input', function () {
            const val = this.value;
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            let strength = 0;
            if (val.length >= 6) strength++;
            if (val.match(/[A-Z]/)) strength++;
            if (val.match(/[0-9]/)) strength++;
            if (val.match(/[^A-Za-z0-9]/)) strength++;

            const levels = [
                { width: '0%', color: '', label: '' },
                { width: '25%', color: '#e74c3c', label: 'Weak' },
                { width: '50%', color: '#e67e22', label: 'Fair' },
                { width: '75%', color: '#f1c40f', label: 'Good' },
                { width: '100%', color: '#2ecc71', label: 'Strong' },
            ];
            bar.style.width = levels[strength].width;
            bar.style.background = levels[strength].color;
            text.textContent = levels[strength].label;
            text.style.color = levels[strength].color;
        });

        // Confirm password match
        document.getElementById('password_confirmation').addEventListener('input', function () {
            const err = document.getElementById('confirmError');
            err.textContent = this.value !== document.getElementById('password').value
                ? 'Passwords do not match.' : '';
        });
    </script>
</body>
</html>
