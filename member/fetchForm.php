<?php
/**
 * fetchForm.php
 *
 * Purpose:
 * - AJAX/JSON endpoint used by the UI to fetch a single form record by formID.
 *
 */

require 'includes/session.php';

/**
 * Force JSON output to prevent browsers from misinterpreting the response.
 */
header('Content-Type: application/json');

/**
 * Validate and normalize the incoming formID.
 * - FILTER_VALIDATE_INT ensures numeric input and prevents "1 OR 1=1" style attempts.
 */
$formID = filter_input(INPUT_GET, 'formID', FILTER_VALIDATE_INT);

if (!$formID) {
    echo json_encode([
        'error' => 'Invalid or missing formID'
    ]);
    exit;
}

/**
 * Open database connection via the app’s Database wrapper.
 * (In this codebase, $pdo is a Database object, not a raw PDO instance.)
 */
$conn = $pdo->open();

try {
    /**
     * Use a prepared statement to safely query by ID.
     * LIMIT 1 is a small optimization and communicates intent.
     */
    $stmt = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID LIMIT 1");
    $stmt->execute(['formID' => $formID]);

    $form = $stmt->fetch(PDO::FETCH_ASSOC);

    /**
     * If no record is found, return a clean error response.
     * (Keeps frontend logic simple and predictable.)
     */
    if (!$form) {
        echo json_encode([
            'error' => 'Form not found'
        ]);
        exit;
    }

    /**
     * Successful response: return the record as JSON.
     */
    echo json_encode($form);

} catch (PDOException $e) {
    /**
     * Security: do not leak raw database errors to the client in production.
     * If you have server-side logging, log $e->getMessage() there instead.
     */
    echo json_encode([
        'error' => 'Database error'
    ]);
} finally {
    /**
     * Ensure DB connection is always closed.
     */
    $pdo->close();
}
