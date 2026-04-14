@extends('layouts.fitlife')

@section('title', 'Login | SkillUp')

@section('content')

<style>
.auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f6f7f8;
    padding: 100px 20px 40px;
    font-family: var(--ff-rubik);
}

.auth-card {
    background: #fff;
    width: 100%;
    max-width: 440px;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    padding: 40px;
    border: 1px solid #edeff1;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h1 {
    font-size: 2.8rem;
    font-weight: 700;
    color: #1c1c1c;
    margin-bottom: 8px;
}

.auth-header p {
    font-size: 1.5rem;
    color: #878a8c;
}

.auth-form-group {
    margin-bottom: 20px;
}

.auth-form-group label {
    display: block;
    font-size: 1.3rem;
    font-weight: 700;
    color: #3c3c3c;
    margin-bottom: 8px;
}

.auth-input {
    width: 100%;
    padding: 12px 16px;
    font-size: 1.5rem;
    color: #1c1c1c;
    background: #f6f7f8;
    border: 1px solid #edeff1;
    border-radius: 4px;
    transition: 0.2s;
}

.auth-input:focus {
    outline: none;
    background: #fff;
    border-color: #ff4500;
    box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
}

.auth-btn {
    width: 100%;
    padding: 14px;
    background: #ff4500;
    color: #fff;
    font-size: 1.5rem;
    font-weight: 700;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: 0.2s;
    margin-top: 10px;
}

.auth-btn:hover {
    background: #e03d00;
}

.auth-links {
    margin-top: 24px;
    text-align: center;
    font-size: 1.4rem;
    color: #878a8c;
}

.auth-links a {
    color: #ff4500;
    font-weight: 700;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}

.auth-error {
    background: #fdf5f5;
    color: #d93d3d;
    padding: 12px;
    border-radius: 4px;
    font-size: 1.3rem;
    margin-bottom: 20px;
    border-left: 4px solid #d93d3d;
}

.auth-remember {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    cursor: pointer;
    user-select: none;
}

.auth-remember input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff4500;
}

.auth-remember span {
    font-size: 1.4rem;
    color: #3c3c3c;
}

</style>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Ready to continue learning?</p>
        </div>

        @if($errors->any())
            <div class="auth-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="auth-form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="auth-input" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="auth-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="auth-input" required>
            </div>

            <label class="auth-remember" for="remember">
                <input type="checkbox" name="remember" id="remember">
                <span>Remember me</span>
            </label>

            <button type="submit" class="auth-btn">Sign In</button>
        </form>

        <div class="auth-links">
            Don't have an account? <a href="{{ route('register') }}">Sign Up</a>
        </div>
    </div>
</div>

@endsection
