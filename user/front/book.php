<?php
session_start();
include('../backend/connect.php');

// Initialize variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$roomTypeFilter = isset($_GET['room_type']) ? trim($_GET['room_type']) : '';

// Prepare the base SQL query
$sql = "SELECT r.*, 
               (SELECT COUNT(*) FROM booking b WHERE b.room_no = r.room_no AND b.status = 'pending') AS pending_count,
               (SELECT COUNT(*) FROM booking b WHERE b.room_no = r.room_no AND b.status = 'confirmed') AS confirmed_count
        FROM room r";

// Add search and room type filtering conditions if provided
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " WHERE r.room_type LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

if (!empty($roomTypeFilter)) {
    // If a search term is provided, append the room type filter with AND
    if (!empty($search)) {
        $sql .= " AND r.room_type = ?";
    } else {
        $sql .= " WHERE r.room_type = ?";
    }
    $params[] = $roomTypeFilter;
    $types .= 's';
}

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters if search or room type filtering is used
if (!empty($search) || !empty($roomTypeFilter)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the statement
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

// Get the result
$result = $stmt->get_result();

// Fetch all rooms
$rooms = $result->fetch_all(MYSQLI_ASSOC);

// Close statement and connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Rooms</title>
    <link rel="stylesheet" href="book.css">
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css'>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="homepage.php"><img src="../image/2.png" alt="Logo"></a>
            <p>Book Now</p>
        </div>
        <div class="right-controls">
            <div class="search-sort-container">
                <!-- Search Form -->
                <div class="search-bar">
                    <form action="book.php" method="get">
                        <input type="text" name="search" placeholder="Search..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class='bx bx-search'></i></button>
                    </form>
                </div>
                <!-- Sort Dropdown for Room Type -->
                <div class="dropdown">
                    <button class="dropbtn"><i class='bx bx-filter'></i></button>
                    <div class="dropdown-content">
                        <form action="book.php" method="get">
                            <!-- Preserve the search term when filtering -->
                            <?php if (!empty($search)) { ?>
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <?php } ?>
                            <!-- Room Type Filters -->
                            <button type="submit" name="room_type" value="Normal">
                                <i class='bx bx-bed'></i> Normal Rooms
                            </button>
                            <button type="submit" name="room_type" value="Dulex">
                                <i class='bx bx-bed'></i> Deluxe Rooms
                            </button>
                            <button type="submit" name="room_type" value="King">
                                <i class='bx bx-bed'></i> King Rooms
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Cross Icon to Go to Homepage -->
                <a href="homepage.php" class="close"><i class='bx bx-x'></i></a>
            </div>
        </div>
    </nav>
    <div class="header"></div>
    <section class="book">
        <?php if (empty($rooms)) { ?>
            <p class="no-results">No rooms found.</p>
        <?php } else { ?>
            <?php foreach ($rooms as $row) { ?>
                <div class="room-content">
                    <div class="room">
                        <img src="../../admin/admin/upload/rooms/<?php echo htmlspecialchars($row["room_image"]); ?>"
                            alt="Room Image" class="room-img">
                        <div class="detail">
                            <h2 class="room-title"><span class='bx bx-room'></span>
                                <?php echo htmlspecialchars($row["room_no"]); ?></h2>
                            <span class="price"><span class='bx bx-bed'></span>
                                <?php echo htmlspecialchars($row["room_type"]); ?></span><br>
                            <span class="price"><span class='bx bx-group'></span>
                                <?php echo htmlspecialchars($row["capacity"]); ?></span><br>
                            <span class="description"><span class='bx bx-wifi'></span>
                                <?php echo htmlspecialchars($row["description"]); ?></span><br>

                            <?php
                            // Determine button color based on booking status
                            $buttonClass = 'btn';
                            if ($row['confirmed_count'] > 0) {
                                $buttonClass .= ' confirmed'; // Red if booked
                            } elseif ($row['pending_count'] > 0) {
                                $buttonClass .= ' pending'; // Yellow if pending
                            }
                            ?>
                            <a href="#divone-<?php echo htmlspecialchars($row['room_no']); ?>"
                                onclick="handleButtonClick('<?php echo htmlspecialchars($row['room_no']); ?>', '<?php echo htmlspecialchars($buttonClass); ?>');">
                                <button class="<?php echo htmlspecialchars($buttonClass); ?>">
                                    Book Now
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </section>

    <!-- Overlay for Booking Form -->
    <?php foreach ($rooms as $row) { ?>
        <div class="overlay" id="divone-<?php echo htmlspecialchars($row['room_no']); ?>" style="display: none;">
            <div class="wrapper">
                <h2>Please Fill up the Form</h2>
                <a href="#" class="close"
                    onclick="closeOverlay(event, '<?php echo htmlspecialchars($row['room_no']); ?>')">&times;</a>
                <div class="content">
                    <div class="container">
                        <form action="../backend/booking.php" method="POST" onsubmit="return validate(this)"
                            enctype="multipart/form-data">
                            <div class="input">
                                <?php if (isset($_SESSION['email'])) { ?>
                                    <input type="hidden" name="email" class="email"
                                        value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                                <?php } else { ?>
                                    <label for="email-<?php echo htmlspecialchars($row['room_no']); ?>">Email:</label><br>
                                    <input type="email" name="email" class="email"
                                        id="email-<?php echo htmlspecialchars($row['room_no']); ?>" required>
                                <?php } ?>
                            </div>
                            <div class="input">
                                <label for="arrival-<?php echo htmlspecialchars($row['room_no']); ?>">Arrival
                                    Date:</label><br>
                                <input type="date" name="arrival" class="arrival"
                                    id="arrival-<?php echo htmlspecialchars($row['room_no']); ?>" required>
                            </div>
                            <div class="input">
                                <label for="arrival_time-<?php echo htmlspecialchars($row['room_no']); ?>">Time:</label><br>
                                <input type="time" name="arrival_time" class="arrival_time"
                                    id="arrival_time-<?php echo htmlspecialchars($row['room_no']); ?>">
                            </div>
                            <div class="input">
                                <label for="departure-<?php echo htmlspecialchars($row['room_no']); ?>">Departure
                                    Date:</label><br>
                                <input type="date" name="depature" class="depature"
                                    id="depature-<?php echo htmlspecialchars($row['room_no']); ?>">
                            </div>
                            <div class="input">
                                <label for="number-<?php echo htmlspecialchars($row['room_no']); ?>">Number of
                                    People:</label><br>
                                <input type="number" name="number" class="number"
                                    id="number-<?php echo htmlspecialchars($row['room_no']); ?>"
                                    value="<?php echo htmlspecialchars($row['capacity']); ?>" readonly>
                            </div>
                            <div class="input">
                                <label for="id_card-<?php echo htmlspecialchars($row['room_no']); ?>">Upload ID Card
                                    Photo:</label><br>
                                <input type="file" name="id_card"
                                    id="id_card-<?php echo htmlspecialchars($row['room_no']); ?>" accept="image/*" required>
                            </div>
                            <div class="input">
                                <input type="hidden" name="status" value="Pending">
                            </div>
                            <div class="input">
                                <!-- Hidden input for room_type -->
                                <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($row['room_type']); ?>">
                                <input type="hidden" name="room_no" value="<?php echo htmlspecialchars($row['room_no']); ?>">
                            </div>
                            
                            <button class="button" type="submit">Book</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Date Validation Script -->
    <script>
        function validate(form) {
            var inputDate = form.querySelector(".arrival").value;
            var inputDate1 = form.querySelector(".depature").value;

            if (new Date(inputDate) < new Date()) {
                alert("Arrival date cannot be in the past.");
                return false;
            }
            if (new Date(inputDate) >= new Date(inputDate1)) {
                alert("Departure date must be after arrival date.");
                return false;
            }
            return true;
        }
    </script>

    <!-- Handle Button Click -->
    <script>
        function handleButtonClick(roomNo, buttonClass) {
            if (buttonClass.includes('confirmed')) {
                alert("This room is already booked.");
            } else {
                const overlay = document.getElementById("divone-" + roomNo);
                if (overlay) {
                    overlay.style.display = 'block'; // Show the overlay form
                }
                document.getElementById('room_no-' + roomNo).value = roomNo; // Set the room number
            }
        }

        function closeOverlay(event, roomNo) {
            event.preventDefault();
            const overlay = document.getElementById("divone-" + roomNo);
            if (overlay) {
                overlay.style.display = 'none'; // Hide the overlay form
            }
        }

        // Close overlay on clicking outside
        window.onclick = function (event) {
            const overlays = document.querySelectorAll('.overlay');
            overlays.forEach(overlay => {
                if (event.target === overlay) {
                    overlay.style.display = 'none';
                }
            });
        };
        function validate(form) {
            var inputDate = form.querySelector(".arrival").value;
            var inputTime = form.querySelector(".arrival_time").value;
            var inputDepartureDate = form.querySelector(".depature").value;

            // Create a Date object for the arrival date
            var arrivalDateTime = new Date(inputDate);
            if (inputTime) {
                var timeParts = inputTime.split(':');
                arrivalDateTime.setHours(timeParts[0], timeParts[1]); // Set the hours and minutes
            }

            // Create a Date object for the departure date
            var departureDateTime = new Date(inputDepartureDate);

            // Check if the arrival date is in the past
            if (arrivalDateTime < new Date()) {
                alert("Arrival date cannot be in the past.");
                return false;
            }

            // Check if the departure date is after the arrival date
            if (arrivalDateTime >= departureDateTime) {
                alert("Departure date must be after arrival date.");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>