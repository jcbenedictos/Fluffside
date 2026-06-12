<?php
require 'auth.inc.php';
require_admin();
require_once '../db.inc.php';
require_once '../db_helper.inc.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status   = trim($_POST['status'] ?? '');
    $allowed  = ['Pending','Processing','Shipped','Delivered','Cancelled'];
    if ($order_id && in_array($status, $allowed)) {
        update_order_status($order_id, $status);
    }
    header("Location: orders.php?updated=" . $order_id);
    exit;
}

$orders    = get_all_orders();
$view_id   = (int)($_GET['view'] ?? 0);
$view_order = $view_id ? get_order_by_id($view_id) : null;

// Summary stats
$total_revenue  = array_sum(array_column($orders, 'total_amount'));
$total_donation = array_sum(array_column($orders, 'donation_amount'));
$pending_count  = count(array_filter($orders, fn($o) => $o['status'] === 'Pending'));
$delivered_count= count(array_filter($orders, fn($o) => $o['status'] === 'Delivered'));

$status_colors = [
    'Pending'    => ['bg'=>'#FEF9E7','color'=>'#B7950B'],
    'Processing' => ['bg'=>'#EAF2FF','color'=>'#1A5276'],
    'Shipped'    => ['bg'=>'#E8F8F5','color'=>'#148F77'],
    'Delivered'  => ['bg'=>'#E6F4EA','color'=>'#1E8449'],
    'Cancelled'  => ['bg'=>'#FADBD8','color'=>'#C0392B'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders — FluffSide Admin</title>
    <?php include 'header.inc.php'; ?>
    <style>
        .page-body { padding: 40px 5% 80px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
        .page-title  { font-size:26px; font-weight:900; }
        .page-sub    { font-size:13px; color:var(--text-light); font-weight:600; margin-top:4px; }

        /* Stats */
        .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:32px; }
        .stat-card  {
            background:var(--white); border:1px solid var(--border); border-radius:12px;
            padding:20px 20px; display:flex; align-items:center; gap:16px;
        }
        .stat-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .icon-orange { background:#FDF1E6; color:var(--primary-orange); }
        .icon-green  { background:#E6F4EA; color:#27AE60; }
        .icon-blue   { background:#DEEBF7; color:#2980B9; }
        .icon-yellow { background:#FEF9E7; color:#D4AC0D; }
        .stat-card h3 { font-size:24px; font-weight:900; }
        .stat-card p  { font-size:12px; font-weight:700; color:var(--text-light); }

        /* Filter tabs */
        .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
        .filter-tab {
            padding:7px 16px; border-radius:20px; font-size:12px; font-weight:800;
            cursor:pointer; border:1.5px solid var(--border); background:var(--white);
            color:var(--text-dark); transition:all 0.2s;
        }
        .filter-tab:hover, .filter-tab.active { background:var(--primary-orange); color:var(--white); border-color:var(--primary-orange); }

        /* Table */
        .card { background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:24px; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; font-weight:800; text-transform:uppercase;
             letter-spacing:0.5px; color:var(--text-light); padding:12px 16px;
             border-bottom:1px solid var(--border); background:var(--bg-light); }
        td { padding:12px 16px; font-size:13px; font-weight:600; border-bottom:1px solid var(--border); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#FDFBF5; }

        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; text-transform:uppercase; }
        .btn-sm {
            display:inline-flex; align-items:center; gap:5px;
            padding:6px 12px; border-radius:7px; font-size:12px; font-weight:800;
            text-decoration:none; border:1px solid var(--border); cursor:pointer;
            transition:all 0.2s; background:var(--bg-light); color:var(--text-dark);
        }
        .btn-sm:hover { border-color:var(--primary-orange); color:var(--primary-orange); }

        /* Detail modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }
        .modal {
            background:var(--white); border-radius:18px; padding:36px;
            max-width:640px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.2);
            max-height:90vh; overflow-y:auto;
        }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
        .modal-header h3 { font-size:20px; font-weight:900; }
        .modal-close { background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-light); }
        .modal-close:hover { color:var(--text-dark); }

        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
        .info-block h4 { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); margin-bottom:4px; }
        .info-block p  { font-size:13px; font-weight:700; line-height:1.6; }

        .order-items-table { width:100%; border-collapse:collapse; margin:16px 0; }
        .order-items-table th { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:8px 0; border-bottom:2px solid var(--border); text-align:left; }
        .order-items-table th:last-child { text-align:right; }
        .order-items-table td { padding:10px 0; font-size:13px; font-weight:700; border-bottom:1px solid var(--border); }
        .order-items-table td:last-child { text-align:right; font-weight:900; }
        .order-items-table tr:last-child td { border-bottom:none; }

        .totals-block { background:#F7F4EE; border-radius:10px; padding:16px 18px; }
        .total-row { display:flex; justify-content:space-between; padding:5px 0; font-size:13px; font-weight:700; color:var(--text-light); }
        .total-row.donation { color:var(--status-green); }
        .total-row.grand    { font-size:16px; font-weight:900; color:var(--text-dark); padding-top:10px; margin-top:6px; border-top:2px solid var(--border); }
        .total-row.grand span:last-child { color:var(--primary-orange); }

        .status-form { display:flex; align-items:center; gap:10px; margin-top:20px; padding-top:20px; border-top:1px solid var(--border); }
        .status-form label { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); }
        .status-form select {
            padding:9px 14px; border:1.5px solid var(--border); border-radius:8px;
            font-size:13px; font-family:'Nunito',sans-serif; font-weight:700; color:var(--text-dark);
        }
        .status-form select:focus { outline:none; border-color:var(--primary-orange); }
        .btn-primary {
            background:var(--primary-orange); color:var(--white); border:none;
            padding:9px 20px; border-radius:8px; font-size:13px; font-weight:800;
            cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:background 0.2s;
        }
        .btn-primary:hover { background:var(--primary-hover); }

        .alert { padding:12px 16px; border-radius:8px; font-weight:700; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
        .alert-success { background:#E6F4EA; color:#1E8449; }
        .empty-state { text-align:center; padding:48px; color:var(--text-light); font-weight:700; }
        .empty-state i { font-size:36px; margin-bottom:12px; display:block; opacity:0.3; }
    </style>
</head>
<body>
<div class="page-body">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-shopping-bag" style="color:var(--primary-orange)"></i> Orders</div>
            <div class="page-sub"><?= count($orders) ?> total orders</div>
        </div>
    </div>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> Order status updated successfully.</div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-orange"><i class="fas fa-shopping-bag"></i></div>
            <div><h3><?= count($orders) ?></h3><p>Total Orders</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-peso-sign"></i></div>
            <div><h3>&#8369;<?= number_format($total_revenue, 0) ?></h3><p>Total Revenue</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-heart"></i></div>
            <div><h3>&#8369;<?= number_format($total_donation, 0) ?></h3><p>Charity Donated</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-yellow"><i class="fas fa-clock"></i></div>
            <div><h3><?= $pending_count ?></h3><p>Pending Orders</p></div>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="filter-tabs" id="filterTabs">
        <button class="filter-tab active" data-status="all">All (<?= count($orders) ?>)</button>
        <?php foreach (['Pending','Processing','Shipped','Delivered','Cancelled'] as $s):
            $cnt = count(array_filter($orders, fn($o) => $o['status'] === $s));
            if ($cnt === 0) continue;
        ?>
        <button class="filter-tab" data-status="<?= $s ?>"><?= $s ?> (<?= $cnt ?>)</button>
        <?php endforeach; ?>
    </div>

    <!-- Orders table -->
    <div class="card">
        <?php if (empty($orders)): ?>
        <div class="empty-state"><i class="fas fa-shopping-bag"></i>No orders yet.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="ordersTable">
            <?php foreach ($orders as $o):
                $sc = $status_colors[$o['status']] ?? ['bg'=>'#F0F0F0','color'=>'#888'];
            ?>
            <tr data-status="<?= h($o['status']) ?>">
                <td style="font-family:monospace;font-weight:900;"><?= h($o['order_number']) ?></td>
                <td>
                    <strong><?= h($o['account_name']) ?></strong><br>
                    <span style="font-size:11px;color:var(--text-light);"><?= h($o['account_email']) ?></span>
                </td>
                <td style="text-align:center;"><?= (int)$o['total_items'] ?></td>
                <td><?= h($o['payment_method']) ?></td>
                <td style="font-weight:900;color:var(--primary-orange);">&#8369;<?= number_format((float)$o['total_amount'], 2) ?></td>
                <td>
                    <span class="badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
                        <?= h($o['status']) ?>
                    </span>
                </td>
                <td style="font-size:12px;"><?= h(date('M j, Y', strtotime($o['ordered_at']))) ?></td>
                <td>
                    <button onclick="openOrder(<?= (int)$o['order_id'] ?>)" class="btn-sm">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal" id="orderModalContent">
        <div class="modal-header">
            <h3 id="modalOrderNum">Order Details</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div id="modalBody">
            <p style="text-align:center;color:var(--text-light);padding:20px;">Loading...</p>
        </div>
    </div>
</div>

<script>
// Filter tabs
document.getElementById('filterTabs').addEventListener('click', function(e) {
    const tab = e.target.closest('.filter-tab');
    if (!tab) return;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const status = tab.dataset.status;
    document.querySelectorAll('#ordersTable tr').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
});

// All order data embedded for modal (avoids extra AJAX)
const ordersData = <?php
$modal_data = [];
foreach ($orders as $o) {
    $full = get_order_by_id((int)$o['order_id']);
    if ($full) $modal_data[(int)$o['order_id']] = $full;
}
echo json_encode($modal_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
?>;

const statusColors = <?= json_encode($status_colors) ?>;

function openOrder(id) {
    const o = ordersData[id];
    if (!o) return;
    document.getElementById('modalOrderNum').textContent = o.order_number;

    const sc = statusColors[o.status] || {bg:'#F0F0F0', color:'#888'};
    let itemsHtml = o.items.map(item => `
        <tr>
            <td>${escHtml(item.product_title)}</td>
            <td style="text-align:center;">${item.quantity}</td>
            <td style="text-align:right;">₱${parseFloat(item.unit_price).toFixed(2)}</td>
            <td style="text-align:right;">₱${parseFloat(item.subtotal).toFixed(2)}</td>
        </tr>`).join('');

    document.getElementById('modalBody').innerHTML = `
        <div class="info-grid">
            <div class="info-block"><h4>Customer</h4><p>${escHtml(o.full_name)}<br>${escHtml(o.email)}<br>${escHtml(o.phone)}</p></div>
            <div class="info-block"><h4>Ship To</h4><p>${escHtml(o.address)}<br>${escHtml(o.city)}, ${escHtml(o.zip_code)}</p></div>
            <div class="info-block"><h4>Payment</h4><p>${escHtml(o.payment_method)}</p></div>
            <div class="info-block"><h4>Date</h4><p>${escHtml(o.ordered_at)}</p></div>
        </div>
        <table class="order-items-table">
            <thead><tr><th>Item</th><th style="text-align:center;">Qty</th><th style="text-align:right;">Unit</th><th style="text-align:right;">Subtotal</th></tr></thead>
            <tbody>${itemsHtml}</tbody>
        </table>
        <div class="totals-block">
            <div class="total-row"><span>Subtotal</span><span>₱${parseFloat(o.subtotal).toFixed(2)}</span></div>
            <div class="total-row"><span>Shipping</span><span style="color:#9BB374;font-weight:900;">FREE</span></div>
            <div class="total-row donation"><span>♥ Charity (10%)</span><span>₱${parseFloat(o.donation_amount).toFixed(2)}</span></div>
            <div class="total-row grand"><span>Total</span><span>₱${parseFloat(o.total_amount).toFixed(2)}</span></div>
        </div>
        <form method="POST" action="orders.php" class="status-form">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="order_id" value="${o.order_id}">
            <label>Update Status:</label>
            <select name="status">
                ${['Pending','Processing','Shipped','Delivered','Cancelled'].map(s =>
                    `<option value="${s}" ${s === o.status ? 'selected' : ''}>${s}</option>`
                ).join('')}
            </select>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save</button>
            <a href="../receipt.php?order_id=${o.order_id}" target="_blank" class="btn-sm" style="margin-left:auto;">
                <i class="fas fa-receipt"></i> View Receipt
            </a>
        </form>`;
    document.getElementById('orderModal').classList.add('open');
}

function closeModal() {
    document.getElementById('orderModal').classList.remove('open');
}
document.getElementById('orderModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php include '../footer.php'; ?>
</body>
</html>
