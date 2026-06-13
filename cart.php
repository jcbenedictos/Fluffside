<?php
session_start();
require_once 'db.inc.php';
require_once 'product.inc.php';

if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit;
}


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_is_empty = empty($_SESSION['cart']);
$cart_total_items = 0;
$grand_total = 0;

foreach ($_SESSION['cart'] as $quantity) {
    $cart_total_items += $quantity;
}

$product_lookup = [];
if (isset($products)) {
    foreach ($products as $p) {
        $product_lookup[$p->id] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
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

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Nunito', sans-serif;
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-dark);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    .container {
        max-width: 100%;
        margin: 0;
        padding: 0 5%;
        width: 100%;
    }

    

    .section-title {
        font-size: 22px;
        font-weight: 900;
        margin-bottom: 30px;
        text-transform: uppercase;
    }

    .section-title span {
        color: var(--primary-orange);
    }

    .empty-cart-wrapper {
        background-color: var(--white);
        border-radius: 15px;
        border: 1px solid #EAE3D9;
        padding: 80px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 80px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .empty-cart-icon {
        width: 120px;
        height: 120px;
        background-color: var(--bg-yellow-light);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 50px;
        color: var(--primary-orange);
        margin-bottom: 25px;
    }

    .empty-cart-wrapper h2 {
        font-size: 28px;
        font-weight: 900;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .empty-cart-wrapper p {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-light);
        margin-bottom: 35px;
        max-width: 400px;
        line-height: 1.5;
    }

    .cart-layout {
        display: flex;
        gap: 30px;
        align-items: flex-start;
        margin-bottom: 80px;
        width: 100%;
    }

    .cart-items-container {
        flex: 1;
        background-color: var(--white);
        border-radius: 15px;
        border: 1px solid #EAE3D9;
        overflow: hidden;
    }

    .cart-header-row {
        display: grid;
        grid-template-columns: 3fr 1fr 1fr auto;
        padding: 15px 25px;
        background-color: #F8F6F0;
        border-bottom: 1px solid #EAE3D9;
        font-weight: 800;
        font-size: 12px;
        text-transform: uppercase;
        color: var(--text-light);
    }

    .cart-item {
        display: grid;
        grid-template-columns: 3fr 1fr 1fr auto;
        align-items: center;
        padding: 25px;
        border-bottom: 1px solid #EAE3D9;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .item-img-box {
        width: 80px;
        height: 80px;
        background-color: #F8F8F8;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
    }

    .item-img-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        mix-blend-mode: multiply;
    }

    .item-details h3 {
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .item-details p {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-orange);
    }

    .item-qty {
        font-weight: 800;
        font-size: 15px;
    }

    .item-subtotal {
        font-weight: 900;
        font-size: 16px;
        color: var(--text-dark);
    }

    .btn-remove {
        color: #E74C3C;
        text-decoration: none;
        font-size: 18px;
        transition: 0.2s;
    }

    .btn-remove:hover {
        transform: scale(1.1);
    }

    .cart-summary {
        width: 350px;
        background-color: var(--white);
        border-radius: 15px;
        border: 1px solid #EAE3D9;
        padding: 30px;
        position: sticky;
        top: 20px;
    }

    .summary-title {
        font-size: 18px;
        font-weight: 900;
        margin-bottom: 20px;
        border-bottom: 2px solid #F8F6F0;
        padding-bottom: 15px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 15px;
        font-weight: 700;
        color: var(--text-light);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #F8F6F0;
        font-size: 20px;
        font-weight: 900;
        color: var(--text-dark);
    }

    .btn-checkout {
        display: block;
        width: 100%;
        background-color: var(--btn-green);
        color: var(--white);
        text-align: center;
        padding: 15px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 900;
        font-size: 15px;
        margin-top: 25px;
        transition: 0.2s;
        text-transform: uppercase;
    }

    .btn-checkout:hover {
        background-color: #8DA466;
        transform: translateY(-2px);
    }

    .btn-browse {
        background-color: var(--primary-orange);
        color: var(--white);
        padding: 15px 40px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 800;
        font-size: 15px;
        display: inline-block;
        transition: 0.2s;
    }

    .btn-browse:hover {
        background-color: var(--primary-hover);
    }
</style>

<body>

    <div class="container">
       <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?>


        <section class="cart-section">
            <h2 class="section-title">YOUR <span>CART</span></h2>

            <?php if ($cart_is_empty): ?>

                <div class="empty-cart-wrapper">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <h2>Your cart is empty!</h2>
                    <p>Looks like you haven't added any treats, toys, or supplies for your furry friends yet.</p>

                    <a href="supplies.php" class="btn-browse">
                        <i class="fas fa-paw"></i> BROWSE SUPPLIES
                    </a>
                </div>
            <?php else: ?>

                <div class="cart-layout">

                    <div class="cart-items-container">
                        <div class="cart-header-row">
                            <div>Product</div>
                            <div>Quantity</div>
                            <div>Subtotal</div>
                            <div></div>
                        </div>

                        <?php foreach ($_SESSION['cart'] as $id => $quantity): ?>
                            <?php
                            if (isset($product_lookup[$id])) {
                                $product = $product_lookup[$id];

                                $item_subtotal = $product->price * $quantity;
                                $grand_total += $item_subtotal;
                            } else {
                                continue;
                            }
                            ?>
                            <div class="cart-item">
                                <div class="item-info">
                                    <div class="item-img-box">
                                        <img src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->title) ?>" onerror="this.src='placeholder.jpg'; this.style.opacity='0.2';">
                                    </div>
                                    <div class="item-details">
                                        <h3><?= htmlspecialchars($product->title) ?></h3>
                                        <p><?= $product->getFormattedPrice() ?></p>
                                    </div>
                                </div>
                                <div class="item-qty">
                                    <?= $quantity ?>
                                </div>
                                <div class="item-subtotal">
                                    ₱<?= number_format($item_subtotal, 2) ?>
                                </div>
                                <a href="cart.php?remove=<?= $product->id ?>" class="btn-remove" title="Remove Item">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h3 class="summary-title">Order Summary</h3>

                        <div class="summary-row">
                            <span>Total Items:</span>
                            <span><?= $cart_total_items ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>₱<?= number_format($grand_total, 2) ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Charity Donation (10%):</span>
                            <span style="color: var(--primary-orange);"><i class="fas fa-heart"></i> ₱<?= number_format($grand_total * 0.10, 2) ?></span>
                        </div>

                        <div class="summary-total">
                            <span>Total:</span>
                            <span style="color: var(--primary-orange);">₱<?= number_format($grand_total * 1.10, 2) ?></span>
                        </div>

                        <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                    </div>
                </div>
            <?php endif; ?>

        </section>

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
        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function(e) {
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>

</body>

</html>