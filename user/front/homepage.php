<?php
session_start();
include('../backend/connect.php');

// Check for a valid database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch upcoming events
$current_date = date('Y-m-d');
$sql_events = "SELECT * FROM events WHERE start_date >= ?";
$stmt_events = $conn->prepare($sql_events);
$stmt_events->bind_param('s', $current_date);
$stmt_events->execute();
$result_events = $stmt_events->get_result();

$events = [];
if ($result_events) {
    while ($row = $result_events->fetch_assoc()) {
        $events[] = $row;
    }
}

// Fetch user's previous room types from checkout history
$email = $_SESSION['email'] ?? null;
$previous_room_types = [];
if ($email) {
    $sql_previous_rooms = "
        SELECT DISTINCT room.room_type 
        FROM checkout_history 
        JOIN room ON checkout_history.room_no = room.room_no 
        WHERE checkout_history.email = ?";
    $stmt_previous_rooms = $conn->prepare($sql_previous_rooms);
    $stmt_previous_rooms->bind_param('s', $email);
    $stmt_previous_rooms->execute();
    $result_previous_rooms = $stmt_previous_rooms->get_result();

    if ($result_previous_rooms) {
        while ($row = $result_previous_rooms->fetch_assoc()) {
            $previous_room_types[] = $row['room_type'];
        }
    }
    $stmt_previous_rooms->close();
}

// Fetch all room types
$sql_all_rooms = "SELECT DISTINCT room_type FROM room";
$stmt_all_rooms = $conn->prepare($sql_all_rooms);
$stmt_all_rooms->execute();
$result_all_rooms = $stmt_all_rooms->get_result();
$all_room_types = [];
while ($row = $result_all_rooms->fetch_assoc()) {
    $all_room_types[] = $row['room_type'];
}

// Create user-item interaction matrix (user-room type interactions)
function get_user_room_interaction($room_type, $conn) {
    $sql = "
        SELECT email
        FROM checkout_history
        JOIN room ON checkout_history.room_no = room.room_no
        WHERE room.room_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $room_type);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['email'];
    }
    return $users;
}

// Define Cosine Similarity calculation
function calculate_cosine_similarity($users_room1, $users_room2) {
    // Check if either user list is empty to prevent division by zero
    if (count($users_room1) == 0 || count($users_room2) == 0) {
        return 0; // Return similarity as 0 if any list is empty
    }

    // Find the intersection of the user lists
    $intersection = array_intersect($users_room1, $users_room2);

    // Cosine similarity formula
    return count($intersection) / (sqrt(count($users_room1)) * sqrt(count($users_room2)));
}

// Generate recommendations based on similar room types
$recommended_room_types = [];
if (!empty($previous_room_types)) {
    foreach ($previous_room_types as $prev_type) {
        // Get user interactions for the current room type
        $users_for_prev_type = get_user_room_interaction($prev_type, $conn);

        // Compare with other room types and calculate similarity
        foreach ($all_room_types as $room_type) {
            if ($prev_type == $room_type) continue; // Skip the same room type

            // Get user interactions for the other room type
            $users_for_current_type = get_user_room_interaction($room_type, $conn);

            // Calculate cosine similarity
            $similarity_score = calculate_cosine_similarity($users_for_prev_type, $users_for_current_type);
            if ($similarity_score > 0.5) { // Adjust threshold as needed
                $recommended_room_types[] = $room_type;
            }
        }
    }
}

// Fetch room offers based on recommended room types
$room_offers = [];
if (!empty($recommended_room_types)) {
    foreach ($recommended_room_types as $room_type) {
        $sql_room_offers = "SELECT room_no, room_type, title, description FROM room_offers WHERE room_type = ?";
        $stmt_room_offers = $conn->prepare($sql_room_offers);
        $stmt_room_offers->bind_param('s', $room_type);
        $stmt_room_offers->execute();
        $result_room_offers = $stmt_room_offers->get_result();

        if ($result_room_offers) {
            while ($row = $result_room_offers->fetch_assoc()) {
                $room_offers[] = $row;
            }
        }
        $stmt_room_offers->close();
    }
}

$menu_items = [
    ['image' => '../image/burger.jpg', 'title' => 'Burger', 'description' => 'A perfectly grilled burger served with fresh, crisp toppings.', 'price' => 'NRs 200'],
    ['image' => '../image/biryani.jpg', 'title' => 'Chicken Biryani', 'description' => 'A fragrant and flavorful chicken biryani, featuring tender chicken pieces.', 'price' => 'NRs 180'],
    ['image' => '../image/thakali.jpg', 'title' => 'Thakali Khana', 'description' => 'A traditional Thakali meal is a vibrant and wholesome platter.', 'price' => 'NRs 160'],
    ['image' => '../image/cheesecake.jpg', 'title' => 'Cheesecake', 'description' => 'Try our chef’s special dessert for the day.', 'price' => 'NRs 250']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel</title>
    <link rel="stylesheet" href="homepage.css">
</head>
<body>
    <header>

    <div class="main">
            <div class="logo">
                <img src="../image/logo.png" alt="Hotel Logo">
            </div>
            <div class="icon">
                <a href=""><img src="../image/icon.png" alt="User Icon"></a>
                <div class="dropdown">
                    <a href="../backend/profile.php">My Profile</a>
                    <a href="../backend/booking_table.php">My Booking</a>
                    <a href="../backend/logout.php">Logout</a>
                </div>
            </div>
            <ul>
                <li class="active"><a href="#">Home</a></li>
                <li><a href="aboutus.php">About</a></li>
                <li><a href="book.php">Booking</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="title">
            <h2>Book A Room</h2><br>
            <h3>For Your</h3><br>
            <h1>Holiday & Business Trip</h1>
        </div>
        <div class="btn">
            <a href="#learn">Learn More</a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="learn">
            <section class="about">
                <div class="about_img">
                    <img src="../image/5.7.jpg" alt="Hotel Interior">
                </div>
                <div class="content">
                    <p>When it comes to choosing a hotel for a holiday or business trip, one of the most important factors to consider is the availability and suitability of the rooms. Our hotel offers many types of rooms for your holidays and business trips to fulfill your needs and preferences.</p>
                </div>
            </section>
        </div>

        <div class="packages">
            <div class="offers">
                <?php if (!empty($room_offers)): ?>
                    <h2>Our Current Offers</h2>
                    <?php foreach ($room_offers as $offer): ?>
                        <div class="offer-card">
                            <h3><?php echo htmlspecialchars($offer['title']); ?></h3>
                            <p><?php echo htmlspecialchars($offer['description']); ?></p>
                            <a href="http://localhost/project/user/front/book.php#divone-<?php echo htmlspecialchars($offer['room_no']); ?>" class="btn-book">Book</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="menu-section">
                        <h3>Our Restaurant Menu</h3>
                        <div class="menu-items">
                            <?php foreach ($menu_items as $item): ?>
                                <div class="menu-card">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                    <p class="price"><?php echo htmlspecialchars($item['price']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar for upcoming events -->
            <div class="events-sidebar">
                <h2>Upcoming Events</h2>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                            <p><?php echo htmlspecialchars($event['description']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['start_date']); ?></p>
                            <a href="event_details.php?id=<?php echo htmlspecialchars($event['event_id']); ?>">More Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No upcoming events at this time.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
