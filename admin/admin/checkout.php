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
    <title>Checkout History</title>
    <link rel="stylesheet" href="../admin_css/header.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css'>
</head>

<body>

    <!-- Checkout History Section -->
    <?php
    // Updated query to join checkout_history with the rooms table to fetch room_type
    $query = "
        SELECT checkout_history.*, room.room_type 
        FROM checkout_history 
        JOIN room ON checkout_history.room_no = room.room_no";
    
    $result = mysqli_query($conn, $query);

    // Error handling for SQL query
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    ?>

    <div class="table-data">
        <div class="order">
            <h2>Checkout History</h2>
            <table class="table" border="2px">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Room No</th>
                        <th>Room Type</th> <!-- Added Room Type column -->
                        <th>Code</th>
                        <th>Arrival</th>
                        <th>Arrival Time</th>
                        <th>Departure</th>
                        <th>Number</th>
                        <th>Status</th>
                        <th>ID Card</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['room_no']) ?></td>
                                <td><?= htmlspecialchars($row['room_type']) ?></td> <!-- Display Room Type -->
                                <td><?= htmlspecialchars($row['code']) ?></td>
                                <td><?= htmlspecialchars($row['arrival']) ?></td>
                                <td><?= htmlspecialchars($row['arrival_time']) ?></td>
                                <td><?= htmlspecialchars($row['depature']) ?></td>
                                <td><?= htmlspecialchars($row['number']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['id_card']) ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='10'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

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
