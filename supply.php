<?php
// supply.php — LAHAT NA NG PRODUCT ANDITO, SABAYAN NA
// URL: supply.php?id=1  →  loads product #1
// No dupe need

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

require_once 'product.inc.php';

// ── Grab the product ID from the URL ─────────────────────────
$product_id = (int)($_GET['id'] ?? 0);

// ── Guard: 404 if not found ───────────────────────────────────
if ($product_id === 0 || !isset($product_lookup[$product_id])) {
    http_response_code(404);
    echo'<!DOCTYPE html><html><head><title>Product not found — FluffSide</title>
         <style>body{font-family:Nunito,sans-serif;display:flex;align-items:center;
         justify-content:center;height:100vh;flex-direction:column;gap:16px;color:#5A483E;}
         a{color:#EF8E35;font-weight:800;}</style></head>
         <body><h2>Product not found.</h2>
         <a href="supplies.php">← Back to Supplies</a>    <script>
        // 30-second inactivity logout
        let inactivityTimer;
        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(function() {
                window.location.href = "logout.php?reason=inactive";
            }, 30000);
        }
        ["mousemove","keydown","click","scroll","touchstart"].forEach(function(e) {
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>
</body></html>'; exit;
}

$product = $product_lookup[$product_id];

// ── Add to cart (POST from THIS page) ────────────────────────
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    if (isset($_SESSION['cart'][$product->id])) {
        $_SESSION['cart'][$product->id] += $qty;
    } else {
        $_SESSION['cart'][$product->id] = $qty;
    }
    // Redirect back to same page (PRG pattern)
    header("Location: supply.php?id={$product->id}&added=1");
    exit;
}

$cart_total_items = array_sum($_SESSION['cart']);
$just_added = isset($_GET['added']);

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($product->title) ?> — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #EF8E35;
            --primary-hover:  #D67A26;
            --bg-light:       #FDFBF5;
            --text-dark:      #5A483E;
            --text-light:     #8E8279;
            --box-yellow:     #FFF9EE;
            --box-border:     #EAE3D9;
            --btn-green:      #9BB374;
            --btn-green-hover:#8DA466;
            --white:          #FFFFFF;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html { min-height: 100%; overflow-y: scroll; }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0 5%;
            width: 100%;
        }

        /* ── Back link ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-light);
            text-decoration: none;
            margin-bottom: 28px;
            transition: color .2s;
        }

        .back-link:hover { color: var(--primary-orange); }
        .back-link i { font-size: 11px; }

        /* ── Toast: added to cart ── */
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            background: var(--btn-green);
            color: var(--white);
            padding: 14px 22px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 999;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
            animation: slideIn .3s ease, fadeOut .4s ease 2.4s forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(40px); }
        }

        /* ── Product detail grid ── */
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 50px;
            align-items: flex-start;
        }

        /* ── LEFT: Gallery ── */
        .gallery { display: flex; flex-direction: column; gap: 14px; }

        .gallery-main {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: contain;
            border-radius: 18px;
            background: var(--white);
            border: 1px solid var(--box-border);
            display: block;
            padding: 30px;
            transition: opacity .25s;
        }

        .gallery-thumbs { display: flex; gap: 12px; }

        .gallery-thumb {
            width: 110px;
            height: 80px;
            object-fit: contain;
            border-radius: 10px;
            cursor: pointer;
            border: 3px solid transparent;
            background: var(--white);
            padding: 6px;
            transition: border-color .2s, opacity .2s;
            flex-shrink: 0;
        }

        .gallery-thumb:hover   { opacity: .85; }
        .gallery-thumb.active  { border-color: var(--primary-orange); }

        /* ── RIGHT: Info panel ── */
        .product-info-panel { display: flex; flex-direction: column; gap: 18px; }

        /* Breadcrumb-style subtitle */
        .product-subtitle-line {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-light);
        }

        .product-name {
            font-size: 38px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--text-dark);
        }

        /* Star rating row */
        .rating-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
        }

        .stars { color: #F5A623; font-size: 18px; letter-spacing: 1px; }

        .rating-row .rating-text { color: var(--text-dark); }
        .rating-row .reviews-link {
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 800;
        }

        /* Divider */
        .info-divider {
            border: none;
            border-top: 1.5px solid var(--box-border);
        }

        /* SIZE label */
        .size-label {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .size-label span { color: var(--primary-orange); }

        /* Short description */
        .short-desc {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.65;
            color: var(--text-dark);
        }

        /* Flavor chips */
        .flavor-section { display: flex; flex-direction: column; gap: 10px; }
        .flavor-label { font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }

        .flavor-chips { display: flex; gap: 10px; flex-wrap: wrap; }

        .flavor-chip {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1.5px solid var(--box-border);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            background: var(--white);
        }

        .flavor-chip:hover,
        .flavor-chip.active {
            border-color: var(--primary-orange);
            background: #FFF3E6;
        }

        /* Price */
        .product-price {
            font-size: 44px;
            font-weight: 900;
            color: var(--primary-orange);
            line-height: 1;
        }

        /* Quantity + ADD TO CART */
        .qty-cart-row {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .qty-label {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            width: 100%;
            margin-bottom: 10px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--box-border);
            border-radius: 8px;
            overflow: hidden;
            height: 46px;
        }

        .qty-btn {
            width: 42px;
            height: 100%;
            background: var(--box-yellow);
            border: none;
            font-size: 18px;
            font-weight: 900;
            cursor: pointer;
            color: var(--text-dark);
            transition: background .15s;
            font-family: 'Nunito', sans-serif;
        }

        .qty-btn:hover { background: #EDE9D8; }

        .qty-input {
            width: 52px;
            height: 100%;
            border: none;
            border-left: 1.5px solid var(--box-border);
            border-right: 1.5px solid var(--box-border);
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
            color: var(--text-dark);
            background: var(--white);
            outline: none;
        }

        /* Remove number spinners */
        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }
        .qty-input[type=number] { appearance: textfield; }

        .btn-add-to-cart {
            flex: 1;
            min-width: 160px;
            background-color: var(--primary-orange);
            color: var(--white);
            padding: 0 24px;
            height: 46px;
            border-radius: 8px;
            border: none;
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .2s, transform .1s;
        }

        .btn-add-to-cart:hover { background: var(--primary-hover); }
        .btn-add-to-cart:active { transform: scale(.97); }

        .btn-reviews {
            height: 46px;
            padding: 0 24px;
            border-radius: 8px;
            border: 2px solid var(--primary-orange);
            background: transparent;
            color: var(--primary-orange);
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .btn-reviews:hover {
            background: var(--primary-orange);
            color: var(--white);
        }

        /* BUY NOW */
        .btn-buy-now {
            display: block;
            width: 100%;
            height: 50px;
            border-radius: 8px;
            border: 2px solid var(--primary-orange);
            background: transparent;
            color: var(--primary-orange);
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s, color .2s;
            letter-spacing: .06em;
        }

        .btn-buy-now:hover {
            background: var(--primary-orange);
            color: var(--white);
        }

        /* ── Bottom: Specs + Description ── */
        .product-bottom {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-bottom: 80px;
        }

        /* Specs table */
        .specs-box {
            background: var(--white);
            border: 1px solid var(--box-border);
            border-radius: 16px;
            overflow: hidden;
        }

        .specs-header {
            padding: 18px 28px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            background: var(--box-yellow);
            border-bottom: 1px solid var(--box-border);
        }

        .specs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .specs-table tr { border-bottom: 1px solid var(--box-border); }
        .specs-table tr:last-child { border-bottom: none; }

        .specs-table td {
            padding: 12px 28px;
            font-size: 13px;
            font-weight: 600;
            vertical-align: top;
        }

        .specs-table td:first-child {
            width: 180px;
            color: var(--text-dark);
            font-weight: 800;
        }

        .specs-table td:last-child { color: var(--primary-orange); font-weight: 700; }

        /* Description box */
        .desc-box {
            background: var(--white);
            border: 1px solid var(--box-border);
            border-radius: 16px;
            overflow: hidden;
        }

        .desc-header {
            padding: 18px 28px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            background: var(--box-yellow);
            border-bottom: 1px solid var(--box-border);
        }

        .desc-body { padding: 28px; }

        .desc-body p {
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.75;
            color: var(--text-dark);
            margin-bottom: 14px;
        }

        .desc-body p:last-child { margin-bottom: 0; }

        .desc-section-title {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--primary-orange);
            letter-spacing: .05em;
            margin: 22px 0 10px;
        }

        .desc-list {
            list-style: none;
            padding: 0;
            margin: 0 0 8px 0;
        }

        .desc-list li {
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.7;
            color: var(--text-dark);
            padding-left: 14px;
            position: relative;
        }

        .desc-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--primary-orange);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .product-detail { grid-template-columns: 1fr; gap: 30px; }
            .product-name   { font-size: 28px; }
        }

        @media (max-width: 600px) {
            .gallery-thumb  { width: 72px; height: 54px; }
            .qty-cart-row   { flex-direction: column; align-items: stretch; }
            .btn-add-to-cart, .btn-reviews { width: 100%; }
            .product-price  { font-size: 34px; }
        }
    </style>
