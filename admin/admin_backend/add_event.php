<?php
session_start();
include('../admin_backend/connect.php');

// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['save_event_image'])) {
    // Get data from the form
    $eventName = $_POST['event_name'];
    $startDate = $_POST['start_date'];  // Fixed variable name
    $endDate = $_POST['end_date'];
    $eventDescription = $_POST['event_description'];
    $filename = $_FILES["image"]["name"];
    $tempname = $_FILES["image"]["tmp_name"];

    $folder = "../admin/upload/offer/" . $filename; // Ensure the correct path

    // Get all the submitted data from the form
    $query = "INSERT INTO events (name, description, start_date, end_date, image) 
              VALUES ('$eventName', '$eventDescription', '$startDate', '$endDate', '$filename')"; // Removed extra comma

    // Execute query
    if (mysqli_query($conn, $query)) {
        // Move the uploaded image to the designated folder
        if (move_uploaded_file($tempname, $folder)) {
            echo "<script>alert('Event added successfully'); window.location='../admin/event.php';</script>";
        } else {
            echo "<script>alert('Event added, but image upload failed'); window.location='../admin/event.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to add event: " . mysqli_error($conn) . "'); window.location='../admin/event.php';</script>";
    }
}

$conn->close();
?>
