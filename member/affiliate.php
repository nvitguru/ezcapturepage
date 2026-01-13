<?php
/**
 * Manage Affiliate
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Display the member's affiliate code + registration link with copy buttons
 * - Show a list of referred members (where users.affiliate == current userID)
 * - Provide affiliate program explanation content (right column)
 *
 * Notes:
 * - Relies on variables hydrated by included files (ex: $pdo, $user, SYSTEM_URL, SYSTEM_NAME).
 */

/** @var Database $pdo */
session_start();

// -----------------------------------------------------------------------------
// Page metadata (used by layout/template includes)
// -----------------------------------------------------------------------------
$pageTitle = "Manage Affiliate";
$tab = "affiliate";

// -----------------------------------------------------------------------------
// Bootstrap / shared includes
// -----------------------------------------------------------------------------
include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Auth gate: affiliate management requires an authenticated session
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
$conn = $pdo->open();

// -----------------------------------------------------------------------------
// Small HTML escape helper (consistent output encoding)
// -----------------------------------------------------------------------------
$e = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

// Normalize IDs used in output/queries
$currentUserId = isset($user['userID']) ? (int)$user['userID'] : 0;

// Build registration link exactly like the original pattern
$registrationUrl = "https://" . SYSTEM_URL . "/register/" . $currentUserId;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $e($pageTitle); ?> | <?php echo $e(SYSTEM_NAME); ?></title>

    <?php include_once 'includes/css-fonts.php'; ?>
    <?php include_once 'includes/css-plugins.php'; ?>
    <?php include_once 'includes/css.php'; ?>
</head>

<body>
<?php include_once 'includes/loader.php'; ?>

