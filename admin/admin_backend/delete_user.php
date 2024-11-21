<?php
include('../admin_backend/connect.php');

if (isset($_GET['event_id'])) {
    // Sanitize the event_id parameter
    $event_id = $_GET['event_id'];

    // Prepare the query to delete the event
    $query = "DELETE FROM events WHERE event_id = $event_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "
        <script> 
            confirm('Do you want to delete the event?'); 
            window.location = '../admin/event.php';
        </script>
        ";
    } else {
        echo "
        <script> 
            confirm('Error while deleting the event'); 
            window.location = '../admin/event.php';
        </script>
        ";
    }
} else {
    echo "
    <script> 
        alert('No event ID provided for deletion'); 
        window.location = '../admin/event.php';
    </script>
    ";
}

// Close the database connection
mysqli_close($conn);
?>
