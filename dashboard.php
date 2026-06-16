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
$first_name = trim($_SESSION['first_name'] ?? '');

// ── Load user applications ─────────────────────────────────────
$all_apps  = get_applications_by_user($user_id);
$active    = array_filter($all_apps, fn($a) => $a['status'] === 'active' && !$a['rejected']);
$past      = array_filter($all_apps, fn($a) => $a['status'] === 'completed' || $a['rejected']);

$active_count    = count($active);
$approved_count  = count(array_filter($all_apps, fn($a) => (int)$a['current_step'] >= 4 && !$a['rejected']));
$rejected_count  = count(array_filter($all_apps, fn($a) => $a['rejected']));
$completed_count = count(array_filter($all_apps, fn($a) => $a['status'] === 'completed'));

// ── Notifications ──────────────────────────────────────────────
$active_tab = $_GET['tab'] ?? 'active';
// Mark notifications as read when user visits updates tab
if ($active_tab === 'updates') {
    mark_notifications_read($user_id);
}
$unread_notif_count = count_unread_notifications($user_id);
$all_notifications  = get_all_notifications($user_id);

$steps_label = ['', 'Submitted', 'Under Review', 'Interview', 'Approved', 'Meet & Greet', 'Take Home'];
$steps_icon  = ['', 'fa-file-alt', 'fa-search', 'fa-video', 'fa-check-circle', 'fa-handshake', 'fa-home'];

// Orders
$user_orders = get_orders_by_user($user_id);
$order_count = count($user_orders);

