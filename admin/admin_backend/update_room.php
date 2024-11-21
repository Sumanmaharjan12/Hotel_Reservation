<?php
include "../admin_backend/connect.php";

if (isset($_GET['id'])) {
    $room_no = $_GET['id'];
    
    // Include the 'description' field in the SELECT query
    $sql = "SELECT * FROM room WHERE room.id = $room_no";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Fetching the record
        $row = mysqli_fetch_assoc($result);
        $record = array(
            "id" => $row['id'],
            "room_no" => $row['room_no'],
            "room_type" => $row['room_type'],
            "capacity" => $row['capacity'],
            "room_image" => $row['room_image'],
            "description" => $row['description']  // Added description field
        );
    } else {
        echo "No records found!!";
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Room</title>
    <link rel="stylesheet" href="../admin_css/update.css">
</head>
<body>
    <div class="container">
        <div class="center">
            <h1>Update Room</h1>
            <form action="../admin_backend/roomedit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="room_no" value="<?= $record['id'] ?>">
                <div class="text">
                    <label for="room_type">Room No:</label>
                    <input type="text" id="room_type" name="room_no" value="<?= $record['room_no'] ?>" readonly>
                </div>
                <div class="text">
                    <label for="room_type">Room Type:</label>
                    <input type="text" id="room_type" name="room_type" value="<?= $record['room_type'] ?>" required>
                </div>
                <div class="text">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" value="<?= $record['description'] ?>" required>
                </div>
                <div class="text">
                    <label for="capacity">Capacity:</label>
                    <input type="number" id="capacity" name="capacity" value="<?= $record['capacity'] ?>" required>
                </div>
                <div class="text">
                    <label for="room_image">Room Image:</label>
                    <input type="file" name="room_img" accept=".jpg, .png, .jpeg">
                    <img src="upload/<?= $record['room_image'] ?>" width="80">
                </div>
                <input type="submit" value="Update Room" name="update_room">
                <button class="display-button"><a href="../admin/room.php">Go back</a></button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
} else {
    echo "Invalid room number!";
}
mysqli_close($conn);
?>
