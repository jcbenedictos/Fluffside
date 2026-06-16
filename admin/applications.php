<?php
require 'auth.inc.php';
require_admin();
require_once '../db.inc.php'; require_once '../db_helper.inc.php';

$success = '';
$error   = '';

// ── POST handlers ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Advance application step
    if ($action === 'advance_step') {
        $app_id = trim($_POST['app_id'] ?? '');
        $note   = trim($_POST['note'] ?? '');
        $app = get_application_by_id($app_id);
        if ($app && !$app['rejected']) {
            $app['current_step'] = min(6, (int)$app['current_step'] + 1);
            $steps_label_local = ['','Submitted','Under Review','Interview / Zoom','Approved','Meet & Greet','Take Home'];
            $step_name = $steps_label_local[$app['current_step']] ?? 'Step '.$app['current_step'];
            $update_msg = $note ?: 'Your application has been moved to: ' . $step_name . '.';
            $app['last_update'] = $update_msg;
            if ($app['current_step'] === 6) $app['status'] = 'completed';
            save_application($app);
            // Notify the user
            add_notification((int)$app['user_id'], $app_id,
                'Your application (' . $app_id . ') has been updated to stage: ' . $step_name . '. ' . $update_msg);
            $success = 'Application advanced to step ' . $app['current_step'] . ' and user notified.';
        }
    }

    // Reject application
    if ($action === 'reject') {
        $app_id = trim($_POST['app_id'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        $app = get_application_by_id($app_id);
        if ($app) {
            $app['rejected']    = true;
            $app['status']      = 'rejected';
            $reason_msg = $reason ?: 'Your application has been reviewed and we are unable to proceed at this time. Thank you for your interest.';
            $app['last_update'] = $reason_msg;
            save_application($app);
            // Notify the user
            add_notification((int)$app['user_id'], $app_id,
                'Update on your application (' . $app_id . '): ' . $reason_msg);
            $success = 'Application rejected and user notified.';
        }
    }
}

// ── Load data ──────────────────────────────────────────────────
$all_apps  = get_all_applications();
$view_id   = $_GET['id'] ?? null;
$view_app  = $view_id ? get_application_by_id($view_id) : null;

$view_details = $view_app ? get_app_full_details($view_id) : ['applicant'=>null,'adoption'=>null,'foster'=>null];

$steps_label = ['', 'Submitted', 'Under Review', 'Interview / Zoom', 'Approved', 'Meet & Greet', 'Take Home'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications — FluffSide Admin</title>
    <?php include 'header.inc.php'; ?>
    <style>
        .page-body { padding: 40px 5% 80px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
        .page-title  { font-size:26px; font-weight:900; }
        .page-sub    { font-size:13px; color:var(--text-light); font-weight:600; margin-top:4px; }

        .alert { padding:13px 18px; border-radius:9px; font-weight:700; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#E6F4EA; color:#1E8449; }
        .alert-error   { background:var(--admin-red-light); color:var(--admin-red); }

        /* ── Split layout when viewing an app ── */
        .split-view { display:grid; grid-template-columns:360px 1fr; gap:24px; align-items:start; }
        .split-list { display:flex; flex-direction:column; gap:10px; }

        /* App list card */
        .app-list-card {
            background:var(--white); border:1.5px solid var(--border); border-radius:12px;
            padding:14px 16px; cursor:pointer; transition:all 0.2s; text-decoration:none; display:block;
        }
        .app-list-card:hover { border-color:var(--primary-orange); }
        .app-list-card.active { border-color:var(--primary-orange); background:#FFF8F1; }
        .app-list-card .meta { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px; }
        .app-list-card h4 { font-size:14px; font-weight:900; margin-bottom:2px; }
        .app-list-card p  { font-size:12px; color:var(--text-light); font-weight:600; }
        .app-list-card .unread {
            width:8px; height:8px; border-radius:50%;
            background:var(--primary-orange); flex-shrink:0; margin-top:4px;
        }

        .badge {
            display:inline-block; padding:3px 10px; border-radius:20px;
            font-size:11px; font-weight:800; text-transform:uppercase;
        }
        .badge-active   { background:#E6F4EA; color:#27AE60; }
        .badge-rejected { background:var(--admin-red-light); color:var(--admin-red); }
        .badge-done     { background:#E8EAF6; color:#3949AB; }
        .badge-adopt    { background:#E3F2FD; color:#1565C0; }
        .badge-foster   { background:#F3E5F5; color:#6A1B9A; }

        /* Detail panel */
        .detail-panel { display:flex; flex-direction:column; gap:18px; }
        .card { background:var(--white); border:1px solid var(--border); border-radius:14px; padding:24px; }
        .card h3 { font-size:16px; font-weight:900; margin-bottom:16px; display:flex; align-items:center; gap:8px; }

        /* App info */
        .app-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .info-item label { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); display:block; margin-bottom:3px; }
        .info-item span  { font-size:14px; font-weight:700; }

        /* Progress stepper */
        .stepper { display:flex; align-items:flex-start; gap:0; margin:8px 0; position:relative; }
        .stepper::before {
            content:''; position:absolute; top:16px; left:16px; right:16px;
            height:2px; background:var(--border); z-index:0;
        }
        .step {
            flex:1; display:flex; flex-direction:column; align-items:center; gap:6px;
            position:relative; z-index:1;
        }
        .step-dot {
            width:32px; height:32px; border-radius:50%; border:2px solid var(--border);
            background:var(--white); display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:900; color:var(--text-light);
            transition:all 0.3s;
        }
        .step.done   .step-dot { background:var(--primary-orange); border-color:var(--primary-orange); color:var(--white); }
        .step.current .step-dot { background:var(--white); border-color:var(--primary-orange); color:var(--primary-orange); box-shadow:0 0 0 4px rgba(239,142,53,0.15); }
        .step.rejected-step .step-dot { background:var(--admin-red); border-color:var(--admin-red); color:var(--white); }
        .step-label { font-size:9px; font-weight:800; text-align:center; color:var(--text-light); text-transform:uppercase; letter-spacing:0.3px; }
        .step.done   .step-label { color:var(--primary-orange); }
        .step.current .step-label { color:var(--primary-orange); }
        .step.rejected-step .step-label { color:var(--admin-red); }

        /* Admin controls */
        .controls-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .control-card { border:1.5px solid var(--border); border-radius:10px; padding:16px; }
        .control-card h4 { font-size:13px; font-weight:800; margin-bottom:10px; }
        .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:10px; }
        .form-group label { font-size:11px; font-weight:800; color:var(--text-light); text-transform:uppercase; letter-spacing:0.4px; }
        .form-group input, .form-group textarea {
            padding:9px 12px; border:1.5px solid var(--border); border-radius:7px;
            font-size:13px; font-family:'Nunito',sans-serif; font-weight:600; color:var(--text-dark);
        }
        .form-group input:focus, .form-group textarea:focus { outline:none; border-color:var(--primary-orange); }
        .form-group textarea { resize:vertical; min-height:60px; }
        .btn-primary {
            background:var(--primary-orange); color:var(--white); border:none;
            padding:9px 18px; border-radius:8px; font-size:12px; font-weight:800;
            cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:background 0.2s;
        }
        .btn-primary:hover { background:var(--primary-hover); }
        .btn-red {
            background:var(--admin-red); color:var(--white); border:none;
            padding:9px 18px; border-radius:8px; font-size:12px; font-weight:800;
            cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:opacity 0.2s;
        }
        .btn-red:hover { opacity:0.85; }

        /* Chat — admin perspective: YOU (admin) on right orange, USER on left white */
        .chat-log {
            background:#F7F4EE; border-radius:10px; padding:16px;
            display:flex; flex-direction:column; gap:14px;
            max-height:400px; overflow-y:auto; margin-bottom:14px;
        }
        .msg-bubble { display:flex; flex-direction:column; max-width:72%; }
        /* Admin = YOU = right side */
        .msg-bubble.admin { align-self:flex-end; align-items:flex-end; }
        /* User = them = left side */
        .msg-bubble.user  { align-self:flex-start; align-items:flex-start; }
        .msg-content {
            padding:10px 14px; border-radius:14px; font-size:13px; font-weight:600; line-height:1.6; word-break:break-word;
        }
        .msg-bubble.admin .msg-content {
            background:var(--primary-orange); color:var(--white);
            border-bottom-right-radius:3px;
        }
        .msg-bubble.user  .msg-content {
            background:var(--white); color:var(--text-dark);
            border:1px solid var(--border); border-bottom-left-radius:3px;
        }
        .msg-meta { font-size:10px; font-weight:700; color:var(--text-light); margin-top:4px; }
        .msg-bubble.admin .msg-meta { text-align:right; }
        .msg-bubble.user  .msg-meta { text-align:left; }
        /* Sender label pill */
        .msg-sender {
            font-size:10px; font-weight:800; text-transform:uppercase;
            letter-spacing:0.4px; margin-bottom:4px; color:var(--text-light);
        }
        .msg-bubble.admin .msg-sender { color:var(--primary-orange); }
        .msg-bubble.user  .msg-sender { color:var(--text-light); }
        .chat-input-row { display:flex; gap:10px; }
        .chat-input-row input {
            flex:1; padding:10px 14px; border:1.5px solid var(--border); border-radius:8px;
            font-size:13px; font-family:'Nunito',sans-serif; font-weight:600; color:var(--text-dark);
        }
        .chat-input-row input:focus { outline:none; border-color:var(--primary-orange); }
        .chat-input-row input:focus { outline:none; border-color:var(--primary-orange); }

        /* Table fallback */
        .full-table { background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; font-weight:800; text-transform:uppercase;
             letter-spacing:0.5px; color:var(--text-light); padding:12px 16px;
             border-bottom:1px solid var(--border); background:var(--bg-light); }
        td { padding:12px 16px; font-size:13px; font-weight:600; border-bottom:1px solid var(--border); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#FDFBF5; }
        .btn-sm {
            display:inline-flex; align-items:center; gap:6px;
            padding:6px 12px; border-radius:7px; font-size:12px; font-weight:800;
            text-decoration:none; border:1px solid var(--border); cursor:pointer; transition:all 0.2s;
            background:var(--bg-light); color:var(--text-dark);
        }
        .btn-sm:hover { border-color:var(--primary-orange); color:var(--primary-orange); }
        .empty-state { text-align:center; padding:48px 20px; color:var(--text-light); font-weight:700; font-size:14px; }
        .empty-state i { font-size:40px; margin-bottom:14px; display:block; opacity:0.3; }
    </style>
</head>
<body>
<div class="page-body">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-envelope-open-text" style="color:var(--primary-orange)"></i> Applications</div>
            <div class="page-sub"><?= count($all_apps) ?> total &mdash;
                <?= count(array_filter($all_apps, fn($a) => $a['status']==='active' && !$a['rejected'])) ?> active</div>
        </div>
        <?php if ($view_app): ?>
        <a href="applications.php" class="btn-sm"><i class="fas fa-arrow-left"></i> Back to All</a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php elseif ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>

    <?php if ($view_app): ?>
    <!-- ============================================================
         DETAIL VIEW
         ============================================================ -->
    <?php
    $step    = (int)$view_app['current_step'];
    $rejected = (bool)$view_app['rejected'];
    ?>
    <div class="split-view">
        <!-- Left: app list mini -->
        <div class="split-list">
            <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-light);margin-bottom:4px;">All Applications</div>
            <?php foreach ($all_apps as $a):
                $is_active_card = $a['id'] === $view_id;
            ?>
            <a href="applications.php?id=<?= h($a['id']) ?>"
               class="app-list-card <?= $is_active_card ? 'active' : '' ?>">
                <div class="meta">
                    <div>
                        <h4><?= h($a['user_name']) ?></h4>
                        <p><?= h($a['pet_name']) ?> &bull; <?= h($a['type']) ?></p>
                    </div>
                </div>
                <div style="display:flex;gap:6px;align-items:center;">
                    <?php
                    if ($a['rejected']) {
                        echo '<span class="badge badge-rejected">Rejected</span>';
                    } elseif ($a['status'] === 'completed') {
                        echo '<span class="badge badge-done">Completed</span>';
                    } else {
                        echo '<span class="badge badge-active">' . h($steps_label[$a['current_step']] ?? 'Step '.$a['current_step']) . '</span>';
                    }
                    ?>
                    <span style="font-size:11px;color:var(--text-light);font-weight:600;margin-left:auto;"><?= h($a['submitted_at']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if (empty($all_apps)): ?>
            <div style="text-align:center;padding:20px;color:var(--text-light);font-weight:700;font-size:13px;">No applications yet.</div>
            <?php endif; ?>
        </div>

        <!-- Right: detail -->
        <div class="detail-panel">

            <!-- App Info -->
            <div class="card">
                <h3><i class="fas fa-clipboard-list" style="color:var(--primary-orange)"></i>
                    <?= h($view_app['id']) ?> &mdash; <?= h($view_app['type']) ?> Application</h3>
                <div class="app-info-grid">
                    <div class="info-item"><label>Applicant</label><span><?= h($view_app['user_name']) ?></span></div>
                    <div class="info-item"><label>Email</label><span><?= h($view_app['user_email']) ?></span></div>
                    <div class="info-item"><label>Pet</label><span><?= h($view_app['pet_name']) ?> (<?= h($view_app['pet_breed']) ?>)</span></div>
                    <div class="info-item"><label>Submitted</label><span><?= h($view_app['submitted_at']) ?></span></div>
                    <div class="info-item">
                        <label>Status</label>
                        <span>
                        <?php if ($rejected): ?>
                            <span class="badge badge-rejected">Rejected</span>
                        <?php elseif ($view_app['status'] === 'completed'): ?>
                            <span class="badge badge-done">Completed</span>
                        <?php else: ?>
                            <span class="badge badge-active">Active</span>
                        <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-item"><label>Last Update</label><span style="font-size:12px;"><?= h($view_app['last_update'] ?? '—') ?></span></div>
                </div>
            </div>

            <!-- Full Form Details -->
            <?php if ($view_details['applicant']): $ap = $view_details['applicant']; ?>
            <div class="card">
                <h3><i class="fas fa-id-card" style="color:var(--primary-orange)"></i> Submitted Form Details</h3>

                <div style="margin-bottom:18px;">
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-light);margin-bottom:10px;">Personal Information</div>
                    <div class="app-info-grid">
                        <div class="info-item"><label>Full Name</label><span><?= h($ap['first_name'].' '.$ap['last_name']) ?></span></div>
                        <div class="info-item"><label>Birthdate</label><span><?= h($ap['birthdate'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Pronouns</label><span><?= h($ap['pronouns'] === 'others' ? $ap['pronouns_other'] : ($ap['pronouns'] ?? '—')) ?></span></div>
                        <div class="info-item"><label>Civil Status</label><span><?= h($ap['civil_status'] === 'others' ? $ap['civil_status_other'] : ($ap['civil_status'] ?? '—')) ?></span></div>
                        <div class="info-item"><label>Phone</label><span><?= h($ap['phone'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Email</label><span><?= h($ap['email'] ?? '—') ?></span></div>
                        <div class="info-item" style="grid-column:1/-1;"><label>Address</label><span><?= h($ap['address'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Occupation</label><span><?= h($ap['occupation'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Company</label><span><?= h($ap['company'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Social Media</label><span><?= h($ap['social_media'] ?? '—') ?></span></div>
                        <div class="info-item"><label>How They Found Us</label><span><?= h($ap['prompt_src'] === 'others' ? $ap['prompt_src_other'] : ($ap['prompt_src'] ?? '—')) ?></span></div>
                        <div class="info-item"><label>Adopted Before?</label><span><?= h($ap['adopted_before'] ?? '—') ?></span></div>
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-light);margin-bottom:10px;">Alternate / Emergency Contact</div>
                    <div class="app-info-grid">
                        <div class="info-item"><label>Name</label><span><?= h(($ap['alt_first_name'] ?? '').' '.($ap['alt_last_name'] ?? '')) ?></span></div>
                        <div class="info-item"><label>Relationship</label><span><?= h($ap['alt_relationship'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Phone</label><span><?= h($ap['alt_phone'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Email</label><span><?= h($ap['alt_email'] ?? '—') ?></span></div>
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-light);margin-bottom:10px;">Housing &amp; Household</div>
                    <div class="app-info-grid">
                        <div class="info-item"><label>Building Type</label><span><?= h($ap['building_type'] === 'others' ? $ap['building_type_other'] : ($ap['building_type'] ?? '—')) ?></span></div>
                        <div class="info-item"><label>Renting?</label><span><?= h($ap['do_rent'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Lives With</label><span><?= h(str_replace(',', ', ', $ap['live_with'] ?? '—')) ?></span></div>
                        <div class="info-item"><label>Anyone Allergic?</label><span><?= h($ap['allergic'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Household Support?</label><span><?= h($ap['household_support'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Other Pets?</label><span><?= h($ap['other_pets'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Past Pets?</label><span><?= h($ap['past_pets'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Near Road?</label><span><?= h($ap['near_road'] ?? '—') ?></span></div>
                    </div>
                    <?php if ($ap['support_explain']): ?>
                    <div style="margin-top:10px;padding:12px 14px;background:var(--bg-light);border-radius:8px;font-size:13px;font-weight:600;">
                        <strong>Household support explanation:</strong> <?= h($ap['support_explain']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <?php foreach ([
                    'move_plan'      => 'What happens if they move',
                    'care_plan'      => 'Who cares for the pet',
                    'financial_plan' => 'Financial responsibility',
                    'emergency_plan' => 'Emergency caretaker',
                    'hours_alone'    => 'Hours pet left alone',
                ] as $field => $label):
                    if (empty($ap[$field])) continue; ?>
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.4px;color:var(--text-light);margin-bottom:4px;"><?= h($label) ?></div>
                    <div style="padding:12px 14px;background:var(--bg-light);border-radius:8px;font-size:13px;font-weight:600;line-height:1.6;"><?= nl2br(h($ap[$field])) ?></div>
                </div>
                <?php endforeach; ?>

                <?php
                // Adoption-specific
                if ($view_details['adoption']): $ad = $view_details['adoption']; ?>
                <div style="margin-top:18px;">
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-light);margin-bottom:10px;">Interview Preference</div>
                    <div class="app-info-grid">
                        <div class="info-item"><label>Preferred Date</label><span><?= h($ad['interview_date'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Preferred Time</label><span><?= h($ad['interview_time'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Same Month?</label><span><?= h($ad['same_month'] ?? '—') ?></span></div>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                // Foster-specific
                if ($view_details['foster']): $fo = $view_details['foster']; ?>
                <div style="margin-top:18px;">
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-light);margin-bottom:10px;">Foster Details</div>
                    <div class="app-info-grid">
                        <div class="info-item"><label>Foster Duration</label><span><?= h($fo['foster_duration'] ?? '—') ?></span></div>
                        <div class="info-item"><label>Visited Shelter?</label><span><?= h($fo['shelter_visit'] ?? '—') ?></span></div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <!-- Progress Stepper -->
            <div class="card">
                <h3><i class="fas fa-route" style="color:var(--primary-orange)"></i> Application Progress</h3>
                <div class="stepper">
                    <?php foreach ($steps_label as $i => $label):
                        if ($i === 0) continue;
                        if ($rejected && $i > $step) {
                            $cls = 'rejected-step';
                        } elseif ($i < $step || ($i === $step && $view_app['status'] === 'completed')) {
                            $cls = 'done';
                        } elseif ($i === $step) {
                            $cls = $rejected ? 'rejected-step' : 'current';
                        } else {
                            $cls = '';
                        }
                    ?>
                    <div class="step <?= $cls ?>">
                        <div class="step-dot">
                            <?php if ($cls === 'done'): ?>
                                <i class="fas fa-check" style="font-size:11px;"></i>
                            <?php elseif ($cls === 'rejected-step' && $i > $step): ?>
                                <i class="fas fa-times" style="font-size:11px;"></i>
                            <?php else: ?>
                                <?= $i ?>
                            <?php endif; ?>
                        </div>
                        <div class="step-label"><?= $label ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Admin Controls -->
            <?php if (!$rejected && $view_app['status'] !== 'completed'): ?>
            <div class="card">
                <h3><i class="fas fa-sliders-h" style="color:var(--primary-orange)"></i> Admin Actions</h3>
                <div class="controls-grid">
                    <!-- Advance step -->
                    <div class="control-card">
                        <h4><i class="fas fa-arrow-right" style="color:var(--status-green)"></i> Advance to Next Step</h4>
                        <form method="POST">
                            <input type="hidden" name="action"  value="advance_step">
                            <input type="hidden" name="app_id" value="<?= h($view_app['id']) ?>">
                            <div class="form-group">
                                <label>Status note (shown to user)</label>
                                <textarea name="note" rows="2" placeholder="e.g. We have reviewed your application and would like to schedule a Zoom interview."
                                ><?= $step < 6 ? h($steps_label[$step + 1] ?? '') : '' ?></textarea>
                            </div>
                            <button type="submit" class="btn-primary" <?= $step >= 6 ? 'disabled' : '' ?>>
                                <i class="fas fa-arrow-right"></i>
                                Move to: <?= $step < 6 ? h($steps_label[$step + 1]) : 'Already Complete' ?>
                            </button>
                        </form>
                    </div>
                    <!-- Reject -->
                    <div class="control-card">
                        <h4><i class="fas fa-ban" style="color:var(--admin-red)"></i> Reject Application</h4>
                        <form method="POST">
                            <input type="hidden" name="action"  value="reject">
                            <input type="hidden" name="app_id" value="<?= h($view_app['id']) ?>">
                            <div class="form-group">
                                <label>Reason (sent to applicant)</label>
                                <textarea name="reason" rows="2" placeholder="e.g. We regret that we are unable to approve this application at this time..."></textarea>
                            </div>
                            <button type="submit" class="btn-red" onclick="return confirm('Are you sure you want to reject this application?')">
                                <i class="fas fa-ban"></i> Reject Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php elseif ($rejected): ?>
            <div class="card" style="border-color:var(--admin-red);">
                <h3 style="color:var(--admin-red);"><i class="fas fa-ban"></i> Application Rejected</h3>
                <p style="font-size:13px;font-weight:600;color:var(--text-light);">This application was rejected. The applicant has been notified.</p>
            </div>
            <?php else: ?>
            <div class="card" style="border-color:var(--status-green);">
                <h3 style="color:var(--status-green);"><i class="fas fa-check-circle"></i> Application Completed</h3>
                <p style="font-size:13px;font-weight:600;color:var(--text-light);"><?= h($view_app['pet_name']) ?> has been successfully adopted/fostered.</p>
            </div>
            <?php endif; ?>



        </div>
    </div>

    <?php else: ?>
    <!-- ============================================================
         LIST VIEW (no app selected)
         ============================================================ -->
    <?php if (empty($all_apps)): ?>
    <div class="full-table">
        <div class="empty-state">
            <i class="fas fa-envelope-open"></i>
            No applications received yet.
        </div>
    </div>
    <?php else: ?>
    <div class="full-table">
        <table>
            <thead>
                <tr>
                    <th>App ID</th>
                    <th>Applicant</th>
                    <th>Pet</th>
                    <th>Type</th>
                    <th>Stage</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (array_reverse($all_apps) as $a):
                $badge_cls = $a['rejected'] ? 'badge-rejected' : ($a['status']==='completed' ? 'badge-done' : 'badge-active');
                $badge_lbl = $a['rejected'] ? 'Rejected' : ($a['status']==='completed' ? 'Completed' : 'Active');
                $step_name = $steps_label[$a['current_step']] ?? 'Step '.$a['current_step'];
            ?>
            <tr>
                <td style="font-family:monospace;font-weight:800;"><?= h($a['id']) ?></td>
                <td><?= h($a['user_name']) ?><br><span style="font-size:11px;color:var(--text-light);"><?= h($a['user_email']) ?></span></td>
                <td><strong><?= h($a['pet_name']) ?></strong><br><span style="font-size:11px;color:var(--text-light);"><?= h($a['pet_breed']) ?></span></td>
                <td><span class="badge <?= $a['type']==='Adoption' ? 'badge-adopt' : 'badge-foster' ?>"><?= h($a['type']) ?></span></td>
                <td><?= h($step_name) ?></td>
                <td><span class="badge <?= $badge_cls ?>"><?= $badge_lbl ?></span></td>
                <td><?= h($a['submitted_at']) ?></td>
                <td><a href="?id=<?= h($a['id']) ?>" class="btn-sm"><i class="fas fa-eye"></i> View</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
</body>
</html>

<!-- for clean comments -->