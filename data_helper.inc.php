<?php
// data_helper.inc.php
// Central read/write functions for all JSON data files.
// When moving to DB: replace each function body with a PDO query.
// The rest of the app stays untouched.

define('DATA_DIR', __DIR__ . '/data/');

// ── Generic helpers ────────────────────────────────────────────

function fs_read(string $file): array {
    $path = DATA_DIR . $file;
    if (!file_exists($path)) return [];
    $raw = file_get_contents($path);
    return json_decode($raw, true) ?? [];
}

function fs_write(string $file, array $data): bool {
    $path = DATA_DIR . $file;
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
}

// ── PETS ───────────────────────────────────────────────────────

function get_all_pets(): array {
    return fs_read('pets_data.json');
}

function get_pet_by_id(string $id): ?array {
    foreach (get_all_pets() as $pet) {
        if ($pet['id'] === $id) return $pet;
    }
    return null;
}

function save_pet(array $pet): bool {
    $pets = get_all_pets();
    foreach ($pets as $i => $p) {
        if ($p['id'] === $pet['id']) {
            $pets[$i] = $pet;
            return fs_write('pets_data.json', $pets);
        }
    }
    // New pet
    $pets[] = $pet;
    return fs_write('pets_data.json', $pets);
}

function delete_pet(string $id): bool {
    $pets = array_values(array_filter(get_all_pets(), fn($p) => $p['id'] !== $id));
    return fs_write('pets_data.json', $pets);
}

// ── PRODUCTS ───────────────────────────────────────────────────

function get_all_products(): array {
    return fs_read('products_data.json');
}

function get_product_by_id(int $id): ?array {
    foreach (get_all_products() as $p) {
        if ((int)$p['id'] === $id) return $p;
    }
    return null;
}

function save_product(array $product): bool {
    $products = get_all_products();
    foreach ($products as $i => $p) {
        if ((int)$p['id'] === (int)$product['id']) {
            $products[$i] = $product;
            return fs_write('products_data.json', $products);
        }
    }
    // New product — assign next ID
    $max = 0;
    foreach ($products as $p) { if ($p['id'] > $max) $max = $p['id']; }
    $product['id'] = $max + 1;
    $products[] = $product;
    return fs_write('products_data.json', $products);
}

function delete_product(int $id): bool {
    $products = array_values(array_filter(get_all_products(), fn($p) => (int)$p['id'] !== $id));
    return fs_write('products_data.json', $products);
}

// ── APPLICATIONS ───────────────────────────────────────────────
// Steps: 1=Submitted, 2=Reviewed, 3=Interview, 4=Approved, 5=Meet&Greet, 6=TakeHome

function get_all_applications(): array {
    return fs_read('applications.json');
}

function get_applications_by_user(int $user_id): array {
    return array_values(array_filter(get_all_applications(), fn($a) => (int)$a['user_id'] === $user_id));
}

function get_application_by_id(string $app_id): ?array {
    foreach (get_all_applications() as $a) {
        if ($a['id'] === $app_id) return $a;
    }
    return null;
}

function save_application(array $app): bool {
    $apps = get_all_applications();
    foreach ($apps as $i => $a) {
        if ($a['id'] === $app['id']) {
            $apps[$i] = $app;
            return fs_write('applications.json', $apps);
        }
    }
    // New application
    $max = 0;
    foreach ($apps as $a) {
        $num = (int)substr($a['id'], 4);
        if ($num > $max) $max = $num;
    }
    $app['id'] = 'APP-' . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
    $apps[] = $app;
    return fs_write('applications.json', $apps);
}

// ── MESSAGES ───────────────────────────────────────────────────

function get_messages_by_app(string $app_id): array {
    return array_values(array_filter(fs_read('messages.json'), fn($m) => $m['app_id'] === $app_id));
}

function get_all_messages(): array {
    return fs_read('messages.json');
}

function send_message(string $app_id, int $user_id, string $sender, string $message): bool {
    $msgs = fs_read('messages.json');
    $max = 0;
    foreach ($msgs as $m) {
        $num = (int)substr($m['id'], 4);
        if ($num > $max) $max = $num;
    }
    $msgs[] = [
        'id'      => 'MSG-' . str_pad($max + 1, 3, '0', STR_PAD_LEFT),
        'app_id'  => $app_id,
        'user_id' => $user_id,
        'sender'  => $sender, // 'admin' or 'user'
        'message' => trim($message),
        'sent_at' => date('Y-m-d H:i:s'),
    ];
    return fs_write('messages.json', $msgs);
}
?>
<!-- for clean comments -->