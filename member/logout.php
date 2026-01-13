<?php
/**
 * logout.php
 *
 * Responsibilities:
 * - Terminate the current user session
 * - Redirect the user back to the login page
 */

session_start();

/**
 * Destroy all session data.
 * This invalidates the current authentication state.
 */
session_destroy();

/**
 * Redirect to login page after logout.
 */
header('location: index');
exit;
