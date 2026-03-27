<?php
session_start();
require 'config.php';

if (!isset($_SESSION['payment_verified'])) {
    header("Location: index.php");
    exit();
}

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email          = $_SESSION['email'];
$ticket         = $_SESSION['ticket'];
$event_name     = $_SESSION['event_name']    ?? 'HAU Event';
$event_date     = $_SESSION['event_date']    ?? '';
$event_venue    = $_SESSION['event_venue']   ?? '';
$reference_code = $_SESSION['reference_code'] ?? 'N/A';
$order_id       = $_SESSION['order_id']       ?? null;
$role           = $_SESSION['role']           ?? 'guest';

$formattedDate = $event_date ? date('F j, Y', strtotime($event_date)) : '';

// Pick the correct ticket image based on role
$ticketImagePath = ($role === 'student')
    ? __DIR__ . '/tickets/angelites.jfif'
    : __DIR__ . '/tickets/non-angelites.jfif';

$ticketImageName = ($role === 'student') ? 'angelites.jfif' : 'non-angelites.jfif';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yaomanpineda@gmail.com';
    $mail->Password   = 'ohcikczruibefehj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('yaomanpineda@gmail.com', 'HAU Ticket System');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Ticket Confirmed — {$event_name}";

    if (file_exists($ticketImagePath)) {
        $mail->addEmbeddedImage($ticketImagePath, 'ticket_image', $ticketImageName, 'base64', 'image/jpeg');
        $mail->addAttachment($ticketImagePath, $ticketImageName, 'base64', 'image/jpeg');
        $inlineImg = '<img src="cid:ticket_image" alt="Your Ticket"
                          style="max-width:100%;border-radius:8px;margin-top:16px;">';
    } else {
        $inlineImg = '<p><em>Ticket image could not be loaded. Please contact support.</em></p>';
    }

    $mail->Body = "
        <div style='font-family:Segoe UI,sans-serif;color:#2c2c2c;max-width:520px;margin:auto;'>
            <h2 style='color:#800000;'>Payment Verified ✅</h2>
            <p><strong>Event:</strong> {$event_name}</p>
            " . ($formattedDate ? "<p><strong>Date:</strong> {$formattedDate}</p>" : "") . "
            " . ($event_venue   ? "<p><strong>Venue:</strong> {$event_venue}</p>"  : "") . "
            <p><strong>Ticket:</strong> {$ticket}</p>
            <p><strong>Reference:</strong> {$reference_code}</p>
            <p>Your ticket is confirmed. See you there!</p>
            {$inlineImg}
            <p style='margin-top:16px;font-size:13px;color:#888;'>
                Your ticket image is also attached so you can save or print it.
            </p>
        </div>
    ";

    $mail->send();

    if ($order_id) {
        $stmt = $conn->prepare("INSERT IGNORE INTO sent_tickets (order_id) VALUES (?)");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    }

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Ticket Confirmed</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Payment Verified 🎉</h2>
    <p>Your ticket has been sent to:</p>
    <strong><?php echo htmlspecialchars($email); ?></strong>
    <br><br>
    <p><strong>Event:</strong> <?php echo htmlspecialchars($event_name); ?></p>
    <?php if ($formattedDate): ?>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($formattedDate); ?></p>
    <?php endif; ?>
    <?php if ($event_venue): ?>
    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event_venue); ?></p>
    <?php endif; ?>
    <br>
    <p class="note">Reference: <strong><?php echo htmlspecialchars($reference_code); ?></strong></p>
    <br>
    <a href="logout.php">Finish</a>
</div>

</body>
</html>