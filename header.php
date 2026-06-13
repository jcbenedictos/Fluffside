<<<<<<< HEAD
<?php
$current = basename($_SERVER['PHP_SELF']);
$residents_pages = ['residents.php', 'pet.php', 'adoptform.php', 'fosterform.php'];
?>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-orange: #EF8E35;
        --primary-hover: #D67A26;
        --bg-light: #FDFBF5;
        --text-dark: #5A483E;
        --text-light: #8E8279;
        --accent-yellow: #F6D884;
        --accent-green-light: #E1E8B8;
        --bg-yellow-light: #FCEABB;
        --bg-pink-light: #F8E1DF;
        --bg-blue-light: #DEEBF7;
        --btn-green: #9BB374;
        --footer-green: #B8C7A1;
        --white: #FFFFFF;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Nunito', sans-serif;
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-dark);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: -50px 0;
        margin-bottom: 10px;
        position: relative;
        overflow: hidden;
    }

    .logo-img {
        height: 250px;
        width: auto;
        mix-blend-mode: multiply;
        margin-top: -80px;
        margin-bottom: -80px;
    }

    nav ul {
        display: flex;
        list-style: none;
        gap: 30px;
        align-items: center;
    }

    nav a {
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 800;
        font-size: 13px;
        text-transform: uppercase;
    }

    nav a:hover {
        color: var(--primary-orange);
    }

    nav a.active {
        border-bottom: 2px solid var(--primary-orange);
        color: var(--primary-orange);
        padding-bottom: 4px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .cart-icon {
        color: var(--primary-orange);
        font-size: 20px;
    }

    .btn {
        background-color: var(--primary-orange);
        color: var(--white);
        padding: 12px 28px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 800;
        font-size: 14px;
        border: 2px solid var(--primary-orange);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-outline {
        background-color: transparent;
        color: var(--text-dark);
    }

    .btn-square {
        border-radius: 8px;
    }
</style>

<header>
    <a href="index.php"><img src="Assets/Fluffside.png" alt="Logo" class="logo-img" onerror="this.outerHTML='<h1 style=&quot;color:#EF8E35&quot;>FluffSide</h1>'"></a>
    <nav>
        <ul>
            <li><a href="index.php" <?= $current === 'index.php'                        ? 'class="active"' : '' ?>>HOME</a></li>
            <li><a href="residents.php" <?= in_array($current, $residents_pages)             ? 'class="active"' : '' ?>>RESIDENTS</a></li>
            <li><a href="supplies.php" <?= $current === 'supplies.php'                     ? 'class="active"' : '' ?>>SUPPLIES</a></li>
            <li><a href="dashboard.php" <?= $current === 'dashboard.php'                    ? 'class="active"' : '' ?>>DASHBOARD</a></li>
            <li><a href="about.php" <?= $current === 'about.php'                        ? 'class="active"' : '' ?>>ABOUT US</a></li>
            <li><a href="help.php" <?= $current === 'help.php'                         ? 'class="active"' : '' ?>>HELP</a></li>
        </ul>
    </nav>
    <div class="header-actions">
        <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="profile.php" class="btn"><i class="fas fa-user"></i> PROFILE</a>
        <?php else: ?>
            <a href="login.php" class="btn">LOG IN/SIGN UP</a>
        <?php endif; ?>
    </div>
=======
<?php
$current = basename($_SERVER['PHP_SELF']);
$residents_pages = ['residents.php', 'pet.php', 'adoptform.php', 'fosterform.php'];
?>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-orange: #EF8E35;
        --primary-hover: #D67A26;
        --bg-light: #FDFBF5;
        --text-dark: #5A483E;
        --text-light: #8E8279;
        --accent-yellow: #F6D884;
        --accent-green-light: #E1E8B8;
        --bg-yellow-light: #FCEABB;
        --bg-pink-light: #F8E1DF;
        --bg-blue-light: #DEEBF7;
        --btn-green: #9BB374;
        --footer-green: #B8C7A1;
        --white: #FFFFFF;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Nunito', sans-serif;
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-dark);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: -50px 0;
        margin-bottom: 10px;
        position: relative;
        overflow: hidden;
    }

    .logo-img {
        height: 250px;
        width: auto;
        mix-blend-mode: multiply;
        margin-top: -80px;
        margin-bottom: -80px;
    }

    nav ul {
        display: flex;
        list-style: none;
        gap: 30px;
        align-items: center;
    }

    nav a {
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 800;
        font-size: 13px;
        text-transform: uppercase;
    }

    nav a:hover {
        color: var(--primary-orange);
    }

    nav a.active {
        border-bottom: 2px solid var(--primary-orange);
        color: var(--primary-orange);
        padding-bottom: 4px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .cart-icon {
        color: var(--primary-orange);
        font-size: 20px;
    }

    .btn {
        background-color: var(--primary-orange);
        color: var(--white);
        padding: 12px 28px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 800;
        font-size: 14px;
        border: 2px solid var(--primary-orange);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-outline {
        background-color: transparent;
        color: var(--text-dark);
    }

    .btn-square {
        border-radius: 8px;
    }
</style>

<header>
    <a href="index.php"><img src="Assets/Fluffside.png" alt="Logo" class="logo-img" onerror="this.outerHTML='<h1 style=&quot;color:#EF8E35&quot;>FluffSide</h1>'"></a>
    <nav>
        <ul>
            <li><a href="index.php" <?= $current === 'index.php'                        ? 'class="active"' : '' ?>>HOME</a></li>
            <li><a href="residents.php" <?= in_array($current, $residents_pages)             ? 'class="active"' : '' ?>>RESIDENTS</a></li>
            <li><a href="supplies.php" <?= $current === 'supplies.php'                     ? 'class="active"' : '' ?>>SUPPLIES</a></li>
            <li><a href="dashboard.php" <?= $current === 'dashboard.php'                    ? 'class="active"' : '' ?>>DASHBOARD</a></li>
            <li><a href="about.php" <?= $current === 'about.php'                        ? 'class="active"' : '' ?>>ABOUT US</a></li>
            <li><a href="help.php" <?= $current === 'help.php'                         ? 'class="active"' : '' ?>>HELP</a></li>
        </ul>
    </nav>
    <div class="header-actions">
        <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="profile.php" class="btn"><i class="fas fa-user"></i> PROFILE</a>
        <?php else: ?>
            <a href="login.php" class="btn">LOG IN/SIGN UP</a>
        <?php endif; ?>
    </div>
>>>>>>> 5811b114e5fd1e327cc690ba83d3e4517f2253b4
</header>