<?php
// migrate.php — ONE-TIME USE. Delete after running.
// Migrates pets and products from JSON files into the DB.
// Run at: http://localhost/Fluffside/migrate.php

session_start();
require_once 'db.inc.php';
require_once 'db_helper.inc.php';

$log = [];
$errors = [];

// ── Migrate Pets ──────────────────────────────────────────────
$pets_json = __DIR__ . '/data/pets_data.json';
if (!file_exists($pets_json)) {
    $errors[] = "pets_data.json not found at $pets_json";
} else {
    $pets = json_decode(file_get_contents($pets_json), true) ?? [];
    $pet_count = 0;

    foreach ($pets as $pet) {
        // Normalize field names from JSON to what save_pet() expects
        $normalized = [
            'id'          => $pet['id'],
            'name'        => $pet['name'],
            'breed'       => $pet['breed']      ?? '',
            'type'        => $pet['type']        ?? 'DOG',
            'gender'      => $pet['gender']      ?? 'MALE',
            'age'         => $pet['age']         ?? '',
            'age_group'   => $pet['age_group']   ?? 'Adult',
            'image'       => $pet['image']       ?? '',
            'gallery'     => $pet['gallery']     ?? [],
            'traits'      => $pet['traits']      ?? [],
            'likes'       => $pet['likes']       ?? [],
            'dislikes'    => $pet['dislikes']    ?? [],
            'description' => $pet['description'] ?? '',
        ];

        try {
            save_pet($normalized);
            $pet_count++;
            $log[] = "✓ Pet: {$normalized['name']} ({$normalized['id']})";
        } catch (Exception $e) {
            $errors[] = "✗ Pet {$pet['id']}: " . $e->getMessage();
        }
    }
    $log[] = "<strong>Migrated $pet_count pets.</strong>";
}

// ── Migrate Products ──────────────────────────────────────────
$prod_json = __DIR__ . '/data/products_data.json';
if (!file_exists($prod_json)) {
    $errors[] = "products_data.json not found at $prod_json";
} else {
    $products = json_decode(file_get_contents($prod_json), true) ?? [];
    $prod_count = 0;

    foreach ($products as $prod) {
        $normalized = [
            'id'               => 0,  // force insert (new auto-increment IDs)
            'image'            => $prod['image']        ?? '',
            'gallery'          => $prod['gallery']      ?? [],
            'title'            => $prod['title']        ?? '',
            'subtitle'         => $prod['subtitle']     ?? '',
            'description'      => $prod['description']  ?? '',
            'full_description' => $prod['full_description'] ?? '',
            'price'            => (float)($prod['price'] ?? 0),
            'category'         => $prod['category']     ?? '',
            'pet_type'         => $prod['pet_type']     ?? '',
            'brand'            => $prod['brand']        ?? '',
            'life_stage'       => $prod['life_stage']   ?? '',
            'weight'           => $prod['weight']       ?? '',
            'food_form'        => $prod['food_form']    ?? '',
            'storage_type'     => $prod['storage_type'] ?? '',
            'origin'           => $prod['origin']       ?? '',
            'rating'           => (float)($prod['rating'] ?? 5.0),
            'review_count'     => (int)($prod['review_count'] ?? 0),
            'flavors'          => $prod['flavors']      ?? [],
        ];

        try {
            save_product($normalized);
            $prod_count++;
            $log[] = "✓ Product: {$normalized['title']}";
        } catch (Exception $e) {
            $errors[] = "✗ Product '{$prod['title']}': " . $e->getMessage();
        }
    }
    $log[] = "<strong>Migrated $prod_count products.</strong>";
}

