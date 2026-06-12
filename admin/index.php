<?php
require 'auth.inc.php';
require_admin();
require_once '../db.inc.php'; require_once '../db_helper.inc.php';

$pets     = get_all_pets();
$products = get_all_products();
$apps     = get_all_applications();
$orders   = get_all_orders();
$pending_orders = count(array_filter($orders, fn($o) => $o['status'] === 'Pending'));
$total_revenue  = array_sum(array_column($orders, 'total_amount'));

$active_apps   = count(array_filter($apps, fn($a) => $a['status'] === 'active' && !$a['rejected']));
$rejected_apps = count(array_filter($apps, fn($a) => $a['rejected']));
$completed_apps= count(array_filter($apps, fn($a) => $a['status'] === 'completed'));
$pending_msgs  = 0;
$all_msgs = get_all_messages();
// Count conversations that last message is from user (unanswered)
$last_by_app = [];
foreach ($all_msgs as $m) {
    $last_by_app[$m['app_id']] = $m;
}
foreach ($last_by_app as $m) {
    if ($m['sender'] === 'user') $pending_msgs++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Overview — FluffSide</title>
    <?php include 'header.inc.php'; ?>
    <style>
        .page-body { padding: 40px 5% 80px; }
        .page-title { font-size:28px; font-weight:900; margin-bottom:6px; }
        .page-sub   { font-size:14px; color:var(--text-light); font-weight:600; margin-bottom:32px; }

        /* Stat cards */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:18px; margin-bottom:36px; }
        .stat-card {
            background:var(--white); border:1px solid var(--border); border-radius:14px;
            padding:24px 22px; display:flex; align-items:center; gap:18px;
        }
        .stat-icon {
            width:52px; height:52px; border-radius:12px; display:flex;
            align-items:center; justify-content:center; font-size:22px; flex-shrink:0;
        }
        .icon-orange { background:#FDF1E6; color:var(--primary-orange); }
        .icon-green  { background:#E6F4EA; color:#27AE60; }
        .icon-red    { background:var(--admin-red-light); color:var(--admin-red); }
        .icon-blue   { background:#DEEBF7; color:#2980B9; }
        .icon-yellow { background:#FEF9E7; color:#D4AC0D; }
        .stat-card h3 { font-size:28px; font-weight:900; }
        .stat-card p  { font-size:13px; font-weight:700; color:var(--text-light); margin-top:2px; }

        /* Quick actions */
        .section-title { font-size:18px; font-weight:900; margin-bottom:16px; }
        .quick-actions { display:flex; gap:14px; flex-wrap:wrap; margin-bottom:40px; }
        .qa-btn {
            background:var(--white); border:1.5px solid var(--border); border-radius:10px;
            padding:14px 22px; font-size:13px; font-weight:800; color:var(--text-dark);
            text-decoration:none; display:inline-flex; align-items:center; gap:9px;
            transition:all 0.2s; cursor:pointer;
        }
        .qa-btn:hover { border-color:var(--primary-orange); color:var(--primary-orange); background:var(--bg-light); }
        .qa-btn.primary { background:var(--primary-orange); color:var(--white); border-color:var(--primary-orange); }
        .qa-btn.primary:hover { background:var(--primary-hover); border-color:var(--primary-hover); }

        /* Recent apps table */
        .card {
            background:var(--white); border:1px solid var(--border); border-radius:14px;
            padding:24px; margin-bottom:24px;
        }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; font-weight:800; text-transform:uppercase;
             letter-spacing:0.5px; color:var(--text-light); padding:10px 12px; border-bottom:1px solid var(--border); }
        td { padding:12px 12px; font-size:13px; font-weight:600; border-bottom:1px solid var(--border); }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#FDFBF5; }

        .badge {
            display:inline-block; padding:3px 10px; border-radius:20px;
            font-size:11px; font-weight:800; text-transform:uppercase;
        }
        .badge-active   { background:#E6F4EA; color:#27AE60; }
        .badge-rejected { background:var(--admin-red-light); color:var(--admin-red); }
        .badge-done     { background:#E8EAF6; color:#3949AB; }
        .badge-adopt    { background:#E3F2FD; color:#1565C0; }
        .badge-foster   { background:#F3E5F5; color:#6A1B9A; }

        .btn-sm {
            display:inline-flex; align-items:center; gap:6px;
            padding:6px 12px; border-radius:7px; font-size:12px; font-weight:800;
            text-decoration:none; border:none; cursor:pointer; transition:all 0.2s;
        }
        .btn-view { background:var(--bg-light); color:var(--text-dark); border:1px solid var(--border); }
        .btn-view:hover { border-color:var(--primary-orange); color:var(--primary-orange); }
    </style>
</head>
<body>
<div class="page-body">
    <div class="page-title">Good day, <?= h($_SESSION['first_name'] ?? 'Admin') ?>!</div>
    <div class="page-sub">Here is a quick overview of what is happening on FluffSide.</div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-orange"><i class="fas fa-paw"></i></div>
            <div><h3><?= count($pets) ?></h3><p>Residents Listed</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-box-open"></i></div>
            <div><h3><?= count($products) ?></h3><p>Products Listed</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-yellow"><i class="fas fa-envelope-open-text"></i></div>
            <div><h3><?= $active_apps ?></h3><p>Active Applications</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon <?= $pending_msgs > 0 ? 'icon-red' : 'icon-green' ?>">
                <i class="fas fa-comments"></i>
            </div>
            <div><h3><?= $pending_msgs ?></h3><p>Unanswered Messages</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-shopping-bag"></i></div>
            <div><h3><?= count($orders) ?></h3><p>Total Orders</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-orange"><i class="fas fa-peso-sign"></i></div>
            <div><h3>&#8369;<?= number_format($total_revenue, 0) ?></h3><p>Revenue</p></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-title">Quick Actions</div>
    <div class="quick-actions">
        <a href="residents.php?action=add" class="qa-btn primary"><i class="fas fa-plus"></i> Add New Resident</a>
        <a href="supplies.php?action=add" class="qa-btn primary"><i class="fas fa-plus"></i> Add New Product</a>
        <a href="applications.php" class="qa-btn"><i class="fas fa-envelope-open-text"></i> View Applications</a>
        <a href="../residents.php" class="qa-btn"><i class="fas fa-eye"></i> View Resident Page</a>
        <a href="../supplies.php" class="qa-btn"><i class="fas fa-eye"></i> View Supplies Page</a>
    </div>

    <!-- Recent Applications -->
    <div class="section-title">Recent Applications</div>
    <div class="card">
        <?php if (empty($apps)): ?>
            <p style="text-align:center;color:var(--text-light);padding:20px 0;font-weight:700;">No applications yet.</p>
        <?php else: ?>
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
            <?php
            $steps_label = ['','Submitted','Reviewed','Interview','Approved','Meet & Greet','Take Home'];
            $recent = array_slice(array_reverse($apps), 0, 8);
            foreach ($recent as $a):
                $step_name = $steps_label[$a['current_step']] ?? 'Step '.$a['current_step'];
                $badge_cls = $a['rejected'] ? 'badge-rejected' : ($a['status']==='completed' ? 'badge-done' : 'badge-active');
                $badge_lbl = $a['rejected'] ? 'Rejected' : ($a['status']==='completed' ? 'Completed' : 'Active');
            ?>
            <tr>
                <td style="font-family:monospace;font-weight:800;"><?= h($a['id']) ?></td>
                <td><?= h($a['user_name']) ?></td>
                <td><strong><?= h($a['pet_name']) ?></strong></td>
                <td><span class="badge <?= $a['type']==='Adoption' ? 'badge-adopt' : 'badge-foster' ?>"><?= h($a['type']) ?></span></td>
                <td><?= h($step_name) ?></td>
                <td><span class="badge <?= $badge_cls ?>"><?= $badge_lbl ?></span></td>
                <td><?= h($a['submitted_at']) ?></td>
                <td><a href="applications.php?id=<?= h($a['id']) ?>" class="btn-sm btn-view"><i class="fas fa-eye"></i> View</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php if (count($apps) > 8): ?>
        <div style="text-align:right;"><a href="applications.php" style="color:var(--primary-orange);font-weight:800;font-size:13px;text-decoration:none;">View all applications <i class="fas fa-arrow-right"></i></a></div>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
</body>
</html>
