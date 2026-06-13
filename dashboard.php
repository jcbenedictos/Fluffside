<<<<<<< HEAD
<?php 
session_start();
require_once 'db.inc.php';

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;


 if (!$is_logged_in) {
     header("Location: login.php?msg=login_required");
     exit; 
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - Dashboard | FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

    <div class="container">
       <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?> 

        <div class="dash-header-section">
            <h1>My Applications</h1>
            <p>Track your adoption and foster applications in real time.</p>
        </div>

        <main class="dashboard-layout">
            
            <div class="dash-main">
                
                <div class="dash-tabs">
                    <button class="tab-btn active">Active Applications</button>
                    <button class="tab-btn">Past Applications</button>
                    <button class="tab-btn">Messages</button>
                </div>

                <div class="app-card">
                    <div class="app-card-top">
                        <div class="app-pet-info">
                            <img src="scout.jpg" alt="Scout" class="app-pet-img" onerror="this.src='placeholder.jpg';">
                            <div class="app-pet-details">
                                <h2>SCOUT</h2>
                                <p>Golden Retriever</p>
                                <span class="tag-type tag-adoption">ADOPTION</span>
                            </div>
                        </div>
                        <div class="app-status-area">
                            <div class="status-badge status-review">Under Review</div>
                            <div class="submit-date">
                                Submitted on
                                <strong>May 14, 2026</strong>
                            </div>
                        </div>
                    </div>

                    <div class="stepper-container">
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Application<br>Submitted</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Reviewed</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step action-yellow">
                            <div class="step-icon"><i class="fas fa-clock"></i></div>
                            <span class="step-label">Online<br>Interview</span>
                        </div>
                        <div class="step-line"></div> <!-- Gray line -->
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Application<br>Approval</span>
                        </div>
                        <div class="step-line"></div>
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Meet and<br>Greet</span>
                        </div>
                        <div class="step-line"></div>
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Take Home</span>
                        </div>
                    </div>

                    <div class="app-card-bottom">
                        <div class="update-msg">
                            <strong>Last Update:</strong> We've reviewed your application and would like to schedule an interview, please check your messages and coordinate with us.
                        </div>
                        <button class="btn-cancel">Cancel</button>
                    </div>
                </div>

                <div class="app-card">
                    <div class="app-card-top">
                        <div class="app-pet-info">
                            <img src="#" alt="#" class="app-pet-img" onerror="this.src='placeholder.jpg';">
                            <div class="app-pet-details">
                                <h2>BENNY</h2>
                                <p>Persian Cat</p>
                                <span class="tag-type tag-foster">FOSTER</span>
                            </div>
                        </div>
                        <div class="app-status-area">
                            <div class="status-badge status-approved">Approved</div>
                            <div class="submit-date">
                                Submitted on
                                <strong>May 11, 2026</strong>
                            </div>
                        </div>
                    </div>

                    <div class="stepper-container">
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Application<br>Submitted</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Reviewed</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Online<br>Interview</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Application<br>Approved</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step action-green">
                            <div class="step-icon"><i class="fas fa-exclamation"></i></div>
                            <span class="step-label">Meet and<br>Greet</span>
                        </div>
                        <div class="step-line"></div>
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Take Home</span>
                        </div>
                    </div>

                    <div class="app-card-bottom">
                        <div class="update-msg">
                            <strong>Last Update:</strong> We've reviewed your application and would like to schedule an interview, please check your messages and coordinate with us.
                        </div>
                        <button class="btn-cancel" disabled>Cancel</button> <!-- Disabled Cancel Button -->
                    </div>
                </div>

            </div>

            <aside class="dash-sidebar">

                <div class="side-card">
                    <h3>Application Summary</h3>
                    
                    <div class="summary-list">
                        <div class="summary-item">
                            <div class="sum-icon icon-orange"><i class="fas fa-file-alt"></i></div>
                            <div class="sum-text">
                                <h4>2</h4>
                                <p>Active Applications</p>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="sum-icon icon-yellow"></div>
                            <div class="sum-text">
                                <h4>1</h4>
                                <p>Approved</p>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="sum-icon icon-green"></div>
                            <div class="sum-text">
                                <h4>1</h4>
                                <p>In Progress</p>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="sum-icon icon-red"><i class="fas fa-times"></i></div>
                            <div class="sum-text">
                                <h4>2</h4>
                                <p>Withdrawn/ Declined</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="side-card">
                    <h3>Quick Links</h3>
                    
                    <div class="quick-links-list">
                        <a href="residents.php" class="btn-quick-link">Browse Available Residents</a>
                        <a href="supplies.php" class="btn-quick-link">Check Out Pet Supplies</a>
                        <a href="help.php" class="btn-quick-link">Help Center</a>
                    </div>
                </div>

            </aside>
        </main>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // 30-second inactivity logout
        let inactivityTimer;
        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(function() {
                window.location.href = 'logout.php?reason=inactive';
            }, 30000);
        }
        ['mousemove','keydown','click','scroll','touchstart'].forEach(function(e) {
=======
<?php
session_start();
require_once 'db.inc.php';
require_once 'db_helper.inc.php';

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
if (!$is_logged_in) {
    header("Location: login.php?msg=login_required");
    exit;
}

// Block admin from viewing user dashboard
if (($_SESSION['role'] ?? 'User') === 'Admin') {
    header("Location: admin/index.php");
    exit;
}

$user_id   = (int)$_SESSION['user_id'];
$user_name = trim($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);

// ── POST: user sends a message ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_message') {
    $app_id = trim($_POST['app_id'] ?? '');
    $msg    = trim($_POST['message'] ?? '');
    $app    = get_application_by_id($app_id);
    if ($app && (int)$app['user_id'] === $user_id && $msg) {
        send_message($app_id, $user_id, 'user', $msg);
    }
    header("Location: dashboard.php?tab=messages&app=" . urlencode($app_id));
    exit;
}

// ── Load user applications ─────────────────────────────────────
$all_apps  = get_applications_by_user($user_id);
$active    = array_filter($all_apps, fn($a) => $a['status'] === 'active' && !$a['rejected']);
$past      = array_filter($all_apps, fn($a) => $a['status'] === 'completed' || $a['rejected']);

$active_count    = count($active);
$approved_count  = count(array_filter($all_apps, fn($a) => (int)$a['current_step'] >= 4 && !$a['rejected']));
$rejected_count  = count(array_filter($all_apps, fn($a) => $a['rejected']));
$completed_count = count(array_filter($all_apps, fn($a) => $a['status'] === 'completed'));

// Active tab from query string
$active_tab  = $_GET['tab'] ?? 'active';
$selected_app_id = $_GET['app'] ?? (count($all_apps) ? $all_apps[0]['id'] : null);
$selected_app    = $selected_app_id ? get_application_by_id($selected_app_id) : null;
// Validate selected app belongs to user
if ($selected_app && (int)$selected_app['user_id'] !== $user_id) {
    $selected_app = null;
}
$chat_msgs = $selected_app ? get_messages_by_app($selected_app['id']) : [];

$steps_label = ['', 'Application Submitted', 'Under Review', 'Interview / Zoom', 'Approved', 'Meet & Greet', 'Take Home'];
$steps_icon  = ['', 'fa-file-alt', 'fa-search', 'fa-video', 'fa-check-circle', 'fa-handshake', 'fa-home'];

// Orders
$user_orders = get_orders_by_user($user_id);
$order_count = count($user_orders);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #EF8E35;
            --primary-hover: #D67A26;
            --bg-light: #FDFBF5;
            --text-dark: #5A483E;
            --text-light: #8E8279;
            --accent-yellow: #F6D884;
            --accent-green: #E1E8B8;
            --white: #FFFFFF;
            --border: #EAE3D9;
            --status-green: #9BB374;
            --status-red: #C0392B;
            --status-red-bg: #FADBD8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Page header ── */
        .dash-banner {
            background: linear-gradient(135deg, #5A483E 0%, #7A6258 100%);
            color: var(--white);
            padding: 36px 5% 32px;
        }

        .dash-banner h1 {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .dash-banner p {
            font-size: 14px;
            font-weight: 600;
            opacity: 0.75;
        }

        /* ── Stat bar ── */
        .stat-bar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 5%;
            display: flex;
            gap: 0;
        }

        .stat-item {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-right: 1px solid var(--border);
            font-weight: 700;
        }

        .stat-item:first-child {
            padding-left: 0;
        }

        .stat-num {
            font-size: 22px;
            font-weight: 900;
            color: var(--primary-orange);
        }

        .stat-lbl {
            font-size: 12px;
            color: var(--text-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* ── Layout ── */
        .dash-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            padding: 30px 5% 80px;
            align-items: start;
        }

        /* ── Tabs ── */
        .dash-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 9px 20px;
            border-radius: 20px;
            border: 1.5px solid var(--border);
            background: var(--white);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .tab-btn:hover {
            border-color: var(--primary-orange);
            color: var(--primary-orange);
        }

        .tab-btn.active {
            background: var(--primary-orange);
            color: var(--white);
            border-color: var(--primary-orange);
        }

        /* ── App card ── */
        .app-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            transition: box-shadow 0.2s;
        }

        .app-card:hover {
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.07);
        }

        .app-card.rejected {
            border-color: var(--status-red);
            background: #FEF9F9;
        }

        .app-card.completed {
            border-color: var(--status-green);
            background: #F6FAF0;
        }

        .app-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .app-pet {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .app-pet img {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--border);
        }

        .app-pet h3 {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 2px;
        }

        .app-pet p {
            font-size: 13px;
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 6px;
        }

        .app-tag {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .tag-adopt {
            background: #E3F2FD;
            color: #1565C0;
        }

        .tag-foster {
            background: #F3E5F5;
            color: #6A1B9A;
        }

        .app-meta {
            text-align: right;
        }

        .app-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .badge-active {
            background: #E6F4EA;
            color: #27AE60;
        }

        .badge-rejected {
            background: var(--status-red-bg);
            color: var(--status-red);
        }

        .badge-done {
            background: #E8EAF6;
            color: #3949AB;
        }

        .badge-pending {
            background: #FEF9E7;
            color: #B7950B;
        }

        .app-date {
            font-size: 12px;
            color: var(--text-light);
            font-weight: 600;
        }

        /* ── Stepper ── */
        .stepper-wrap {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .stepper {
            display: flex;
            align-items: flex-start;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--border);
            z-index: 0;
        }

        .step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--text-light);
            transition: all 0.3s;
        }

        .step.done .step-circle {
            background: var(--status-green);
            border-color: var(--status-green);
            color: var(--white);
        }

        .step.current .step-circle {
            background: var(--white);
            border-color: var(--primary-orange);
            color: var(--primary-orange);
            box-shadow: 0 0 0 4px rgba(239, 142, 53, 0.15);
        }

        .step.cut .step-circle {
            background: var(--status-red-bg);
            border-color: var(--status-red);
            color: var(--status-red);
        }

        .step-name {
            font-size: 9.5px;
            font-weight: 800;
            text-align: center;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            max-width: 62px;
        }

        .step.done .step-name {
            color: var(--status-green);
        }

        .step.current .step-name {
            color: var(--primary-orange);
        }

        .step.cut .step-name {
            color: var(--status-red);
        }

        /* Rejected banner across stepper */
        .rejected-banner {
            background: var(--status-red-bg);
            border: 1px solid var(--status-red);
            border-radius: 8px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--status-red);
            margin-top: 14px;
        }

        /* ── App bottom ── */
        .app-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .update-msg {
            background: var(--bg-light);
            border-left: 3px solid var(--primary-orange);
            padding: 10px 14px;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            font-weight: 600;
            flex: 1;
            min-width: 0;
        }

        .update-msg strong {
            font-weight: 800;
        }

        .btn-msg {
            background: var(--primary-orange);
            color: var(--white);
            border: none;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .btn-msg:hover {
            background: var(--primary-hover);
        }

        /* ── Empty state ── */
        .empty-state {
            background: var(--white);
            border: 1.5px dashed var(--border);
            border-radius: 16px;
            padding: 48px;
            text-align: center;
        }

        .empty-state i {
            font-size: 40px;
            color: var(--accent-yellow);
            margin-bottom: 14px;
            display: block;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-orange {
            background: var(--primary-orange);
            color: var(--white);
            padding: 11px 22px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-orange:hover {
            background: var(--primary-hover);
        }

        /* ── Messages tab ── */
        .msg-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 16px;
            min-height: 480px;
        }

        .msg-sidebar {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .msg-app-item {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
        }

        .msg-app-item:hover {
            border-color: var(--primary-orange);
        }

        .msg-app-item.active {
            border-color: var(--primary-orange);
            background: #FFF8F1;
        }

        .msg-app-item h4 {
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 2px;
        }

        .msg-app-item p {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 600;
        }

        .msg-app-item .unread-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary-orange);
            margin-left: 6px;
            vertical-align: middle;
        }

        .msg-pane {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .msg-pane-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .msg-pane-header img {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
        }

        .msg-pane-header h4 {
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 1px;
        }

        .msg-pane-header p {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 600;
        }

        .chat-log {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #F7F4EE;
            min-height: 300px;
            max-height: 360px;
        }

        .bubble {
            display: flex;
            flex-direction: column;
            max-width: 70%;
        }

        .bubble.admin {
            align-self: flex-start;
        }

        .bubble.user {
            align-self: flex-end;
        }

        .bubble-content {
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.5;
        }

        .bubble.admin .bubble-content {
            background: var(--white);
            color: var(--text-dark);
            border-bottom-left-radius: 3px;
        }

        .bubble.user .bubble-content {
            background: var(--primary-orange);
            color: var(--white);
            border-bottom-right-radius: 3px;
        }

        .bubble-meta {
            font-size: 10px;
            color: var(--text-light);
            font-weight: 700;
            margin-top: 3px;
        }

        .bubble.user .bubble-meta {
            text-align: right;
        }

        .chat-input-area {
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
        }

        .chat-input-area input {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
            color: var(--text-dark);
        }

        .chat-input-area input:focus {
            outline: none;
            border-color: var(--primary-orange);
        }

        .no-msgs {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            color: var(--text-light);
            font-weight: 700;
            font-size: 13px;
            background: #F7F4EE;
        }

        .no-msgs i {
            font-size: 28px;
            opacity: 0.3;
        }

        /* ── Sidebar ── */
        .side-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .side-card h3 {
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-light);
        }

        .summary-val {
            font-size: 16px;
            font-weight: 900;
            color: var(--text-dark);
        }

        .summary-val.orange {
            color: var(--primary-orange);
        }

        .summary-val.green {
            color: var(--status-green);
        }

        .summary-val.red {
            color: var(--status-red);
        }

        .quick-link {
            display: block;
            padding: 11px 16px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .quick-link:hover {
            border-color: var(--primary-orange);
            color: var(--primary-orange);
            background: var(--bg-light);
        }

        .quick-link:last-child {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Banner -->
    <div class="dash-banner">
        <h1>My Dashboard</h1>
        <p>Track your applications, check status updates, and message the Fluffside team.</p>
    </div>

    <!-- Stat bar -->
    <div class="stat-bar">
        <div class="stat-item">
            <div>
                <div class="stat-num"><?= $active_count ?></div>
                <div class="stat-lbl">Active</div>
            </div>
        </div>
        <div class="stat-item">
            <div>
                <div class="stat-num"><?= $approved_count ?></div>
                <div class="stat-lbl">Approved</div>
            </div>
        </div>
        <div class="stat-item">
            <div>
                <div class="stat-num"><?= $completed_count ?></div>
                <div class="stat-lbl">Completed</div>
            </div>
        </div>
        <div class="stat-item">
            <div>
                <div class="stat-num"><?= $rejected_count ?></div>
                <div class="stat-lbl">Declined</div>
            </div>
        </div>
        <div class="stat-item">
            <div>
                <div class="stat-num"><?= $order_count ?></div>
                <div class="stat-lbl">Orders</div>
            </div>
        </div>
    </div>

    <!-- Main layout -->
    <div class="dash-layout">
        <!-- Left column -->
        <div>
            <!-- Tabs -->
            <div class="dash-tabs">
                <a href="?tab=active" class="tab-btn <?= $active_tab === 'active'   ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Active (<?= count($active) ?>)</a>
                <a href="?tab=past" class="tab-btn <?= $active_tab === 'past'     ? 'active' : '' ?>"><i class="fas fa-history"></i> Past (<?= count($past) ?>)</a>
                <a href="?tab=messages" class="tab-btn <?= $active_tab === 'messages' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Messages</a>
                <a href="?tab=orders" class="tab-btn <?= $active_tab === 'orders'   ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> Orders (<?= $order_count ?>)</a>
            </div>

            <?php
            // ── ACTIVE TAB ─────────────────────────────────────────
            if ($active_tab === 'active'):
                if (empty($active)): ?>
                    <div class="empty-state">
                        <i class="fas fa-paw"></i>
                        <h3>No active applications yet</h3>
                        <p>Browse our residents and submit an adoption or foster application to get started.</p>
                        <a href="residents.php" class="btn-orange">Browse Residents</a>
                    </div>
                    <?php else:
                    foreach ($active as $app):
                        $step = (int)$app['current_step'];
                    ?>
                        <div class="app-card">
                            <div class="app-top">
                                <div class="app-pet">
                                    <img src="<?= htmlspecialchars($app['pet_image']) ?>" alt="<?= htmlspecialchars($app['pet_name']) ?>"
                                        onerror="this.src='https://placehold.co/72x72/EAE3D9/8E8279?text=?'">
                                    <div>
                                        <h3><?= htmlspecialchars($app['pet_name']) ?></h3>
                                        <p><?= htmlspecialchars($app['pet_breed']) ?></p>
                                        <span class="app-tag <?= $app['type'] === 'Adoption' ? 'tag-adopt' : 'tag-foster' ?>"><?= htmlspecialchars($app['type']) ?></span>
                                    </div>
                                </div>
                                <div class="app-meta">
                                    <span class="app-badge badge-active">Active</span><br>
                                    <span class="app-date">Submitted <?= htmlspecialchars($app['submitted_at']) ?></span>
                                </div>
                            </div>

                            <!-- Stepper -->
                            <div class="stepper-wrap">
                                <div class="stepper">
                                    <?php foreach ($steps_label as $i => $label):
                                        if ($i === 0) continue;
                                        $cls = $i < $step ? 'done' : ($i === $step ? 'current' : '');
                                    ?>
                                        <div class="step <?= $cls ?>">
                                            <div class="step-circle">
                                                <?php if ($i < $step): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php else: ?>
                                                    <i class="fas <?= $steps_icon[$i] ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="step-name"><?= $label ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="app-bottom">
                                <div class="update-msg">
                                    <strong>Last Update:</strong> <?= htmlspecialchars($app['last_update'] ?? 'Your application is being processed.') ?>
                                </div>
                                <a href="?tab=messages&app=<?= urlencode($app['id']) ?>" class="btn-msg">
                                    <i class="fas fa-comments"></i> Message Us
                                </a>
                            </div>
                        </div>
                    <?php
                    endforeach;
                endif;

            // ── PAST TAB ───────────────────────────────────────────
            elseif ($active_tab === 'past'):
                if (empty($past)): ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <h3>No past applications</h3>
                        <p>Completed and declined applications will appear here.</p>
                    </div>
                    <?php else:
                    foreach ($past as $app):
                        $step = (int)$app['current_step'];
                        $is_rejected = (bool)$app['rejected'];
                    ?>
                        <div class="app-card <?= $is_rejected ? 'rejected' : 'completed' ?>">
                            <div class="app-top">
                                <div class="app-pet">
                                    <img src="<?= htmlspecialchars($app['pet_image']) ?>" alt="<?= htmlspecialchars($app['pet_name']) ?>"
                                        onerror="this.src='https://placehold.co/72x72/EAE3D9/8E8279?text=?'">
                                    <div>
                                        <h3><?= htmlspecialchars($app['pet_name']) ?></h3>
                                        <p><?= htmlspecialchars($app['pet_breed']) ?></p>
                                        <span class="app-tag <?= $app['type'] === 'Adoption' ? 'tag-adopt' : 'tag-foster' ?>"><?= htmlspecialchars($app['type']) ?></span>
                                    </div>
                                </div>
                                <div class="app-meta">
                                    <span class="app-badge <?= $is_rejected ? 'badge-rejected' : 'badge-done' ?>"><?= $is_rejected ? 'Declined' : 'Completed' ?></span><br>
                                    <span class="app-date">Submitted <?= htmlspecialchars($app['submitted_at']) ?></span>
                                </div>
                            </div>

                            <!-- Stepper: shows how far it got -->
                            <div class="stepper-wrap">
                                <div class="stepper">
                                    <?php foreach ($steps_label as $i => $label):
                                        if ($i === 0) continue;
                                        if ($is_rejected && $i > $step) {
                                            $cls = 'cut';
                                        } elseif ($i <= $step) {
                                            $cls = 'done';
                                        } else {
                                            $cls = '';
                                        }
                                    ?>
                                        <div class="step <?= $cls ?>">
                                            <div class="step-circle">
                                                <?php if ($cls === 'done'): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php elseif ($cls === 'cut'): ?>
                                                    <i class="fas fa-times"></i>
                                                <?php else: ?>
                                                    <i class="fas <?= $steps_icon[$i] ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="step-name"><?= $label ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($is_rejected): ?>
                                    <div class="rejected-banner">
                                        <i class="fas fa-info-circle"></i>
                                        <?= htmlspecialchars($app['last_update'] ?? 'This application was not approved.') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!$is_rejected): ?>
                                <div class="app-bottom">
                                    <div class="update-msg"><strong>Last Update:</strong> <?= htmlspecialchars($app['last_update'] ?? '') ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php
                    endforeach;
                endif;

            // ── MESSAGES TAB ──────────────────────────────────────
            elseif ($active_tab === 'messages'):
                if (empty($all_apps)): ?>
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h3>No conversations yet</h3>
                        <p>Once you submit an application, you can message the Fluffside team here.</p>
                        <a href="residents.php" class="btn-orange">Browse Residents</a>
                    </div>
                <?php else: ?>
                    <div class="msg-layout">
                        <!-- Conversation list -->
                        <div class="msg-sidebar">
                            <?php foreach ($all_apps as $a):
                                $app_msgs = get_messages_by_app($a['id']);
                                $last_msg = end($app_msgs);
                                // Unread = last message is from admin and tab is open to a different app
                                $has_new  = $last_msg && $last_msg['sender'] === 'admin' && $a['id'] !== ($selected_app['id'] ?? '');
                            ?>
                                <a href="?tab=messages&app=<?= urlencode($a['id']) ?>"
                                    class="msg-app-item <?= (($selected_app['id'] ?? '') === $a['id']) ? 'active' : '' ?>">
                                    <h4><?= htmlspecialchars($a['pet_name']) ?>
                                        <?php if ($has_new): ?><span class="unread-dot"></span><?php endif; ?>
                                    </h4>
                                    <p><?= htmlspecialchars($a['type']) ?> &bull; <?= htmlspecialchars($a['id']) ?></p>
                                    <?php if ($last_msg): ?>
                                        <p style="margin-top:4px;font-size:10px;color:var(--text-light);">
                                            <?= htmlspecialchars(substr($last_msg['message'], 0, 45)) ?>...
                                        </p>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <!-- Chat pane -->
                        <div class="msg-pane">
                            <?php if ($selected_app): ?>
                                <div class="msg-pane-header">
                                    <img src="<?= htmlspecialchars($selected_app['pet_image']) ?>" alt=""
                                        onerror="this.src='https://placehold.co/38x38/EAE3D9/8E8279?text=?'">
                                    <div>
                                        <h4><?= htmlspecialchars($selected_app['pet_name']) ?></h4>
                                        <p><?= htmlspecialchars($selected_app['type']) ?> Application &bull; <?= htmlspecialchars($selected_app['id']) ?></p>
                                    </div>
                                </div>

                                <div class="chat-log" id="chatLog">
                                    <?php if (empty($chat_msgs)): ?>
                                        <div style="text-align:center;color:var(--text-light);font-weight:700;font-size:13px;padding:20px 0;">
                                            No messages yet. Say hello to the Fluffside team!
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($chat_msgs as $m): ?>
                                            <div class="bubble <?= $m['sender'] === 'admin' ? 'admin' : 'user' ?>">
                                                <div class="bubble-content"><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                                                <div class="bubble-meta">
                                                    <?= $m['sender'] === 'admin' ? 'FluffSide Team' : 'You' ?>
                                                    &bull; <?= htmlspecialchars(date('M j, g:i A', strtotime($m['sent_at']))) ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (!$selected_app['rejected'] && $selected_app['status'] !== 'completed'): ?>
                                    <div class="chat-input-area">
                                        <form method="POST" style="display:flex;gap:10px;flex:1;">
                                            <input type="hidden" name="action" value="send_message">
                                            <input type="hidden" name="app_id" value="<?= htmlspecialchars($selected_app['id']) ?>">
                                            <input type="text" name="message" placeholder="Type a message..." required autocomplete="off">
                                            <button type="submit" class="btn-msg"><i class="fas fa-paper-plane"></i></button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div style="padding:12px 16px;text-align:center;font-size:12px;color:var(--text-light);font-weight:700;border-top:1px solid var(--border);">
                                        This conversation is closed.
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="no-msgs"><i class="fas fa-comments"></i><span>Select an application to view messages</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php

            // ── ORDERS TAB ────────────────────────────────────────
            elseif ($active_tab === 'orders'):
                if (empty($user_orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>No orders yet</h3>
                        <p>Browse our pet supplies and place your first order!</p>
                        <a href="supplies.php" class="btn-orange">Shop Now</a>
                    </div>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <?php foreach ($user_orders as $ord):
                            $status_colors = [
                                'Pending'    => ['bg' => '#FEF9E7', 'color' => '#B7950B'],
                                'Processing' => ['bg' => '#EAF2FF', 'color' => '#1A5276'],
                                'Shipped'    => ['bg' => '#E8F8F5', 'color' => '#148F77'],
                                'Delivered'  => ['bg' => '#E6F4EA', 'color' => '#1E8449'],
                                'Cancelled'  => ['bg' => '#FADBD8', 'color' => '#C0392B'],
                            ];
                            $sc = $status_colors[$ord['status']] ?? ['bg' => '#F0F0F0', 'color' => '#888'];
                        ?>
                            <div class="app-card" style="padding:20px 24px;">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                                    <div>
                                        <div style="font-size:17px;font-weight:900;margin-bottom:3px;"><?= htmlspecialchars($ord['order_number']) ?></div>
                                        <div style="font-size:12px;color:var(--text-light);font-weight:600;">
                                            <?= htmlspecialchars(date('F j, Y g:i A', strtotime($ord['ordered_at']))) ?>
                                            &bull; <?= htmlspecialchars($ord['payment_method']) ?>
                                        </div>
                                    </div>
                                    <div style="text-align:right;">
                                        <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
                                            <?= htmlspecialchars($ord['status']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                                    <div style="font-size:13px;font-weight:700;color:var(--text-light);">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($ord['city']) ?>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:16px;">
                                        <span style="font-size:18px;font-weight:900;color:var(--primary-orange);">
                                            &#8369;<?= number_format((float)$ord['total_amount'], 2) ?>
                                        </span>
                                        <a href="receipt.php?order_id=<?= (int)$ord['order_id'] ?>" class="btn-msg" style="background:#9BB374;">
                                            <i class="fas fa-receipt"></i> View Receipt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
            <?php endif;
            endif; ?>
        </div>

        <!-- Sidebar -->
        <aside>
            <div class="side-card">
                <h3><i class="fas fa-chart-bar" style="color:var(--primary-orange)"></i> My Summary</h3>
                <div class="summary-row"><span class="summary-label">Active</span><span class="summary-val orange"><?= $active_count ?></span></div>
                <div class="summary-row"><span class="summary-label">Approved</span><span class="summary-val green"><?= $approved_count ?></span></div>
                <div class="summary-row"><span class="summary-label">Completed</span><span class="summary-val"><?= $completed_count ?></span></div>
                <div class="summary-row"><span class="summary-label">Declined</span><span class="summary-val red"><?= $rejected_count ?></span></div>
            </div>

            <div class="side-card">
                <h3><i class="fas fa-link" style="color:var(--primary-orange)"></i> Quick Links</h3>
                <a href="residents.php" class="quick-link"><i class="fas fa-paw"></i> Browse Residents</a>
                <a href="supplies.php" class="quick-link"><i class="fas fa-box-open"></i> Pet Supplies</a>
                <a href="help.php" class="quick-link"><i class="fas fa-question-circle"></i> Help Center</a>
            </div>

            <?php if ($active_count > 0): ?>
                <div class="side-card" style="border:1.5px solid var(--accent-yellow); background:#FFFDF0;">
                    <h3><i class="fas fa-lightbulb" style="color:#D4AC0D"></i> Tip</h3>
                    <p style="font-size:13px;font-weight:600;color:var(--text-dark);line-height:1.6;">
                        Keep an eye on your <strong>Messages</strong> tab &mdash; the Fluffside team may reach out to schedule your interview or confirm a zoom meeting!
                    </p>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Auto-scroll chat
        const log = document.getElementById('chatLog');
        if (log) log.scrollTop = log.scrollHeight;

        // 30-second inactivity logout
        let inactivityTimer;

        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                window.location.href = 'logout.php?reason=inactive';
            }, 30000);
        }
        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(e => {
>>>>>>> 5811b114e5fd1e327cc690ba83d3e4517f2253b4
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>
<<<<<<< HEAD
</body>
=======
</body>

>>>>>>> 5811b114e5fd1e327cc690ba83d3e4517f2253b4
</html>