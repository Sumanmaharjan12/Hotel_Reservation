
<?php
// admin_backend/update_status.php

include_once "connect.php";
include_once "email.php";

if (isset($_POST['status']) && isset($_POST['room_no'])) {
    $status = $_POST['status'];
    $room_no = $_POST['room_no'];

    // Update the status in the database
    $query = "UPDATE booking SET status = '$status' WHERE room_no = '$room_no'";
    mysqli_query($conn, $query);

    if ($status === 'Confirmed') {
        // Get the recipient's email address from the database based on $code
        $query = "SELECT email FROM booking WHERE room_no = '$room_no'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $recipientEmail = $row["email"];
            sendConfirmationEmail($recipientEmail, $code);
        }
    }

    // Return a response to the client
    echo "Status updated successfully!";
}
?>

