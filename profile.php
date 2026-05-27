<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['first_name'] = !empty(trim($_POST['first_name'] ?? '')) ? trim($_POST['first_name']) : ($_SESSION['first_name'] ?? '');
    $_SESSION['last_name']  = !empty(trim($_POST['last_name'] ?? ''))  ? trim($_POST['last_name'])  : ($_SESSION['last_name'] ?? '');
    $_SESSION['email']      = !empty(trim($_POST['email'] ?? ''))      ? trim($_POST['email'])      : ($_SESSION['email'] ?? '');

    $users_file = 'users.json';
    $users_data = json_decode(file_get_contents($users_file), true);
    $current_email = $_SESSION['email'];

    foreach ($users_data as &$user) {
        if ($user['email'] === $current_email) {
            $user['name']    = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
            $user['address'] = trim($_POST['address'] ?? '');
            $user['phone']   = trim($_POST['phone'] ?? '');
            $user['dob']     = trim($_POST['dob'] ?? '');
            if (!empty(trim($_POST['email'] ?? ''))) {
                $user['email'] = trim($_POST['email']);
            }
            break;
        }
    }
    unset($user);
    file_put_contents($users_file, json_encode($users_data, JSON_PRETTY_PRINT));


    $_SESSION['address']    = trim($_POST['address'] ?? '');
    $_SESSION['phone']      = trim($_POST['phone'] ?? '');
    $_SESSION['dob']        = trim($_POST['dob'] ?? '');
    $_SESSION['role']       = trim($_POST['user_role'] ?? ($_SESSION['role'] ?? ''));

    if (!empty($_POST['cropped_image_base64'])) {
        $imgData = $_POST['cropped_image_base64'];
        list($type, $imgData) = explode(';', $imgData);
        list(, $imgData)      = explode(',', $imgData);
        $imgData = base64_decode($imgData);

        $uploadFolder = './uploads/';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0755, true);
        }

        $newFileName = time() . '_adjusted_profile.jpg';
        $dest_path = $uploadFolder . $newFileName;

        if (file_put_contents($dest_path, $imgData)) {
            $_SESSION['profile_pic'] = $dest_path;
        }
    } else if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];

        $uploadFolder = './uploads/';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0755, true);
        }

        $newFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        $dest_path = $uploadFolder . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $_SESSION['profile_pic'] = $dest_path;
        }
    }

    header("Location: profile.php?update=success");
    exit;
}

$first_name  = $_SESSION['first_name'] ?? '';
$last_name   = $_SESSION['last_name'] ?? '';
$email       = $_SESSION['email'] ?? '';
$address     = $_SESSION['address'] ?? '';
$phone       = $_SESSION['phone'] ?? '';
$dob         = $_SESSION['dob'] ?? '';
$role        = $_SESSION['role'] ?? '';
$profile_pic = $_SESSION['profile_pic'] ?? '';

