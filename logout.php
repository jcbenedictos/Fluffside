<?php
session_start();
session_destroy();
$reason = isset($_GET['reason']) && $_GET['reason'] === 'inactive' ? 'inactive' : '';
header("Location: login.php" . ($reason ? "?msg=inactive" : ""));
exit;
?>
