<?php
require 'auth.inc.php';
require_admin();
require_once '../db.inc.php'; require_once '../db_helper.inc.php';

$success = '';
$error   = '';

// ── Handle POST actions ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = trim($_POST['pet_id'] ?? '');
        if ($id && delete_pet($id)) {
            $success = "Pet '{$id}' has been removed from the listings.";
        } else {
            $error = 'Could not delete pet. Please try again.';
        }
    }

    elseif ($action === 'save') {
        $id = strtolower(trim(preg_replace('/\s+/', '_', $_POST['id'] ?? '')));
        if (!$id) { $error = 'Pet ID is required.'; }
        else {
            $traits   = array_filter(array_map('trim', explode(',', $_POST['traits']   ?? '')));
            $likes    = array_filter(array_map('trim', explode(',', $_POST['likes']    ?? '')));
            $dislikes = array_filter(array_map('trim', explode(',', $_POST['dislikes'] ?? '')));
            $gallery_raw = array_filter(array_map('trim', explode("\n", $_POST['gallery'] ?? '')));

            $pet = [
                'id'          => $id,
                'name'        => strtoupper(trim($_POST['name'] ?? $id)),
                'breed'       => trim($_POST['breed']       ?? ''),
                'type'        => strtoupper(trim($_POST['type'] ?? '')),
                'gender'      => strtoupper(trim($_POST['gender'] ?? '')),
                'age'         => trim($_POST['age']         ?? ''),
                'age_group'   => trim($_POST['age_group']   ?? 'Adult'),
                'image'       => trim($_POST['image']       ?? ''),
                'gallery'     => array_values($gallery_raw),
                'traits'      => array_values($traits),
                'likes'       => array_values($likes),
                'dislikes'    => array_values($dislikes),
                'description' => trim($_POST['description'] ?? ''),
            ];
            if (save_pet($pet)) {
                $success = "Pet '{$pet['name']}' saved successfully!";
            } else {
                $error = 'Could not save pet. Check file permissions on /data/.';
            }
        }
    }
}

// ── Load data ──────────────────────────────────────────────────
$pets       = get_all_pets();
$edit_pet   = null;
$show_form  = false;

if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $show_form = true;
}
if (isset($_GET['edit'])) {
    $edit_pet  = get_pet_by_id($_GET['edit']);
    $show_form = !!$edit_pet;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Residents — FluffSide Admin</title>
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
        .btn-danger  { background:var(--admin-red-light); color:var(--admin-red); border:none; }
        .btn-danger:hover  { background:var(--admin-red); color:var(--white); }
        .btn-edit    { background:var(--bg-light); color:var(--text-dark); border:1px solid var(--border); }
        .btn-edit:hover    { border-color:var(--primary-orange); color:var(--primary-orange); background:var(--white); }

        /* Alert */
        .alert { padding:13px 18px; border-radius:9px; font-weight:700; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#E6F4EA; color:#1E8449; }
        .alert-error   { background:var(--admin-red-light); color:var(--admin-red); }

        /* Pet grid */
        .pet-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:40px; }
        .pet-card {
            background:var(--white); border:1px solid var(--border); border-radius:14px;
            overflow:hidden; transition:box-shadow 0.2s;
        }
        .pet-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.08); }
        .pet-card img { width:100%; height:180px; object-fit:cover; }
        .pet-card-body { padding:16px 18px; }
        .pet-card h3 { font-size:16px; font-weight:900; margin-bottom:3px; }
        .pet-card p  { font-size:12px; color:var(--text-light); font-weight:600; margin-bottom:12px; }
        .pet-card-actions { display:flex; gap:8px; }
        .btn-sm {
            display:inline-flex; align-items:center; gap:6px;
            padding:7px 13px; border-radius:7px; font-size:12px; font-weight:800;
            text-decoration:none; border:none; cursor:pointer; transition:all 0.2s;
        }
        .pet-type-tag {
            display:inline-block; padding:2px 9px; border-radius:20px;
            font-size:10px; font-weight:800; text-transform:uppercase; margin-bottom:6px;
            background:var(--accent-yellow); color:var(--text-dark);
        }

        /* Form panel */
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
        .form-group textarea { resize:vertical; min-height:90px; }
        .form-hint { font-size:11px; color:var(--text-light); font-weight:600; margin-top:2px; }
        .form-actions { display:flex; gap:12px; margin-top:24px; align-items:center; }
        .btn-cancel {
            background:transparent; border:1.5px solid var(--border); color:var(--text-dark);
            padding:10px 20px; border-radius:8px; font-size:13px; font-weight:800;
            text-decoration:none; display:inline-flex; align-items:center; gap:7px; cursor:pointer;
            transition:all 0.2s;
        }
        .btn-cancel:hover { border-color:var(--text-dark); }

        /* Delete modal */
        .modal-overlay {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
            z-index:1000; align-items:center; justify-content:center;
        }
        .modal-overlay.open { display:flex; }
        .modal {
            background:var(--white); border-radius:16px; padding:32px 36px;
            max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.2);
        }
        .modal h3 { font-size:20px; font-weight:900; margin-bottom:10px; }
        .modal p  { font-size:14px; color:var(--text-light); font-weight:600; margin-bottom:24px; }
        .modal-actions { display:flex; gap:12px; justify-content:flex-end; }
    </style>
