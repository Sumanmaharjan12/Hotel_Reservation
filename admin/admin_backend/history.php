<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once "../admin_backend/connect.php";

// Check if 'email' parameter is set in the GET request
if (isset($_GET['email'])) {
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    
    // Debug: Show the received email
    echo "Received email: " . htmlspecialchars($email) . "<br>";

    // Retrieve booking data for the provided email
    $query = "SELECT * FROM booking WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    // Debug: Check if query was successful
    if (!$result) {
        echo "Query failed: " . mysqli_error($conn) . "<br>";
    } else {
        // Debug: Show number of rows returned
        echo "Number of bookings found: " . mysqli_num_rows($result) . "<br>";
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Debug output to check the fetched booking data
        echo "<pre>";
        print_r($row);
        echo "</pre>";

        // Check if the booking status is confirmed
        if ($row['status'] === 'Confirmed') {
            // Prepare to insert booking data into checkout_history
            $historyQuery = "INSERT INTO checkout_history (email, room_no, code, arrival, arrival_time, depature, number, room_type, status, id_card) 
                             VALUES ('{$row['email']}', '{$row['room_no']}', '{$row['code']}', '{$row['arrival']}', '{$row['arrival_time']}', 
                             '{$row['depature']}', '{$row['number']}', '{$row['room_type']}', '{$row['status']}', '{$row['id_card']}')";

            // Debug output for history query
            echo "History Query: " . $historyQuery . "<br>"; 

            // Insert into checkout_history and verify success
            if (mysqli_query($conn, $historyQuery)) {
                // Debug: Confirm successful insertion
                echo "Successfully inserted into checkout_history.<br>";

                // Delete the booking record from booking table
                $deleteQuery = "DELETE FROM booking WHERE email = '$email'";
                if (mysqli_query($conn, $deleteQuery)) {
                    echo "<script>
                            alert('Checkout successful!');
                            window.location.href = '../admin/booking.php'; 
                          </script>";
                } else {
                    echo "Error while deleting booking record: " . mysqli_error($conn) . "<br>";
                }
            } else {
                echo "Error during checkout insertion: " . mysqli_error($conn) . "<br>";
            }
        } else {
            // If the status is not confirmed, alert the user and do not allow checkout
            echo "<script>
                    alert('Checkout not allowed. Booking status is not confirmed.');
                    window.location.href = '../admin/booking.php'; 
                  </script>";
        }
    } else {
        echo "No booking found for this email: " . htmlspecialchars($email) . "<br>";
    }
} else {
    echo "Invalid request. Email parameter is missing.<br>";
}
