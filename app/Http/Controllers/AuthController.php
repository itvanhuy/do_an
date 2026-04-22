<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

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
            'is_active' => 1
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Đăng ký thành công!');
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
        return Socialite::driver('facebook')->redirect();
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
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['login' => "Không thể đăng nhập bằng $provider. Vui lòng thử lại."]);
        }

        // Tìm user theo social_id hoặc email
        $user = User::where($provider . '_id', $socialUser->getId())
                    ->orWhere('email', $socialUser->getEmail())
                    ->first();

        if ($user) {
            // Cập nhật ID nếu user tồn tại nhưng chưa có social_id
            $user->update([$provider . '_id' => $socialUser->getId()]);
        } else {
            // Tạo user mới nếu chưa tồn tại
            $user = User::create([
                'full_name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'username' => Str::slug($socialUser->getName(), '_') . '_' . Str::random(5),
                'password' => Hash::make(Str::random(16)), // Mật khẩu ngẫu nhiên
                $provider . '_id' => $socialUser->getId(),
                'role' => 'user',
                'is_active' => 1
            ]);
        }

        Auth::login($user);
        return redirect()->intended('/');
    }
}
