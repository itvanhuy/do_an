<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $login = $request->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $fieldType => $login,
            'password' => $request->password
        ];

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (!Auth::user()->email_verified_at) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Please verify your email before logging in. Check your inbox.',
                ])->onlyInput('login');
            }

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'login' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'agree_terms' => ['accepted']
        ], [
            'username.unique' => 'Tên tài khoản này đã được sử dụng.',
            'username.regex' => 'Tên tài khoản chỉ được chứa chữ cái, số và dấu gạch dưới.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'email_verify_token' => Str::random(64),
        ]);

        $verifyUrl = url('/email/verify/' . $user->email_verify_token . '?email=' . urlencode($user->email));
        Mail::to($user->email)->send(new VerifyEmail($verifyUrl, $user->full_name));

        return redirect()->route('login')->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('home')->with('success', 'Bạn đã đăng xuất thành công.');
        } catch (\Exception $e) {
            // Nếu lỗi session, vẫn điều hướng về home để tránh kẹt trang lỗi
            return redirect('/');
        }
    }

    // --- EMAIL VERIFICATION ---
    public function verifyEmail(Request $request, $token)
    {
        $user = User::where('email_verify_token', $token)
            ->where('email', $request->query('email'))
            ->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['login' => 'Invalid or expired verification link.']);
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Email already verified. Please login.');
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verify_token' => null,
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Email verified successfully! Welcome to TechShop.');
    }

    // --- FORGOT PASSWORD ---
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email này không tồn tại trong hệ thống.']);
        }

        // Xóa token cũ nếu có
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

        Mail::to($request->email)->send(new ResetPasswordMail($resetUrl));

        return back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn.');
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|min:6|confirmed',
        ], [
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Link đặt lại mật khẩu không hợp lệ.']);
        }

        // Kiểm tra hết hạn 60 phút
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Link đặt lại mật khẩu đã hết hạn. Vui lòng thử lại.']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.');
    }

    // --- GOOGLE LOGIN ---
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        return $this->handleSocialCallback('google');
    }

    // --- FACEBOOK LOGIN ---
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->scopes(['public_profile', 'email'])->redirect();
    }

    public function handleFacebookCallback()
    {
        return $this->handleSocialCallback('facebook');
    }

    /**
     * Xử lý logic chung cho đăng nhập mạng xã hội
     */
    protected function handleSocialCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['login' => "Không thể đăng nhập bằng $provider. Vui lòng thử lại."]);
        }

        $email = $socialUser->getEmail();
        $socialId = $socialUser->getId();
        $providerIdField = $provider . '_id';

        // Bước 1: Tìm chính xác theo provider_id
        $user = User::where($providerIdField, $socialId)->first();

        if (!$user && $email) {
            $existingByEmail = User::where('email', $email)->first();

            if ($existingByEmail) {
                $otherProviders = array_diff(['google', 'facebook'], [$provider]);
                $linkedToOther = false;

                foreach ($otherProviders as $other) {
                    if (!empty($existingByEmail->{$other . '_id'})) {
                        $linkedToOther = true;
                        break;
                    }
                }

                if ($linkedToOther) {
                    // Email này đã được dùng bởi provider khác — không cho phép
                    return redirect()->route('login')->withErrors([
                        'login' => 'Email này đã được liên kết với một phương thức đăng nhập khác.'
                    ]);
                }

                // Email tồn tại nhưng chưa liên kết provider nào → link vào
                $user = $existingByEmail;
            }
        }

        if ($user) {
            $user->update([
                $providerIdField => $socialId,
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        } else {
            // Tạo user mới
            $name = $socialUser->getName() ?? 'User';
            $baseUsername = Str::slug($name, '_') ?: 'user';
            $username = $baseUsername . '_' . Str::random(5);

            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . '_' . Str::random(5);
            }

            $user = User::create([
                'full_name'      => $name,
                'email'          => $email,
                'username'       => $username,
                'password'       => Hash::make(Str::random(16)),
                $providerIdField => $socialId,
                'avatar'         => $socialUser->getAvatar(),
                'role'           => 'user',
                'is_active'      => 1,
            ]);
        }

        Auth::login($user, true);
        return redirect()->intended('/');
    }
}
