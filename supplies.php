<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

require_once 'db.inc.php';
require_once 'db_helper.inc.php';
$products = get_all_products();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $product_name = $_POST['product_name'] ?? 'Product';

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    $cart_total_items = array_sum($_SESSION['cart']);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'cart_total' => $cart_total_items,
        'product_name' => h($product_name) // Send the raw name to inject into the card
    ]);
    exit; // Stop executing the rest of the page!
}

$cart_total_items = array_sum($_SESSION['cart']);

$search_query = trim($_GET['search'] ?? '');
$filtered = $products;

if ($search_query !== '') {
    $filtered = array_filter($products, function ($p) use ($search_query) {
        return stripos($p['title'],       $search_query) !== false
            || stripos($p['description'], $search_query) !== false
            || stripos($p['brand'],       $search_query) !== false
            || stripos($p['pet_type'],    $search_query) !== false;
    });
}

$filter_category = $_GET['category'] ?? '';
if ($filter_category !== '') {
    $filtered = array_filter($filtered, fn($p) => $p['category'] === $filter_category);
}

$sort = $_GET['sort'] ?? 'featured';
$filtered = array_values($filtered);
if ($sort === 'price_asc') {
    usort($filtered, fn($a, $b) => $a['price'] <=> $b['price']);
} elseif ($sort === 'price_desc') {
    usort($filtered, fn($a, $b) => $b['price'] <=> $a['price']);
}

$all_categories = ['Foods', 'Treats', 'Accessories', 'Bed', 'Toys', 'Health', 'Travel'];

