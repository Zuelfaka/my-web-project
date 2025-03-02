<?php
session_start();

// If the user is logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
} else {
    // If the user is not logged in, check if there's a logout success message in the URL
    if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
        // Show a logout success message
        $logout_message = "تم تسجيل الخروج بنجاح";
    } else {
        $logout_message = null;
    }

    // Redirect the user to the login page
    header("Location: login.php?logout_message=" . urlencode($logout_message));
    exit();
}
?>
