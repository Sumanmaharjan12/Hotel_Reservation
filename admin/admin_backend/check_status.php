<?php
session_start();
if (!isset($_SESSION['email'])) {
    echo json_encode(['has_pending' => false]);
    exit();
}

$email = $_SESSION['email'];

// Connect to your database
include 'connect.php'; // Ensure your database connection is here

// Query to check for pending bookings
$query = "SELECT COUNT(*) FROM booking WHERE email = '$email' AND status = 'pending'";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $hasPending = $row['pending_count'] > 0;

    echo json_encode(['has_pending' => $hasPending]);
} else {
    echo json_encode(['has_pending' => false]);
}
?>
