<?php
require 'auth.inc.php';
require_admin();
require_once '../db.inc.php'; require_once '../db_helper.inc.php';

$success = '';
$error   = '';

// ── Handle POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['product_id'] ?? 0);
        if ($id && delete_product($id)) {
            $success = "Product #$id has been removed.";
        } else {
            $error = 'Could not delete product.';
        }
    }

    elseif ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0); // 0 = new product

        $gallery_raw = array_filter(array_map('trim', explode("\n", $_POST['gallery'] ?? '')));

        $product = [
            'id'          => $id,
            'image'       => trim($_POST['image']    ?? ''),
            'gallery'     => array_values($gallery_raw),
            'title'       => trim($_POST['title']    ?? ''),
            'subtitle'    => trim($_POST['subtitle'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'full_description' => trim($_POST['full_description'] ?? ''),
            'price'       => (float)($_POST['price']   ?? 0),
            'category'    => trim($_POST['category']   ?? ''),
            'pet_type'    => trim($_POST['pet_type']   ?? ''),
            'brand'       => trim($_POST['brand']      ?? ''),
            'life_stage'  => trim($_POST['life_stage'] ?? ''),
            'weight'      => trim($_POST['weight']     ?? ''),
            'food_form'   => trim($_POST['food_form']  ?? ''),
            'storage_type'=> trim($_POST['storage_type'] ?? ''),
            'origin'      => trim($_POST['origin']     ?? ''),
            'flavors'     => array_filter(array_map('trim', explode(',', $_POST['flavors'] ?? ''))),
            'rating'      => (float)($_POST['rating']  ?? 5.0),
            'review_count'=> (int)($_POST['review_count'] ?? 0),
            'specs'       => [],
            'ingredients' => [],
            'guaranteed_analysis' => [],
            'feeding_guide' => [],
            'materials'   => [],
            'features'    => [],
            'use_guide'   => [],
            'whats_inside'=> [],
        ];

        if (!$product['title']) { $error = 'Product title is required.'; }
        else {
            if (save_product($product)) {
                $success = "Product '{$product['title']}' saved!";
            } else {
                $error = 'Could not save product. Check /data/ folder permissions.';
            }
        }
    }
}

// ── Load data ──────────────────────────────────────────────────
$products   = get_all_products();
$edit_prod  = null;
$show_form  = false;

if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $show_form = true;
}
if (isset($_GET['edit'])) {
    $edit_prod = get_product_by_id((int)$_GET['edit']);
    $show_form = !!$edit_prod;
}

