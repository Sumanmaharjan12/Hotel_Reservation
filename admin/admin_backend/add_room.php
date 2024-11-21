<?php
    session_start();
    include('../admin_backend/connect.php');
    if(isset($_POST['save_room_image'])) {
        $roomNumber=$_POST['room_no'];
        $roomType=$_POST['room_type'];
        $capacity=$_POST['capacity'];
        $description=$_POST['description'];
        $filename = $_FILES["room_img"]["name"];
        $tempname = $_FILES["room_img"]["tmp_name"];
        $folder = "../admin/upload/" . $filename;
     
     
        // Get all the submitted data from the form
        $query="INSERT INTO room(room_no, room_type, capacity,description, room_image) VALUES ('$roomNumber', '$roomType', '$capacity','$description', '$filename')";
     
        // Execute query
        mysqli_query($conn, $query);
     
        // Now let's move the uploaded image into the folder: image
        if (move_uploaded_file($tempname, $folder)) {
            echo "<script> alert('Room added sucessfully'); window.location='../admin/room.php';</script>
            ";
        } else {
            echo "<script> alert('Room not added'); window.location='../admin/room.php';</script>
            ";
        }
    }
  
$conn->close();
?>


    