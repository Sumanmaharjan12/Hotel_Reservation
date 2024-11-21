<?php
session_start();
include('../admin_backend/connect.php');

if (isset($_POST['save_offer'])) {
    // Get data from the form
    $room_no = $_POST['room_no'];
    $offerTitle = $_POST['title'];
    $offerDescription = $_POST['description'];

    // First, check if the room number exists in the rooms table
    $checkRoomQuery = "SELECT * FROM room WHERE room_no = '$room_no'";
    $checkRoomResult = mysqli_query($conn, $checkRoomQuery);

    // Check if the query was successful
    if (!$checkRoomResult) {
        // If the query fails, show the error
        die("Error checking room: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkRoomResult) > 0) {
        // Room exists, now proceed with adding the offer

        // Insert query for the offer
        $query = "INSERT INTO room_offers (room_no, title, description) 
                  VALUES ('$room_no', '$offerTitle', '$offerDescription')";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Offer added successfully'); window.location='../admin/offer.php';</script>";
        } else {
            echo "<script>alert('Failed to add offer: " . mysqli_error($conn) . "'); window.location='../admin/offer.php';</script>";
        }

    } else {
        // Room number does not exist, show an alert
        echo "<script>alert('Room number does not exist. Please check and try again.'); window.location='../admin/offer.php';</script>";
    }
}

$conn->close();
?>
