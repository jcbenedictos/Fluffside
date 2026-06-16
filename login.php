<?php
session_start();

if (!file_exists('db.inc.php')) {
    die("<h2 style='color:red; text-align:center; padding:50px;'>CRITICAL ERROR: db.inc.php file is missing! Please create it.</h2>");
}

require_once 'db.inc.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error_msg = '';
$success_msg = '';
$action_taken = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_taken = $_POST['action'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($action_taken === 'signup') {
        $first_name = trim($_POST['first_name']);
        $last_name  = trim($_POST['last_name']);
        $full_name  = $first_name . ' ' . $last_name;
        $phone      = trim($_POST['phone']);
        $dob        = trim($_POST['dob']);
        $confirm    = $_POST['confirm_password'];

        if ($password !== $confirm) {
            $error_msg = "Passwords do not match!";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT user_id FROM tbl_users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $error_msg = "An account with that email already exists!";
                } else {

                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $insert_stmt = $pdo->prepare("INSERT INTO tbl_users (full_name, email, phone, dob, password_hash) VALUES (?, ?, ?, ?, ?)");
                    $insert_stmt->execute([$full_name, $email, $phone, $dob, $hashed_password]);
                    
                    $success_msg = "Account created successfully! Please log in.";
                    $action_taken = 'login';
                }
            } catch(PDOException $e) {

                $error_msg = "MySQL Error: " . $e->getMessage();
                $action_taken = 'signup';
            }
        }
    } 

    elseif ($action_taken === 'login') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tbl_users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['is_active'] == 0) {
                    $error_msg = "This account has been deactivated.";
                } else {
                    $name_parts = explode(' ', $user['full_name'], 2);
                    
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id']   = $user['user_id'];
                    $_SESSION['first_name']= $name_parts[0];
                    $_SESSION['last_name'] = $name_parts[1] ?? '';
                    $_SESSION['email']     = $user['email'];
                    $_SESSION['phone']     = $user['phone'] ?? '';
                    $_SESSION['dob']       = $user['dob'] ?? '';
                    $_SESSION['address']   = $user['address'] ?? '';
                    $_SESSION['role']      = $user['role'] ?? 'User';
                    $_SESSION['profile_pic']= $user['profile_photo'] ?? '';

                    session_regenerate_id(true); 
                    $session_id = session_id();
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $expires_at = date('Y-m-d H:i:s', time() + (60));// 1 minute lang just for testing purposes

                    $sess_stmt = $pdo->prepare("INSERT INTO tbl_sessions (session_id, user_id, ip_address, expires_at) VALUES (?, ?, ?, ?)");
                    $sess_stmt->execute([$session_id, $user['user_id'], $ip_address, $expires_at]);

                    // Admin users go to the admin panel; regular users go to homepage
                    if (($_SESSION['role'] ?? 'User') === 'Admin') {
                        header("Location: admin/index.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit;
                }
            } else {
                $error_msg = "Invalid email or password!";
            }
        } catch(PDOException $e) {
            $error_msg = "MySQL Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FluffSide - Log In / Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-orange: #EF8E35;
            --primary-hover: #D67A26;
            --bg-light: #FDFBF5;
            --text-dark: #5A483E;
            --accent-yellow: #F6D884;
            --accent-green-light: #E1E8B8;
            --white: #FFFFFF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Nunito', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }
        
        .container { max-width: 100%; margin: 0; padding: 0 5%; width: 100%; display: flex; flex-direction: column; min-height: 100vh; }
        header { padding: 15px 0; width: 100%; }
        .logo-img { height: 250px; width: auto; mix-blend-mode: multiply; margin-top: -100px; margin-bottom: -100px; }

        .alert-box { padding: 15px; border-radius: 8px; text-align: center; font-weight: 800; margin-bottom: 20px; }
        .alert-error { background-color: #F8E1DF; color: #C0392B; border: 2px solid #E6B0AA; }
        .alert-success { background-color: #E1E8B8; color: #5A6B31; border: 2px solid #C5D192; }

        .auth-wrapper { display: flex; justify-content: space-evenly; align-items: center; flex: 1; gap: 100px; padding-bottom: 60px; }
        .auth-visuals { flex: 1; position: relative; display: flex; justify-content: center; align-items: center; min-height: 500px; }
        .auth-blob { position: absolute; width: 450px; height: 450px; background-color: var(--accent-green-light); border-radius: 45% 55% 40% 60% / 55% 45% 60% 45%; z-index: 0; }
        .auth-image { position: relative; z-index: 2; width: 100%; max-width: 1000px; transition: opacity 0.3s ease; transform: translate(25px, 1px); }

        .floating-paw { position: absolute; color: var(--primary-orange); z-index: 1; }
        .paw-1 { top: 5%; left: 15%; font-size: 24px; transform: rotate(-15deg); }
        .paw-2 { top: 45%; left: 0%; font-size: 20px; transform: rotate(-30deg); }
        .paw-3 { bottom: 15%; left: 5%; font-size: 16px; transform: rotate(-10deg); color: var(--accent-yellow); }
        .paw-4 { top: 35%; right: 5%; font-size: 28px; transform: rotate(20deg); }
        .paw-5 { bottom: 25%; right: -5%; font-size: 24px; transform: rotate(15deg); }
        .paw-6 { bottom: 10%; right: 15%; font-size: 14px; transform: rotate(5deg); color: var(--accent-yellow); }

        .auth-card { flex: 0 0 500px; background-color: var(--bg-light); border-radius: 20px; padding: 50px; position: relative; border: 2px solid #EAE3D9; box-shadow: 4px 8px 24px rgba(0, 0, 0, 0.06); }
        .auth-header { text-align: center; margin-bottom: 40px; }
        .auth-header h2 { font-size: 24px; font-weight: 900; color: var(--text-dark); text-transform: uppercase; margin-bottom: 10px; }
        .auth-header p { font-size: 14px; font-weight: 600; color: var(--text-dark); }

        .form-group { margin-bottom: 25px; position: relative; }
        .form-label { display: block; font-size: 14px; font-weight: 800; margin-bottom: 8px; color: var(--text-dark); }
        .form-control { width: 100%; padding: 14px 15px; background-color: var(--white); border-radius: 8px; font-size: 14px; color: var(--text-dark); border: 1px solid #F1CC9F; outline: none; transition: all 0.3s ease; }
        .form-control::placeholder { color: #CFC5BC; font-weight: 600; }
        .form-control:focus { border-color: var(--primary-orange); }

        .forgot-pass { display: block; text-align: right; font-size: 12px; font-weight: 700; color: var(--primary-orange); text-decoration: none; margin-top: -15px; margin-bottom: 25px; }
        .forgot-pass:hover { color: var(--primary-hover); text-decoration: underline; }

        .btn-submit { width: 100%; background-color: var(--primary-orange); color: var(--white); padding: 15px; border-radius: 8px; border: none; font-size: 16px; font-weight: 800; cursor: pointer; transition: background-color 0.2s ease; margin-bottom: 20px; }
        .btn-submit:hover { background-color: var(--primary-hover); }
        .btn-submit:active { transform: translateY(2px); }

        .auth-footer { text-align: center; font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .auth-footer a { color: var(--primary-orange); text-decoration: none; font-weight: 800; }
        .auth-footer a:hover { text-decoration: underline; }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <a href="index.php"><img src="Assets/Fluffside.png" alt="Logo" class="logo-img" onerror="this.outerHTML='<h1 style=&quot;color:#EF8E35&quot;>FluffSide</h1>'"></a>
        </header>

        <?php if($error_msg): ?>
            <div class="alert-box alert-error"><i class="fas fa-exclamation-triangle"></i> <?= $error_msg ?></div>
        <?php elseif($success_msg): ?>
            <div class="alert-box alert-success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'login_required'): ?>
            <div class="alert-box alert-error"><i class="fas fa-lock"></i> Please log in to access the website.</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'inactive'): ?>
            <div class="alert-box alert-error"><i class="fas fa-clock"></i> You were logged out due to 30 seconds of inactivity.</div>
        <?php endif; ?>

        <div class="auth-wrapper">
            <div class="auth-visuals">
                <i class="fas fa-paw floating-paw paw-1"></i><i class="fas fa-paw floating-paw paw-2"></i><i class="fas fa-paw floating-paw paw-3"></i><i class="fas fa-paw floating-paw paw-4"></i><i class="fas fa-paw floating-paw paw-5"></i><i class="fas fa-paw floating-paw paw-6"></i>
                <img src="Assets/log in.png" alt="Animals" class="auth-image" id="img-login" onerror="this.style.display='none'">
                <img src="Assets/sign up.png" alt="Animals" class="auth-image hidden" id="img-signup" onerror="this.style.display='none'">
            </div>

            <div class="auth-card">
                
                <div id="form-login">
                    <div class="auth-header"><h2>WELCOME BACK!</h2><p>Log in to continue your journey with Fluffside.</p></div>
                    <form action="login.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="jcbenedictos@gmail.com" required></div>
                        <div class="form-group"><label class="form-label">Password</label><div style="position:relative;"><input type="password" name="password" id="login-password" class="form-control" placeholder="********" required style="padding-right:45px;"><span onclick="togglePassword('login-password', this)" style="position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;color:#8E8279;"><i class="fas fa-eye"></i></span></div></div>
                        <a href="#" class="forgot-pass">Forgot Password?</a>
                        <button type="submit" class="btn-submit">Log In</button>
                    </form>
                    <div class="auth-footer">Don't have an account? <a href="#" id="toggle-to-signup">Sign up</a></div>
                </div>

                <div id="form-signup" class="hidden">
                    <div class="auth-header"><h2>CREATE YOUR ACCOUNT</h2><p>Join Fluffside and give the residents the home they deserve</p></div>
                    <form action="login.php" method="POST">
                        <input type="hidden" name="action" value="signup">
                        <div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" placeholder="Juan" required></div>
                        <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required></div>
                        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" id="signup-email" class="form-control" placeholder="juan@gmail.com" required><span id="email-error" style="display:none;color:#C0392B;font-size:12px;font-weight:700;margin-top:4px;"><i class="fas fa-exclamation-circle"></i> Please enter a valid Gmail address (e.g. juan@gmail.com)</span></div>
                        <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" name="phone" id="signup-phone" class="form-control" placeholder="09XXXXXXXXX" required><span id="phone-error" style="display:none;color:#C0392B;font-size:12px;font-weight:700;margin-top:4px;"><i class="fas fa-exclamation-circle"></i> Please enter a valid 11-digit phone number (e.g. 09XXXXXXXXX)</span></div>
                        <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control" required></div>
                        <div class="form-group"><label class="form-label">Password</label><div style="position:relative;"><input type="password" name="password" id="signup-password" class="form-control" placeholder="********" required style="padding-right:45px;"><span onclick="togglePassword('signup-password', this)" style="position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;color:#8E8279;"><i class="fas fa-eye"></i></span></div></div>
                        <div class="form-group"><label class="form-label">Confirm Password</label><div style="position:relative;"><input type="password" name="confirm_password" id="signup-confirm" class="form-control" placeholder="********" required style="padding-right:45px;"><span onclick="togglePassword('signup-confirm', this)" style="position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;color:#8E8279;"><i class="fas fa-eye"></i></span></div></div>
                        <button type="submit" class="btn-submit">Sign Up</button>
                    </form>
                    <div class="auth-footer">Already have an account? <a href="#" id="toggle-to-login">Log In</a></div>
                </div>

            </div>
        </div>
    </div>

    <script>

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            const i = icon.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                i.classList.remove('fa-eye');
                i.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                i.classList.remove('fa-eye-slash');
                i.classList.add('fa-eye');
            }
        }

        document.querySelector('#form-signup form').addEventListener('submit', function(e) {
            let valid = true;

            const phone = document.getElementById('signup-phone');
            const phoneError = document.getElementById('phone-error');
            if (!/^09\d{9}$/.test(phone.value.trim())) {
                phoneError.style.display = 'block';
                valid = false;
            } else {
                phoneError.style.display = 'none';
            }

            const email = document.getElementById('signup-email');
            const emailError = document.getElementById('email-error');
            if (!/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email.value.trim())) {
                emailError.style.display = 'block';
                valid = false;
            } else {
                emailError.style.display = 'none';
            }

            if (!valid) e.preventDefault();
        });

        const loginForm = document.getElementById('form-login');
        const signupForm = document.getElementById('form-signup');
        const imgLogin = document.getElementById('img-login');
        const imgSignup = document.getElementById('img-signup');

        function showSignup() {
            loginForm.classList.add('hidden'); imgLogin.classList.add('hidden');
            signupForm.classList.remove('hidden'); imgSignup.classList.remove('hidden');
        }

        function showLogin() {
            signupForm.classList.add('hidden'); imgSignup.classList.add('hidden');
            loginForm.classList.remove('hidden'); imgLogin.classList.remove('hidden');
        }

        document.getElementById('toggle-to-signup').addEventListener('click', function(e) {
            e.preventDefault(); showSignup();
        });

        document.getElementById('toggle-to-login').addEventListener('click', function(e) {
            e.preventDefault(); showLogin();
        });

        <?php if($action_taken === 'signup' && $error_msg): ?>
            showSignup();
        <?php endif; ?>
    </script>
</body>
</html>

<!-- for clean comments -->