// ── Migrate Applications ──────────────────────────────────────
$apps_json = __DIR__ . '/data/applications.json';
if (file_exists($apps_json)) {
    $apps = json_decode(file_get_contents($apps_json), true) ?? [];
    $app_count = 0;

    foreach ($apps as $app) {
        // Find user_id by email
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM tbl_users WHERE email = ? LIMIT 1");
            $stmt->execute([$app['user_email'] ?? '']);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $errors[] = "✗ App {$app['id']}: user email '{$app['user_email']}' not found in tbl_users. Skipped.";
                continue;
            }

            $normalized = [
                'id'           => $app['id'],
                'user_id'      => (int)$user['user_id'],
                'pet_id'       => $app['pet_id'],
                'type'         => $app['type'],
                'status'       => $app['status']        ?? 'active',
                'current_step' => (int)($app['current_step'] ?? 1),
                'last_update'  => $app['last_update']   ?? '',
                'rejected'     => (bool)($app['rejected'] ?? false),
                'submitted_at' => $app['submitted_at']  ?? date('Y-m-d'),
            ];
            save_application($normalized);
            $app_count++;
            $log[] = "✓ Application: {$app['id']} ({$app['pet_name']})";
        } catch (Exception $e) {
            $errors[] = "✗ App {$app['id']}: " . $e->getMessage();
        }
    }
    $log[] = "<strong>Migrated $app_count applications.</strong>";
}

// ── Migrate Messages ──────────────────────────────────────────
$msgs_json = __DIR__ . '/data/messages.json';
if (file_exists($msgs_json)) {
    $msgs = json_decode(file_get_contents($msgs_json), true) ?? [];
    $msg_count = 0;

    foreach ($msgs as $msg) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_applications WHERE app_id = ?");
            $stmt->execute([$msg['app_id']]);
            if ($stmt->fetchColumn() == 0) {
                $errors[] = "✗ Message {$msg['id']}: app_id '{$msg['app_id']}' not found. Skipped.";
                continue;
            }
            send_message($msg['app_id'], (int)$msg['user_id'], $msg['sender'], $msg['message']);
            $msg_count++;
        } catch (Exception $e) {
            $errors[] = "✗ Message {$msg['id']}: " . $e->getMessage();
        }
    }
    $log[] = "<strong>Migrated $msg_count messages.</strong>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FluffSide Migration</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Nunito',sans-serif; background:#FDFBF5; color:#5A483E; padding:40px 5%; }
        h1   { font-size:24px; font-weight:900; margin-bottom:6px; }
        h2   { font-size:16px; font-weight:700; margin:20px 0 10px; }
        .log-wrap { background:#fff; border:1px solid #EAE3D9; border-radius:12px; padding:24px; max-width:740px; }
        .log-item { font-size:13px; font-weight:600; padding:4px 0; border-bottom:1px solid #F0ECE6; }
        .log-item:last-child { border-bottom:none; }
        .err-item { font-size:13px; font-weight:600; color:#C0392B; padding:4px 0; }
        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:900; text-transform:uppercase; margin-right:6px; }
        .badge-ok  { background:#E6F4EA; color:#1E8449; }
        .badge-err { background:#FADBD8; color:#C0392B; }
        .warning { background:#FEF9E7; border:1px solid #F9E79F; border-radius:8px; padding:14px 18px; margin-top:24px; font-size:13px; font-weight:700; color:#B7950B; max-width:740px; }
    </style>
</head>
<body>
<h1>FluffSide — Data Migration</h1>
<p style="font-size:13px;color:#8E8279;margin-bottom:24px;">One-time migration from JSON files to MySQL database.</p>

<?php if (!empty($errors)): ?>
<h2><span class="badge badge-err">Errors (<?= count($errors) ?>)</span></h2>
<div class="log-wrap">
    <?php foreach ($errors as $e): ?>
    <div class="err-item"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<h2><span class="badge badge-ok">Success Log (<?= count($log) ?> entries)</span></h2>
<div class="log-wrap">
    <?php foreach ($log as $entry): ?>
    <div class="log-item"><?= $entry ?></div>
    <?php endforeach; ?>
</div>

<div class="warning">
    <strong>Important:</strong> Migration complete. Delete <code>migrate.php</code> from your Fluffside folder now — it should not be accessible on a live server.
</div>

<p style="margin-top:24px;font-size:13px;">
    <a href="admin/index.php" style="color:#EF8E35;font-weight:800;">Go to Admin Panel</a>
    &nbsp;&mdash;&nbsp;
    <a href="index.php" style="color:#EF8E35;font-weight:800;">Go to User Site</a>
</p>
</body>
</html>
