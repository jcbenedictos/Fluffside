<?php
session_start();
require_once 'db.inc.php';
require_once 'db_helper.inc.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
$order    = $order_id ? get_order_by_id($order_id) : null;

// Security: user can only view their own orders
if (!$order || (int)$order['user_id'] !== (int)$_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt <?= htmlspecialchars($order['order_number']) ?> — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #EF8E35;
            --bg-light:       #FDFBF5;
            --text-dark:      #5A483E;
            --text-light:     #8E8279;
            --btn-green:      #9BB374;
            --border:         #EAE3D9;
            --white:          #FFFFFF;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Nunito',sans-serif; }
        body { background:var(--bg-light); color:var(--text-dark); }

        /* Screen-only actions bar */
        .action-bar {
            background:var(--white); border-bottom:1px solid var(--border);
            padding:14px 5%; display:flex; align-items:center; justify-content:space-between;
        }
        .action-bar .left { display:flex; align-items:center; gap:14px; }
        .btn-back {
            display:inline-flex; align-items:center; gap:7px;
            color:var(--text-light); text-decoration:none; font-weight:800; font-size:13px;
            transition:color 0.2s;
        }
        .btn-back:hover { color:var(--primary-orange); }
        .btn-print {
            background:var(--primary-orange); color:var(--white); border:none;
            padding:10px 22px; border-radius:8px; font-size:13px; font-weight:800;
            cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:background 0.2s;
        }
        .btn-print:hover { background:#D67A26; }

        /* Receipt card */
        .receipt-wrap { max-width:680px; margin:36px auto 80px; padding:0 20px; }
        .receipt {
            background:var(--white); border:1px solid var(--border); border-radius:18px;
            overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.06);
        }

        /* Header */
        .receipt-header {
            background: linear-gradient(135deg, #5A483E 0%, #7A6258 100%);
            color:var(--white); padding:32px 36px; text-align:center;
        }
        .receipt-header .logo { font-size:28px; font-weight:900; letter-spacing:1px; margin-bottom:4px; }
        .receipt-header .logo span { color:var(--primary-orange); }
        .receipt-header .tagline { font-size:12px; font-weight:600; opacity:0.7; margin-bottom:20px; }
        .receipt-header .order-num {
            display:inline-block; background:var(--primary-orange);
            padding:8px 24px; border-radius:30px;
            font-size:18px; font-weight:900; letter-spacing:0.5px;
        }

        /* Body */
        .receipt-body { padding:32px 36px; }

        /* Status banner */
        .status-banner {
            display:flex; align-items:center; gap:10px; padding:12px 18px;
            border-radius:10px; margin-bottom:24px; font-weight:800; font-size:14px;
        }
        .status-pending   { background:#FEF9E7; color:#B7950B; border:1px solid #F9E79F; }
        .status-delivered { background:#E6F4EA; color:#1E8449; border:1px solid #A9DFBF; }
        .status-cancelled { background:#FADBD8; color:#C0392B; border:1px solid #F1948A; }
        .status-other     { background:#EAF2FF; color:#1A5276; border:1px solid #AED6F1; }

        /* Info grid */
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:28px; }
        .info-block h4 { font-size:11px; font-weight:800; text-transform:uppercase;
                         letter-spacing:0.5px; color:var(--text-light); margin-bottom:6px; }
        .info-block p  { font-size:13px; font-weight:700; color:var(--text-dark); line-height:1.6; }

        /* Divider */
        .divider { border:none; border-top:1px dashed var(--border); margin:24px 0; }

        /* Items table */
        .items-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
        .items-table th {
            text-align:left; font-size:11px; font-weight:800; text-transform:uppercase;
            letter-spacing:0.4px; color:var(--text-light); padding:8px 10px;
            border-bottom:2px solid var(--border);
        }
        .items-table th:last-child { text-align:right; }
        .items-table td { padding:12px 10px; font-size:13px; font-weight:700; border-bottom:1px solid var(--border); vertical-align:middle; }
        .items-table td:last-child { text-align:right; font-weight:900; }
        .items-table tr:last-child td { border-bottom:none; }
        .item-title { font-weight:800; font-size:13px; }
        .item-sub   { font-size:11px; color:var(--text-light); font-weight:600; margin-top:2px; }

        /* Totals */
        .totals { background:#F7F4EE; border-radius:10px; padding:18px 20px; }
        .total-row { display:flex; justify-content:space-between; padding:6px 0; font-size:13px; font-weight:700; color:var(--text-light); }
        .total-row.donation { color:var(--btn-green); }
        .total-row.grand { font-size:18px; font-weight:900; color:var(--text-dark); padding-top:12px; margin-top:8px; border-top:2px solid var(--border); }
        .total-row.grand span:last-child { color:var(--primary-orange); }

        /* Footer */
        .receipt-footer {
            background:var(--bg-light); border-top:1px solid var(--border);
            padding:20px 36px; text-align:center;
        }
        .receipt-footer p { font-size:12px; font-weight:700; color:var(--text-light); line-height:1.7; }
        .receipt-footer .paw { color:var(--primary-orange); font-size:16px; margin:8px 0; display:block; }

        /* Print styles */
        @media print {
            .action-bar { display:none !important; }
            body { background:var(--white); }
            .receipt-wrap { margin:0; padding:0; max-width:100%; }
            .receipt { box-shadow:none; border:none; }
        }
    </style>
</head>
<body>

<!-- Action bar (hidden on print) -->
<div class="action-bar">
    <div class="left">
        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
        <span style="color:var(--border);">|</span>
        <span style="font-size:13px;font-weight:800;color:var(--text-light);">Order Receipt</span>
    </div>
    <button class="btn-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Receipt
    </button>
</div>

<div class="receipt-wrap">
    <div class="receipt">

        <!-- Header -->
        <div class="receipt-header">
            <div class="logo">Fluff<span>Side</span></div>
            <div class="tagline">Every purchase helps our shelter residents</div>
            <div class="order-num"><?= htmlspecialchars($order['order_number']) ?></div>
        </div>

        <div class="receipt-body">

            <!-- Status -->
            <?php
            $status_cls = match($order['status']) {
                'Delivered'  => 'status-delivered',
                'Cancelled'  => 'status-cancelled',
                'Pending','Processing','Shipped' => 'status-pending',
                default      => 'status-other',
            };
            $status_icon = match($order['status']) {
                'Delivered'  => 'fa-check-circle',
                'Cancelled'  => 'fa-times-circle',
                default      => 'fa-clock',
            };
            ?>
            <div class="status-banner <?= $status_cls ?>">
                <i class="fas <?= $status_icon ?>"></i>
                Order Status: <?= htmlspecialchars($order['status']) ?>
                <span style="margin-left:auto;font-size:12px;font-weight:700;opacity:0.8;">
                    <?= htmlspecialchars(date('F j, Y g:i A', strtotime($order['ordered_at']))) ?>
                </span>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-block">
                    <h4><i class="fas fa-user"></i> Customer</h4>
                    <p>
                        <?= htmlspecialchars($order['full_name']) ?><br>
                        <?= htmlspecialchars($order['email']) ?><br>
                        <?= htmlspecialchars($order['phone']) ?>
                    </p>
                </div>
                <div class="info-block">
                    <h4><i class="fas fa-map-marker-alt"></i> Ship To</h4>
                    <p>
                        <?= htmlspecialchars($order['address']) ?><br>
                        <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['zip_code']) ?>
                    </p>
                </div>
                <div class="info-block">
                    <h4><i class="fas fa-credit-card"></i> Payment Method</h4>
                    <p><?= htmlspecialchars($order['payment_method']) ?></p>
                </div>
                <div class="info-block">
                    <h4><i class="fas fa-calendar-alt"></i> Order Date</h4>
                    <p><?= htmlspecialchars(date('F j, Y', strtotime($order['ordered_at']))) ?><br>
                       <?= htmlspecialchars(date('g:i A', strtotime($order['ordered_at']))) ?></p>
                </div>
            </div>

            <hr class="divider">

            <!-- Items -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td>
                            <div class="item-title"><?= htmlspecialchars($item['product_title']) ?></div>
                        </td>
                        <td style="text-align:center;"><?= (int)$item['quantity'] ?></td>
                        <td style="text-align:right;">&#8369;<?= number_format((float)$item['unit_price'], 2) ?></td>
                        <td>&#8369;<?= number_format((float)$item['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>&#8369;<?= number_format((float)$order['subtotal'], 2) ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping</span>
                    <span style="color:var(--btn-green);font-weight:900;">FREE</span>
                </div>
                <div class="total-row donation">
                    <span><i class="fas fa-heart"></i> Charity Donation (10%)</span>
                    <span>&#8369;<?= number_format((float)$order['donation_amount'], 2) ?></span>
                </div>
                <div class="total-row grand">
                    <span>Total Paid</span>
                    <span>&#8369;<?= number_format((float)$order['total_amount'], 2) ?></span>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <span class="paw"><i class="fas fa-paw"></i></span>
            <p>
                Thank you for shopping with FluffSide!<br>
                10% of your purchase goes directly to supporting our shelter residents.<br>
                For concerns, please contact us through the Help Center.
            </p>
        </div>

    </div>
</div>

</body>
</html>
