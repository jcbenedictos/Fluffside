<?php
// admin/header.inc.php — shared admin navigation bar
$admin_page = basename($_SERVER['PHP_SELF']);
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-orange: #EF8E35;
    --primary-hover:  #D67A26;
    --bg-light:       #FDFBF5;
    --text-dark:      #5A483E;
    --text-light:     #8E8279;
    --accent-yellow:  #F6D884;
    --accent-green:   #E1E8B8;
    --white:          #FFFFFF;
    --admin-red:      #C0392B;
    --admin-red-light:#FADBD8;
    --border:         #EAE3D9;
    --status-green:   #9BB374;
}
* { margin:0; padding:0; box-sizing:border-box; font-family:'Nunito',sans-serif; }
body { background:#FDFBF5; color:var(--text-dark); min-height:100vh; display:flex; flex-direction:column; }

/* ── Admin top bar ── */
.admin-topbar {
    background: var(--text-dark);
    color: var(--white);
    padding: 0 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 48px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.admin-topbar .admin-badge {
    background: var(--primary-orange);
    color: var(--white);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    margin-right: 10px;
}
.admin-topbar .topbar-left { display:flex; align-items:center; gap:10px; }
.admin-topbar .topbar-right { display:flex; align-items:center; gap:20px; }
.admin-topbar a { color:rgba(255,255,255,0.7); text-decoration:none; font-size:12px; font-weight:700; transition:color 0.2s; }
.admin-topbar a:hover { color:var(--white); }
.admin-topbar .sep { color:rgba(255,255,255,0.3); }

/* ── Main header with nav ── */
header {
    display:flex; justify-content:space-between; align-items:center;
    padding: 0 5%; border-bottom: 2px solid var(--border);
    background: var(--white);
}
.logo-img { height:200px; width:auto; mix-blend-mode:multiply; margin-top:-60px; margin-bottom:-60px; }

nav ul { display:flex; list-style:none; gap:6px; align-items:center; }
nav a {
    text-decoration:none; color:var(--text-dark); font-weight:800;
    font-size:12px; text-transform:uppercase; padding:8px 14px;
    border-radius:8px; transition:all 0.2s; display:flex; align-items:center; gap:7px;
}
nav a:hover { background:var(--bg-light); color:var(--primary-orange); }
nav a.active { background:var(--primary-orange); color:var(--white); }
nav a.active:hover { background:var(--primary-hover); }

.header-actions { display:flex; align-items:center; gap:14px; }
.btn-logout {
    background:transparent; border:2px solid var(--border); color:var(--text-dark);
    padding:9px 18px; border-radius:8px; font-size:12px; font-weight:800;
    cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:7px;
    transition:all 0.2s;
}
.btn-logout:hover { border-color:var(--admin-red); color:var(--admin-red); }
.admin-avatar {
    width:36px; height:36px; border-radius:50%;
    background:var(--primary-orange); color:var(--white);
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:900;
}
</style>

<!-- Top admin banner -->
<div class="admin-topbar">
    <div class="topbar-left">
        <span class="admin-badge"><i class="fas fa-shield-alt"></i> Admin Panel</span>
        <span>You are in the admin dashboard</span>
    </div>
    <div class="topbar-right">
        <a href="../index.php"><i class="fas fa-eye"></i> View User Site</a>
        <span class="sep">|</span>
        <span><?= h(admin_name()) ?></span>
    </div>
</div>

<!-- Main header -->
<header>
    <a href="index.php"><img src="../Assets/Fluffside.png" alt="FluffSide" class="logo-img"
        onerror="this.outerHTML='<h1 style=&quot;color:#EF8E35;font-size:24px;font-weight:900&quot;>FluffSide</h1>'"></a>
    <nav>
        <ul>
            <li><a href="index.php" <?= $admin_page==='index.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-tachometer-alt"></i> Overview</a></li>
            <li><a href="residents.php" <?= $admin_page==='residents.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-paw"></i> Residents</a></li>
            <li><a href="supplies.php" <?= $admin_page==='supplies.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-box-open"></i> Supplies</a></li>
            <li><a href="applications.php" <?= $admin_page==='applications.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-envelope-open-text"></i> Applications</a></li>
            <li><a href="orders.php" <?= $admin_page==='orders.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-shopping-bag"></i> Orders</a></li>
        </ul>
    </nav>
    <div class="header-actions">
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['first_name'] ?? 'A', 0, 1)) ?></div>
        <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Log Out</a>
    </div>
</header>
