<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
</head>
<body style="background:#f6f7fb; font-family:Helvetica,sans-serif; height:600px;width:100%;padding-top:1rem;">
    <h1 style="text-align:center; color:#292928;">Expense Tracker</h1>
    <section class="email-section" style="background: #fff; width:500px; margin: 0 auto; padding: 2rem 3.5rem 3rem 3.5rem;border-radius: 5px;box-shadow: 5px 10px 21px 0px #000000;">
        <h2 class="header" style="color:#292928;margin-bottom: 1rem;">Hello, {{ $details['name'] }}</h2>
        <p class="text" style="color: #3a3a3a;font-size:15px;">You are receiving this email because we received a password reset request for your account.</p>
        <a href="{{ $details['url'] }}" class="btn" target="_blank" style="padding: 14px 25px;border-radius: 7px;display: inline-block;width: fit-content;transition: all .15s ease-in;font-weight: 750;background:#5d61f7;color: #fff;text-decoration: none;font-size:15px;">Reset password</a>
        <p class="text" style="color: #3a3a3a;font-size:15px;">This password reset link will expire in 10 minutes.</p>
        <p class="text" style="color: #3a3a3a;font-size:15px;">If you did not request a password reset, please <a href="mailto:expensetrackerktest@gmail.com?subject=Received suspicious password reset request">contact us</a></p>
        <div class="divider" style="width: 100%;height: 1px;background-color: #e7e7e7;margin-bottom: 1rem;"></div>
        <div class="url" style="line-height: 1.5;font-size: 14px;text-align: left;word-break: break-all;color: #3a3a3a;">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{ $details['url'] }}">{{ $details['url'] }}</a></div>
    </section>
</body>
</html>