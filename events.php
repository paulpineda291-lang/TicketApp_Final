<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

require 'config.php';

// Fetch all active upcoming/current events
$events = [];
$result = $conn->query(
    "SELECT id, name, event_date, venue, banner_image, description
     FROM events
     WHERE is_active = 1
     ORDER BY event_date ASC"
);
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Select Event</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #f5f5f5;
      align-items: flex-start;
      padding: 40px 20px;
    }

    .page-wrapper {
      width: 100%;
      max-width: 960px;
      margin: 0 auto;
    }

    .page-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .page-header h2 {
      font-size: 28px;
      color: #800000;
    }

    .page-header p {
      color: #666;
      margin-top: 6px;
      font-size: 14px;
    }

    /* EVENT GRID */
    .events-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 24px;
    }

    .event-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .event-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 40px rgba(0,0,0,0.15);
    }

    .event-banner {
      width: 100%;
      height: 160px;
      object-fit: cover;
      background: #800000;
    }

    .event-banner-placeholder {
      width: 100%;
      height: 160px;
      background: linear-gradient(135deg, #800000, #a00000);
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(255,255,255,0.4);
      font-size: 40px;
    }

    .event-body {
      padding: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .event-body h3 {
      color: #800000;
      font-size: 17px;
      margin-bottom: 8px;
    }

    .event-meta {
      font-size: 13px;
      color: #666;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .event-meta span.icon {
      font-size: 15px;
    }

    .event-desc {
      font-size: 13px;
      color: #888;
      margin-top: 8px;
      flex: 1;
    }

    .select-btn {
      display: block;
      margin-top: 16px;
      padding: 11px;
      background: #800000;
      color: white;
      text-align: center;
      border-radius: 10px;
      font-weight: bold;
      font-size: 14px;
      text-decoration: none;
      border: none;
      cursor: pointer;
      width: 100%;
      transition: background 0.3s;
    }

    .select-btn:hover {
      background: #a00000;
    }

    .no-events {
      text-align: center;
      color: #888;
      padding: 60px 20px;
      font-size: 15px;
    }

    .back-link {
      display: inline-block;
      margin-top: 30px;
      color: #800000;
      font-size: 13px;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="page-wrapper">

  <div class="page-header">
    <h2>Select an Event</h2>
    <p>Signed in as <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
       &nbsp;·&nbsp; <?php echo ucfirst($_SESSION['role']); ?>
    </p>
  </div>

  <?php if (empty($events)): ?>
    <div class="no-events">
      <p>No active events at the moment. Please check back later.</p>
    </div>
  <?php else: ?>
    <div class="events-grid">
      <?php foreach ($events as $event): ?>
        <div class="event-card">

          <?php if (!empty($event['banner_image']) && file_exists("images/events/" . $event['banner_image'])): ?>
            <img class="event-banner"
                 src="images/events/<?php echo htmlspecialchars($event['banner_image']); ?>"
                 alt="<?php echo htmlspecialchars($event['name']); ?>">
          <?php else: ?>
            <div class="event-banner-placeholder">🎟</div>
          <?php endif; ?>

          <div class="event-body">
            <h3><?php echo htmlspecialchars($event['name']); ?></h3>

            <div class="event-meta">
              <span class="icon">📅</span>
              <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
            </div>

            <div class="event-meta">
              <span class="icon">📍</span>
              <?php echo htmlspecialchars($event['venue']); ?>
            </div>

            <?php if (!empty($event['description'])): ?>
              <p class="event-desc"><?php echo htmlspecialchars($event['description']); ?></p>
            <?php endif; ?>

            <!-- POST to ticket.php with the chosen event ID -->
            <form action="ticket.php" method="post">
              <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
              <button type="submit" class="select-btn">Get Tickets</button>
            </form>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <a href="logout.php" class="back-link">← Sign out</a>

</div>

</body>
</html>
