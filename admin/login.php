<?php
require_once dirname(__DIR__) . '/includes/auth.php';
admin_session_start();
if (admin_logged_in()) { header('Location: /admin/'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (admin_login($email, $pass)) {
        header('Location: /admin/');
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIDIS Admin — Login</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#0f0f18;color:#e0e0e0;display:flex;align-items:center;justify-content:center;min-height:100vh}
.login{background:#1a1a2e;border-radius:12px;padding:40px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.5)}
.login__logo{text-align:center;margin-bottom:32px}
.login__logo svg{color:#fff}
h1{font-size:20px;font-weight:600;text-align:center;margin-bottom:24px;color:#fff}
label{display:block;font-size:13px;color:#999;margin-bottom:6px}
input{display:block;width:100%;padding:10px 14px;background:#0f0f18;border:1px solid #333;border-radius:8px;color:#fff;font-size:14px;margin-bottom:16px;outline:none}
input:focus{border-color:#772885}
button{width:100%;padding:12px;background:#772885;border:none;border-radius:8px;color:#fff;font-size:15px;font-weight:600;cursor:pointer;margin-top:8px}
button:hover{background:#8e35a0}
.error{background:#ff4444;color:#fff;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
</style>
</head>
<body>
<div class="login">
    <div class="login__logo">
        <svg width="100" height="26" viewBox="0 0 115 29" fill="none"><path d="M53.6506 13.8293C53.6506 14.7154 53.3599 15.4805 52.7785 16.1294C52.1971 16.7759 51.3877 17.2736 50.3458 17.6155C49.3062 17.9597 48.1131 18.1318 46.7665 18.1318C46.3897 18.1318 45.9292 18.1132 45.3827 18.076C44.8361 18.0388 44.1105 17.9342 43.2058 17.7644C42.3011 17.5946 41.3592 17.3411 40.3801 17.0085V13.5339C41.2987 13.9758 42.2011 14.3456 43.0872 14.6386C43.9733 14.9316 44.9245 15.0782 45.9385 15.0782C46.8735 15.0782 47.4782 14.9572 47.7549 14.7154C48.0317 14.4735 48.1689 14.2479 48.1689 14.0362C48.1689 13.6548 47.934 13.3339 47.4619 13.0711C46.9898 12.8083 46.3037 12.5385 45.4036 12.2594C44.4082 11.9315 43.5337 11.5756 42.7825 11.1919C42.0313 10.8081 41.4173 10.3244 40.9406 9.74529C40.4638 9.16618 40.2266 8.48242 40.2266 7.69167C40.2266 6.90093 40.4661 6.2381 40.9475 5.62178C41.429 5.00546 42.1616 4.51241 43.15 4.14495C44.1384 3.77748 45.3432 3.59375 46.7688 3.59375C47.7828 3.59375 48.7201 3.6705 49.5806 3.824C50.4411 3.97749 51.1505 4.1496 51.711 4.34031C52.2715 4.53102 52.6552 4.67986 52.8645 4.78917V8.1103C52.1226 7.694 51.2947 7.32421 50.3807 6.99628C49.4667 6.66835 48.4899 6.50555 47.4503 6.50555C46.7688 6.50555 46.2758 6.60789 45.9688 6.81255C45.6618 7.01721 45.5106 7.26839 45.5106 7.56841C45.5106 7.8475 45.6641 8.08937 45.9688 8.29403C46.2734 8.4987 46.82 8.74755 47.6061 9.04059C49.0038 9.55225 50.1225 10.0104 50.9621 10.4197C51.8017 10.8291 52.4575 11.3012 52.9343 11.8361C53.4111 12.371 53.6483 13.0362 53.6483 13.8339Z" fill="white"/><path d="M63.5419 17.9472H58.0625V3.78125H63.5419V17.9472Z" fill="white"/><path d="M3.47226 4.24281L13.3833 0.63547C18.8536-1.35553 24.9108 1.46899 26.9018 6.93921L7.0796 14.1539C4.34339 15.1498 1.31479 13.7375 0.318896 11.0013C-0.676204 8.26731 0.738242 5.23791 3.47226 4.24281Z" fill="white"/><path d="M23.8629 24.4425L13.9518 28.0498C8.48159 30.0408 2.42438 27.2163 0.433387 21.7461L20.2555 14.5314C22.9917 13.5355 26.0203 14.9478 27.0162 17.684C28.0121 20.4202 26.5999 23.4488 23.8637 24.4447Z" fill="#772885"/></svg>
    </div>
    <h1>Admin Panel</h1>
    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required autofocus placeholder="admin@sidis.agency">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">
        <button type="submit">Sign In</button>
    </form>
</div>
</body>
</html>
