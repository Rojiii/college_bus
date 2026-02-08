<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page (absolute path)
header("Location: /college_bus/php/auth/login.php");
exit;
