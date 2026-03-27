<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['ticket']) || !isset($_SESSION['event_name'])) {
    header("Location: events.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
    <style>
      .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        color: #444;
      }
      .summary-row:last-of-type { border-bottom: none; }
      .summary-row .label { color: #888; }
      .summary-row .value { font-weight: 600; color: #222; text-align: right; max-width: 65%; }
      .total-row .value { color: #800000; font-size: 18px; }
      .pay-btn {
        display: block;
        margin-top: 24px;
        padding: 13px;
        background: #800000;
        color: white;
        text-align: center;
        border-radius: 10px;
        font-weight: bold;
        text-decoration: none;
        transition: background 0.3s;
      }
      .pay-btn:hover { background: #a00000; }
      a.back-link {
        display: inline-block;
        margin-top: 12px;
        color: #800000;
        font-size: 13px;
        text-decoration: none;
      }
      a.back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Summary</h2>

    <div class="summary-row">
      <span class="label">Event</span>
      <span class="value"><?php echo htmlspecialchars($_SESSION['event_name']); ?></span>
    </div>

    <div class="summary-row">
      <span class="label">Date</span>
      <span class="value"><?php echo date('F j, Y', strtotime($_SESSION['event_date'])); ?></span>
    </div>

    <div class="summary-row">
      <span class="label">Venue</span>
      <span class="value"><?php echo htmlspecialchars($_SESSION['event_venue']); ?></span>
    </div>

    <div class="summary-row">
      <span class="label">Ticket</span>
      <span class="value"><?php echo htmlspecialchars($_SESSION['ticket']); ?></span>
    </div>

    <div class="summary-row">
      <span class="label">Email</span>
      <span class="value"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
    </div>

    <a href="dragonpay.php" class="pay-btn">Pay via DragonPay (QR)</a>
    <a href="events.php" class="back-link">← Back to Events</a>
</div>

</body>
</html>