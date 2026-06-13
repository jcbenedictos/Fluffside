<?php
session_start();
require_once 'db.inc.php';
require_once 'product.inc.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Handle order placement
$order_placed = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Clear the cart after order
    $_SESSION['cart'] = [];
    $order_placed = true;
}

$product_lookup = [];
if (isset($products)) {
    foreach ($products as $p) {
        $product_lookup[$p->id] = $p;
    }
}

$grand_total = 0;
$cart_total_items = 0;
if (!$order_placed) {
    foreach ($_SESSION['cart'] as $id => $quantity) {
        if (isset($product_lookup[$id])) {
            $grand_total += $product_lookup[$id]->price * $quantity;
            $cart_total_items += $quantity;
        }
    }
}
$donation = $grand_total * 0.10;
$final_total = $grand_total + $donation;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #EF8E35;
            --primary-hover: #D67A26;
            --bg-light: #FDFBF5;
            --text-dark: #5A483E;
            --text-light: #8E8279;
            --bg-yellow-light: #FCEABB;
            --btn-green: #9BB374;
            --footer-green: #B8C7A1;
            --white: #FFFFFF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Nunito', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }
        .container { max-width: 100%; margin: 0; padding: 0 5%; width: 100%; }

        header { display: flex; justify-content: space-between; align-items: center; padding: 0; margin-top: 15px; margin-bottom: 30px; width: 100%; }
        .logo-img { height: 250px; width: auto; mix-blend-mode: multiply; margin-top: -100px; margin-bottom: -100px; }
        nav ul { display: flex; list-style: none; gap: 30px; align-items: center; margin: 0; }
        nav a { text-decoration: none; color: var(--text-dark); font-weight: 800; font-size: 13px; text-transform: uppercase; transition: color 0.2s; }
        nav a:hover { color: var(--primary-orange); }
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .cart-icon { color: var(--primary-orange); font-size: 20px; text-decoration: none; position: relative; }
        .btn-header { background-color: var(--primary-orange); color: var(--white); padding: 10px 24px; border-radius: 30px; text-decoration: none; font-weight: 800; font-size: 14px; text-transform: uppercase; }

        .section-title { font-size: 22px; font-weight: 900; margin-bottom: 30px; text-transform: uppercase; }
        .section-title span { color: var(--primary-orange); }

        /* Steps */
        .checkout-steps { display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 40px; }
        .step { display: flex; align-items: center; gap: 10px; }
        .step-circle { width: 36px; height: 36px; border-radius: 50%; background-color: var(--bg-yellow-light); color: var(--text-light); font-weight: 900; font-size: 14px; display: flex; align-items: center; justify-content: center; }
        .step.active .step-circle { background-color: var(--primary-orange); color: white; }
        .step.done .step-circle { background-color: var(--btn-green); color: white; }
        .step-label { font-weight: 800; font-size: 13px; color: var(--text-light); text-transform: uppercase; }
        .step.active .step-label { color: var(--primary-orange); }
        .step.done .step-label { color: var(--btn-green); }
        .step-line { width: 60px; height: 2px; background-color: #EAE3D9; margin: 0 5px; }
        .step-line.done { background-color: var(--btn-green); }

        /* Layout */
        .checkout-layout { display: flex; gap: 30px; align-items: flex-start; margin-bottom: 80px; }
        .checkout-main { flex: 1; }
        .checkout-card { background-color: var(--white); border-radius: 15px; border: 1px solid #EAE3D9; padding: 30px; margin-bottom: 25px; }
        .checkout-card h3 { font-size: 16px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase; border-bottom: 2px solid #F8F6F0; padding-bottom: 12px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group.full { grid-column: 1 / -1; }
        .form-label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 6px; color: var(--text-dark); }
        .form-control { width: 100%; padding: 12px 14px; background-color: var(--bg-light); border-radius: 8px; font-size: 14px; color: var(--text-dark); border: 1px solid #EAE3D9; outline: none; transition: border-color 0.2s; font-family: 'Nunito', sans-serif; }
        .form-control:focus { border-color: var(--primary-orange); }

        .payment-options { display: flex; gap: 12px; flex-wrap: wrap; }
        .payment-btn { flex: 1; min-width: 120px; padding: 14px; border: 2px solid #EAE3D9; border-radius: 10px; background: var(--white); cursor: pointer; font-weight: 800; font-size: 13px; color: var(--text-light); text-align: center; transition: all 0.2s; }
        .payment-btn:hover, .payment-btn.selected { border-color: var(--primary-orange); color: var(--primary-orange); background-color: #FFF8F0; }
        .payment-btn i { display: block; font-size: 22px; margin-bottom: 6px; }

        /* Order Summary */
        .checkout-summary { width: 350px; position: sticky; top: 20px; }
        .summary-card { background-color: var(--white); border-radius: 15px; border: 1px solid #EAE3D9; padding: 30px; }
        .summary-title { font-size: 18px; font-weight: 900; margin-bottom: 20px; border-bottom: 2px solid #F8F6F0; padding-bottom: 15px; }
        .summary-item { display: flex; gap: 12px; align-items: center; margin-bottom: 15px; }
        .summary-item img { width: 50px; height: 50px; object-fit: contain; border-radius: 6px; background: #F8F8F8; padding: 4px; mix-blend-mode: multiply; }
        .summary-item-info { flex: 1; }
        .summary-item-info h4 { font-size: 13px; font-weight: 800; }
        .summary-item-info p { font-size: 12px; color: var(--text-light); font-weight: 700; }
        .summary-item-price { font-size: 14px; font-weight: 900; white-space: nowrap; }
        .summary-divider { border: none; border-top: 1px solid #EAE3D9; margin: 15px 0; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; font-weight: 700; color: var(--text-light); }
        .summary-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px solid #F8F6F0; font-size: 20px; font-weight: 900; color: var(--text-dark); }
        .summary-donation { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; font-weight: 700; color: var(--btn-green); }

        .btn-place-order { display: block; width: 100%; background-color: var(--btn-green); color: var(--white); text-align: center; padding: 16px; border-radius: 30px; font-weight: 900; font-size: 15px; margin-top: 20px; border: none; cursor: pointer; text-transform: uppercase; transition: 0.2s; }
        .btn-place-order:hover { background-color: #8DA466; transform: translateY(-2px); }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; color: var(--text-light); text-decoration: none; font-weight: 800; font-size: 13px; margin-bottom: 20px; transition: color 0.2s; }
        .btn-back:hover { color: var(--primary-orange); }

        /* Success */
        .success-wrapper { text-align: center; background: var(--white); border-radius: 20px; border: 1px solid #EAE3D9; padding: 80px 40px; margin-bottom: 80px; }
        .success-icon { width: 100px; height: 100px; background-color: #E1E8B8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 45px; color: var(--btn-green); margin: 0 auto 25px; }
        .success-wrapper h2 { font-size: 28px; font-weight: 900; margin-bottom: 10px; }
        .success-wrapper p { font-size: 15px; color: var(--text-light); font-weight: 700; margin-bottom: 30px; max-width: 420px; margin-left: auto; margin-right: auto; }
        .btn-home { background-color: var(--primary-orange); color: var(--white); padding: 14px 35px; border-radius: 8px; text-decoration: none; font-weight: 800; font-size: 15px; display: inline-block; transition: 0.2s; }
        .btn-home:hover { background-color: var(--primary-hover); }

        .note-donation { background: #E1E8B8; border-radius: 10px; padding: 12px 15px; font-size: 13px; font-weight: 700; color: #5A6B31; margin-top: 15px; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <a href="index.php"><img src="Assets/Fluffside.png" alt="Logo" class="logo-img" onerror="this.outerHTML='<h1 style=&quot;color:#EF8E35&quot;>FluffSide</h1>'"></a>
        <nav>
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="residents.php">RESIDENTS</a></li>
                <li><a href="supplies.php">SUPPLIES</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a>
            <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <a href="profile.php" class="btn-header">ACCOUNT</a>
            <?php else: ?>
                <a href="login.php" class="btn-header">LOG IN</a>
            <?php endif; ?>
        </div>
    </header>

    <h2 class="section-title">CHECK<span>OUT</span></h2>

    <?php if ($order_placed): ?>

        <div class="success-wrapper">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Order Placed! 🐾</h2>
            <p>Thank you for your purchase! A portion of your order goes directly to supporting our shelter residents.</p>
            <a href="supplies.php" class="btn-home"><i class="fas fa-paw"></i> Continue Shopping</a>
        </div>

    <?php else: ?>

        <!-- Steps -->
        <div class="checkout-steps">
            <div class="step done">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <span class="step-label">Cart</span>
            </div>
            <div class="step-line done"></div>
            <div class="step active">
                <div class="step-circle">2</div>
                <span class="step-label">Checkout</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-circle">3</div>
                <span class="step-label">Confirm</span>
            </div>
        </div>

        <a href="cart.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Cart</a>

        <form action="checkout.php" method="POST">
            <div class="checkout-layout">

                <div class="checkout-main">

                    <!-- Shipping Info -->
                    <div class="checkout-card">
                        <h3><i class="fas fa-map-marker-alt" style="color:var(--primary-orange)"></i> Shipping Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['first_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['last_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" placeholder="09XXXXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Complete Address</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['address'] ?? '') ?>" placeholder="House No., Street, Barangay, City, Province" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">City / Municipality</label>
                                <input type="text" class="form-control" placeholder="e.g. Quezon City" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" placeholder="e.g. 1100" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-card">
                        <h3><i class="fas fa-credit-card" style="color:var(--primary-orange)"></i> Payment Method</h3>
                        <div class="payment-options">
                            <button type="button" class="payment-btn selected" onclick="selectPayment(this)">
                                <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                            </button>
                            <button type="button" class="payment-btn" onclick="selectPayment(this)">
                                <i class="fas fa-university"></i> GCash
                            </button>
                            <button type="button" class="payment-btn" onclick="selectPayment(this)">
                                <i class="fas fa-credit-card"></i> Credit / Debit Card
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Order Summary -->
                <div class="checkout-summary">
                    <div class="summary-card">
                        <h3 class="summary-title">Order Summary</h3>

                        <?php foreach ($_SESSION['cart'] as $id => $quantity):
                            if (!isset($product_lookup[$id])) continue;
                            $p = $product_lookup[$id];
                            $sub = $p->price * $quantity;
                        ?>
                        <div class="summary-item">
                            <img src="<?= htmlspecialchars($p->image) ?>" alt="<?= htmlspecialchars($p->title) ?>" onerror="this.style.opacity='0.2'">
                            <div class="summary-item-info">
                                <h4><?= htmlspecialchars($p->title) ?></h4>
                                <p>Qty: <?= $quantity ?> &times; <?= $p->getFormattedPrice() ?></p>
                            </div>
                            <div class="summary-item-price">₱<?= number_format($sub, 2) ?></div>
                        </div>
                        <?php endforeach; ?>

                        <hr class="summary-divider">

                        <div class="summary-row">
                            <span>Subtotal (<?= $cart_total_items ?> item<?= $cart_total_items > 1 ? 's' : '' ?>):</span>
                            <span>₱<?= number_format($grand_total, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span style="color:var(--btn-green);font-weight:900;">FREE</span>
                        </div>
                        <div class="summary-donation">
                            <span><i class="fas fa-heart"></i> Charity Donation (10%):</span>
                            <span>₱<?= number_format($donation, 2) ?></span>
                        </div>

                        <div class="summary-total">
                            <span>Total:</span>
                            <span style="color:var(--primary-orange);">₱<?= number_format($final_total, 2) ?></span>
                        </div>

                        <div class="note-donation">
                            <i class="fas fa-paw"></i>
                            10% of every purchase supports our shelter residents!
                        </div>

                        <button type="submit" name="place_order" class="btn-place-order">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                    </div>
                </div>

            </div>
        </form>

    <?php endif; ?>

</div>

<script>
    function selectPayment(btn) {
        document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
    }

    // 30-second inactivity logout
    let inactivityTimer;
    function resetTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(function() {
            window.location.href = 'logout.php?reason=inactive';
        }, 30000);
    }
    ['mousemove','keydown','click','scroll','touchstart'].forEach(function(e) {
        document.addEventListener(e, resetTimer);
    });
    resetTimer();
</script>

</body>
</html>