<!-- page-wrapper Start-->
<div class="page-wrapper" id="pageWrapper">

    <!-- Page Header Start-->
    <div class="page-header">
        <?php include_once 'includes/member-header.php'; ?>
    </div>
    <!-- Page Header Ends-->

    <!-- Page Body Start-->
    <div class="page-body-wrapper">

        <!-- Page Sidebar Start-->
        <?php include_once 'includes/member-sidebar.php'; ?>
        <!-- Page Sidebar Ends-->

        <div class="page-body">
            <div class="container-fluid"></div>

            <!-- Container-fluid starts-->
            <div class="container-fluid dashboard_default">
                <div class="row widget-grid">

                    <!-- -----------------------------------------------------------------
                         Page title + breadcrumbs
                         ----------------------------------------------------------------- -->
                    <div class="col-12">
                        <div class="page-title mt-2">
                            <div class="row">
                                <div class="col-sm-6 ps-0">
                                    <h3><?php echo $e($pageTitle); ?></h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active"><?php echo $e($pageTitle); ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- -----------------------------------------------------------------
                         Server-side alerts/flash messages
                         ----------------------------------------------------------------- -->
                    <?php include_once 'includes/alerts.php'; ?>

                    <!-- -----------------------------------------------------------------
                         Left column: affiliate codes + referred members list
                         ----------------------------------------------------------------- -->
                    <div class="col-lg-7">
                        <div class="row">

                            <!-- Affiliate code + registration link card -->
                            <div class="col-8 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-12">
                                                <p class="mb-1">Affiliate Code</p>
                                            </div>

                                            <div class="input-group input-group-lg col-12 mb-3">
                                                <input
                                                        class="form-control"
                                                        id="affiliateLink"
                                                        value="<?php echo $e($currentUserId); ?>"
                                                        readonly
                                                >
                                                <button
                                                        class="btn btn-primary js-copy"
                                                        type="button"
                                                        data-copy-target="affiliateLink"
                                                        data-copy-alert="Your Affiliate ID has been copied"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Copy Link"
                                                ><i class="fa fa-copy"></i> Copy</button>
                                            </div>

                                            <div class="col-12">
                                                <p class="mb-1">Affiliate Registration Link</p>
                                            </div>

                                            <div class="input-group input-group-lg col-12">
                                                <input
                                                        class="form-control"
                                                        id="registrationLink"
                                                        value="<?php echo $e($registrationUrl); ?>"
                                                        readonly
                                                >
                                                <button
                                                        class="btn btn-primary js-copy"
                                                        type="button"
                                                        data-copy-target="registrationLink"
                                                        data-copy-alert="Your Affiliate Registration Link has been copied"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Copy Link"
                                                ><i class="fa fa-copy"></i> Copy</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Referred members table -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive theme-scrollbar">
                                            <table class="display" id="basic-1">
                                                <thead>
                                                <tr>
                                                    <th class="d-none">id</th>
                                                    <th>Member Name</th>
                                                    <th>Member Email</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                try {
                                                    $affiliateStmt = $conn->prepare("
                                                        SELECT *
                                                        FROM `users`
                                                        WHERE `affiliate` = :userID
                                                        ORDER BY `id` DESC
                                                    ");
                                                    $affiliateStmt->execute(['userID' => $currentUserId]);

                                                    $referrals = $affiliateStmt->fetchAll();

                                                    foreach ($referrals as $row) {
                                                        $id = isset($row['id']) ? (int)$row['id'] : 0;

                                                        // Normalize boolean-ish values
                                                        $isActive = !empty($row['active']);

                                                        $affiliateStatusHtml = $isActive
                                                            ? "<span class='badge rounded-pill badge-success'>ACTIVE</span>"
                                                            : "<span class='badge rounded-pill badge-warning'>INACTIVE</span>";

                                                        $fullName = trim((string)($row['fname'] ?? '') . ' ' . (string)($row['lname'] ?? ''));

                                                        echo "
                                                            <tr>
                                                                <td class='d-none'>" . $e($id) . "</td>
                                                                <td><h5>" . $e($fullName) . "</h5></td>
                                                                <td><h5>" . $e($row['email'] ?? '') . "</h5></td>
                                                                <td><h5>{$affiliateStatusHtml}</h5></td>
                                                            </tr>
                                                        ";
                                                    }
                                                } catch (PDOException $ex) {
                                                    error_log("Affiliate referrals load error (userID={$currentUserId}): " . $ex->getMessage());
                                                    echo "<tr><td colspan='4'><div class='alert alert-danger mb-0'>Unable to load referrals right now.</div></td></tr>";
                                                }

                                                $pdo->close();
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- -----------------------------------------------------------------
                         Right column: program explanation / marketing content
                         ----------------------------------------------------------------- -->
                    <div class="col-5" style="border-left: 1px solid #ccc;">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>GET YOUR MEMBERSHIP FREE!</h1>
                                <hr>
                            </div>
                            <div class="col-12">
                                <h2>How Does It Work?</h2>
                                <p class="mb-1" style="font-size: 16px;">
                                    By simply being a member of <?php echo $e(SYSTEM_NAME); ?>, you are automatically registered for our Affiliate Program and receive your unique Affiliate ID.
                                </p>
                                <ul class="mb-3">
                                    <li style="font-size: 16px;"><strong>Spread the Word</strong>: Share your Affiliate ID with friends, family, colleagues, or anyone who could benefit from our amazing SaaS platform. You can share it via social media, email, your blog, or any other creative way you can think of!</li>
                                    <li style="font-size: 16px;"><strong>Get New Members to Register</strong>: When someone registers using your Affiliate ID and stays active, you start earning towards your free membership.</li>
                                    <li style="font-size: 16px;"><strong>Enjoy Free Membership</strong>: The more active members you bring in, the more you save! Once you've reached the required number of active referrals, your membership fee will be waived.</li>
                                </ul>

                                <h2>Why Should You Participate?</h2>
                                <ul class="mb-3">
                                    <li style="font-size: 16px;"><strong>Save Money</strong>: Who doesn't love free stuff? By bringing in new members, you can enjoy our top-notch services without spending a dime.</li>
                                    <li style="font-size: 16px;"><strong>Help Friends and Family</strong>: Introduce your network to the incredible benefits of our platform, helping them achieve their goals while you reap the rewards.</li>
                                    <li style="font-size: 16px;"><strong>Boost Your Influence</strong>: Gain recognition as an influential member of our community by spreading the word and helping us grow.</li>
                                    <li style="font-size: 16px;"><strong>Exclusive Rewards</strong>: Apart from free membership, active affiliates may also receive exclusive perks, bonuses, and special offers as a token of our appreciation.</li>
                                </ul>

                                <h2>Tips to Get Started</h2>
                                <ul class="mb-3">
                                    <li style="font-size: 16px;"><strong>Social Media</strong>: Share your unique Affiliate ID on your social media profiles. Create engaging posts about your experience with our platform and how it has helped you.</li>
                                    <li style="font-size: 16px;"><strong>Blog/Website</strong>: Write a blog post or a review about our platform, including your Affiliate ID, and encourage your readers to join.</li>
                                    <li style="font-size: 16px;"><strong>Email Campaign</strong>: Send out personalized emails to your contacts explaining the benefits of our platform and how they can join using your Affiliate ID.</li>
                                    <li style="font-size: 16px;"><strong>Word of Mouth</strong>: Talk to your friends, family, and colleagues. A personal recommendation goes a long way!</li>
                                </ul>

                                <h2>Join Now and Start Saving!</h2>
                                <p style="font-size: 16px;">
                                    Don't miss out on this fantastic opportunity to get your membership for FREE. Take advantage of our Affiliate Program today and start spreading the word. The more you share, the more you save!
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Container-fluid Ends-->
        </div>

        <?php include_once 'includes/member-footer.php'; ?>

    </div>
</div>

<!-- -----------------------------------------------------------------------------
     Scripts (vendor + plugins + custom)
     ----------------------------------------------------------------------------- -->
<?php include_once 'includes/js.php'; ?>
<?php include_once 'includes/js-plugins.php'; ?>
<?php include_once 'includes/js-custom.php'; ?>
<?php include_once 'includes/live-chat.php'; ?>

<script>
    // -----------------------------------------------------------------------------
    // DataTable init
    // FIX: markup uses id="basic-1" (original JS targeted #dataTable, which wouldn't bind)
    // -----------------------------------------------------------------------------
    $(document).ready(function () {
        $("#basic-1").DataTable({
            order: [[0, 'desc']],
            responsive: true
        });
    });

    // -----------------------------------------------------------------------------
    // Delegated copy handler: uses Clipboard API with fallback to execCommand
    // Preserves original alert messages.
    // -----------------------------------------------------------------------------
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-copy');
        if (!btn) return;

        const targetId = btn.getAttribute('data-copy-target');
        const alertMsg = btn.getAttribute('data-copy-alert') || 'Copied';
        if (!targetId) return;

        const input = document.getElementById(targetId);
        if (!input) return;

        const textToCopy = input.value || '';

        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(textToCopy);
            } else {
                input.focus();
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');
            }
            alert(alertMsg);
        } catch (err) {
            // One more try with legacy copy
            try {
                input.focus();
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');
                alert(alertMsg);
            } catch (err2) {
                alert('Unable to copy. Please copy manually.');
            }
        }
    });
</script>

<script>new WOW().init();</script>
</body>
</html>