$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
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
            --orange: #EF8E35;
            --orange-hover: #D67A26;
            --orange-soft: #FEF3E8;
            --orange-mid: #FDDCB5;
            --brown-dark: #3E2C23;
            --brown-mid: #5A483E;
            --brown-light: #8E8279;
            --cream: #FDFBF5;
            --cream-dark: #F3EDE3;
            --border: #E8DDD3;
            --white: #FFFFFF;
            --green: #7DAF5A;
            --green-soft: #EBF5E1;
            --red: #C0392B;
            --red-soft: #FCEAEA;
            --blue-soft: #E3F0FF;
            --blue: #2471A3;
            --purple-soft: #F0EAF8;
            --purple: #7D3C98;
            --yellow-soft: #FEF9E7;
            --yellow: #B7950B;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--cream);
            color: var(--brown-mid);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0 5%;
            width: 100%;
        }

        /* ── HERO STRIP ───────────────────────────────── */
        .db-hero {
            background: var(--brown-dark);
            padding: 32px 5% 0;
            position: relative;
            overflow: hidden;
        }

        .db-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .db-hero-inner {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            position: relative;
            z-index: 1;
        }

        .db-hero-text {
            padding-bottom: 28px;
        }

        .db-eyebrow {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 10px;
        }

        .db-hero-text h1 {
            font-size: clamp(26px, 3.5vw, 38px);
            font-weight: 900;
            color: var(--white);
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .db-hero-text h1 span {
            color: var(--orange);
        }

        .db-hero-text p {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, .55);
            max-width: 400px;
        }

        /* ── STAT CARDS inside hero ── */
        .db-stats {
            display: flex;
            gap: 10px;
            padding-bottom: 28px;
            flex-shrink: 0;
        }

        .db-stat {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 14px;
            padding: 16px 20px;
            text-align: center;
            min-width: 80px;
            backdrop-filter: blur(6px);
            transition: background .2s;
        }

        .db-stat:hover {
            background: rgba(255, 255, 255, .13);
        }

        .db-stat-num {
            font-size: 26px;
            font-weight: 900;
            color: var(--orange);
            line-height: 1;
            margin-bottom: 4px;
        }

        .db-stat-lbl {
            font-size: 10px;
            font-weight: 800;
            color: rgba(255, 255, 255, .45);
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        /* ── TAB RAIL (attached to hero bottom) ── */
        .db-tab-rail {
            background: var(--brown-dark);
            padding: 0 5%;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            display: flex;
            gap: 2px;
            position: relative;
            z-index: 2;
        }

        .db-tab {
            padding: 14px 18px;
            font-size: 12.5px;
            font-weight: 800;
            color: rgba(255, 255, 255, .45);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-bottom: 3px solid transparent;
            transition: color .15s, border-color .15s;
            white-space: nowrap;
        }

        .db-tab:hover {
            color: rgba(255, 255, 255, .8);
        }

        .db-tab.active {
            color: var(--orange);
            border-bottom-color: var(--orange);
        }

        .db-tab-count {
            background: rgba(239, 142, 53, .2);
            color: var(--orange);
            font-size: 10px;
            font-weight: 900;
            padding: 1px 7px;
            border-radius: 20px;
        }

        .db-tab.active .db-tab-count {
            background: var(--orange);
            color: var(--white);
        }

        /* ── MAIN LAYOUT ── */
        .db-body {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 28px;
            padding: 30px 5% 80px;
            align-items: start;
        }

        /* ── APPLICATION CARD ── */
        .app-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 16px;
            transition: box-shadow .2s, transform .15s;
        }

        .app-card:hover {
            box-shadow: 0 6px 28px rgba(62, 44, 35, .09);
            transform: translateY(-1px);
        }

        .app-card.is-rejected {
            border-left: 4px solid var(--red);
        }

        .app-card.is-completed {
            border-left: 4px solid var(--green);
        }

        .app-card.is-active {
            border-left: 4px solid var(--orange);
        }

        .app-card-top {
            display: flex;
            gap: 18px;
            align-items: flex-start;
            padding: 22px 22px 16px;
        }

        .app-pet-thumb {
            width: 80px;
            height: 80px;
            border-radius: 14px;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid var(--cream-dark);
        }

        .app-pet-info {
            flex: 1;
            min-width: 0;
        }

        .app-pet-info h3 {
            font-size: 17px;
            font-weight: 900;
            color: var(--brown-dark);
            margin-bottom: 2px;
        }

        .app-pet-info .breed {
            font-size: 12px;
            font-weight: 600;
            color: var(--brown-light);
            margin-bottom: 8px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .pill-adopt {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .pill-foster {
            background: var(--purple-soft);
            color: var(--purple);
        }

        .pill-active {
            background: var(--orange-soft);
            color: var(--orange-hover);
        }

        .pill-done {
            background: var(--green-soft);
            color: var(--green);
        }

        .pill-rejected {
            background: var(--red-soft);
            color: var(--red);
        }

        .app-meta {
            text-align: right;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .app-date {
            font-size: 11px;
            font-weight: 700;
            color: var(--brown-light);
        }

        .app-id {
            font-size: 10px;
            font-weight: 700;
            color: var(--border);
            letter-spacing: .5px;
        }

        /* ── STEPPER ── */
        .stepper-area {
            padding: 0 22px 16px;
        }

        .stepper-track {
            display: flex;
            align-items: flex-start;
            position: relative;
            padding: 18px 0 8px;
        }

        .stepper-track::before {
            content: '';
            position: absolute;
            top: 38px;
            left: calc(100% / 12);
            right: calc(100% / 12);
            height: 2px;
            background: var(--border);
        }

        .step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
        }

        .step-dot {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            border: 2px solid var(--border);
            background: var(--white);
            color: var(--border);
            position: relative;
            z-index: 1;
            transition: all .25s;
        }

        .step.done .step-dot {
            background: var(--green);
            border-color: var(--green);
            color: var(--white);
        }

        .step.current .step-dot {
            background: var(--white);
            border-color: var(--orange);
            color: var(--orange);
            box-shadow: 0 0 0 5px rgba(239, 142, 53, .15);
        }

        .step.cut .step-dot {
            background: var(--red-soft);
            border-color: var(--red);
            color: var(--red);
        }

        .step-lbl {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: var(--border);
            text-align: center;
            max-width: 58px;
            line-height: 1.3;
        }

        .step.done .step-lbl {
            color: var(--green);
        }

        .step.current .step-lbl {
            color: var(--orange);
        }

        .step.cut .step-lbl {
            color: var(--red);
        }

        /* Progress bar under stepper */
        .progress-bar-wrap {
            height: 4px;
            background: var(--cream-dark);
            border-radius: 99px;
            overflow: hidden;
            margin-top: 4px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--orange) 0%, #F6B56B 100%);
            border-radius: 99px;
            transition: width .4s ease;
        }

        /* ── APP FOOTER ── */
        .app-card-foot {
            padding: 14px 22px;
            background: var(--cream);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .update-chip {
            flex: 1;
            min-width: 0;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--brown-mid);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .update-chip strong {
            font-weight: 900;
            color: var(--brown-dark);
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            transition: background .15s;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--orange);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--orange-hover);
        }

        .btn-ghost {
            background: var(--white);
            color: var(--brown-mid);
            border: 1.5px solid var(--border);
        }

        .btn-ghost:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        .rejected-note {
            margin: 0 22px 16px;
            background: var(--red-soft);
            border: 1px solid rgba(192, 57, 43, .2);
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--red);
            line-height: 1.5;
        }

        .rejected-note i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* ── EMPTY STATE ── */
        .empty-card {
            background: var(--white);
            border: 1.5px dashed var(--border);
            border-radius: 18px;
            padding: 56px 32px;
            text-align: center;
        }

        .empty-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--orange-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 26px;
            color: var(--orange);
        }

        .empty-card h3 {
            font-size: 17px;
            font-weight: 900;
            color: var(--brown-dark);
            margin-bottom: 6px;
        }

        .empty-card p {
            font-size: 13px;
            font-weight: 600;
            color: var(--brown-light);
            margin-bottom: 22px;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── NOTIFICATIONS ── */
        .notif-badge {
            position: absolute;
            top: -4px;
            right: -8px;
            background: var(--orange);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            border-radius: 99px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            line-height: 1;
        }

        .notif-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .notif-item {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            position: relative;
            transition: box-shadow .15s;
        }

        .notif-item.notif-unread {
            border-color: var(--orange);
            background: var(--orange-soft, #FFF7F0);
        }

        .notif-item:hover {
            box-shadow: 0 4px 14px rgba(62, 44, 35, .08);
        }

        .notif-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--orange);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .notif-body {
            flex: 1;
        }

        .notif-body p {
            font-size: 13px;
            font-weight: 700;
            color: var(--brown-dark);
            margin: 0 0 5px;
            line-height: 1.5;
        }

        .notif-time {
            font-size: 11px;
            font-weight: 600;
            color: var(--brown-light);
        }

        .notif-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--orange);
            flex-shrink: 0;
            margin-top: 5px;
        }

        /* ── ORDERS ── */
        .order-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: box-shadow .2s;
        }

        .order-card:hover {
            box-shadow: 0 4px 18px rgba(62, 44, 35, .08);
        }

        .order-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--orange-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--orange);
            flex-shrink: 0;
        }

        .order-info {
            flex: 1;
            min-width: 0;
        }

        .order-num {
            font-size: 15px;
            font-weight: 900;
            color: var(--brown-dark);
            margin-bottom: 2px;
        }

        .order-sub {
            font-size: 12px;
            font-weight: 600;
            color: var(--brown-light);
        }

        .order-right {
            text-align: right;
            flex-shrink: 0;
        }

        .order-amount {
            font-size: 17px;
            font-weight: 900;
            color: var(--orange);
            margin-bottom: 5px;
        }

        /* ── SIDEBAR ── */
        .side-section {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 14px;
        }

        .side-section-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--brown-light);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .side-section-title i {
            color: var(--orange);
        }

        /* Summary numbers */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .summary-cell {
            background: var(--cream);
            border-radius: 12px;
            padding: 14px;
            text-align: center;
        }

        .summary-cell .s-num {
            font-size: 22px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 3px;
        }

        .summary-cell .s-lbl {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--brown-light);
        }

        .s-orange {
            color: var(--orange);
        }

        .s-green {
            color: var(--green);
        }

        .s-red {
            color: var(--red);
        }

        .s-dark {
            color: var(--brown-dark);
        }

        /* Quick links */
        .qlink {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            color: var(--brown-mid);
            border: 1.5px solid var(--border);
            margin-bottom: 8px;
            transition: all .15s;
        }

        .qlink:last-child {
            margin-bottom: 0;
        }

        .qlink i {
            width: 20px;
            text-align: center;
            color: var(--orange);
        }

        .qlink:hover {
            border-color: var(--orange);
            color: var(--orange);
            background: var(--orange-soft);
        }

        /* Tip card */
        .tip-card {
            background: linear-gradient(135deg, #FFF8EC 0%, #FFF0D6 100%);
            border: 1.5px solid var(--orange-mid);
            border-radius: 14px;
            padding: 18px 20px;
        }

        .tip-card-title {
            font-size: 12px;
            font-weight: 900;
            color: var(--orange-hover);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tip-card p {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--brown-mid);
            line-height: 1.65;
        }

        /* ── Profile bar ── */
        .profile-strip {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            margin-bottom: 14px;
        }

        .profile-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--orange-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            color: var(--orange);
            flex-shrink: 0;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-strip-info {
            flex: 1;
            min-width: 0;
        }

        .profile-strip-name {
            font-size: 14px;
            font-weight: 900;
            color: var(--brown-dark);
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-strip-sub {
            font-size: 11px;
            font-weight: 600;
            color: var(--brown-light);
        }

        .profile-strip a {
            font-size: 11px;
            font-weight: 800;
            color: var(--orange);
            text-decoration: none;
            white-space: nowrap;
        }

        .profile-strip a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'header.php'; ?>
    </div>

    <!-- ── HERO ── -->
    <div class="db-hero">
        <div class="db-hero-inner">
            <div class="db-hero-text">
                <div class="db-eyebrow"><?= htmlspecialchars($greeting) ?></div>
                <h1><?= htmlspecialchars($first_name ?: $user_name) ?></h1>
                <p>Here's everything happening with your applications and orders.</p>
            </div>
            <div class="db-stats">
                <div class="db-stat">
                    <div class="db-stat-num"><?= $active_count ?></div>
                    <div class="db-stat-lbl">Active</div>
                </div>
                <div class="db-stat">
                    <div class="db-stat-num"><?= $approved_count ?></div>
                    <div class="db-stat-lbl">Approved</div>
                </div>
                <div class="db-stat">
                    <div class="db-stat-num"><?= $completed_count ?></div>
                    <div class="db-stat-lbl">Done</div>
                </div>
                <div class="db-stat">
                    <div class="db-stat-num"><?= $order_count ?></div>
                    <div class="db-stat-lbl">Orders</div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- ── TAB RAIL ── -->
    <div class="db-tab-rail">
        <a href="?tab=active" class="db-tab <?= $active_tab === 'active'   ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Active
            <span class="db-tab-count"><?= count($active) ?></span>
        </a>
        <a href="?tab=past" class="db-tab <?= $active_tab === 'past'     ? 'active' : '' ?>">
            <i class="fas fa-history"></i> Past
            <span class="db-tab-count"><?= count($past) ?></span>
        </a>
        <a href="?tab=updates" class="db-tab <?= $active_tab === 'updates' ? 'active' : '' ?>" style="position:relative;">
            <i class="fas fa-bell"></i> Updates
            <?php if ($unread_notif_count > 0): ?>
                <span class="notif-badge"><?= $unread_notif_count ?></span>
            <?php endif; ?>
        </a>
        <a href="?tab=orders" class="db-tab <?= $active_tab === 'orders'   ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i> Orders
            <span class="db-tab-count"><?= $order_count ?></span>
        </a>
    </div>

    <!-- ── BODY ── -->
    <div class="db-body">
        <!-- Main column -->
        <div>

            <?php
            // ── ACTIVE ──────────────────────────────────────────────
            if ($active_tab === 'active'):
                if (empty($active)): ?>
                    <div class="empty-card">
                        <div class="empty-icon-wrap"><i class="fas fa-paw"></i></div>
                        <h3>No active applications yet</h3>
                        <p>Browse our residents and start an adoption or foster application.</p>
                        <a href="residents.php" class="btn-sm btn-primary" style="margin:0 auto;">Browse Residents</a>
                    </div>
                    <?php else:
                    foreach ($active as $app):
                        $step = (int)$app['current_step'];
                        $pct  = round(($step / 6) * 100);
                    ?>
                        <div class="app-card is-active">
                            <div class="app-card-top">
                                <img class="app-pet-thumb"
                                    src="<?= htmlspecialchars($app['pet_image']) ?>"
                                    alt="<?= htmlspecialchars($app['pet_name']) ?>"
                                    onerror="this.src='https://placehold.co/80x80/F3EDE3/8E8279?text=?'">
                                <div class="app-pet-info">
                                    <h3><?= htmlspecialchars($app['pet_name']) ?></h3>
                                    <div class="breed"><?= htmlspecialchars($app['pet_breed']) ?></div>
                                    <span class="pill <?= $app['type'] === 'Adoption' ? 'pill-adopt' : 'pill-foster' ?>">
                                        <i class="fas <?= $app['type'] === 'Adoption' ? 'fa-heart' : 'fa-home' ?>"></i>
                                        <?= htmlspecialchars($app['type']) ?>
                                    </span>
                                </div>
                                <div class="app-meta">
                                    <span class="pill pill-active">Active</span>
                                    <div class="app-date">Submitted <?= htmlspecialchars($app['submitted_at']) ?></div>
                                    <div class="app-id"><?= htmlspecialchars($app['id']) ?></div>
                                </div>
                            </div>

                            <!-- Stepper -->
                            <div class="stepper-area">
                                <div class="stepper-track">
                                    <?php foreach ($steps_label as $i => $label):
                                        if ($i === 0) continue;
                                        $cls = $i < $step ? 'done' : ($i === $step ? 'current' : '');
                                    ?>
                                        <div class="step <?= $cls ?>">
                                            <div class="step-dot">
                                                <?php if ($i < $step): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php else: ?>
                                                    <i class="fas <?= $steps_icon[$i] ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="step-lbl"><?= $label ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-fill" style="width:<?= $pct ?>%"></div>
                                </div>
                            </div>

                            <div class="app-card-foot">
                                <div class="update-chip">
                                    <strong>Update: </strong><?= htmlspecialchars($app['last_update'] ?? 'Your application is being processed.') ?>
                                </div>
                                <a href="?tab=updates" class="btn-sm btn-primary">
                                    <i class="fas fa-bell"></i> View Updates
                                </a>
                            </div>
                        </div>
                    <?php endforeach;
                endif;

            // ── PAST ────────────────────────────────────────────────
            elseif ($active_tab === 'past'):
                if (empty($past)): ?>
                    <div class="empty-card">
                        <div class="empty-icon-wrap"><i class="fas fa-history"></i></div>
                        <h3>No past applications</h3>
                        <p>Completed and declined applications will show up here.</p>
                    </div>
                    <?php else:
                    foreach ($past as $app):
                        $step = (int)$app['current_step'];
                        $is_rej = (bool)$app['rejected'];
                        $pct = round(($step / 6) * 100);
                    ?>
                        <div class="app-card <?= $is_rej ? 'is-rejected' : 'is-completed' ?>">
                            <div class="app-card-top">
                                <img class="app-pet-thumb"
                                    src="<?= htmlspecialchars($app['pet_image']) ?>"
                                    alt="<?= htmlspecialchars($app['pet_name']) ?>"
                                    onerror="this.src='https://placehold.co/80x80/F3EDE3/8E8279?text=?'">
                                <div class="app-pet-info">
                                    <h3><?= htmlspecialchars($app['pet_name']) ?></h3>
                                    <div class="breed"><?= htmlspecialchars($app['pet_breed']) ?></div>
                                    <span class="pill <?= $app['type'] === 'Adoption' ? 'pill-adopt' : 'pill-foster' ?>">
                                        <i class="fas <?= $app['type'] === 'Adoption' ? 'fa-heart' : 'fa-home' ?>"></i>
                                        <?= htmlspecialchars($app['type']) ?>
                                    </span>
                                </div>
                                <div class="app-meta">
                                    <span class="pill <?= $is_rej ? 'pill-rejected' : 'pill-done' ?>">
                                        <?= $is_rej ? 'Declined' : 'Completed' ?>
                                    </span>
                                    <div class="app-date">Submitted <?= htmlspecialchars($app['submitted_at']) ?></div>
                                    <div class="app-id"><?= htmlspecialchars($app['id']) ?></div>
                                </div>
                            </div>

                            <div class="stepper-area">
                                <div class="stepper-track">
                                    <?php foreach ($steps_label as $i => $label):
                                        if ($i === 0) continue;
                                        if ($is_rej && $i > $step) $cls = 'cut';
                                        elseif ($i <= $step)        $cls = 'done';
                                        else                        $cls = '';
                                    ?>
                                        <div class="step <?= $cls ?>">
                                            <div class="step-dot">
                                                <?php if ($cls === 'done'): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php elseif ($cls === 'cut'): ?>
                                                    <i class="fas fa-times"></i>
                                                <?php else: ?>
                                                    <i class="fas <?= $steps_icon[$i] ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="step-lbl"><?= $label ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-fill" style="width:<?= $pct ?>%;background:<?= $is_rej ? 'var(--red)' : 'var(--green)' ?>;"></div>
                                </div>
                            </div>

                            <?php if ($is_rej): ?>
                                <div class="rejected-note">
                                    <i class="fas fa-info-circle"></i>
                                    <span><?= htmlspecialchars($app['last_update'] ?? 'This application was not approved.') ?></span>
                                </div>
                            <?php else: ?>
                                <div class="app-card-foot">
                                    <div class="update-chip">
                                        <strong>Last Update: </strong><?= htmlspecialchars($app['last_update'] ?? '') ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                endif;

            // ── UPDATES (Notifications) ──────────────────────────────
            elseif ($active_tab === 'updates'):
                if (empty($all_notifications)): ?>
                    <div class="empty-card">
                        <div class="empty-icon-wrap"><i class="fas fa-bell"></i></div>
                        <h3>No updates yet</h3>
                        <p>When FluffSide processes your application, updates will appear here.</p>
                        <a href="residents.php" class="btn-sm btn-primary" style="margin:0 auto;">Browse Residents</a>
                    </div>
                <?php else: ?>
                    <div class="notif-list">
                        <?php foreach ($all_notifications as $n): ?>
                            <div class="notif-item <?= $n['is_read'] ? '' : 'notif-unread' ?>">
                                <div class="notif-icon"><i class="fas fa-paw"></i></div>
                                <div class="notif-body">
                                    <p><?= htmlspecialchars($n['message']) ?></p>
                                    <span class="notif-time"><?= date('F j, Y \a\t g:i A', strtotime($n['created_at'])) ?></span>
                                </div>
                                <?php if (!$n['is_read']): ?><span class="notif-dot"></span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif;

            // ── ORDERS ──────────────────────────────────────────────
            elseif ($active_tab === 'orders'):
                if (empty($user_orders)): ?>
                    <div class="empty-card">
                        <div class="empty-icon-wrap"><i class="fas fa-shopping-bag"></i></div>
                        <h3>No orders yet</h3>
                        <p>Browse our pet supplies and place your first order!</p>
                        <a href="supplies.php" class="btn-sm btn-primary" style="margin:0 auto;">Shop Now</a>
                    </div>
                    <?php else:
                    $status_styles = [
                        'Pending'    => ['bg' => 'var(--yellow-soft)', 'color' => 'var(--yellow)'],
                        'Processing' => ['bg' => 'var(--blue-soft)',   'color' => 'var(--blue)'],
                        'Shipped'    => ['bg' => '#E8F8F5',            'color' => '#148F77'],
                        'Delivered'  => ['bg' => 'var(--green-soft)',  'color' => 'var(--green)'],
                        'Cancelled'  => ['bg' => 'var(--red-soft)',    'color' => 'var(--red)'],
                    ];
                    foreach ($user_orders as $ord):
                        $sc = $status_styles[$ord['status']] ?? ['bg' => '#F0F0F0', 'color' => '#888'];
                    ?>
                        <div class="order-card">
                            <div class="order-icon"><i class="fas fa-box"></i></div>
                            <div class="order-info">
                                <div class="order-num"><?= htmlspecialchars($ord['order_number']) ?></div>
                                <div class="order-sub">
                                    <?= htmlspecialchars(date('F j, Y', strtotime($ord['ordered_at']))) ?>
                                    &bull; <?= htmlspecialchars($ord['payment_method']) ?>
                                    &bull; <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($ord['city']) ?>
                                </div>
                            </div>
                            <div class="order-right">
                                <div class="order-amount">&#8369;<?= number_format((float)$ord['total_amount'], 2) ?></div>
                                <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                    <span style="padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:800;text-transform:uppercase;background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
                                        <?= htmlspecialchars($ord['status']) ?>
                                    </span>
                                    <a href="receipt.php?order_id=<?= (int)$ord['order_id'] ?>" class="btn-sm btn-ghost" style="font-size:11px;padding:5px 12px;">
                                        <i class="fas fa-receipt"></i> Receipt
                                    </a>
                                </div>
                            </div>
                        </div>
            <?php endforeach;
                endif;
            endif; ?>

        </div><!-- /main col -->

        <!-- ── SIDEBAR ── -->
        <aside>
            <!-- Profile strip -->
            <div class="profile-strip">
                <div class="profile-avatar">
                    <?php
                    $photo = $_SESSION['profile_photo'] ?? '';
                    if ($photo): ?>
                        <img src="<?= htmlspecialchars($photo) ?>" alt="">
                    <?php else: ?>
                        <?= strtoupper(substr($first_name ?: $user_name, 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-strip-info">
                    <div class="profile-strip-name"><?= htmlspecialchars($user_name) ?></div>
                    <div class="profile-strip-sub"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></div>
                </div>
                <a href="profile.php">Edit</a>
            </div>

            <!-- Summary -->
            <div class="side-section">
                <div class="side-section-title"><i class="fas fa-chart-pie"></i> My Summary</div>
                <div class="summary-grid">
                    <div class="summary-cell">
                        <div class="s-num s-orange"><?= $active_count ?></div>
                        <div class="s-lbl">Active</div>
                    </div>
                    <div class="summary-cell">
                        <div class="s-num s-green"><?= $approved_count ?></div>
                        <div class="s-lbl">Approved</div>
                    </div>
                    <div class="summary-cell">
                        <div class="s-num s-dark"><?= $completed_count ?></div>
                        <div class="s-lbl">Completed</div>
                    </div>
                    <div class="summary-cell">
                        <div class="s-num s-red"><?= $rejected_count ?></div>
                        <div class="s-lbl">Declined</div>
                    </div>
                </div>
            </div>

            <!-- Quick links -->
            <div class="side-section">
                <div class="side-section-title"><i class="fas fa-bolt"></i> Quick Actions</div>
                <a href="residents.php" class="qlink"><i class="fas fa-paw"></i> Browse Residents</a>
                <a href="supplies.php" class="qlink"><i class="fas fa-box-open"></i> Pet Supplies</a>
                <a href="help.php" class="qlink"><i class="fas fa-question-circle"></i> Help Center</a>
                <a href="profile.php" class="qlink"><i class="fas fa-user-cog"></i> My Profile</a>
            </div>

            <!-- Tip -->
            <?php if ($active_count > 0): ?>
                <div class="tip-card">
                    <div class="tip-card-title"><i class="fas fa-lightbulb"></i> Heads up</div>
                    <p>Check your <strong>Updates</strong> tab regularly — FluffSide will notify you when your application moves to a new stage.</p>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // 30-second inactivity logout
        let inactivityTimer;

        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                window.location.href = 'logout.php?reason=inactive';
            }, 30000);
        }
        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(e => {
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>
</body>

</html>

<!-- for clean comments -->