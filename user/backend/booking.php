<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $arrival = isset($_POST['arrival']) ? date('Y-m-d', strtotime($_POST['arrival'])) : '';
    $arrival_time = isset($_POST['arrival_time']) ? $_POST['arrival_time'] : '';
    $depature = isset($_POST['depature']) ? date('Y-m-d', strtotime($_POST['depature'])) : '';
    $number = isset($_POST['number']) ? intval($_POST['number']) : 0;
    $room_no = isset($_POST['room_no']) ? $_POST['room_no'] : '';
    $room_type = isset($_POST['room_type']) ? $_POST['room_type'] : ''; // New field
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Handle file upload for ID card
    if (isset($_FILES['id_card']) && $_FILES['id_card']['error'] === 0) {
        $uploadDir = '../../admin/admin/upload/id_card/'; // Directory to store uploaded files
        $fileName = basename($_FILES['id_card']['name']);
        $fileTmp = $_FILES['id_card']['tmp_name'];
        $filePath = $uploadDir . $fileName;

        // Check if file is a valid image or document
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'pdf']; // Allowed file types
        $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedFileTypes)) {
            // Move the uploaded file to the destination
            if (move_uploaded_file($fileTmp, $filePath)) {
                $id_card = $fileName; // Save the filename to store in the database
            } else {
                echo "<script>alert('Error uploading ID card. Please try again.'); window.location='../front/book.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, PNG, or PDF files are allowed.'); window.location='../front/book.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please upload an ID card.'); window.location='../front/book.php';</script>";
        exit();
    }

    // Check for overlapping bookings
    $query = "SELECT * FROM booking WHERE room_no = ? AND (('$arrival' >= arrival AND '$arrival' <= depature) OR ('$depature' >= arrival AND '$depature' <= depature))";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $room_no);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If there are overlapping bookings
        if ($result->num_rows > 0) {
            $stmt->close();
            echo "<script>
                alert('Sorry, this room is already booked during the selected dates: $arrival to $depature');
                window.location='../front/book.php';
                </script>";
            exit();
        }
        $stmt->close();
    } else {
        // Handle query preparation error
        echo "<script>
            alert('Error preparing the query: " . $conn->error . "');
            window.location='../front/book.php';
            </script>";
        exit();
    }

    // Insert new booking, including id_card and room_type fields
    $query = "INSERT INTO booking (email, arrival, arrival_time, depature, number, room_no, room_type, status, id_card) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('sssssssss', $email, $arrival, $arrival_time, $depature, $number, $room_no, $room_type, $status, $id_card);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Booking Successful');
                window.location='../front/homepage.php';
                </script>";
        } else {
            echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location='../front/book.php';
                </script>";
        }
        $stmt->close();
    } else {
        // Handle insert query preparation error
        echo "<script>
            alert('Error preparing the insert query: " . $conn->error . "');
            window.location='../front/book.php';
            </script>";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
