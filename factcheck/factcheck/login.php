<?php
require 'config/db.php';
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM USER WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "ইমেইল বা পাসওয়ার্ড ভুল।";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_register'])) {
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $role = in_array($_POST['role'], ['user', 'fact_checker']) ? $_POST['role'] : 'user';

    if ($user_name === "" || $email === "" || $password === "") {
        $error = "সব ফিল্ড পূরণ করুন।";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM USER WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "এই ইমেইল দিয়ে আগে থেকেই একটা অ্যাকাউন্ট আছে।";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO USER (user_name, email, role, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_name, $email, $role, $hashed]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $user_name;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit();
        }
    }
}

$activeTab = isset($_POST['do_register']) ? 'register' : 'login';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>TruthCheck - Sign In</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-card {
            max-width: 380px;
            width: 100%;
            margin: 20px;
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 14px 45px rgba(0,0,0,0.18);
            overflow: hidden;
        }
        .auth-logo-circle {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal), var(--navy));
            display: flex; align-items: center; justify-content: center;
            margin: 30px auto 8px;
            color: white;
        }
        .auth-title { text-align: center; font-size: 19px; font-weight: 700; color: var(--navy); margin: 0 0 2px; }
        .auth-subtitle { text-align: center; font-size: 12px; color: var(--text-muted); margin: 0 0 20px; }
        .tab-switch { display: flex; margin: 0 24px 20px; background: #f0f2f6; border-radius: 10px; padding: 4px; }
        .tab-switch button {
            flex: 1; background: transparent; color: var(--text-muted);
            border: none; padding: 9px; border-radius: 8px; font-weight: 600; font-size: 13px;
            cursor: pointer;
        }
        .tab-switch button.active { background: white; color: var(--navy); box-shadow: 0 1px 4px rgba(0,0,0,0.12); }
        .tab-panel { display: none; padding: 0 24px 28px; flex-direction: column; gap: 12px; }
        .tab-panel.active { display: flex; }
        .auth-footnote { text-align: center; font-size: 11px; color: var(--text-muted); padding: 0 24px 22px; }
    </style>
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-logo-circle">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="2"/>
                    <line x1="15.4" y1="15.4" x2="21" y2="21" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                    <path d="M7.5 10.5L9.5 12.5L13.5 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="auth-title">TruthCheck</div>
            <div class="auth-subtitle">Verify claims, together</div>

            <div class="tab-switch">
                <button type="button" id="tabLoginBtn" class="<?= $activeTab === 'login' ? 'active' : '' ?>" onclick="showTab('login')">Sign In</button>
                <button type="button" id="tabRegisterBtn" class="<?= $activeTab === 'register' ? 'active' : '' ?>" onclick="showTab('register')">Register</button>
            </div>

            <?php if ($error): ?><p class="error" style="margin:0 24px 14px"><?= htmlspecialchars($error) ?></p><?php endif; ?>

            <form method="POST" id="loginPanel" class="tab-panel <?= $activeTab === 'login' ? 'active' : '' ?>">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="do_login">Sign In</button>
            </form>

            <form method="POST" id="registerPanel" class="tab-panel <?= $activeTab === 'register' ? 'active' : '' ?>">
                <input type="text" name="user_name" placeholder="Full Name" required>
                <input type="email" name="reg_email" placeholder="Email" required>
                <input type="password" name="reg_password" placeholder="Password" required>
                <select name="role">
                    <option value="user">User (Claim submit করবেন)</option>
                    <option value="fact_checker">Fact Checker (Verdict দেবেন)</option>
                </select>
                <button type="submit" name="do_register">Register</button>
            </form>

            <div class="auth-footnote">Claims Today, Clarity Tomorrow.</div>
        </div>
    </div>
    <script>
        function showTab(tab) {
            document.getElementById('loginPanel').classList.toggle('active', tab === 'login');
            document.getElementById('registerPanel').classList.toggle('active', tab === 'register');
            document.getElementById('tabLoginBtn').classList.toggle('active', tab === 'login');
            document.getElementById('tabRegisterBtn').classList.toggle('active', tab === 'register');
        }
    </script>
</body>
</html>
