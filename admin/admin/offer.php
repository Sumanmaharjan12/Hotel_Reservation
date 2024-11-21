<?php
// Include the database connection file
include_once "../admin_backend/connect.php";

// Check if the database connection is established
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
require_once("admin_template.php");
?>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <link rel="stylesheet" href="../admin_css/header.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css'>
</head>

<body>

    <!-- Offers Section -->
    <?php
    // Fetch offers including room_type
    $query = "SELECT room_no, title, description, room_type, created_at FROM room_offers";
    $result = mysqli_query($conn, $query);

    // Error handling for SQL query
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    ?>
    <div class="form">
        <form action="../admin_backend/add_offer.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <label for="room_number">Room No:</label>
                <input type="text" id="room_no" name="room_no" required>
            </div>
            <div class="form-row">
                <label for="room_type">Room Type:</label>
                <input type="text" id="room_type" name="room_type" required>
            </div>
            <div class="form-row">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-row">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required>
            </div>
            <div class="form-row">
                <input type="submit" value="Add Offer" name="save_offer">
            </div>
        </form>
    </div>

    <div class="table-data">
        <div class="order">
            <h2>Offers</h2>
            <table class="table" border="2px">
                <thead>
                    <tr>
                        <th>Room No</th>
                        <th>Room Type</th> <!-- Added room type column -->
                        <th>Title</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['room_no']) ?></td>
                                <td><?= htmlspecialchars($row['room_type']) ?></td> <!-- Display room type -->
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No offers found</td></tr>"; // Adjusted colspan
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</section> <!-- main section ends -->

</div> <!-- dashboard ends -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
        const currentPage = window.location.pathname.split('/').pop(); // Get the current page URL
        allSideMenu.forEach(item => {
            const li = item.parentElement;
            if (item.getAttribute('href') === currentPage) {
                li.classList.add('active');
            }

            item.addEventListener('click', function () {
                allSideMenu.forEach(i => {
                    i.parentElement.classList.remove('active');
                })
                li.classList.add('active');
            });
        });
    });
</script>

</body>

</html>
