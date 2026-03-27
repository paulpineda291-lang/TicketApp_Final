<?php
session_start();
require 'config.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['ticket']) || !isset($_SESSION['event_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {

    $email    = $_SESSION['email'];
    $ticket   = $_SESSION['ticket'];
    $event_id = (int) $_SESSION['event_id'];
    $role     = $_SESSION['role'] ?? '';

    /* ----------------------------------------------------------
       1. Resolve the user ID
    ---------------------------------------------------------- */
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        header("Location: index.php");
        exit();
    }

    /* ----------------------------------------------------------
       2. Resolve ticket_type_id for this event + role
    ---------------------------------------------------------- */
    $stmt = $conn->prepare(
        "SELECT id, price FROM ticket_types
         WHERE event_id = ? AND allowed_for = ? AND is_active = 1
         LIMIT 1"
    );
    $stmt->bind_param("is", $event_id, $role);
    $stmt->execute();
    $stmt->bind_result($ticket_type_id, $amount);
    $stmt->fetch();
    $stmt->close();

    if (!$ticket_type_id) {
        echo "No valid ticket type found for your account. Please contact support.";
        exit();
    }

    /* ----------------------------------------------------------
       3. Create reference code and insert order (with event_id)
    ---------------------------------------------------------- */
    $reference_code = 'HAU-' . strtoupper(bin2hex(random_bytes(5)));

    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, event_id, ticket_type_id, amount_paid, status, reference_code)
         VALUES (?, ?, ?, ?, 'payment_uploaded', ?)"
    );
    $stmt->bind_param("iiids", $user_id, $event_id, $ticket_type_id, $amount, $reference_code);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    $_SESSION['order_id']       = $order_id;
    $_SESSION['reference_code'] = $reference_code;

    /* ----------------------------------------------------------
       4. Save the uploaded proof file
    ---------------------------------------------------------- */
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext        = pathinfo($_FILES["proof"]["name"], PATHINFO_EXTENSION);
    $fileName   = time() . "_" . $order_id . "." . $ext;
    $targetFile = $uploadDir . $fileName;

    move_uploaded_file($_FILES["proof"]["tmp_name"], $targetFile);

    $stmt = $conn->prepare(
        "INSERT INTO payment_proofs (order_id, file_name) VALUES (?, ?)"
    );
    $stmt->bind_param("is", $order_id, $fileName);
    $stmt->execute();
    $stmt->close();

    /* ----------------------------------------------------------
       5. Mark as verified and redirect
    ---------------------------------------------------------- */
    sleep(2);

    $stmt = $conn->prepare("UPDATE orders SET status = 'verified' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['payment_verified'] = true;

    header("Location: send_ticket.php");
    exit();

} else {
    echo "Upload failed. Please go back and try again.";
}
?>