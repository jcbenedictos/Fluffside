<?php
session_start();

date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

require_once 'db.inc.php';
require_once 'db_helper.inc.php';
$_all_pets = get_all_pets();
$pets = [];
foreach ($_all_pets as $_p) { $pets[$_p['id']] = $_p; }
$pet_id = strtolower(trim($_GET['pet'] ?? 'scout'));
$selected_pet = $pets[$pet_id] ?? $pets['scout'];

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ─── helpers ───────────────────────────────────────────────────────────────
function old(string $k, string $fallback = ''): string
{
    return htmlspecialchars($_POST[$k] ?? $fallback);
}
function oldArr(string $k): array
{
    return $_POST[$k] ?? [];
}
function radioChecked(string $k, string $v): string
{
    return (($_POST[$k] ?? '') === $v) ? 'checked' : '';
}
function chkChecked(string $k, string $v): string
{
    return in_array($v, ($_POST[$k] ?? [])) ? 'checked' : '';
}
function fieldErr(array $errs, string $key): string
{
    return isset($errs[$key])
        ? '<span class="field-err"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($errs[$key]) . '</span>'
        : '';
}
function hasErr(array $errs, string $key): string
{
    return isset($errs[$key]) ? ' has-error' : '';
}

// ─── validation ────────────────────────────────────────────────────────────
$success = false;
$errors  = [];   // keyed by field name

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Applicant Info ──
    if (empty(trim($_POST['first_name'] ?? '')))   $errors['first_name']   = 'First name is required.';
    if (empty(trim($_POST['last_name'] ?? '')))    $errors['last_name']    = 'Last name is required.';
    if (empty(trim($_POST['address'] ?? '')))      $errors['address']      = 'Address is required.';
    if (empty(trim($_POST['phone'] ?? '')))        $errors['phone']        = 'Phone number is required.';
    if (empty(trim($_POST['email'] ?? '')) || !filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL))
        $errors['email']        = 'A valid email is required.';
    if (empty(trim($_POST['birthdate'] ?? '')))    $errors['birthdate']    = 'Birthdate is required.';
    if (empty(trim($_POST['company'] ?? '')))      $errors['company']      = 'Company / business name is required (type N/A if unemployed).';
    if (empty(trim($_POST['social'] ?? '')))       $errors['social']       = 'Social media link is required (type N/A if none).';

    // status + others
    $status = trim($_POST['status'] ?? '');
    if (empty($status))                            $errors['status']       = 'Status is required.';
    if ($status === 'others' && empty(trim($_POST['status_other'] ?? '')))
        $errors['status_other'] = 'Please specify your status.';

    // pronouns + others
    $pronouns = trim($_POST['pronouns'] ?? '');
    if (empty($pronouns))                          $errors['pronouns']     = 'Pronouns is required.';
    if ($pronouns === 'others' && empty(trim($_POST['pronouns_other'] ?? '')))
        $errors['pronouns_other'] = 'Please specify your pronouns.';

    // prompt source + others
    $prompt_src = trim($_POST['prompt_src'] ?? '');
    if (empty($prompt_src))                        $errors['prompt_src']   = 'Please tell us what prompted you to adopt.';
    if ($prompt_src === 'others' && empty(trim($_POST['prompt_src_other'] ?? '')))
        $errors['prompt_src_other'] = 'Please specify how you found us.';

    if (empty(trim($_POST['adopted_before'] ?? ''))) $errors['adopted_before'] = 'Please answer this question.';

    // Alternate contact
    if (empty(trim($_POST['alt_first'] ?? '')))    $errors['alt_first']    = 'Alternate contact first name is required.';
    if (empty(trim($_POST['alt_last'] ?? '')))     $errors['alt_last']     = 'Alternate contact last name is required.';
    if (empty(trim($_POST['relationship'] ?? ''))) $errors['relationship'] = 'Relationship is required.';
    if (empty(trim($_POST['alt_phone'] ?? '')))    $errors['alt_phone']    = 'Alternate contact phone is required.';
    if (empty(trim($_POST['alt_email'] ?? '')) || !filter_var(trim($_POST['alt_email'] ?? ''), FILTER_VALIDATE_EMAIL))
        $errors['alt_email']    = 'A valid alternate contact email is required.';

    // ── Questionnaire ──

    $live_with = $_POST['live_with'] ?? [];
    if (empty($live_with))                         $errors['live_with']    = 'Please select at least one option.';

    if (empty(trim($_POST['foster_duration'] ?? ''))) $errors['foster_duration'] = 'Please answer this question.';
    if (empty(trim($_POST['shelter_visit'] ?? '')))   $errors['shelter_visit']   = 'Please answer this question.';

    // ── Photo uploads ──
    $photo_fields = ['1' => 'Front of the house', '2' => 'Living Room', '3' => 'Dining Area', '4' => 'Kitchen', '5' => 'Bedroom'];
    foreach ($photo_fields as $num => $plabel) {
        if (empty($_FILES['photo_' . $num]['name'])) {
            $errors['photo_' . $num] = 'Photo of ' . $plabel . ' is required.';
        }
    }
    if (empty($_FILES['valid_id']['name']))         $errors['valid_id']     = 'A valid ID is required.';

    if (!isset($_POST['agreement']))                 $errors['agreement']     = 'You must agree to the foster parent agreement.';
    if (!isset($_POST['authorize']))                 $errors['authorize']      = 'You must authorize FluffSide to proceed.';

    if (empty($errors)) {
        $success = true;
        // ── Save application to data store ──
        require_once 'db_helper.inc.php';
        $new_app = [
            'id'           => '',
            'user_id'      => (int)$_SESSION['user_id'],
            'user_name'    => trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? '')),
            'user_email'   => $_SESSION['email'] ?? '',
            'pet_id'       => $selected_pet['id'],
            'pet_name'     => $selected_pet['name'],
            'pet_breed'    => $selected_pet['breed'],
            'pet_image'    => $selected_pet['image'],
            'type'         => 'Foster',
            'status'       => 'active',
            'current_step' => 1,
            'last_update'  => 'Your foster application has been received! We will review it shortly.',
            'submitted_at' => date('Y-m-d'),
            'rejected'     => false,
        ];
        save_application($new_app);

        // ── Save full form details ──
        global $pdo;
        $stmt = $pdo->prepare("SELECT app_id FROM tbl_applications WHERE user_id = ? AND pet_id = ? ORDER BY submitted_at DESC LIMIT 1");
        $stmt->execute([(int)$_SESSION['user_id'], $selected_pet['id']]);
        $saved_app_id = $stmt->fetchColumn();

        if ($saved_app_id) {
            save_app_applicant($saved_app_id, $_POST);
            save_app_foster_details($saved_app_id, $_POST);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foster Application — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>


        .adopt-page {
            max-width: 860px;
            margin: 2.5rem auto 4rem;
            padding: 0 1.5rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #888;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: color .2s;
        }

        .back-link:hover {
            color: #EF8E35;
        }

        .adopt-intro {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin-bottom: .5rem;
        }

        .adopt-intro a {
            color: #EF8E35;
            text-decoration: none;
        }

        .adopt-intro p {
            margin-bottom: .4rem;
        }

        hr.divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 1.2rem 0 2rem;
        }

        .form-card {
            background: #fff;
            border-radius: 14px;
            padding: 2.5rem 2.8rem;
            box-shadow: 0 2px 18px rgba(0, 0, 0, .07);
        }

        .form-title {
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: .5px;
            color: #222;
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #222;
            margin: 2rem 0 .3rem;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .form-section-title .required-note {
            font-size: 11px;
            font-weight: 600;
            color: #EF8E35;
        }

        .section-sub {
            font-size: 12.5px;
            color: #888;
            margin-bottom: 1rem;
        }

        /* resident box */
        .resident-box {
            background: #fffaf4;
            border: 1.5px solid #f5dbb8;
            border-radius: 10px;
            padding: 1rem 1.4rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .6rem 1.5rem;
            margin-bottom: .5rem;
        }

        .resident-box .field-lbl {
            font-size: 11px;
            font-weight: 700;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .resident-box .field-val {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin-top: 2px;
        }

        /* rows */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-group label {
            font-size: 12.5px;
            font-weight: 700;
            color: #444;
        }

        .form-group label .req,
        .req {
            color: #EF8E35;
        }

        .form-group .hint {
            font-size: 11px;
            color: #aaa;
            margin-top: 1px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group select,
        .form-group textarea {
            border: none;
            border-bottom: 1.5px solid #ddd;
            border-radius: 0;
            padding: 7px 2px;
            font-family: 'Nunito', sans-serif;
            font-size: 13.5px;
            color: #222;
            background: transparent;
            outline: none;
            transition: border-color .2s;
            width: 100%;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-bottom-color: #EF8E35;
        }

        .form-group textarea {
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 10px 12px;
            resize: vertical;
            min-height: 90px;
            margin-top: 2px;
        }

        .form-group textarea:focus {
            border-color: #EF8E35;
        }

        .form-group.has-error input,
        .form-group.has-error textarea,
        .form-group.has-error select {
            border-color: #e05252;
        }

        /* field-level error */
        .field-err {
            display: block;
            font-size: 11.5px;
            color: #c0392b;
            margin-top: 4px;
        }

        .field-err i {
            margin-right: 3px;
        }

        /* radio / check */
        .radio-group,
        .check-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 18px;
            margin-top: 4px;
        }

        .stay-options {
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 10px;
        }

        .stay-options label {
            white-space: nowrap;
        }

        .radio-group label,
        .check-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            font-weight: 400;
            color: #444;
            cursor: pointer;
        }

        .radio-group input[type="radio"],
        .check-group input[type="checkbox"] {
            accent-color: #EF8E35;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .radio-inline-label {
            font-size: 12.5px;
            font-weight: 700;
            color: #444;
            margin-bottom: 4px;
            display: block;
        }

        /* "others specify" input */
        .others-input {
            display: none;
            margin-top: 6px;
        }

        .others-input input[type="text"] {
            border: none;
            border-bottom: 1.5px solid #ddd;
            background: transparent;
            font-family: 'Nunito', sans-serif;
            font-size: 13px;
            color: #222;
            outline: none;
            padding: 4px 2px;
            width: 100%;
            transition: border-color .2s;
        }

        .others-input input[type="text"]:focus {
            border-bottom-color: #EF8E35;
        }

        .others-input.visible {
            display: block;
        }

        /* q-item */
        .q-item {
            margin-bottom: 1.4rem;
        }

        .q-item .q-label {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }

        /* file */
        .file-group {
            margin-bottom: .9rem;
        }

        .file-group label {
            font-size: 12.5px;
            font-weight: 700;
            color: #444;
            display: block;
            margin-bottom: 4px;
        }

        .file-group input[type="file"] {
            font-size: 12.5px;
            font-family: 'Nunito', sans-serif;
            color: #555;
        }

        .file-group.has-error input[type="file"] {
            outline: 2px solid #e05252;
            border-radius: 4px;
        }

        /* auth */
        .auth-box {
            background: #fffaf4;
            border: 1.5px solid #f5dbb8;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            margin: 1.5rem 0;
        }

        .auth-box.has-error {
            border-color: #e05252;
            background: #fff5f5;
        }

        .auth-box {
            background: #fffaf4;
            border: 1.5px solid #f5dbb8;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            margin: 1.5rem 0;
        }

        .auth-box.has-error {
            border-color: #e05252;
            background: #fff5f5;
        }

        .foster-agreement-box {
            background: #FEE8BE;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 1.2rem 1.3rem;
            margin: 1.5rem 0;
            letter-spacing: 0.2px;
            line-height: 1.7;
            color: #3d2f22;
        }

        .foster-agreement-box .form-section-title {
            margin-bottom: 0.8rem;
        }

        .foster-agreement-box .agreement-intro {
            margin-bottom: 1rem;
            font-size: 13px;
            color: #2e2118;
        }

        .foster-agreement-box .agreement-list {
            margin: 0;
            padding-left: 1.2rem;
            list-style-type: disc;
            font-size: 13px;
            color: #2e2118;
        }

        .foster-agreement-box .agreement-list li {
            margin-bottom: 0.85rem;
        }

        .auth-item {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .auth-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .auth-item.has-error {
            border-color: #e05252;
        }

        .auth-box label,
        .auth-item label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #444;
            line-height: 1.6;
            cursor: pointer;
        }

        .auth-box input[type="checkbox"] {
            accent-color: #EF8E35;
            width: 16px;
            height: 16px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* submit */
        .submit-wrap {
            text-align: center;
            margin-top: 1.5rem;
        }

        .btn-submit {
            background: #EF8E35;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .5px;
            padding: 13px 60px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }

        .btn-submit:hover {
            background: #d97a28;
        }

        .btn-submit:active {
            transform: scale(.98);
        }

        /* alerts */
        .alert {
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 1.2rem;
            font-size: 13.5px;
            line-height: 1.6;
        }

        .alert-error {
            background: #fff3f3;
            border: 1.5px solid #f5a0a0;
            color: #b83232;
        }

        .alert ul {
            margin: 6px 0 0 16px;
        }

        .alert ul li {
            margin-bottom: 3px;
        }

        /* ── Toast notification ── */
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.14), 0 1px 4px rgba(0,0,0,.06);
            display: flex;
            align-items: center;
            z-index: 9999;
            overflow: hidden;
            min-width: 320px;
            max-width: 400px;
            animation: toastIn .35s cubic-bezier(.21,1.02,.73,1) both,
                       toastOut .3s ease forwards;
            animation-delay: 0s, 4s;
        }

        .toast-accent {
            width: 5px;
            align-self: stretch;
            background: #4CAF7D;
            flex-shrink: 0;
        }

        .toast-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #edfaf3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #4CAF7D;
            flex-shrink: 0;
            margin: 14px 4px 14px 14px;
        }

        .toast-text {
            flex: 1;
            padding: 14px 6px 14px 10px;
        }

        .toast-title {
            font-size: 14px;
            font-weight: 900;
            color: #2d2d2d;
            margin-bottom: 3px;
        }

        .toast-sub {
            font-size: 12px;
            font-weight: 600;
            color: #888;
            line-height: 1.5;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #bbb;
            padding: 14px 14px 14px 6px;
            line-height: 1;
            transition: color .15s;
            align-self: flex-start;
        }

        .toast-close:hover { color: #555; }

        .toast-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .toast-btn {
            font-size: 11.5px;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .toast-btn-primary { background: #EF8E35; color: #fff; }
        .toast-btn-ghost   { background: #f0f0f0; color: #555; }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(60px) scale(.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes toastOut {
            to { opacity: 0; transform: translateX(60px); }
        }

        @media(max-width:600px) {
            .form-card {
                padding: 1.5rem 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .resident-box {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- ── HEADER ── -->
        <?php include 'header.php'; ?>
        <div class="adopt-page">
            <a href="pet.php?id=<?= h($pet_id) ?>" class="back-link"><i class="fas fa-chevron-left"></i> Back</a>

            <div class="adopt-intro">
                <p><a href="#">Fostering</a> is temporarily caring for a rescued animal by providing them with a safe and loving home until they are ready for adoption or permanent placement.</p>
                <p>All of our adoptable cats and dogs are <strong>already spayed/neutered (kapan)</strong> and vaccinated before being placed with their future families. Many of our rescued pets came from difficult situations such as abandonment, neglect, or life on the streets, which is why we carefully ensure they are matched with caring adopters and foster families.</p>
            </div>
            <hr class="divider">

            <div class="form-card">
                <div class="form-title">FOSTER APPLICATION</div>

                <?php if ($success): ?>
                    <div class="toast" id="submitToast">
                        <div class="toast-accent"></div>
                        <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="toast-text">
                            <div class="toast-title">Foster application submitted!</div>
                            <div class="toast-sub">
                                Thank you, <strong><?php echo old('first_name'); ?></strong>! We'll review your application and reach out within 3–5 business days.
                                <div class="toast-actions">
                                    <a href="dashboard.php" class="toast-btn toast-btn-primary">View Dashboard</a>
                                    <a href="residents.php" class="toast-btn toast-btn-ghost">Browse More</a>
                                </div>
                            </div>
                        </div>
                        <button class="toast-close" onclick="document.getElementById('submitToast').style.display='none'">&times;</button>
                    </div>
                <?php elseif (!empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="alert alert-error">
                        <strong><i class="fas fa-exclamation-circle"></i> Please fix the following before submitting:</strong>
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="fosterform.php?pet=<?= h($pet_id) ?>" enctype="multipart/form-data" novalidate>

                    <!-- ══ SELECTED RESIDENT ══ -->
                    <div class="form-section-title">
                        Selected Resident's Info
                        <a href="#" style="font-size:12px;color:#EF8E35;text-decoration:none;font-weight:600;">Change again</a>
                    </div>
                    <div class="resident-box">
                        <div>
                            <div class="field-lbl">Name</div>
                            <div class="field-val"><?= h(strtoupper($selected_pet['name'])) ?></div>
                        </div>
                        <div>
                            <div class="field-lbl">Pet Type</div>
                            <div class="field-val"><?= h($selected_pet['type']) ?></div>
                        </div>
                        <div>
                            <div class="field-lbl">Pet Breed</div>
                            <div class="field-val"><?= h(strtoupper($selected_pet['breed'])) ?></div>
                        </div>
                        <div>
                            <div class="field-lbl">Age</div>
                            <div class="field-val"><?= h(strtoupper($selected_pet['age'])) ?></div>
                        </div>
                        <div>
                            <div class="field-lbl">Sex</div>
                            <div class="field-val"><?= h($selected_pet['gender']) ?></div>
                        </div>
                    </div>

                    <!-- ══ APPLICANT INFO ══ -->
                    <div class="form-section-title" style="margin-top:2rem;">
                        Applicant's Info <span class="required-note">* indicates required fields</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'first_name'); ?>">
                            <label>First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" placeholder="First Name" value="<?php echo old('first_name'); ?>">
                            <?php echo fieldErr($errors, 'first_name'); ?>
                        </div>
                        <div class="form-group<?php echo hasErr($errors, 'last_name'); ?>">
                            <label>Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" placeholder="Last Name" value="<?php echo old('last_name'); ?>">
                            <?php echo fieldErr($errors, 'last_name'); ?>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group<?php echo hasErr($errors, 'address'); ?>">
                            <label>Address <span class="req">*</span></label>
                            <input type="text" name="address" placeholder="Full address" value="<?php echo old('address'); ?>">
                            <?php echo fieldErr($errors, 'address'); ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'phone'); ?>">
                            <label>Phone <span class="req">*</span></label>
                            <input type="tel" name="phone" placeholder="+63" value="<?php echo old('phone'); ?>">
                            <?php echo fieldErr($errors, 'phone'); ?>
                        </div>
                        <div class="form-group<?php echo hasErr($errors, 'email'); ?>">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" name="email" placeholder="email@example.com" value="<?php echo old('email'); ?>">
                            <?php echo fieldErr($errors, 'email'); ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'birthdate'); ?>">
                            <label>Birthdate <span class="req">*</span></label>
                            <input type="date" name="birthdate" value="<?php echo old('birthdate'); ?>">
                            <?php echo fieldErr($errors, 'birthdate'); ?>
                        </div>
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" name="occupation" placeholder="Your occupation" value="<?php echo old('occupation'); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'company'); ?>">
                            <label>Company / Business Name <span class="req">*</span></label>
                            <span class="hint">Type N/A if unemployed</span>
                            <input type="text" name="company" placeholder="Company name" value="<?php echo old('company'); ?>">
                            <?php echo fieldErr($errors, 'company'); ?>
                        </div>
                        <div class="form-group<?php echo hasErr($errors, 'social'); ?>">
                            <label>Social Media Profile Link <span class="req">*</span></label>
                            <span class="hint">Type N/A if no social media</span>
                            <input type="text" name="social" placeholder="Facebook / Instagram / Twitter link" value="<?php echo old('social'); ?>">
                            <?php echo fieldErr($errors, 'social'); ?>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-row">
                        <div class="form-group">
                            <span class="radio-inline-label">Status <span class="req">*</span></span>
                            <div class="radio-group">
                                <?php foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $s): ?>
                                    <label><input type="radio" name="status" value="<?php echo $s; ?>" <?php echo radioChecked('status', $s); ?> onchange="toggleOther(this,'status_other_box')"> <?php echo $s; ?></label>
                                <?php endforeach; ?>
                                <label><input type="radio" name="status" value="others" <?php echo radioChecked('status', 'others'); ?> onchange="toggleOther(this,'status_other_box')"> Others</label>
                            </div>
                            <div class="others-input<?php echo (($_POST['status'] ?? '') === 'others') ? ' visible' : ''; ?>" id="status_other_box">
                                <input type="text" name="status_other" placeholder="Please specify..." value="<?php echo old('status_other'); ?>">
                            </div>
                            <?php echo fieldErr($errors, 'status');
                            echo fieldErr($errors, 'status_other'); ?>
                        </div>
                        <div class="form-group">
                            <span class="radio-inline-label">Pronouns <span class="req">*</span></span>
                            <div class="radio-group">
                                <?php foreach (['She/Her', 'He/Him', 'Prefer not to say'] as $p): ?>
                                    <label><input type="radio" name="pronouns" value="<?php echo $p; ?>" <?php echo radioChecked('pronouns', $p); ?> onchange="toggleOther(this,'pronouns_other_box')"> <?php echo $p; ?></label>
                                <?php endforeach; ?>
                                <label><input type="radio" name="pronouns" value="others" <?php echo radioChecked('pronouns', 'others'); ?> onchange="toggleOther(this,'pronouns_other_box')"> Others</label>
                            </div>
                            <div class="others-input<?php echo (($_POST['pronouns'] ?? '') === 'others') ? ' visible' : ''; ?>" id="pronouns_other_box">
                                <input type="text" name="pronouns_other" placeholder="Please specify..." value="<?php echo old('pronouns_other'); ?>">
                            </div>
                            <?php echo fieldErr($errors, 'pronouns');
                            echo fieldErr($errors, 'pronouns_other'); ?>
                        </div>
                    </div>

                    <!-- Prompt source + Adopted before -->
                    <div class="form-row">
                        <div class="form-group">
                            <span class="radio-inline-label">What prompted you to adopt from FluffSide? <span class="req">*</span></span>
                            <div class="radio-group" style="flex-direction:column;gap:4px;">
                                <?php foreach (['Friends', 'Social Media', 'Website'] as $pr): ?>
                                    <label><input type="radio" name="prompt_src" value="<?php echo $pr; ?>" <?php echo radioChecked('prompt_src', $pr); ?> onchange="toggleOther(this,'prompt_other_box')"> <?php echo $pr; ?></label>
                                <?php endforeach; ?>
                                <label><input type="radio" name="prompt_src" value="others" <?php echo radioChecked('prompt_src', 'others'); ?> onchange="toggleOther(this,'prompt_other_box')"> Others</label>
                            </div>
                            <div class="others-input<?php echo (($_POST['prompt_src'] ?? '') === 'others') ? ' visible' : ''; ?>" id="prompt_other_box">
                                <input type="text" name="prompt_src_other" placeholder="Please specify..." value="<?php echo old('prompt_src_other'); ?>">
                            </div>
                            <?php echo fieldErr($errors, 'prompt_src');
                            echo fieldErr($errors, 'prompt_src_other'); ?>
                        </div>
                        <div class="form-group">
                            <span class="radio-inline-label">Have you adopted from FluffSide before? <span class="req">*</span></span>
                            <div class="radio-group">
                                <label><input type="radio" name="adopted_before" value="Yes" <?php echo radioChecked('adopted_before', 'Yes'); ?>> Yes</label>
                                <label><input type="radio" name="adopted_before" value="No" <?php echo radioChecked('adopted_before', 'No');  ?>> No</label>
                            </div>
                            <?php echo fieldErr($errors, 'adopted_before'); ?>
                        </div>
                    </div>

                    <!-- Alternate Contact -->
                    <div class="form-section-title" style="font-size:14px;margin-top:1.5rem;">Alternate Contact <span class="req">*</span></div>
                    <p class="section-sub">If the applicant is a minor, a parent or guardian must be the alternate contact and co-sign the application.</p>

                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'alt_first'); ?>">
                            <label>First Name <span class="req">*</span></label>
                            <input type="text" name="alt_first" placeholder="First Name" value="<?php echo old('alt_first'); ?>">
                            <?php echo fieldErr($errors, 'alt_first'); ?>
                        </div>
                        <div class="form-group<?php echo hasErr($errors, 'alt_last'); ?>">
                            <label>Last Name <span class="req">*</span></label>
                            <input type="text" name="alt_last" placeholder="Last Name" value="<?php echo old('alt_last'); ?>">
                            <?php echo fieldErr($errors, 'alt_last'); ?>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="form-group<?php echo hasErr($errors, 'relationship'); ?>">
                            <label>Relationship <span class="req">*</span></label>
                            <input type="text" name="relationship" placeholder="e.g. Parent, Spouse, Sibling" value="<?php echo old('relationship'); ?>">
                            <?php echo fieldErr($errors, 'relationship'); ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'alt_phone'); ?>">
                            <label>Phone <span class="req">*</span></label>
                            <input type="tel" name="alt_phone" placeholder="+63" value="<?php echo old('alt_phone'); ?>">
                            <?php echo fieldErr($errors, 'alt_phone'); ?>
                        </div>
                        <div class="form-group<?php echo hasErr($errors, 'alt_email'); ?>">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" name="alt_email" placeholder="email@example.com" value="<?php echo old('alt_email'); ?>">
                            <?php echo fieldErr($errors, 'alt_email'); ?>
                        </div>
                    </div>

                    <!-- ══ QUESTIONNAIRE ══ -->
                    <div class="form-section-title" style="margin-top:2.5rem;">
                        Questionnaire <span class="required-note">* indicates required fields</span>
                    </div>
                    <p class="questionnaire-intro" style="font-size:13px;color:#666;margin-bottom:1.2rem;line-height:1.6;">In an effort to help the process go smoothly, please be as detailed as possible with your responses to the questions below.</p>

                    <!-- Q: Where will your fostered pet stay -->
                    <div class="q-item">
                        <span class="q-label">Where will your fostered pet stay? <span class="req">*</span></span>
                        <div class="field-note" style="font-size:12px;color:#666;margin:4px 0 8px;">Check all that apply.</div>
                        <div class="check-group stay-options">
                            <?php
                            $live_options = ['Indoors only', 'Outdoors only', 'Indoors and outdoors arrangement', 'In a crate/cage/pen'];
                            foreach ($live_options as $lo): ?>
                                <label><input type="checkbox" name="live_with[]" value="<?php echo $lo; ?>" <?php echo chkChecked('live_with', $lo); ?>> <?php echo $lo; ?></label>
                            <?php endforeach; ?>
                        </div>
                        <?php echo fieldErr($errors, 'live_with'); ?>
                    </div>

                    <!-- Foster commitment duration -->
                    <div class="q-item">
                        <span class="q-label">How long can you commit to this foster arrangement? <span class="req">*</span></span>
                        <div class="radio-group">
                            <label><input type="radio" name="foster_duration" value="Less than 3 months" <?php echo radioChecked('foster_duration', 'Less than 3 months'); ?>> Less than 3 months</label>
                            <label><input type="radio" name="foster_duration" value="3 months" <?php echo radioChecked('foster_duration', '3 months'); ?>> 3 months</label>
                            <label><input type="radio" name="foster_duration" value="Indefinitely" <?php echo radioChecked('foster_duration', 'Indefinitely'); ?>> Indefinitely</label>
                            <label><input type="radio" name="foster_duration" value="Unsure" <?php echo radioChecked('foster_duration', 'Unsure'); ?>> Unsure</label>
                        </div>
                        <?php echo fieldErr($errors, 'foster_duration'); ?>
                    </div>

                    <!-- Vaccination and treatment plan -->
                    <div class="q-item">
                        <span class="q-label">Will you take your fostered pet to the FLUFFSIDE shelter for scheduled vaccinations, treatment, etc during your fostering period? <span class="req">*</span></span>
                        <div class="radio-group" style="margin-bottom:8px;">
                            <label><input type="radio" name="shelter_visit" value="Yes" <?php echo radioChecked('shelter_visit', 'Yes'); ?>> Yes</label>
                            <label><input type="radio" name="shelter_visit" value="No" <?php echo radioChecked('shelter_visit', 'No'); ?>> No</label>
                            <label><input type="radio" name="shelter_visit" value="I have a private vet" <?php echo radioChecked('shelter_visit', 'I have a private vet'); ?>> I have a private vet</label>
                            <label><input type="radio" name="shelter_visit" value="No, I prefer our community’s pet clinic" <?php echo radioChecked('shelter_visit', 'No, I prefer our community’s pet clinic'); ?>> No, I prefer our community’s pet clinic</label>
                            <label><input type="radio" name="shelter_visit" value="Unsure" <?php echo radioChecked('shelter_visit', 'Unsure'); ?>> Unsure</label>
                        </div>
                        <?php echo fieldErr($errors, 'shelter_visit'); ?>
                    </div>

                    <!-- ══ PHOTOS ══ -->
                    <div class="form-section-title" style="font-size:15px;margin-top:2rem;">
                        Please attach photos of your home. <span class="required-note">* Required</span>
                    </div>
                    <p class="section-sub">This has replaced our on-site ocular inspections. We value your privacy — your photos will not be used for purposes other than this adoption application.</p>

                    <?php
                    $photo_fields = [
                        '1' => 'Front of the house',
                        '2' => 'Living Room',
                        '3' => 'Dining Area',
                        '4' => 'Kitchen',
                        '5' => 'Bedroom (one you\'ll allow your pet into)',
                    ];
                    foreach ($photo_fields as $num => $plabel):
                    ?>
                        <div class="file-group<?php echo isset($errors['photo_' . $num]) ? ' has-error' : ''; ?>">
                            <label><?php echo $num . '. ' . $plabel; ?> <span class="req">*</span></label>
                            <input type="file" name="photo_<?php echo $num; ?>" accept="image/*">
                            <?php echo fieldErr($errors, 'photo_' . $num); ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="file-group<?php echo isset($errors['valid_id']) ? ' has-error' : ''; ?>" style="margin-top:.8rem;">
                        <label>Upload a valid ID <span class="req">*</span></label>
                        <span style="font-size:11.5px;color:#aaa;display:block;margin-bottom:4px;">If a minor, upload the ID of your parent/guardian.</span>
                        <input type="file" name="valid_id" accept="image/*,.pdf">
                        <?php echo fieldErr($errors, 'valid_id'); ?>
                    </div>

                    <div class="foster-agreement-box">
                        <div class="form-section-title" style="margin-top:0;">Foster Agreement</div>
                        <p class="agreement-intro"><strong>The animal remains the property of FLUFFSIDE.</strong></p>
                        <ul class="agreement-list">
                            <li>The animal will remain up for adoption through FLUFFSIDE.</li>
                            <li>Adoptions will only be authorized by FLUFFSIDE.</li>
                            <li>The foster parent will take prudent and responsible care of the animal. He/she will ensure that the animal will not be stolen, lost, injured, or exposed to unsafe environments. Cats must stay indoors where it is safe and secure at all times. Dogs/puppies will need a place inside the house or a fenced yard adequate for their size, with complete shelter from the heat and rain. The foster parent will use a collar and leash on the dog when taken outside of his/her enclosed property. Birds must be kept in a clean, secure, and properly sized cage with access to food, water, and a safe environment away from predators and extreme temperatures. Rabbits and hamsters must be housed in safe, spacious, and well-maintained enclosures with proper ventilation, bedding, food, water, and protection from stress, injury, and escape.</li>
                            <li>The foster parent is willing to take on the entire expense of an animal’s care. He/she is responsible for providing food, water, shelter, grooming, and bedding for the animal using his/her own resources without reimbursement from the shelter. The shelter can provide dry adult dog/cat food, formula milk, and feeding bottles for unweaned puppies/kittens only if they are available.</li>
                            <li>Should the foster animal need medical attention, the foster parent should schedule an appointment with the shelter veterinarian to avail of free services.</li>
                            <li>If the medication for the animal is unavailable at the shelter, the foster parent agrees to acquire it outside.</li>
                            <li>Should there be a medical emergency, the foster parent should contact the shelter immediately to determine the best course of action. A shelter staff’s mobile phone number will be given should the emergency occur during non-office hours. If there is a need for the foster parent to take the animal to a clinic outside of FLUFFSIDE, the foster parent is responsible for the expenses incurred without reimbursement from the shelter. In case of extensive medical needs, he/she may return the animal to the shelter.</li>
                            <li>The foster parent is responsible for taking the animal back to the shelter for all required medical appointments, such as scheduled vaccinations, deworming, and spaying/neutering, which are all free of charge.</li>
                            <li>If the foster parent already has animals at home, they must be healthy and up-to-date with vaccination shots. They also must be spayed/neutered if he/she will allow them to interact with the foster animal to avoid unwanted pregnancies. If his/her own pets are not spayed/neutered, he/she will ensure that they will be separated from the foster animal and that no breeding will occur.</li>
                            <li>FLUFFSIDE will not be responsible for damages caused by a foster animal.</li>
                            <li>If there are problems with the foster animal, the foster parent will return the animal to the shelter as soon as possible. The foster parent is not allowed to transfer the animal to another individual without the approval of FLUFFSIDE.</li>
                            <li>A tentative return date shall be determined as to when is the best time to return the animal to the shelter. If the animal has not recovered, FLUFFSIDE will discuss the animal’s situation with the foster parent and a veterinarian (if needed) to determine if the animal’s stay should be extended.</li>
                            <li>In the unfortunate event that the animal becomes ill during foster care as to warrant humane euthanasia as advised by an outside veterinarian, the foster parent should contact the shelter immediately to determine the best course of action. He/she is to return the animal to FLUFFSIDE. He/she may also have an outside veterinarian perform the humane euthanasia procedure at his/her expense without reimbursement from the shelter. Emergency euthanasia is allowed if the animal has to be spared from unnecessary pain due to accident, provided that it is supported by a medical document signed by the attending veterinarian stating the reason for euthanasia and procedure applied.</li>
                            <li>Should the foster parent or another person referred by him/her express interest in adopting the animal permanently, the usual adoption process will be followed.</li>
                            <li>FLUFFSIDE is released from any liability for injury or illness that the foster parent, their family, or their pets may receive while volunteering as a foster parent for FLUFFSIDE.</li>
                        </ul>
                    </div>

                    <div class="auth-box<?php echo (isset($errors['agreement']) || isset($errors['authorize'])) ? ' has-error' : ''; ?>">
                        <div class="auth-item<?php echo isset($errors['agreement']) ? ' has-error' : ''; ?>">
                            <label>
                                <input type="checkbox" name="agreement" <?php echo isset($_POST['agreement']) ? 'checked' : ''; ?>>
                                I have read, understood, and agreed to abide by the foster parent agreement. <span class="req">*</span>
                            </label>
                            <?php echo fieldErr($errors, 'agreement'); ?>
                        </div>

                        <div class="auth-item<?php echo isset($errors['authorize']) ? ' has-error' : ''; ?>">
                            <label>
                                <input type="checkbox" name="authorize" <?php echo isset($_POST['authorize']) ? 'checked' : ''; ?>>
                                I hereby authorize FluffSide to review and securely keep the information I provided for the evaluation and processing of my adoption/fostering application, in support of responsible pet placement and animal welfare. <span class="req">*</span>
                            </label>
                            <?php echo fieldErr($errors, 'authorize'); ?>
                        </div>
                    </div>

                    <div class="submit-wrap">
                        <button type="submit" class="btn-submit">SUBMIT!</button>
                    </div>

                </form>
            </div><!-- /.form-card -->
        </div><!-- /.adopt-page -->
    </div><!-- /.container -->

    <!-- ── FOOTER ── -->
    <?php include 'footer.php'; ?>

    <script>
        // ── "Others — please specify" toggle for radio buttons ──────────────────────
        function toggleOther(radio, boxId) {
            // hide all boxes belonging to the same radio group first
            const allInGroup = document.querySelectorAll('input[name="' + radio.name + '"]');
            allInGroup.forEach(function(r) {
                // find the box associated with this radio if any
                const attr = r.getAttribute('onchange');
                if (attr) {
                    const match = attr.match(/toggleOther\(this,'([^']+)'\)/);
                    if (match) {
                        const box = document.getElementById(match[1]);
                        if (box) box.classList.remove('visible');
                    }
                }
            });
            if (radio.value === 'others') {
                const box = document.getElementById(boxId);
                if (box) box.classList.add('visible');
            }
        }

        // ── "Others" toggle for checkboxes ──────────────────────────────────────────
        function toggleOtherCheckbox(checkbox, boxId) {
            const box = document.getElementById(boxId);
            if (!box) return;
            if (checkbox.checked) {
                box.classList.add('visible');
            } else {
                box.classList.remove('visible');
            }
        }

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