$categories = ['Foods','Treats','Toys','Grooming','Accessories','Health','Travel','Housing'];
$pet_types  = ['Dog','Cat','Rabbit','Hamster','Bird','All Pets'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplies — FluffSide Admin</title>
    <?php include 'header.inc.php'; ?>
    <style>
        .page-body { padding: 40px 5% 80px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
        .page-title  { font-size:26px; font-weight:900; }
        .page-sub    { font-size:13px; color:var(--text-light); font-weight:600; margin-top:4px; }

        .btn-primary {
            background:var(--primary-orange); color:var(--white); border:none;
            padding:11px 22px; border-radius:9px; font-size:13px; font-weight:800;
            cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
            transition:background 0.2s;
        }
        .btn-primary:hover { background:var(--primary-hover); }
        .btn-danger  { background:var(--admin-red-light); color:var(--admin-red); }
        .btn-danger:hover  { background:var(--admin-red); color:var(--white); }
        .btn-edit    { background:var(--bg-light); color:var(--text-dark); border:1px solid var(--border); }
        .btn-edit:hover    { border-color:var(--primary-orange); color:var(--primary-orange); }

        .alert { padding:13px 18px; border-radius:9px; font-weight:700; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#E6F4EA; color:#1E8449; }
        .alert-error   { background:var(--admin-red-light); color:var(--admin-red); }

        /* Filter tabs */
        .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:24px; }
        .filter-tab {
            padding:7px 16px; border-radius:20px; font-size:12px; font-weight:800;
            cursor:pointer; border:1.5px solid var(--border); background:var(--white);
            color:var(--text-dark); transition:all 0.2s;
        }
        .filter-tab:hover, .filter-tab.active { background:var(--primary-orange); color:var(--white); border-color:var(--primary-orange); }

        /* Product table */
        .card { background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:24px; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; font-weight:800; text-transform:uppercase;
             letter-spacing:0.5px; color:var(--text-light); padding:12px 16px; border-bottom:1px solid var(--border);
             background:var(--bg-light); }
        td { padding:12px 16px; font-size:13px; font-weight:600; border-bottom:1px solid var(--border); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#FDFBF5; }
        .prod-img { width:54px; height:54px; object-fit:cover; border-radius:8px; border:1px solid var(--border); }
        .prod-title { font-weight:800; font-size:13px; }
        .prod-sub   { font-size:11px; color:var(--text-light); font-weight:600; margin-top:2px; }
        .badge {
            display:inline-block; padding:3px 10px; border-radius:20px;
            font-size:11px; font-weight:800; text-transform:uppercase;
            background:var(--accent-yellow); color:var(--text-dark);
        }
        .badge-type { background:#E3F2FD; color:#1565C0; }
        .td-actions { display:flex; gap:7px; }
        .btn-sm {
            display:inline-flex; align-items:center; gap:5px;
            padding:6px 12px; border-radius:7px; font-size:12px; font-weight:800;
            text-decoration:none; border:none; cursor:pointer; transition:all 0.2s;
        }

        /* Form */
        .form-panel {
            background:var(--white); border:1px solid var(--border); border-radius:16px;
            padding:32px 36px; margin-bottom:36px;
        }
        .form-panel h2 { font-size:20px; font-weight:900; margin-bottom:24px; display:flex; align-items:center; gap:10px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px; }
        .form-row.triple { grid-template-columns:1fr 1fr 1fr; }
        .form-row.full   { grid-template-columns:1fr; }
        .form-group { display:flex; flex-direction:column; gap:6px; }
        .form-group label { font-size:12px; font-weight:800; color:var(--text-light); text-transform:uppercase; letter-spacing:0.5px; }
        .form-group input, .form-group select, .form-group textarea {
            padding:10px 14px; border:1.5px solid var(--border); border-radius:8px;
            font-size:13px; font-family:'Nunito',sans-serif; font-weight:600;
            color:var(--text-dark); background:var(--white); transition:border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline:none; border-color:var(--primary-orange);
        }
        .form-group textarea { resize:vertical; min-height:80px; }
        .form-hint { font-size:11px; color:var(--text-light); font-weight:600; }
        .form-actions { display:flex; gap:12px; margin-top:24px; }
        .btn-cancel {
            background:transparent; border:1.5px solid var(--border); color:var(--text-dark);
            padding:10px 20px; border-radius:8px; font-size:13px; font-weight:800;
            text-decoration:none; display:inline-flex; align-items:center; gap:7px; cursor:pointer;
            transition:border-color 0.2s;
        }
        .btn-cancel:hover { border-color:var(--text-dark); }
        .price-input { position:relative; }
        .price-input span { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:800; color:var(--text-light); }
        .price-input input { padding-left:26px; }

        /* Delete modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; }
        .modal-overlay.open { display:flex; }
        .modal { background:var(--white); border-radius:16px; padding:32px 36px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
        .modal h3 { font-size:20px; font-weight:900; margin-bottom:10px; }
        .modal p  { font-size:14px; color:var(--text-light); font-weight:600; margin-bottom:24px; }
        .modal-actions { display:flex; gap:12px; justify-content:flex-end; }
    </style>
</head>
<body>
<div class="page-body">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-box-open" style="color:var(--primary-orange)"></i> Manage Supplies</div>
            <div class="page-sub"><?= count($products) ?> products listed</div>
        </div>
        <?php if (!$show_form): ?>
        <a href="?action=add" class="btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php elseif ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>

    <!-- ── Add / Edit Form ── -->
    <?php if ($show_form): ?>
    <div class="form-panel">
        <h2><i class="fas fa-<?= $edit_prod ? 'edit' : 'plus-circle' ?>" style="color:var(--primary-orange)"></i>
            <?= $edit_prod ? 'Edit: ' . h($edit_prod['title']) : 'Add New Product' ?></h2>
        <form method="POST" action="supplies.php">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($edit_prod['id'] ?? 0) ?>">

            <div class="form-row full">
                <div class="form-group">
                    <label>Product Title *</label>
                    <input type="text" name="title" value="<?= h($edit_prod['title'] ?? '') ?>"
                           placeholder="e.g. Pedigree Adult Complete Nutrition" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Subtitle / Tagline</label>
                    <input type="text" name="subtitle" value="<?= h($edit_prod['subtitle'] ?? '') ?>"
                           placeholder="Short description shown on card">
                </div>
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="brand" value="<?= h($edit_prod['brand'] ?? '') ?>"
                           placeholder="e.g. Pedigree">
                </div>
            </div>

            <div class="form-row triple">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category">
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c ?>" <?= ($edit_prod['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pet Type *</label>
                    <select name="pet_type">
                        <?php foreach ($pet_types as $t): ?>
                        <option value="<?= $t ?>" <?= ($edit_prod['pet_type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (PHP) *</label>
                    <div class="price-input">
                        <span>&#8369;</span>
                        <input type="number" name="price" step="0.01" min="0"
                               value="<?= number_format((float)($edit_prod['price'] ?? 0), 2, '.', '') ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-row triple">
                <div class="form-group">
                    <label>Life Stage</label>
                    <input type="text" name="life_stage" value="<?= h($edit_prod['life_stage'] ?? '') ?>"
                           placeholder="Adult, Puppy, All Life Stages">
                </div>
                <div class="form-group">
                    <label>Weight / Size</label>
                    <input type="text" name="weight" value="<?= h($edit_prod['weight'] ?? '') ?>"
                           placeholder="e.g. 1.5kg">
                </div>
                <div class="form-group">
                    <label>Origin</label>
                    <input type="text" name="origin" value="<?= h($edit_prod['origin'] ?? '') ?>"
                           placeholder="e.g. Philippine pet supplier">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Main Image Path</label>
                    <input type="text" name="image" value="<?= h($edit_prod['image'] ?? '') ?>"
                           placeholder="Assets/Supplies/Food/dog_food1.jpg">
                    <span class="form-hint">Relative path from Fluffside root.</span>
                </div>
                <div class="form-group">
                    <label>Flavors (comma-separated)</label>
                    <input type="text" name="flavors"
                           value="<?= h(implode(', ', $edit_prod['flavors'] ?? [])) ?>"
                           placeholder="Chicken, Beef">
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Gallery Images (one path per line)</label>
                    <textarea name="gallery" rows="3"><?= h(implode("\n", $edit_prod['gallery'] ?? [])) ?></textarea>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Short Description</label>
                    <textarea name="description" rows="3"><?= h($edit_prod['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Product</button>
                <a href="supplies.php" class="btn-cancel"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- ── Filter tabs ── -->
    <div class="filter-tabs" id="filterTabs">
        <button class="filter-tab active" data-cat="all">All (<?= count($products) ?>)</button>
        <?php
        $cats = array_unique(array_column($products, 'category'));
        foreach ($cats as $cat):
            $cnt = count(array_filter($products, fn($p) => $p['category'] === $cat));
        ?>
        <button class="filter-tab" data-cat="<?= h($cat) ?>"><?= h($cat) ?> (<?= $cnt ?>)</button>
        <?php endforeach; ?>
    </div>

    <!-- ── Products Table ── -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Pet Type</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTable">
            <?php foreach ($products as $p): ?>
            <tr data-cat="<?= h($p['category']) ?>">
                <td>
                    <img src="../<?= h($p['image']) ?>" class="prod-img"
                         alt="<?= h($p['title']) ?>"
                         onerror="this.src='https://placehold.co/54x54/EAE3D9/8E8279?text=?'">
                </td>
                <td>
                    <div class="prod-title"><?= h($p['title']) ?></div>
                    <div class="prod-sub"><?= h($p['subtitle'] ?? '') ?></div>
                </td>
                <td><span class="badge"><?= h($p['category']) ?></span></td>
                <td><span class="badge badge-type"><?= h($p['pet_type']) ?></span></td>
                <td style="font-weight:900;">&#8369;<?= number_format((float)$p['price'], 2) ?></td>
                <td>
                    <div class="td-actions">
                        <a href="?edit=<?= (int)$p['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                        <button onclick="confirmDelete(<?= (int)$p['id'] ?>, <?= json_encode($p['title']) ?>)"
                                class="btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3><i class="fas fa-exclamation-triangle" style="color:var(--admin-red)"></i> Remove Product?</h3>
        <p id="deleteMsg">This will permanently remove the product.</p>
        <form method="POST" action="supplies.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="product_id" id="deleteProdId">
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-primary" style="background:var(--admin-red);border:none;">
                    <i class="fas fa-trash"></i> Yes, Remove
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Category filter
document.getElementById('filterTabs').addEventListener('click', function(e) {
    const tab = e.target.closest('.filter-tab');
    if (!tab) return;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const cat = tab.dataset.cat;
    document.querySelectorAll('#productsTable tr').forEach(row => {
        row.style.display = (cat === 'all' || row.dataset.cat === cat) ? '' : 'none';
    });
});

function confirmDelete(id, title) {
    document.getElementById('deleteProdId').value = id;
    document.getElementById('deleteMsg').textContent = `Remove "${title}"? This cannot be undone.`;
    document.getElementById('deleteModal').classList.add('open');
}
function closeModal() {
    document.getElementById('deleteModal').classList.remove('open');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include '../footer.php'; ?>
</body>
</html>