</head>
<body>
<div class="page-body">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-paw" style="color:var(--primary-orange)"></i> Manage Residents</div>
            <div class="page-sub"><?= count($pets) ?> pets listed &mdash; add, edit, or remove entries from the site</div>
        </div>
        <?php if (!$show_form): ?>
        <a href="?action=add" class="btn-primary"><i class="fas fa-plus"></i> Add New Pet</a>
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
        <h2><i class="fas fa-<?= $edit_pet ? 'edit' : 'plus-circle' ?>" style="color:var(--primary-orange)"></i>
            <?= $edit_pet ? 'Edit Pet: ' . h($edit_pet['name']) : 'Add New Pet' ?></h2>
        <form method="POST" action="residents.php">
            <input type="hidden" name="action" value="save">

            <div class="form-row">
                <div class="form-group">
                    <label>Pet ID (slug) *</label>
                    <input type="text" name="id" value="<?= h($edit_pet['id'] ?? '') ?>"
                           placeholder="e.g. scout" required <?= $edit_pet ? 'readonly' : '' ?>>
                    <span class="form-hint">Lowercase, no spaces. Used in URL. Cannot change after creation.</span>
                </div>
                <div class="form-group">
                    <label>Display Name *</label>
                    <input type="text" name="name" value="<?= h($edit_pet['name'] ?? '') ?>" placeholder="e.g. SCOUT" required>
                </div>
            </div>

            <div class="form-row triple">
                <div class="form-group">
                    <label>Animal Type *</label>
                    <select name="type">
                        <?php foreach (['DOG','CAT','RABBIT','HAMSTER','BIRD','OTHER'] as $t): ?>
                        <option value="<?= $t ?>" <?= ($edit_pet['type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <select name="gender">
                        <option value="MALE"   <?= ($edit_pet['gender'] ?? '') === 'MALE'   ? 'selected' : '' ?>>MALE</option>
                        <option value="FEMALE" <?= ($edit_pet['gender'] ?? '') === 'FEMALE' ? 'selected' : '' ?>>FEMALE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Age Group</label>
                    <select name="age_group">
                        <?php foreach (['Young','Adult','Senior'] as $ag): ?>
                        <option value="<?= $ag ?>" <?= ($edit_pet['age_group'] ?? 'Adult') === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Breed</label>
                    <input type="text" name="breed" value="<?= h($edit_pet['breed'] ?? '') ?>" placeholder="e.g. Golden Retriever">
                </div>
                <div class="form-group">
                    <label>Age Description</label>
                    <input type="text" name="age" value="<?= h($edit_pet['age'] ?? '') ?>" placeholder="e.g. 12 weeks old">
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Main Image Path</label>
                    <input type="text" name="image" value="<?= h($edit_pet['image'] ?? '') ?>" placeholder="Assets/Residents/Dog/Scout1.jpg">
                    <span class="form-hint">Relative path from the Fluffside root folder.</span>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Gallery Images (one path per line)</label>
                    <textarea name="gallery" rows="4" placeholder="Assets/Residents/Dog/Scout1.jpg&#10;Assets/Residents/Dog/Scout2.jpg"><?= h(implode("\n", $edit_pet['gallery'] ?? [])) ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Traits (comma-separated)</label>
                    <input type="text" name="traits" value="<?= h(implode(', ', $edit_pet['traits'] ?? [])) ?>" placeholder="Playful, Social, Curious">
                </div>
                <div class="form-group">
                    <label>Likes (comma-separated)</label>
                    <input type="text" name="likes" value="<?= h(implode(', ', $edit_pet['likes'] ?? [])) ?>" placeholder="Belly Rubs, Napping">
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Dislikes (comma-separated)</label>
                    <input type="text" name="dislikes" value="<?= h(implode(', ', $edit_pet['dislikes'] ?? [])) ?>" placeholder="Loud Noises, Being Alone">
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="6" required><?= h($edit_pet['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Pet</button>
                <a href="residents.php" class="btn-cancel"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- ── Pet Grid ── -->
    <div class="pet-grid">
        <?php foreach ($pets as $pet): ?>
        <div class="pet-card">
            <img src="../<?= h($pet['image']) ?>"
                 alt="<?= h($pet['name']) ?>"
                 onerror="this.src='https://placehold.co/280x180/EAE3D9/8E8279?text=<?= urlencode($pet['name']) ?>'">
            <div class="pet-card-body">
                <span class="pet-type-tag"><?= h($pet['type']) ?></span>
                <h3><?= h($pet['name']) ?></h3>
                <p><?= h($pet['breed']) ?> &bull; <?= h($pet['age']) ?> &bull; <?= h($pet['gender']) ?></p>
                <div class="pet-card-actions">
                    <a href="?edit=<?= h($pet['id']) ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    <button onclick="confirmDelete('<?= h($pet['id']) ?>','<?= h($pet['name']) ?>')"
                            class="btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3><i class="fas fa-exclamation-triangle" style="color:var(--admin-red)"></i> Remove Pet?</h3>
        <p id="deleteMsg">This will remove the pet from the listings. This action cannot be undone.</p>
        <form method="POST" action="residents.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="pet_id" id="deletePetId">
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-primary btn-danger" style="background:var(--admin-red);color:#fff;border:none;">
                    <i class="fas fa-trash"></i> Yes, Remove
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deletePetId').value = id;
    document.getElementById('deleteMsg').textContent = `Are you sure you want to remove "${name}" from the listings? This cannot be undone.`;
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
