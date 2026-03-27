<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

// Must arrive via POST from events.php
if (!isset($_POST['event_id'])) {
    header("Location: events.php");
    exit();
}

require 'config.php';

$event_id = (int) $_POST['event_id'];
$role     = $_SESSION['role'];

// Load the event
$stmt = $conn->prepare(
    "SELECT id, name, event_date, venue
     FROM events
     WHERE id = ? AND is_active = 1
     LIMIT 1"
);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    // Event not found or inactive
    header("Location: events.php");
    exit();
}

// Load the correct ticket type for this role + event
$stmt = $conn->prepare(
    "SELECT id, name, price
     FROM ticket_types
     WHERE event_id = ? AND allowed_for = ? AND is_active = 1
     LIMIT 1"
);
$stmt->bind_param("is", $event_id, $role);
$stmt->execute();
$ticket_type = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket_type) {
    echo "No ticket available for your account type at this event. <a href='events.php'>Go back</a>";
    exit();
}

// Store in session so checkout.php can use it
$_SESSION['event_id']      = $event['id'];
$_SESSION['event_name']    = $event['name'];
$_SESSION['event_date']    = $event['event_date'];
$_SESSION['event_venue']   = $event['venue'];
$_SESSION['ticket']        = $ticket_type['name'] . ' — ₱' . number_format($ticket_type['price'], 2);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Your Ticket — <?php echo htmlspecialchars($event['name']); ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .ticket-box {
      background: #fff8f8;
      border: 2px dashed #800000;
      border-radius: 14px;
      padding: 20px 24px;
      margin: 16px 0 24px;
    }
    .ticket-box p {
      margin: 6px 0;
      font-size: 14px;
      color: #444;
    }
    .ticket-box .ticket-name {
      font-size: 20px;
      font-weight: 800;
      color: #800000;
      margin-bottom: 10px;
    }
    .ticket-box .price {
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }
    .note {
      font-size: 13px;
      color: #888;
    }
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
  <h2>Your Ticket</h2>

  <div class="ticket-box">
    <p class="ticket-name"><?php echo htmlspecialchars($event['name']); ?></p>
    <p>📅 <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
    <p>📍 <?php echo htmlspecialchars($event['venue']); ?></p>
    <br>
    <p><?php echo ucfirst($role); ?> Ticket</p>
    <p class="price">₱<?php echo number_format($ticket_type['price'], 2); ?></p>
  </div>

  <p class="note">
    Signed in as <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
  </p>
  <br>

  <form action="checkout.php" method="post">
    <button type="submit">Proceed to Checkout</button>
  </form>

  <a href="events.php" class="back-link">← Back to Events</a>
</div>

</body>
</html>