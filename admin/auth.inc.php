<?php
// admin/auth.inc.php
// Include at the top of every admin page.
// Checks that the logged-in user has role = 'Admin'.
// When moving to DB: the role already comes from tbl_users.role via login.php — no changes needed here.

session_start();

function require_admin(): void {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ../login.php?msg=login_required');
        exit;
    }
    if (($_SESSION['role'] ?? '') !== 'Admin') {
        // Not admin — kick to user side
        header('Location: ../index.php');
        exit;
    }
}

function admin_name(): string {
    return trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''));
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
