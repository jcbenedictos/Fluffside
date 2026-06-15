<?php
// db_helper.inc.php
// All data access goes through here using PDO.
// Replaces the old JSON-based data_helper.inc.php.
// db.inc.php must be required before including this file.

// ── PETS ───────────────────────────────────────────────────────

function get_all_pets(): array {
    global $pdo;
    $pets = $pdo->query("SELECT * FROM tbl_pets WHERE is_available = 1 ORDER BY pet_id")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($pets as &$pet) {
        $pet = _hydrate_pet($pet);
    }
    return $pets;
}

function get_pet_by_id(string $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tbl_pets WHERE pet_id = ?");
    $stmt->execute([$id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    return $pet ? _hydrate_pet($pet) : null;
}

function _hydrate_pet(array $pet): array {
    global $pdo;
    // Remap columns to expected keys
    $pet['id']        = $pet['pet_id'];
    $pet['name']      = $pet['pet_name'];
    $pet['type']      = $pet['animal_type'];
    $pet['age']       = $pet['age_desc'];
    $pet['image']     = $pet['image_path'];

    // Gallery
    $g = $pdo->prepare("SELECT image_path FROM tbl_pet_gallery WHERE pet_id = ? ORDER BY sort_order");
    $g->execute([$pet['pet_id']]);
    $pet['gallery'] = array_column($g->fetchAll(PDO::FETCH_ASSOC), 'image_path');

    // Traits
    $t = $pdo->prepare("SELECT trait_type, trait_value FROM tbl_pet_traits WHERE pet_id = ?");
    $t->execute([$pet['pet_id']]);
    $pet['traits'] = $pet['likes'] = $pet['dislikes'] = [];
    foreach ($t->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $pet[$row['trait_type'] . 's'][] = $row['trait_value'];
    }
    // 'traits' key maps to 'trait' type (no plural collision)
    $t2 = $pdo->prepare("SELECT trait_value FROM tbl_pet_traits WHERE pet_id = ? AND trait_type = 'trait'");
    $t2->execute([$pet['pet_id']]);
    $pet['traits'] = array_column($t2->fetchAll(PDO::FETCH_ASSOC), 'trait_value');

    return $pet;
}

function save_pet(array $pet): bool {
    global $pdo;
    $exists = $pdo->prepare("SELECT pet_id FROM tbl_pets WHERE pet_id = ?");
    $exists->execute([$pet['id']]);

    if ($exists->rowCount() > 0) {
        $pdo->prepare("UPDATE tbl_pets SET pet_name=?, breed=?, animal_type=?, gender=?, age_desc=?, age_group=?, image_path=?, description=? WHERE pet_id=?")
            ->execute([$pet['name'], $pet['breed'], $pet['type'], $pet['gender'], $pet['age'], $pet['age_group'], $pet['image'], $pet['description'], $pet['id']]);
    } else {
        $pdo->prepare("INSERT INTO tbl_pets (pet_id,pet_name,breed,animal_type,gender,age_desc,age_group,image_path,description) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$pet['id'], $pet['name'], $pet['breed'], $pet['type'], $pet['gender'], $pet['age'], $pet['age_group'], $pet['image'], $pet['description']]);
    }

    // Replace gallery
    $pdo->prepare("DELETE FROM tbl_pet_gallery WHERE pet_id = ?")->execute([$pet['id']]);
    foreach ($pet['gallery'] as $i => $path) {
        $pdo->prepare("INSERT INTO tbl_pet_gallery (pet_id, image_path, sort_order) VALUES (?,?,?)")->execute([$pet['id'], $path, $i]);
    }

    // Replace traits/likes/dislikes
    $pdo->prepare("DELETE FROM tbl_pet_traits WHERE pet_id = ?")->execute([$pet['id']]);
    foreach (($pet['traits']   ?? []) as $v) { $pdo->prepare("INSERT INTO tbl_pet_traits (pet_id,trait_type,trait_value) VALUES (?,?,?)")->execute([$pet['id'],'trait',$v]); }
    foreach (($pet['likes']    ?? []) as $v) { $pdo->prepare("INSERT INTO tbl_pet_traits (pet_id,trait_type,trait_value) VALUES (?,?,?)")->execute([$pet['id'],'like',$v]); }
    foreach (($pet['dislikes'] ?? []) as $v) { $pdo->prepare("INSERT INTO tbl_pet_traits (pet_id,trait_type,trait_value) VALUES (?,?,?)")->execute([$pet['id'],'dislike',$v]); }

    return true;
}

function delete_pet(string $id): bool {
    global $pdo;
    $pdo->prepare("UPDATE tbl_pets SET is_available = 0 WHERE pet_id = ?")->execute([$id]);
    return true;
}

// ── PRODUCTS ───────────────────────────────────────────────────

function get_all_products(): array {
    global $pdo;
    $rows = $pdo->query("SELECT * FROM tbl_products WHERE is_active = 1 ORDER BY product_id")->fetchAll(PDO::FETCH_ASSOC);
    return array_map('_hydrate_product', $rows);
}

function get_product_by_id(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tbl_products WHERE product_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? _hydrate_product($row) : null;
}

function _hydrate_product(array $row): array {
    global $pdo;
    $row['id']     = (int)$row['product_id'];
    $row['price']  = (float)$row['price'];
    $row['image']  = $row['image_path'];
    $row['weight'] = $row['weight_size'];
    // Gallery
    $g = $pdo->prepare("SELECT image_path FROM tbl_product_gallery WHERE product_id = ? ORDER BY sort_order");
    $g->execute([$row['product_id']]);
    $row['gallery'] = array_column($g->fetchAll(PDO::FETCH_ASSOC), 'image_path');
    // Decode JSON-stored rich fields; fall back to empty array if null/missing
    foreach (['flavors','specs','ingredients','guaranteed_analysis','feeding_guide','materials','features','use_guide','whats_inside'] as $k) {
        $raw = $row[$k] ?? null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            $row[$k] = is_array($decoded) ? $decoded : [];
        } else {
            $row[$k] = [];
        }
    }
    return $row;
}

function save_product(array $product): bool {
    global $pdo;
    $gallery = $product['gallery'] ?? [];
    $id = (int)($product['id'] ?? 0);

    $cols = [
        $product['image']        ?? '',
        $product['title']        ?? '',
        $product['subtitle']     ?? '',
        $product['description']  ?? '',
        $product['full_description'] ?? '',
        $product['price']        ?? 0,
        $product['category']     ?? '',
        $product['pet_type']     ?? '',
        $product['brand']        ?? '',
        $product['life_stage']   ?? '',
        $product['weight']       ?? '',
        $product['food_form']    ?? '',
        $product['storage_type'] ?? '',
        $product['origin']       ?? '',
        $product['rating']       ?? 5.0,
        $product['review_count'] ?? 0,
    ];

    if ($id > 0) {
        $pdo->prepare("UPDATE tbl_products SET image_path=?,title=?,subtitle=?,description=?,full_description=?,price=?,category=?,pet_type=?,brand=?,life_stage=?,weight_size=?,food_form=?,storage_type=?,origin=?,rating=?,review_count=? WHERE product_id=?")
            ->execute(array_merge($cols, [$id]));
    } else {
        $pdo->prepare("INSERT INTO tbl_products (image_path,title,subtitle,description,full_description,price,category,pet_type,brand,life_stage,weight_size,food_form,storage_type,origin,rating,review_count) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute($cols);
        $id = (int)$pdo->lastInsertId();
    }

    // Replace gallery
    $pdo->prepare("DELETE FROM tbl_product_gallery WHERE product_id = ?")->execute([$id]);
    foreach ($gallery as $i => $path) {
        $pdo->prepare("INSERT INTO tbl_product_gallery (product_id, image_path, sort_order) VALUES (?,?,?)")->execute([$id, $path, $i]);
    }
    return true;
}

function delete_product(int $id): bool {
    global $pdo;
    $pdo->prepare("UPDATE tbl_products SET is_active = 0 WHERE product_id = ?")->execute([$id]);
    return true;
}

// ── APPLICATIONS ───────────────────────────────────────────────

function get_all_applications(): array {
    global $pdo;
    $rows = $pdo->query("
        SELECT a.*, u.full_name AS user_name, u.email AS user_email,
               p.pet_name, p.breed AS pet_breed, p.image_path AS pet_image
        FROM tbl_applications a
        JOIN tbl_users u ON a.user_id = u.user_id
        JOIN tbl_pets  p ON a.pet_id  = p.pet_id
        ORDER BY a.submitted_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    return array_map('_normalize_app', $rows);
}

function get_applications_by_user(int $user_id): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.*, u.full_name AS user_name, u.email AS user_email,
               p.pet_name, p.breed AS pet_breed, p.image_path AS pet_image
        FROM tbl_applications a
        JOIN tbl_users u ON a.user_id = u.user_id
        JOIN tbl_pets  p ON a.pet_id  = p.pet_id
        WHERE a.user_id = ?
        ORDER BY a.submitted_at DESC
    ");
    $stmt->execute([$user_id]);
    return array_map('_normalize_app', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

function get_application_by_id(string $app_id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.*, u.full_name AS user_name, u.email AS user_email,
               p.pet_name, p.breed AS pet_breed, p.image_path AS pet_image
        FROM tbl_applications a
        JOIN tbl_users u ON a.user_id = u.user_id
        JOIN tbl_pets  p ON a.pet_id  = p.pet_id
        WHERE a.app_id = ?
    ");
    $stmt->execute([$app_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? _normalize_app($row) : null;
}

function _normalize_app(array $row): array {
    $row['id']           = $row['app_id'];
    $row['type']         = $row['app_type'];
    $row['rejected']     = (bool)$row['rejected'];
    $row['current_step'] = (int)$row['current_step'];
    return $row;
}

function save_application(array $app): bool {
    global $pdo;
    $check = $pdo->prepare("SELECT app_id FROM tbl_applications WHERE app_id = ?");
    $check->execute([$app['id'] ?? '']);

    if ($check->rowCount() > 0) {
        $pdo->prepare("UPDATE tbl_applications SET status=?,current_step=?,last_update=?,rejected=? WHERE app_id=?")
            ->execute([$app['status'], $app['current_step'], $app['last_update'], $app['rejected'] ? 1 : 0, $app['id']]);
    } else {
        // Generate next app_id
        $max = $pdo->query("SELECT MAX(CAST(SUBSTRING(app_id,5) AS UNSIGNED)) FROM tbl_applications")->fetchColumn();
        $new_id = 'APP-' . str_pad((int)$max + 1, 3, '0', STR_PAD_LEFT);

        $pdo->prepare("INSERT INTO tbl_applications (app_id,user_id,pet_id,app_type,status,current_step,last_update,rejected,submitted_at) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([
                $new_id,
                $app['user_id'],
                $app['pet_id'],
                $app['type'],
                $app['status'],
                $app['current_step'],
                $app['last_update'],
                0,
                $app['submitted_at'],
            ]);
    }
    return true;
}

// ── ORDERS ─────────────────────────────────────────────────────

function generate_order_number(): string {
    global $pdo;
    $today = date('Ymd');
    $count = $pdo->query("SELECT COUNT(*) FROM tbl_orders WHERE DATE(ordered_at) = CURDATE()")->fetchColumn();
    return 'FS-' . $today . '-' . str_pad((int)$count + 1, 4, '0', STR_PAD_LEFT);
}

function create_order(array $data, array $cart_items): int {
    global $pdo;

    $order_number = generate_order_number();
    $subtotal     = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['unit_price'] * $item['quantity'];
    }
    $donation = round($subtotal * 0.10, 2);
    $total    = $subtotal + $donation;

    $pdo->prepare("INSERT INTO tbl_orders
        (order_number, user_id, full_name, email, phone, address, city, zip_code, payment_method, subtotal, donation_amount, total_amount, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'Pending')")
        ->execute([
            $order_number,
            $data['user_id'],
            $data['full_name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['zip_code'],
            $data['payment_method'],
            $subtotal,
            $donation,
            $total,
        ]);
    $order_id = (int)$pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $sub = round($item['unit_price'] * $item['quantity'], 2);
        $pdo->prepare("INSERT INTO tbl_order_items (order_id, product_id, product_title, unit_price, quantity, subtotal) VALUES (?,?,?,?,?,?)")
            ->execute([$order_id, $item['product_id'], $item['product_title'], $item['unit_price'], $item['quantity'], $sub]);
    }
    return $order_id;
}

function get_order_by_id(int $order_id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT o.*, u.full_name AS account_name FROM tbl_orders o JOIN tbl_users u ON o.user_id = u.user_id WHERE o.order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) return null;
    $items = $pdo->prepare("SELECT * FROM tbl_order_items WHERE order_id = ?");
    $items->execute([$order_id]);
    $order['items'] = $items->fetchAll(PDO::FETCH_ASSOC);
    return $order;
}

function get_orders_by_user(int $user_id): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tbl_orders WHERE user_id = ? ORDER BY ordered_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_order_items(int $order_id): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT oi.*, p.name AS product_name FROM tbl_order_items oi LEFT JOIN tbl_products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_all_orders(): array {
    global $pdo;
    return $pdo->query("
        SELECT o.*, u.full_name AS account_name, u.email AS account_email,
               COUNT(i.item_id) AS total_items
        FROM tbl_orders o
        JOIN tbl_users u      ON o.user_id  = u.user_id
        JOIN tbl_order_items i ON o.order_id = i.order_id
        GROUP BY o.order_id
        ORDER BY o.ordered_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

function update_order_status(int $order_id, string $status): bool {
    global $pdo;
    $pdo->prepare("UPDATE tbl_orders SET status = ? WHERE order_id = ?")->execute([$status, $order_id]);
    return true;
}

// ── APPLICATION DETAIL TABLES ──────────────────────────────────
// These save the full form data from adoptform.php / fosterform.php
// into tbl_app_applicant, tbl_app_adoption, tbl_app_foster.

function save_app_applicant(string $app_id, array $post): bool {
    global $pdo;
    $live_with = isset($post['live_with']) && is_array($post['live_with'])
        ? implode(',', $post['live_with'])
        : ($post['live_with'] ?? '');

    $pdo->prepare("
        INSERT INTO tbl_app_applicant
        (app_id, first_name, last_name, birthdate, pronouns, pronouns_other,
         email, phone, address, social_media, occupation, company,
         civil_status, civil_status_other, prompt_src, prompt_src_other,
         adopted_before, alt_first_name, alt_last_name, alt_relationship,
         alt_phone, alt_email, building_type, building_type_other,
         do_rent, live_with, live_with_other, allergic, household_support,
         support_explain, other_pets, past_pets, near_road,
         move_plan, care_plan, financial_plan, emergency_plan, hours_alone)
        VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE
         first_name=VALUES(first_name), last_name=VALUES(last_name),
         birthdate=VALUES(birthdate), pronouns=VALUES(pronouns),
         pronouns_other=VALUES(pronouns_other), email=VALUES(email),
         phone=VALUES(phone), address=VALUES(address),
         social_media=VALUES(social_media), occupation=VALUES(occupation),
         company=VALUES(company), civil_status=VALUES(civil_status),
         civil_status_other=VALUES(civil_status_other),
         prompt_src=VALUES(prompt_src), prompt_src_other=VALUES(prompt_src_other),
         adopted_before=VALUES(adopted_before),
         alt_first_name=VALUES(alt_first_name), alt_last_name=VALUES(alt_last_name),
         alt_relationship=VALUES(alt_relationship), alt_phone=VALUES(alt_phone),
         alt_email=VALUES(alt_email), building_type=VALUES(building_type),
         building_type_other=VALUES(building_type_other),
         do_rent=VALUES(do_rent), live_with=VALUES(live_with),
         live_with_other=VALUES(live_with_other), allergic=VALUES(allergic),
         household_support=VALUES(household_support),
         support_explain=VALUES(support_explain), other_pets=VALUES(other_pets),
         past_pets=VALUES(past_pets), near_road=VALUES(near_road),
         move_plan=VALUES(move_plan), care_plan=VALUES(care_plan),
         financial_plan=VALUES(financial_plan), emergency_plan=VALUES(emergency_plan),
         hours_alone=VALUES(hours_alone)
    ")->execute([
        $app_id,
        trim($post['first_name']       ?? ''),
        trim($post['last_name']        ?? ''),
        $post['birthdate']             ?: null,
        trim($post['pronouns']         ?? ''),
        trim($post['pronouns_other']   ?? '') ?: null,
        trim($post['email']            ?? ''),
        trim($post['phone']            ?? ''),
        trim($post['address']          ?? ''),
        trim($post['social']           ?? '') ?: null,
        trim($post['occupation']       ?? '') ?: null,
        trim($post['company']          ?? '') ?: null,
        trim($post['status']           ?? '') ?: null,
        trim($post['status_other']     ?? '') ?: null,
        trim($post['prompt_src']       ?? '') ?: null,
        trim($post['prompt_src_other'] ?? '') ?: null,
        trim($post['adopted_before']   ?? '') ?: null,
        trim($post['alt_first']        ?? '') ?: null,
        trim($post['alt_last']         ?? '') ?: null,
        trim($post['relationship']     ?? '') ?: null,
        trim($post['alt_phone']        ?? '') ?: null,
        trim($post['alt_email']        ?? '') ?: null,
        trim($post['building_type']    ?? '') ?: null,
        trim($post['building_type_other'] ?? '') ?: null,
        trim($post['do_rent']          ?? '') ?: null,
        $live_with                           ?: null,
        trim($post['live_with_other']  ?? '') ?: null,
        trim($post['allergic']         ?? '') ?: null,
        trim($post['household_support'] ?? '') ?: null,
        trim($post['support_explain']  ?? '') ?: null,
        trim($post['other_pets']       ?? '') ?: null,
        trim($post['past_pets']        ?? '') ?: null,
        trim($post['near_road']        ?? '') ?: null,
        trim($post['move_plan']        ?? '') ?: null,
        trim($post['care_plan']        ?? '') ?: null,
        trim($post['financial_plan']   ?? '') ?: null,
        trim($post['emergency_plan']   ?? '') ?: null,
        trim($post['hours_alone']      ?? '') ?: null,
    ]);
    return true;
}

function save_app_adoption_details(string $app_id, array $post): bool {
    global $pdo;
    $pdo->prepare("
        INSERT INTO tbl_app_adoption (app_id, interview_date, interview_time, same_month)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
          interview_date=VALUES(interview_date),
          interview_time=VALUES(interview_time),
          same_month=VALUES(same_month)
    ")->execute([
        $app_id,
        $post['interview_date'] ?: null,
        $post['interview_time'] ?: null,
        trim($post['same_month'] ?? '') ?: null,
    ]);
    return true;
}

function save_app_foster_details(string $app_id, array $post): bool {
    global $pdo;
    $pdo->prepare("
        INSERT INTO tbl_app_foster (app_id, foster_duration, shelter_visit)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
          foster_duration=VALUES(foster_duration),
          shelter_visit=VALUES(shelter_visit)
    ")->execute([
        $app_id,
        trim($post['foster_duration'] ?? '') ?: null,
        trim($post['shelter_visit']   ?? '') ?: null,
    ]);
    return true;
}

function get_app_full_details(string $app_id): array {
    global $pdo;
    $result = ['applicant' => null, 'adoption' => null, 'foster' => null];

    $stmt = $pdo->prepare("SELECT * FROM tbl_app_applicant WHERE app_id = ?");
    $stmt->execute([$app_id]);
    $result['applicant'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    $stmt = $pdo->prepare("SELECT * FROM tbl_app_adoption WHERE app_id = ?");
    $stmt->execute([$app_id]);
    $result['adoption'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    $stmt = $pdo->prepare("SELECT * FROM tbl_app_foster WHERE app_id = ?");
    $stmt->execute([$app_id]);
    $result['foster'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    return $result;
}

// ── NOTIFICATIONS ────────────────────────────────────────────────

function add_notification(int $user_id, string $app_id, string $message): void {
    global $pdo;
    $pdo->prepare("INSERT INTO tbl_notifications (user_id, app_id, message) VALUES (?,?,?)")
        ->execute([$user_id, $app_id, trim($message)]);
}

function get_unread_notifications(int $user_id): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tbl_notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_all_notifications(int $user_id): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tbl_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 30");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mark_notifications_read(int $user_id): void {
    global $pdo;
    $pdo->prepare("UPDATE tbl_notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);
}

function count_unread_notifications(int $user_id): int {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return (int)$stmt->fetchColumn();
}

// ── HOME PAGE STATS ──────────────────────────────────────────────

function get_homepage_stats(): array {
    global $pdo;
    $waiting  = (int)$pdo->query("SELECT COUNT(*) FROM tbl_pets WHERE is_available = 1")->fetchColumn();
    $adopted  = (int)$pdo->query("SELECT COUNT(*) FROM tbl_applications WHERE app_type='Adoption' AND status='completed' AND rejected=0 AND submitted_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)")->fetchColumn();
    $fostered = (int)$pdo->query("SELECT COUNT(*) FROM tbl_applications WHERE app_type='Foster'   AND status='completed' AND rejected=0 AND submitted_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)")->fetchColumn();
    return ['waiting' => $waiting, 'adopted' => $adopted, 'fostered' => $fostered];
}
