<?php
include_once "connect.php"; // Ensure this includes your database connection

if (isset($_GET['room_no'])) {
    $room_no = mysqli_real_escape_string($conn, $_GET['room_no']);
    
    // Fetch booking data for the given room number
    $query = "SELECT * FROM booking WHERE room_no = '$room_no'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Insert the fetched booking data into checkout_history
        $historyQuery = "INSERT INTO checkout_history (email, room_no, code, arrival, arrival_time, depature, number, status, id_card) 
                         VALUES ('{$row['email']}', '{$row['room_no']}', '{$row['code']}', '{$row['arrival']}', '{$row['arrival_time']}', '{$row['depature']}', 
                         '{$row['number']}', '{$row['status']}', '{$row['id_card']}')";

        // Perform the insert into checkout_history
        if (mysqli_query($conn, $historyQuery)) {
            // Delete the record from booking table if insertion was successful
            $deleteQuery = "DELETE FROM booking WHERE room_no = '$room_no'";
            if (mysqli_query($conn, $deleteQuery)) {
                echo "<script>
                        alert('Booking deleted and recorded in checkout history successfully.');
                        window.location.href = '../admin/booking.php'; // Redirect to your desired page
                      </script>";
            } else {
                echo "Error while deleting booking record: " . mysqli_error($conn);
            }
        } else {
            echo "Error while inserting into checkout_history: " . mysqli_error($conn);
        }
    } else {
        echo "No booking found for this room number: " . $room_no;
    }
} else {
    echo "Invalid request. Room number parameter is missing.";
}

mysqli_close($conn);
?>