</head>
<body>

<div class="container">

    <?php include 'header.php'; ?>

    <!-- Toast: added to cart confirmation -->
    <?php if ($just_added): ?>
        <div class="toast">
            <i class="fas fa-check-circle"></i>
            Added to cart!
        </div>
    <?php endif; ?>

    <!-- Back link -->
    <a href="supplies.php" class="back-link">
        <i class="fas fa-chevron-left"></i> Back to Results
    </a>

    <!-- ════ PRODUCT DETAIL TOP ════ -->
    <div class="product-detail">

        <!-- ── LEFT: Gallery ── -->
        <div class="gallery">
            <img src="<?= h($product->gallery[0] ?? $product->image) ?>"
                 alt="<?= h($product->title) ?>"
                 class="gallery-main"
                 id="mainPhoto"
                 onerror="this.src='placeholder.jpg';">

            <?php if (count($product->gallery) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($product->gallery as $i => $img): ?>
                        <img src="<?= h($img) ?>"
                             alt="<?= h($product->title) ?> photo <?= $i + 1 ?>"
                             class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                             onclick="switchPhoto(this, '<?= h($img) ?>')"
                             onerror="this.style.display='none';">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── RIGHT: Info panel ── -->
        <div class="product-info-panel">

            <!-- Subtitle / tagline -->
            <p class="product-subtitle-line"><?= h($product->subtitle) ?></p>

            <!-- Product name -->
            <h1 class="product-name"><?= h($product->title) ?></h1>

            <!-- Star rating -->
            <div class="rating-row">
                <span class="stars"><?= str_repeat('★', (int)floor($product->rating)) ?></span>
                <span class="rating-text"><?= number_format($product->rating, 1) ?></span>
                <a href="#reviews" class="reviews-link">Rates &amp; Reviews</a>
            </div>

            <hr class="info-divider">

            <!-- Size -->
            <p class="size-label">SIZE: <span><?= h(strtoupper($product->weight)) ?></span></p>

            <!-- Short description -->
            <p class="short-desc"><?= h($product->description) ?></p>

            <!-- Flavors (only shown if product has flavors) -->
            <?php if (!empty($product->flavors)): ?>
                <div class="flavor-section">
                    <span class="flavor-label">Flavor</span>
                    <div class="flavor-chips">
                        <?php foreach ($product->flavors as $i => $flavor): ?>
                            <div class="flavor-chip <?= $i === 0 ? 'active' : '' ?>"
                                 onclick="selectFlavor(this)">
                                <?= h($flavor) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Price -->
            <div class="product-price"><?= $product->getFormattedPrice() ?></div>

            <!-- Quantity + Add to Cart form -->
            <form method="POST" action="supply.php?id=<?= $product->id ?>">
                <div class="qty-label">Quantity</div>
                <div class="qty-cart-row">
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                        <input type="number" name="quantity" id="qtyInput" class="qty-input"
                               value="1" min="1" max="99">
                        <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                    </div>

                    <button type="submit" name="add_to_cart" class="btn-add-to-cart">
                        <i class="fas fa-cart-plus"></i> ADD TO CART
                    </button>

                    <button type="button" class="btn-reviews" onclick="document.querySelector('#reviews').scrollIntoView({behavior:'smooth'})">
                        REVIEWS
                    </button>
                </div>
            </form>

            <!-- BUY NOW (can wire to checkout later) -->
            <button class="btn-buy-now" onclick="window.location.href='cart.php'">
                BUY NOW
            </button>

        </div><!-- /.product-info-panel -->
    </div><!-- /.product-detail -->

    <!-- ════ PRODUCT BOTTOM: SPECS + DESCRIPTION ════ -->
    <div class="product-bottom">

        <!-- Specs table -->
        <?php if (!empty($product->specs)): ?>
            <div class="specs-box">
                <div class="specs-header">Product Specifications</div>
                <table class="specs-table">
                    <?php foreach ($product->specs as $key => $val): ?>
                        <tr>
                            <td><?= h($key) ?></td>
                            <td><?= h($val) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>

        <!-- Full description -->
        <div class="desc-box" id="reviews">
            <div class="desc-header">Product Description</div>
            <div class="desc-body">

                <!-- Main description paragraphs -->
                <?php
                // Split full_description by ". " for bullet-style rendering when it contains bullets (·)
                $sentences = explode('. ', trim($product->full_description));
                $first = array_shift($sentences);
                ?>
                <p><strong><?= h($first) ?></strong><?= count($sentences) ? '.' : '' ?></p>
                <?php if (!empty($sentences)): ?>
                    <ul class="desc-list">
                        <?php foreach ($sentences as $s): ?>
                            <?php $s = trim($s); if ($s === '') continue; ?>
                            <li><?= h(rtrim($s, '.')) ?>.</li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- High-Quality Ingredients -->
                <?php if (!empty($product->ingredients)): ?>
                    <p class="desc-section-title">High-Quality Ingredients</p>
                    <ul class="desc-list">
                        <?php foreach ($product->ingredients as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Guaranteed Analysis -->
                <?php if (!empty($product->guaranteed_analysis)): ?>
                    <p class="desc-section-title">Guaranteed Analysis</p>
                    <ul class="desc-list">
                        <?php foreach ($product->guaranteed_analysis as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Feeding Guide -->
                <?php if (!empty($product->feeding_guide)): ?>
                    <p class="desc-section-title">Daily Feeding Guide</p>
                    <ul class="desc-list">
                        <?php foreach ($product->feeding_guide as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Materials -->
                <?php if (!empty($product->materials)): ?>
                    <p class="desc-section-title">Materials</p>
                    <ul class="desc-list">
                        <?php foreach ($product->materials as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Features -->
                <?php if (!empty($product->features)): ?>
                    <p class="desc-section-title">Features</p>
                    <ul class="desc-list">
                        <?php foreach ($product->features as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Use Guide -->
                <?php if (!empty($product->use_guide)): ?>
                    <p class="desc-section-title">Use Guide</p>
                    <ul class="desc-list">
                        <?php foreach ($product->use_guide as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- What's in the Package -->
                <?php if (!empty($product->whats_inside)): ?>
                    <p class="desc-section-title">What's Inside the Package</p>
                    <ul class="desc-list">
                        <?php foreach ($product->whats_inside as $item): ?>
                            <li><?= h($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
        </div>

    </div><!-- /.product-bottom -->

</div><!-- /.container -->

<script>
    // Gallery switcher (mirrors pet.php)
    function switchPhoto(thumb, src) {
        const main = document.getElementById('mainPhoto');
        main.style.opacity = '0';
        setTimeout(() => { main.src = src; main.style.opacity = '1'; }, 150);
        document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }

    // Quantity control
    function changeQty(delta) {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value) + delta;
        if (val < 1)  val = 1;
        if (val > 99) val = 99;
        input.value = val;
    }

    // Flavor chip selection
    function selectFlavor(chip) {
        document.querySelectorAll('.flavor-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
    }

    // Auto-dismiss toast
    const toast = document.querySelector('.toast');
    if (toast) setTimeout(() => toast.remove(), 3000);
</script>

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
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>
</body>
</html>
