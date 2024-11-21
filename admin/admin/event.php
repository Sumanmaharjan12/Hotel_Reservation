<?php
require_once("admin_template.php");
?>
<div class="form">

    <form action="../admin_backend/add_event.php" method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" required>
        </div>
        <div class="form-row">
            <label for="start_time">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        <div class="form-row">
            <label for="end_time">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
        <div class="form-row">
            <label for="event_description">Description:</label>
            <textarea id="event_description" name="event_description" required></textarea>
        </div>
        <div class="form-row">
            <label for="image">Event Image:</label>
            <input type="file" name="image" accept=".jpg, .png, .jpeg" required>
        </div>
        <div class="form-row">
            <input type="submit" value="Add Event" name="save_event_image">
        </div>
    </form>
</div>

<div class="table-data">
    <div class="order">
        <h1>EVENTS</h1>
        <table class="table" border="2px">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Event Name</th>
                    <th class="text-center">Start Time</th>
                    <th class="text-center">End Time</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Image</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include_once "../admin_backend/connect.php";

                // Get the current date
                $currentDate = date('Y-m-d');

                // Fetch event data excluding past events
                $eventQuery = "SELECT * FROM events WHERE end_date >= '$currentDate'"; // Filter events by end date
                $eventResult = mysqli_query($conn, $eventQuery);

                if (mysqli_num_rows($eventResult) > 0) {
                    while ($eventRow = mysqli_fetch_assoc($eventResult)) {
                        ?>
                        <tr>
                            <td><?= $eventRow["event_id"] ?></td> <!-- Assuming event_id is the primary key -->
                            <td><?= $eventRow["name"] ?></td> <!-- Changed to match the correct column names -->
                            <td><?= $eventRow["start_date"] ?></td>
                            <td><?= $eventRow["end_date"] ?></td>
                            <td><?= $eventRow["description"] ?></td>
                             <!-- Display Room No -->
                            <td>
                                <img src="upload/offer/<?= $eventRow["image"] ?>" width="80">
                            </td>
                            <td>
                                <a href='../admin_backend/event_delete.php?event_id=<?= $eventRow['event_id'] ?>' class="btn">
                                    <i class="bx bx-trash delete-icon" style="margin-right: -7px;"></i>
                                </a>
                                <a href='update_event.php?id=<?= $eventRow['event_id'] ?>' class="btn">
                                    <i class="bx bx-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='8'>No events found</td></tr>"; // Updated colspan to match the number of columns
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
                });
                li.classList.add('active');
            });
        });
    });
</script>

</body>
</html>