function product_category_count(array $products, string $cat): int
{
    return count(array_filter($products, fn($p) => ($p['category'] ?? '') === $cat));
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Shop — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #EF8E35;
            --primary-hover: #D67A26;
            --bg-light: #FDFBF5;
            --text-dark: #5A483E;
            --text-light: #8E8279;
            --box-yellow: #FFF9EE;
            --box-green-header: #EAE6D1;
            --btn-green: #9BB374;
            --btn-green-hover: #8DA466;
            --white: #FFFFFF;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Nunito', sans-serif;
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

        .page-layout {
            display: flex;
            gap: 40px;
            margin-top: 20px;
            margin-bottom: 60px;
            align-items: flex-start;
        }

        .sidebar {
            flex: 0 0 300px;
            position: sticky;
            top: 20px;
        }

        .sidebar h1 {
            font-size: 32px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .sidebar p.subtitle {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .search-box {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--primary-orange);
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 30px;
            outline: none;
            background: var(--white);
            font-family: 'Nunito', sans-serif;
        }

        .filter-container {
            background-color: var(--box-yellow);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #EAE3D9;
            margin-bottom: 30px;
        }

        .filter-header {
            background-color: var(--box-green-header);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 900;
        }

        .filter-header a {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
        }

        .filter-header a:hover {
            color: var(--primary-orange);
        }

        .filter-body {
            padding: 20px;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group-title {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            cursor: pointer;
            user-select: none;
        }

        .filter-items {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .filter-item input[type="radio"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary-orange);
            cursor: pointer;
        }

        .btn-apply {
            width: 100%;
            background-color: #EAE6D1;
            border: 1px solid #EAE3D9;
            padding: 12px;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s;
            font-size: 13px;
            font-family: 'Nunito', sans-serif;
        }

        .btn-apply:hover {
            background-color: #d5d0bb;
        }

        .info-box {
            background-color: #F8E1DF;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #E6B0AA;
            text-align: center;
        }

        .info-box i {
            font-size: 28px;
            color: #C0392B;
            margin-bottom: 12px;
            display: block;
        }

        .info-box h3 {
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 8px;
            color: #C0392B;
        }

        .info-box p {
            font-size: 12px;
            font-weight: 700;
            color: #C0392B;
            line-height: 1.5;
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }

        .shop-banner {
            background-color: var(--box-yellow);
            border: 1px solid #EAE3D9;
            border-radius: 15px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .banner-text {
            position: relative;
            z-index: 2;
            max-width: 420px;
        }

        .banner-text h2 {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .banner-text p {
            font-size: 14px;
            font-weight: 600;
        }

        .banner-image {
            position: absolute;
            right: -20px;
            top: 0;
            height: 110%;
            mix-blend-mode: multiply;
            z-index: 1;
            opacity: .85;
        }

        .results-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .sort-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-dropdown {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #EAE3D9;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 13px;
            outline: none;
            cursor: pointer;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .product-card {
            background-color: var(--white);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #EAE3D9;
            display: flex;
            flex-direction: column;
            transition: transform .2s, box-shadow .2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .05);
        }

        .product-img-box {
            width: 100%;
            height: 200px;
            background-color: #F8F8F8;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            flex-shrink: 0;
        }

        .product-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .product-info {
            padding: 18px 18px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-meta {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .tag-category {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            background: #EAE6D1;
            color: var(--text-dark);
        }

        .tag-pet-type {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            background: #FEE8BE;
            color: var(--text-dark);
        }

        .product-title {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .product-subtitle {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 6px;
        }

        .product-rating {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-orange);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .product-rating .review-count {
            color: var(--text-light);
            font-size: 11px;
        }

        .product-desc {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            line-height: 1.45;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            gap: 10px;
        }

        .product-price {
            font-size: 20px;
            font-weight: 900;
            color: var(--primary-orange);
            white-space: nowrap;
        }

        .card-btns {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-view {
            background-color: var(--primary-orange);
            color: var(--white);
            padding: 8px 18px;
            border-radius: 20px;
            border: none;
            font-weight: 900;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            transition: .2s;
            font-family: 'Nunito', sans-serif;
            white-space: nowrap;
        }

        .btn-view:hover {
            background-color: var(--primary-hover);
        }

        .btn-add-cart {
            background-color: var(--btn-green);
            color: var(--white);
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: .2s;
            font-size: 14px;
            flex-shrink: 0;
        }

        .btn-add-cart:hover {
            background-color: var(--btn-green-hover);
            transform: scale(1.1);
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
            font-size: 15px;
            font-weight: 600;
        }

        .no-results i {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
        }

        /* ── TOAST NOTIFICATION CONTAINER (AJAX Stacking) ── */
        #toastContainer {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
            /* Let clicks pass through empty space */
        }

        /* Beautiful white card design matching supply.php */
        .toast {
            pointer-events: auto;
            /* Re-enable clicks on the toast itself */
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .14), 0 1px 4px rgba(0, 0, 0, .06);
            display: flex;
            align-items: center;
            gap: 0;
            overflow: hidden;
            min-width: 300px;
            max-width: 360px;
            /* Stays visible for 2.8s before fading out */
            animation: toastIn .35s cubic-bezier(.21, 1.02, .73, 1) both,
                toastOut .3s ease 2.8s forwards;
        }

        .toast-accent {
            width: 5px;
            align-self: stretch;
            background: #4CAF7D;
            flex-shrink: 0;
        }

        .toast-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #edfaf3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #4CAF7D;
            flex-shrink: 0;
            margin: 14px 4px 14px 14px;
        }

        .toast-text {
            flex: 1;
            padding: 14px 6px 14px 10px;
        }

        .toast-title {
            font-size: 13.5px;
            font-weight: 900;
            color: #2d2d2d;
            margin-bottom: 2px;
        }

        .toast-sub {
            font-size: 12px;
            font-weight: 600;
            color: #888;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 15px;
            color: #bbb;
            padding: 14px 14px 14px 6px;
            line-height: 1;
            transition: color .15s;
            align-self: flex-start;
        }

        .toast-close:hover {
            color: #666;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(60px) scale(.95);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastOut {
            to {
                opacity: 0;
                transform: translateX(60px);
            }
        }

        @media (max-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .page-layout {
                flex-direction: column;
            }

            .sidebar {
                flex: none;
                width: 100%;
                position: static;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            #toastContainer {
                right: 10px;
                left: 10px;
            }

            .toast {
                min-width: unset;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'header.php'; ?>

        <main class="page-layout">

            <aside class="sidebar">
                <h1>Supply<br>Shop</h1>
                <p class="subtitle">Everything your furry friend needs<br>to be happy and healthy.</p>

                <form method="GET" action="supplies.php" id="searchForm">
                    <input type="text" name="search" class="search-box"
                        placeholder="Search toys, food, grooming..."
                        value="<?= h($search_query) ?>">
                    <?php if ($filter_category): ?>
                        <input type="hidden" name="category" value="<?= h($filter_category) ?>">
                    <?php endif; ?>
                    <?php if ($sort !== 'featured'): ?>
                        <input type="hidden" name="sort" value="<?= h($sort) ?>">
                    <?php endif; ?>
                </form>

                <form method="GET" action="supplies.php" id="filterForm">
                    <?php if ($search_query): ?>
                        <input type="hidden" name="search" value="<?= h($search_query) ?>">
                    <?php endif; ?>

                    <div class="filter-container">
                        <div class="filter-header">
                            <span>Categories</span>
                            <a href="supplies.php">Clear all</a>
                        </div>
                        <div class="filter-body">
                            <div class="filter-group">
                                <div class="filter-group-title" onclick="toggleGroup('group-cat')">
                                    SHOP BY <i class="fas fa-chevron-up" id="icon-group-cat"></i>
                                </div>
                                <div class="filter-items" id="group-cat">
                                    <label class="filter-item">
                                        <input type="radio" name="category" value=""
                                            <?= $filter_category === '' ? 'checked' : '' ?>>
                                        All Supplies
                                        <span style="color:var(--text-light);font-size:11px;">(<?= count($products) ?>)</span>
                                    </label>
                                    <?php foreach ($all_categories as $cat):
                                        $cnt = product_category_count($products, $cat);
                                    ?>
                                        <label class="filter-item">
                                            <input type="radio" name="category" value="<?= h($cat) ?>"
                                                <?= $filter_category === $cat ? 'checked' : '' ?>>
                                            <?= h($cat) ?>
                                            <span style="color:var(--text-light);font-size:11px;">(<?= $cnt ?>)</span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn-apply">Apply Filter</button>
                        </div>
                    </div>
                </form>

                <div class="info-box">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h3>SHOP FOR A CAUSE</h3>
                    <p>10% of every purchase goes directly to supporting our rescued animals!</p>
                </div>
            </aside>

            <section class="main-content">

                <div class="shop-banner">
                    <div class="banner-text">
                        <h2>New Arrivals Are Here!</h2>
                        <p>Check out our latest collection of premium pet food, durable toys, and cozy accessories for your companions.</p>
                    </div>
                    <img src="Assets/supplies-banner.png" alt="" class="banner-image" onerror="this.style.display='none'">
                </div>

                <div class="results-bar">
                    <span>
                        Showing <strong><?= count($filtered) ?></strong>
                        <?= count($filtered) === 1 ? 'product' : 'products' ?>
                        <?= $search_query !== '' ? '— results for "<strong>' . h($search_query) . '</strong>"' : '' ?>
                        <?= $filter_category !== '' ? ' in <strong>' . h($filter_category) . '</strong>' : '' ?>
                    </span>

                    <form method="GET" action="supplies.php" class="sort-form" id="sortForm">
                        <?php if ($search_query):   ?><input type="hidden" name="search" value="<?= h($search_query) ?>"><?php endif; ?>
                        <?php if ($filter_category): ?><input type="hidden" name="category" value="<?= h($filter_category) ?>"><?php endif; ?>
                        <label style="font-weight:700;font-size:13px;">Sort by:</label>
                        <select name="sort" class="sort-dropdown" onchange="document.getElementById('sortForm').submit()">
                            <option value="featured" <?= $sort === 'featured'   ? 'selected' : '' ?>>Featured</option>
                            <option value="price_asc" <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                <?php if (empty($filtered)): ?>
                    <div class="no-results">
                        <i class="fas fa-box-open"></i>
                        No products found. Try different keywords or clear the filters.
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($filtered as $product): ?>
                            <div class="product-card">

                                <div class="product-img-box">
                                    <img src="<?= h($product['image']) ?>" alt="<?= h($product['title']) ?>" class="product-img" onerror="this.src='placeholder.jpg'; this.style.opacity='.25';">
                                </div>

                                <div class="product-info">
                                    <div class="product-meta">
                                        <span class="tag-category"><?= h($product['category']) ?></span>
                                        <span class="tag-pet-type"><?= h($product['pet_type']) ?></span>
                                    </div>

                                    <h3 class="product-title"><?= h($product['title']) ?></h3>
                                    <p class="product-subtitle"><?= h($product['subtitle']) ?></p>

                                    <div class="product-rating">
                                        <span><?= str_repeat('★', (int)floor($product['rating'])) ?></span>
                                        <span><?= number_format($product['rating'], 1) ?></span>
                                        <span class="review-count">(<?= $product['review_count'] ?> reviews)</span>
                                    </div>

                                    <p class="product-desc"><?= h($product['description']) ?></p>

                                    <div class="price-action-row">
                                        <span class="product-price">₱<?= number_format($product['price'], 2) ?></span>

                                        <div class="card-btns">
                                            <a href="supply.php?id=<?= $product['id'] ?>" class="btn-view">VIEW</a>

                                            <!-- AJAX Form -->
                                            <form method="POST" class="ajax-add-to-cart" style="margin:0;">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <input type="hidden" name="product_name" value="<?= h($product['title']) ?>">

                                                <button type="submit" class="btn-add-cart" title="Add to Cart">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </section>
        </main>
    </div>

    <!-- Container for stacking AJAX Toasts -->
    <div id="toastContainer"></div>

    <?php include 'footer.php'; ?>

    <script>
        function toggleGroup(id) {
            const group = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            const isOpen = group.style.display !== 'none';
            group.style.display = isOpen ? 'none' : '';
            icon.style.transform = isOpen ? 'rotate(180deg)' : '';
        }

        document.querySelector('.search-box').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') document.getElementById('searchForm').submit();
        });

        // AJAX Add to Cart Logic (Builds the beautiful white card!)
        document.querySelectorAll('.ajax-add-to-cart').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('ajax_add_to_cart', '1');

                fetch('supplies.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {

                            // Update cart badge in header
                            const badge = document.querySelector('.cart-badge');
                            if (badge) {
                                badge.textContent = data.cart_total;
                            } else if (data.cart_total > 0) {
                                // Badge doesn't exist yet (cart was empty), create it
                                const cartIcon = document.querySelector('.cart-icon');
                                if (cartIcon) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'cart-badge';
                                    newBadge.textContent = data.cart_total;
                                    cartIcon.appendChild(newBadge);
                                }
                            }

                            // Build the exact HTML from supply.php
                            const toastHTML = `
                            <div class="toast">
                                <div class="toast-accent"></div>
                                <div class="toast-icon"><i class="fas fa-shopping-cart"></i></div>
                                <div class="toast-text">
                                    <div class="toast-title">Added to cart!</div>
                                    <div class="toast-sub">${data.product_name} is in your cart.</div>
                                </div>
                                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
                            </div>
                        `;

                            // Inject it into the container (so they stack if clicked fast!)
                            const container = document.getElementById('toastContainer');
                            container.insertAdjacentHTML('beforeend', toastHTML);

                            // Find the toast we just created and remove it from the DOM after 3.2 seconds
                            const newToast = container.lastElementChild;
                            setTimeout(() => {
                                if (newToast && newToast.parentElement) {
                                    newToast.remove();
                                }
                            }, 3200);
                        }
                    })
                    .catch(error => console.error('Error adding to cart:', error));
            });
        });
    </script>

    <script>
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