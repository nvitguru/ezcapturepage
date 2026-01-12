<?php
session_start();
include '../includes/settings.php';
$_SESSION['warning'] = "Oops! Something went wrong with your processing your payment. Please feel free to try again. If this problem persists, you may want to reach out to your financial institution to resolve the problem.";
header('Location: https://'.SYSTEM_URL.'/registration', true, 302);