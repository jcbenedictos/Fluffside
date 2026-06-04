<?php
session_start();

date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

require_once 'pets.inc.php';
$pet_id = strtolower(trim($_GET['pet'] ?? 'scout'));
$selected_pet = $pets[$pet_id] ?? $pets['scout'];

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ─── helpers ───────────────────────────────────────────────────────────────
function wc(string $s): int
{
    return str_word_count(trim($s));
}
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
    $building_type = trim($_POST['building_type'] ?? '');
    if (empty($building_type))                     $errors['building_type'] = 'Building type is required.';
    if ($building_type === 'others' && empty(trim($_POST['building_type_other'] ?? '')))
        $errors['building_type_other'] = 'Please specify your building type.';

    if (empty(trim($_POST['do_rent'] ?? '')))      $errors['do_rent']      = 'Please answer this question.';

    // textarea word-count validation (min 20 words each)
    $textareas = [
        'move_plan'      => 'What happens to your pet if/when you move',
        'care_plan'      => 'Who will be responsible for caring for your pet',
        'financial_plan' => 'Who will be financially responsible',
        'emergency_plan' => 'Who will look after your pet in emergency',
        'hours_alone'    => 'How many hours will your pet be left alone',
        'support_explain' => 'Please explain household support',
    ];
    foreach ($textareas as $field => $label) {
        $val = trim($_POST[$field] ?? '');
        if (empty($val)) {
            $errors[$field] = 'This field is required.';
        } elseif (wc($val) < 20) {
            $need = 20 - wc($val);
            $errors[$field] = 'Please add ' . $need . ' more word' . ($need > 1 ? 's' : '') . ' (minimum 20).';
        }
    }

    $live_with = $_POST['live_with'] ?? [];
    if (empty($live_with))                         $errors['live_with']    = 'Please select at least one option.';
    if (in_array('others', $live_with) && empty(trim($_POST['live_with_other'] ?? '')))
        $errors['live_with_other'] = 'Please specify who else you live with.';

    if (empty(trim($_POST['allergic'] ?? '')))        $errors['allergic']         = 'Please answer this question.';
    if (empty(trim($_POST['household_support'] ?? ''))) $errors['household_support'] = 'Please answer this question.';
    if (empty(trim($_POST['other_pets'] ?? '')))      $errors['other_pets']       = 'Please answer this question.';
    if (empty(trim($_POST['past_pets'] ?? '')))       $errors['past_pets']        = 'Please answer this question.';
    if (empty(trim($_POST['near_road'] ?? '')))       $errors['near_road']        = 'Please answer this question.';

    // ── Photo uploads ──
    $photo_fields = ['1' => 'Front of the house', '2' => 'Living Room', '3' => 'Dining Area', '4' => 'Kitchen', '5' => 'Bedroom'];
    foreach ($photo_fields as $num => $plabel) {
        if (empty($_FILES['photo_' . $num]['name'])) {
            $errors['photo_' . $num] = 'Photo of ' . $plabel . ' is required.';
        }
    }
    if (empty($_FILES['valid_id']['name']))         $errors['valid_id']     = 'A valid ID is required.';

    // ── Interview ──
    if (empty(trim($_POST['interview_date'] ?? ''))) $errors['interview_date'] = 'Preferred interview date is required.';
    if (empty(trim($_POST['interview_time'] ?? ''))) $errors['interview_time'] = 'Preferred interview time is required.';
    if (empty(trim($_POST['same_month'] ?? '')))     $errors['same_month']     = 'Please answer this question.';
    if (!isset($_POST['authorize']))                 $errors['authorize']      = 'You must authorize FluffSide to proceed.';

    if (empty($errors)) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Application — FluffSide</title>
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

        /* word counter */
        .word-counter {
            font-size: 11.5px;
            margin-top: 5px;
            font-weight: 600;
            transition: color .25s;
        }

        .word-counter.ok {
            color: #27ae60;
        }

        .word-counter.warn {
            color: #e67e22;
        }

        .word-counter.over {
            color: #e05252;
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

        /* interview note */
        .interview-note {
            font-size: 11px;
            color: #bbb;
            margin-top: 3px;
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

        .auth-box label {
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

        .alert-success {
            background: #edfaf3;
            border: 1.5px solid #6dd5a0;
            color: #1e7a4a;
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
                <p><a href="#">Adoption</a> means giving a rescued animal a second chance at life by welcoming them into a safe, loving, and responsible home.</p>
                <p>All of our adoptable cats and dogs are <strong>already spayed/neutered (kapan)</strong> and vaccinated before being placed with their future families. Many of our rescued pets came from difficult situations such as abandonment, neglect, or life on the streets, which is why we carefully ensure they are matched with caring adopters and foster families.</p>
            </div>
            <hr class="divider">

            <div class="form-card">
                <div class="form-title">ADOPTION APPLICATION</div>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <strong><i class="fas fa-check-circle"></i> Application submitted!</strong><br>
                        Thank you, <strong><?php echo old('first_name') . ' ' . old('last_name'); ?></strong>. Our team will review your application and reach out within 3–5 business days.
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

                <form method="POST" action="adoptform.php?pet=<?= h($pet_id) ?>" enctype="multipart/form-data" novalidate>

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

                    <!-- Q1 + Q2 -->
                    <div class="form-row">
                        <div class="q-item">
                            <span class="q-label">What type of building do you live in? <span class="req">*</span></span>
                            <div class="radio-group" style="flex-direction:column;gap:4px;">
                                <?php foreach (['House', 'Apartment', 'Condo'] as $b): ?>
                                    <label><input type="radio" name="building_type" value="<?php echo $b; ?>" <?php echo radioChecked('building_type', $b); ?> onchange="toggleOther(this,'building_other_box')"> <?php echo $b; ?></label>
                                <?php endforeach; ?>
                                <label><input type="radio" name="building_type" value="others" <?php echo radioChecked('building_type', 'others'); ?> onchange="toggleOther(this,'building_other_box')"> Others</label>
                            </div>
                            <div class="others-input<?php echo (($_POST['building_type'] ?? '') === 'others') ? ' visible' : ''; ?>" id="building_other_box">
                                <input type="text" name="building_type_other" placeholder="Please specify..." value="<?php echo old('building_type_other'); ?>">
                            </div>
                            <?php echo fieldErr($errors, 'building_type');
                            echo fieldErr($errors, 'building_type_other'); ?>
                        </div>
                        <div class="q-item">
                            <span class="q-label">Do you rent? <span class="req">*</span></span>
                            <div class="radio-group">
                                <label><input type="radio" name="do_rent" value="Yes" <?php echo radioChecked('do_rent', 'Yes'); ?>> Yes</label>
                                <label><input type="radio" name="do_rent" value="No" <?php echo radioChecked('do_rent', 'No');  ?>> No</label>
                            </div>
                            <?php echo fieldErr($errors, 'do_rent'); ?>
                        </div>
                    </div>

                    <?php
                    // Build textarea questions array for looping (L2: Looping + Building Functions)
                    $textarea_questions = [
                        'move_plan'      => 'What happens to your pet if/when you move?',
                        'care_plan'      => 'Who will be responsible for feeding, grooming, and generally caring for your pet?',
                        'financial_plan' => 'Who will be financially responsible for your pet\'s needs (i.e. food, vet, bills, etc.)?',
                        'emergency_plan' => 'Who will look after your pet if you go on vacation or in case of emergency?',
                        'hours_alone'    => 'How many hours in an average workday will your pet be left alone?',
                    ];
                    foreach ($textarea_questions as $field => $question):
                    ?>
                        <div class="q-item">
                            <span class="q-label"><?php echo $question; ?> <span class="req">*</span></span>
                            <div class="form-group<?php echo hasErr($errors, $field); ?>">
                                <textarea name="<?php echo $field; ?>"
                                    id="<?php echo $field; ?>"
                                    placeholder="I will plan to..."
                                    data-minwords="20"
                                    onblur="countWords(this)"
                                    oninput="countWords(this)"><?php echo old($field); ?></textarea>
                                <div class="word-counter" id="wc_<?php echo $field; ?>"></div>
                                <?php echo fieldErr($errors, $field); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Q: Who do you live with -->
                    <div class="q-item">
                        <span class="q-label">Who do you live with? <span class="req">*</span></span>
                        <div class="check-group">
                            <?php
                            $live_options = ['Living alone', 'Parents', 'Children below 18', 'Spouse', 'Relatives', 'Children over 18', 'Roommate(s)', 'Friends'];
                            foreach ($live_options as $lo): ?>
                                <label><input type="checkbox" name="live_with[]" value="<?php echo $lo; ?>" <?php echo chkChecked('live_with', $lo); ?>> <?php echo $lo; ?></label>
                            <?php endforeach; ?>
                            <label><input type="checkbox" name="live_with[]" value="others" <?php echo chkChecked('live_with', 'others'); ?> onchange="toggleOtherCheckbox(this,'live_other_box')"> Others</label>
                        </div>
                        <div class="others-input<?php echo in_array('others', oldArr('live_with')) ? ' visible' : ''; ?>" id="live_other_box" style="margin-top:6px;">
                            <input type="text" name="live_with_other" placeholder="Please specify..." value="<?php echo old('live_with_other'); ?>">
                        </div>
                        <?php echo fieldErr($errors, 'live_with');
                        echo fieldErr($errors, 'live_with_other'); ?>
                    </div>

                    <!-- Allergic -->
                    <div class="q-item">
                        <span class="q-label">Are any members of your household allergic to animals? <span class="req">*</span></span>
                        <div class="radio-group">
                            <label><input type="radio" name="allergic" value="Yes" <?php echo radioChecked('allergic', 'Yes'); ?>> Yes</label>
                            <label><input type="radio" name="allergic" value="No" <?php echo radioChecked('allergic', 'No');  ?>> No</label>
                        </div>
                        <?php echo fieldErr($errors, 'allergic'); ?>
                    </div>

                    <!-- Household support + explain -->
                    <div class="q-item">
                        <span class="q-label">Does everyone in the household support your decision to adopt a pet? <span class="req">*</span></span>
                        <div class="radio-group" style="margin-bottom:8px;">
                            <label><input type="radio" name="household_support" value="Yes" <?php echo radioChecked('household_support', 'Yes'); ?>> Yes</label>
                            <label><input type="radio" name="household_support" value="No" <?php echo radioChecked('household_support', 'No');  ?>> No</label>
                        </div>
                        <?php echo fieldErr($errors, 'household_support'); ?>
                        <div class="form-group<?php echo hasErr($errors, 'support_explain'); ?>" style="margin-top:6px;">
                            <label>Please explain <span class="req">*</span></label>
                            <textarea name="support_explain"
                                id="support_explain"
                                placeholder="I will plan to..."
                                data-minwords="20"
                                onblur="countWords(this)"
                                oninput="countWords(this)"><?php echo old('support_explain'); ?></textarea>
                            <div class="word-counter" id="wc_support_explain"></div>
                            <?php echo fieldErr($errors, 'support_explain'); ?>
                        </div>
                    </div>

                    <!-- Other pets / past pets / near road -->
                    <?php
                    $yn_questions = [
                        'other_pets' => 'Do you have other pets?',
                        'past_pets'  => 'Have you had pets in the past?',
                        'near_road'  => 'Is your house along the road/highway?',
                    ];
                    foreach ($yn_questions as $field => $question):
                    ?>
                        <div class="q-item">
                            <span class="q-label"><?php echo $question; ?> <span class="req">*</span></span>
                            <div class="radio-group">
                                <label><input type="radio" name="<?php echo $field; ?>" value="Yes" <?php echo radioChecked($field, 'Yes'); ?>> Yes</label>
                                <label><input type="radio" name="<?php echo $field; ?>" value="No" <?php echo radioChecked($field, 'No');  ?>> No</label>
                            </div>
                            <?php echo fieldErr($errors, $field); ?>
                        </div>
                    <?php endforeach; ?>

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

                    <!-- ══ INTERVIEWS ══ -->
                    <div class="form-section-title" style="margin-top:2.5rem;">
                        Interviews and Visitations set up <span class="required-note">* indicates required fields</span>
                    </div>
                    <p class="section-sub">Minors must be accompanied by a parent or guardian.</p>

                    <div class="form-row">
                        <div class="form-group<?php echo hasErr($errors, 'interview_date'); ?>">
                            <label>Preferred date for Zoom Interview <span class="req">*</span></label>
                            <input type="date" name="interview_date" value="<?php echo old('interview_date'); ?>">
                            <span class="interview-note">We can't guarantee the availability of your requested date.</span>
                            <?php echo fieldErr($errors, 'interview_date'); ?>
                        </div>
                        <div class="form-group<?php echo hasErr($errors, 'interview_time'); ?>">
                            <label>Preferred time for Zoom Interview <span class="req">*</span></label>
                            <input type="time" name="interview_time" value="<?php echo old('interview_time'); ?>">
                            <span class="interview-note">We can't guarantee the availability of your requested time.</span>
                            <?php echo fieldErr($errors, 'interview_time'); ?>
                        </div>
                    </div>

                    <div class="q-item" style="margin-top:.5rem;">
                        <span class="q-label">Will you be able to visit the shelter for the meet-and-greet on the same month if accepted?</span>
                        <div class="radio-group">
                            <label><input type="radio" name="same_month" value="Yes" <?php echo radioChecked('same_month', 'Yes'); ?>> Yes</label>
                            <label><input type="radio" name="same_month" value="No" <?php echo radioChecked('same_month', 'No');  ?>> No</label>
                        </div>
                        <?php echo fieldErr($errors, 'same_month'); ?>
                    </div>

                    <!-- ══ AUTHORIZATION ══ -->
                    <div class="auth-box<?php echo isset($errors['authorize']) ? ' has-error' : ''; ?>">
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

        // ── Live word counter ────────────────────────────────────────────────────────
        function countWords(textarea) {
            const minWords = parseInt(textarea.getAttribute('data-minwords')) || 20;
            const text = textarea.value.trim();
            const count = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
            const counter = document.getElementById('wc_' + textarea.id);
            if (!counter) return;

            if (text === '') {
                counter.textContent = '';
                counter.className = 'word-counter';
                return;
            }

            const missing = minWords - count;
            if (count >= minWords) {
                counter.textContent = '✓ ' + count + ' words — looks good!';
                counter.className = 'word-counter ok';
            } else {
                counter.textContent = count + ' word' + (count !== 1 ? 's' : '') + ' — ' + missing + ' more needed';
                counter.className = 'word-counter warn';
            }
        }

        // Run counters on page load to restore state after validation error
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('textarea[data-minwords]').forEach(function(ta) {
                if (ta.value.trim() !== '') countWords(ta);
            });
        });
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