$display_name = (!empty($first_name) || !empty($last_name)) ? trim("$first_name $last_name") : "NAME";
$highlight = isset($_GET['highlight']) && $_GET['highlight'] === '1';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Profile - FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        :root {
            --fs-orange: #EF8E35;
            --fs-orange-light: #FDF1E6;
            --fs-text-dark: #655345;
            --fs-gray-bg: #E3E1DF;
            --fs-gray-locked: #ECEAE8;
            --fs-card-bg: #F5F1ED;

            --bg-light: #FDFBF5;
            --footer-green: #B8C7A1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--fs-text-dark);
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


        .site-footer {
            background-color: var(--footer-green);
            position: relative;
            padding: 100px 0 50px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer-wave {
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .footer-wave svg {
            display: block;
            width: calc(100% + 1.3px);
            height: 70px;
        }

        .footer-wave .shape-fill {
            fill: var(--footer-green);
        }

        .site-footer h1 {
            font-size: 36px;
            font-weight: 600;
            color: #1A3026;
            letter-spacing: 2px;
            position: relative;
            z-index: 2;
        }

        .profile-dashboard-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
            margin: 20px 0 60px 0;
            font-family: 'Nunito', sans-serif;
            color: var(--fs-text-dark);
        }

        .profile-sidebar {
            background-color: var(--fs-card-bg);
            border: 2px solid var(--fs-orange);
            border-radius: 30px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-height: 520px;
        }

        .avatar-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin-bottom: 25px;
            pointer-events: none;
        }

        .avatar-container {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 4px solid var(--fs-text-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            overflow: hidden;
            position: relative;
        }

        .avatar-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background-color: rgba(101, 83, 69, 0.65);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            opacity: 0;
            transition: opacity 0.2s ease;
            font-weight: 700;
            font-size: 0.85rem;
            text-align: center;
        }

        .role-selector-container {
            margin: 10px 0 30px 0;
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            min-height: 40px;
            width: 100%;
        }

        .role-btn {
            display: none;
            background-color: transparent;
            border: 2px solid var(--fs-orange);
            color: var(--fs-orange);
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .role-btn:hover {
            background-color: var(--fs-orange);
            color: white;
        }

        .role-btn.selected-choice {
            background-color: var(--fs-orange);
            color: white;
            border-color: var(--fs-orange);
            font-weight: 800;
        }

        .role-badge-locked {
            background-color: var(--fs-orange);
            color: white;
            border: 2px solid var(--fs-orange);
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: 0.95rem;
            padding: 8px 24px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(239, 142, 53, 0.2);
        }

        .is-editing .role-btn {
            display: inline-block;
        }

        .is-editing .role-badge-locked {
            display: none;
        }

        .profile-form-container {
            background-color: var(--fs-card-bg);
            border: 2px solid var(--fs-orange);
            border-radius: 30px;
            padding: 40px;
        }

        .profile-form-container h2 {
            margin-top: 0;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 30px;
            row-gap: 20px;
        }

        .full-width-field {
            grid-column: span 2;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 700;
            font-size: 1.05rem;
            padding-left: 5px;
        }

        .form-group input {
            background-color: var(--fs-gray-locked);
            border: none;
            border-radius: 25px;
            padding: 16px 20px;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #92847A;
            outline: none;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .form-group input[type="date"] {
            position: relative;
            font-family: 'Nunito', sans-serif;
        }

        .form-group input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: transparent;
            cursor: pointer;
            color: var(--fs-text-dark);
        }

        .is-editing .form-group input {
            background-color: var(--fs-gray-bg);
            color: var(--fs-text-dark);
        }

        .is-editing .avatar-wrapper {
            pointer-events: auto;
            cursor: pointer;
        }

        .is-editing .avatar-wrapper:hover .avatar-overlay {
            opacity: 1;
        }

        .edit-mode-actions {
            display: none;
            margin-top: 50px;
            justify-content: space-between;
            gap: 30px;
        }

        .view-mode-actions {
            display: flex;
            margin-top: 50px;
            justify-content: flex-end;
        }

        .is-editing .edit-mode-actions {
            display: flex;
        }

        .is-editing .view-mode-actions {
            display: none;
        }

        .btn-edit-profile {
            background-color: var(--fs-orange);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 14px 40px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .btn-edit-profile:hover {
            background-color: #d67a26;
        }

        .btn-discard {
            background-color: var(--fs-orange-light);
            border: 2px solid var(--fs-orange);
            color: var(--fs-text-dark);
            border-radius: 25px;
            padding: 14px 0;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            width: 45%;
            text-align: center;
            text-decoration: none;
        }

        .btn-save {
            background-color: var(--fs-orange);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 14px 0;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            width: 45%;
        }

        .btn-save:hover {
            background-color: #d67a26;
        }

        .profile-sidebar h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
        }

        .sidebar-menu-btn {
            width: 90%;
            background-color: transparent;
            border: 2px solid var(--fs-orange);
            border-radius: 20px;
            padding: 12px 15px;
            color: var(--fs-orange);
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .sidebar-logout-btn {
            position: absolute;
            bottom: 30px;
            width: 80%;
            background-color: var(--fs-orange-light);
            border: 2px solid var(--fs-text-dark);
            border-radius: 25px;
            padding: 10px 15px;
            color: var(--fs-text-dark);
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        @media (max-width: 850px) {
            .profile-dashboard-layout {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width-field {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?>

        <form id="profileForm" action="" method="POST" enctype="multipart/form-data">

            <input type="hidden" id="cropped_image_data" name="cropped_image_base64">
            <input type="hidden" id="user_role_input" name="user_role" value="<?php echo htmlspecialchars($role); ?>">

            <div class="profile-dashboard-layout" id="dashboardWrapper">

                <aside class="profile-sidebar">
                    <div class="avatar-wrapper" onclick="triggerFileInput()">
                        <div class="avatar-container">
                            <?php if (!empty($profile_pic)): ?>
                                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Photo" id="avatarImage">
                            <?php else: ?>
                                <i class="fas fa-user fa-5x" id="avatarIcon" style="color: var(--fs-text-dark);"></i>
                                <img src="" alt="Profile Photo" id="avatarImage" style="display: none;">
                            <?php endif; ?>
                        </div>
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                            <span>Change Photo</span>
                        </div>
                        <input type="file" id="profile_pic_input" name="profile_pic" accept="image/*" style="display: none;" onchange="previewImage(this)">
                    </div>

                    <h2><?php echo htmlspecialchars($display_name); ?></h2>

                    <div class="role-selector-container">
                        <?php if (!empty($role)): ?>
                            <div class="role-badge-locked" id="staticRoleBadge"><?php echo htmlspecialchars($role); ?></div>
                        <?php endif; ?>

                        <button type="button" class="role-btn <?php echo ($role === 'Adopter') ? 'selected-choice' : ''; ?>" id="roleBtnAdopter" onclick="selectProfileRole('Adopter')">Adopter</button>
                        <button type="button" class="role-btn <?php echo ($role === 'Foster') ? 'selected-choice' : ''; ?>" id="roleBtnFoster" onclick="selectProfileRole('Foster')">Foster</button>
                    </div>

                    <button type="button" class="sidebar-menu-btn">
                        <i class="fas fa-user-circle"></i> Personal Information
                    </button>

                    <a href="logout.php" class="sidebar-logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </a>
                </aside>

                <main class="profile-form-container">
                    <h2>Personal Information</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" placeholder="First Name" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" placeholder="Last Name" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" readonly>
                        </div>

                        <div class="form-group full-width-field">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                        </div>

                        <div class="form-group full-width-field">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" placeholder="Address" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" readonly <?php if($highlight && empty($address)) echo 'style="border:2px solid #EF8E35;background:#FDF1E6;"'; ?>>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" placeholder="Phone Number" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>" readonly>
                        </div>
                    </div>

                    <div class="view-mode-actions" id="viewActions">
                        <button type="button" class="btn-edit-profile" onclick="enableEditMode()">Edit Profile</button>
                    </div>

                    <div class="edit-mode-actions" id="editActions">
                        <a href="profile.php" class="btn-discard">Discard Changes</a>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </main>

            </div>
        </form>
    </div>

    <div id="cropperModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:var(--fs-card-bg); border:2px solid var(--fs-orange); border-radius:30px; padding:30px; max-width:500px; width:90%; text-align:center; font-family:'Nunito', sans-serif;">
            <h3 style="margin-top:0; font-weight:800; color:var(--fs-text-dark); font-size:1.4rem;">Adjust Your Profile Photo</h3>

            <div style="max-height:350px; overflow:hidden; margin-bottom:20px; border-radius:15px; background:#fff;">
                <img id="imageToCrop" style="max-width:100%;">
            </div>

            <button type="button" id="rotateBtn" style="background:transparent; border:2px solid var(--fs-orange); color:var(--fs-orange); border-radius:20px; padding:8px 15px; font-weight:700; cursor:pointer; margin-bottom:20px; font-family:'Nunito';">
                <i class="fas fa-redo"></i> Rotate 90°
            </button>

            <div style="display:flex; justify-content:space-between; gap:20px;">
                <button type="button" onclick="closeCropperModal()" style="background:var(--fs-orange-light); border:2px solid var(--fs-orange); color:var(--fs-text-dark); border-radius:25px; padding:12px 0; font-weight:800; width:45%; cursor:pointer; font-family:'Nunito';">Cancel</button>
                <button type="button" id="cropSaveBtn" style="background:var(--fs-orange); border:none; color:white; border-radius:25px; padding:12px 0; font-weight:800; width:45%; cursor:pointer; font-family:'Nunito';">Apply Photo</button>
            </div>
        </div>
    </div>


    <script>
        let cropper;
        const cropperModal = document.getElementById('cropperModal');
        const imageToCrop = document.getElementById('imageToCrop');

        function enableEditMode() {
            document.getElementById('dashboardWrapper').classList.add('is-editing');
            const inputs = document.querySelectorAll('.form-grid input');
            inputs.forEach(input => {
                input.removeAttribute('readonly');
            });
        }

        function selectProfileRole(selectedRole) {
            document.getElementById('user_role_input').value = selectedRole;

            const btnAdopter = document.getElementById('roleBtnAdopter');
            const btnFoster = document.getElementById('roleBtnFoster');

            if (selectedRole === 'Adopter') {
                btnAdopter.classList.add('selected-choice');
                btnFoster.classList.remove('selected-choice');
            } else if (selectedRole === 'Foster') {
                btnFoster.classList.add('selected-choice');
                btnAdopter.classList.remove('selected-choice');
            }
        }

        function triggerFileInput() {
            document.getElementById('profile_pic_input').click();
        }

        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageToCrop.src = e.target.result;
                    cropperModal.style.display = 'flex';

                    if (cropper) {
                        cropper.destroy();
                    }

                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        background: false
                    });
                }
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('rotateBtn').addEventListener('click', function() {
            if (cropper) {
                cropper.rotate(90);
            }
        });

        document.getElementById('cropSaveBtn').addEventListener('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300
                });

                const croppedImageDataURL = canvas.toDataURL('image/jpeg');

                const imgElement = document.getElementById('avatarImage');
                const iconElement = document.getElementById('avatarIcon');

                imgElement.src = croppedImageDataURL;
                imgElement.style.display = 'block';
                if (iconElement) iconElement.style.display = 'none';

                document.getElementById('cropped_image_data').value = croppedImageDataURL;

                closeCropperModal();
            }
        });

        function closeCropperModal() {
            cropperModal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
            }
        }

        <?php if($highlight): ?>
        window.addEventListener('load', function() {
            document.getElementById('dashboardWrapper').classList.add('is-editing');
            const inputs = document.querySelectorAll('.form-grid input');
            inputs.forEach(input => input.removeAttribute('readonly'));

            const addressField = document.getElementById('address');
            addressField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            addressField.focus();
            addressField.style.border = '2px solid #EF8E35';
            addressField.style.background = '#FDF1E6';
        });
        <?php endif; ?>
    </script>
</body>

</html>