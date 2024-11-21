<?php
include('connect.php');
if (isset($_GET['room_no'])) {
    $room_no = $_GET['room_no'];

    // Use a script to handle the confirmation
    echo "
    <script>
        if (confirm('Do you want to delete the data?')) {
            window.location.href = '../admin_backend/room_delete.php?room_no=$room_no';
        } else {
            window.location.href = '../admin/room.php';
        }
    </script>
    ";
}

mysqli_close($conn);
?>
