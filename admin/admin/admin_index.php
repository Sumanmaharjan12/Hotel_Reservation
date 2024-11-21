<?php
require_once ("admin_template.php");
?>
<section class="right-lower">
    <ul class="box-info">
        <?php
        include "../admin_backend/connect.php";

        // Fetch user count
        $query = "SELECT COUNT(*) AS user_count FROM sign";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Error fetching user count: " . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($result);
        $userCount = $row['user_count'];

        // Fetch admin count
        $query = "SELECT COUNT(*) AS admin_count FROM admin_detail";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Error fetching admin count: " . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($result);
        $adminCount = $row['admin_count'];

        // Fetch checkout count (from checkout_history)
        $query = "SELECT COUNT(*) AS total_booking FROM booking";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Error fetching checkout count: " . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($result);
        $booking = $row['total_booking'];
        ?>
        <li>
            <i class='bx bx-user user-icon'></i>
            <span class="text">
                <h3><?php echo $userCount; ?></h3>
                <p>No. of Users</p>
            </span>
        </li>
        <li>
            <i class='bx bx-user-circle admin-icon'></i>
            <span class="text">
                <h3><?php echo $adminCount; ?></h3>
                <p>No of Admins</p>
            </span>
        </li>
        <li>
            <i class='bx bx-calendar booking-icon'></i>
            <span class="text">
                <h3><?php echo $booking; ?></h3>
                <p>Total Booking</p>
            </span>
        </li>
    </ul>

    <!-- Charts Section -->
    <div class="table-data">
        <div class="chart-section">
            <h2>Checkout Data </h2>
            <canvas id="checkoutChart" width="250" height="100"></canvas>
        </div>
        <div class="chart-section">
            <h2>User Logins </h2>
            <canvas id="userChart" width="250" height="100"></canvas>
        </div>
    </div>
</section><!-- right lower -->

<?php
// Fetch checkout data by date (using 'created_at' from checkout_history)
$checkoutData = [];
$query = "SELECT DATE(created_at) AS date, COUNT(*) AS total_checkouts FROM checkout_history GROUP BY DATE(created_at) ORDER BY DATE(created_at)";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching checkout data: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($result)) {
    $checkoutData[] = $row;
}

// Fetch user login data by date
$userLoginData = [];
$query = "SELECT DATE(created_at) AS date, COUNT(*) AS logins FROM sign GROUP BY DATE(created_at) ORDER BY DATE(created_at)";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching user login data: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($result)) {
    $userLoginData[] = $row;
}

// Encode data to JSON format for JavaScript
$checkoutDataJSON = json_encode($checkoutData);
$userLoginDataJSON = json_encode($userLoginData);
?>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Get checkout and user login data from PHP
    const checkoutData = <?php echo $checkoutDataJSON; ?>;
    const userLoginData = <?php echo $userLoginDataJSON; ?>;

    // Prepare data for Checkout Chart
    const checkoutLabels = checkoutData.map(data => data.date);
    const checkoutCounts = checkoutData.map(data => data.total_checkouts);

    // Checkout Bar Chart
    const checkoutCtx = document.getElementById('checkoutChart').getContext('2d');
    const checkoutChart = new Chart(checkoutCtx, {
        type: 'bar',  // Change chart type to 'bar'
        data: {
            labels: checkoutLabels,
            datasets: [{
                label: 'Total Checkouts',
                data: checkoutCounts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',  // Bar color
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Prepare data for User Login Chart
    const loginLabels = userLoginData.map(data => data.date);
    const loginCounts = userLoginData.map(data => data.logins);

    // User Login Bar Chart
    const userCtx = document.getElementById('userChart').getContext('2d');
    const userChart = new Chart(userCtx, {
        type: 'bar',  // Change chart type to 'bar'
        data: {
            labels: loginLabels,
            datasets: [{
                label: 'User Logins',
                data: loginCounts,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',  // Bar color
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
