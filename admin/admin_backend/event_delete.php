<?php
include_once '../admin_backend/connect.php';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Prepare the SQL query to delete the event
    $sql = "DELETE FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);

    // Attempt to execute the query
    if ($stmt->execute()) {
        // Redirect to the event page with a success message
        header("Location: ../event.php?message=Event deleted successfully.");
        exit();
    } else {
        // Redirect to the event page with an error message
        header("Location: ../event.php?error=Error deleting event.");
        exit();
    }

    $stmt->close();
} else {
    // Redirect to the event page with an error message if event_id is not set
    header("Location: ../event.php?error=Invalid request.");
    exit();
}

// Close the database connection
$conn->close();
?>
