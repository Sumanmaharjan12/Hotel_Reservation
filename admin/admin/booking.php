<?php
require_once("admin_template.php");
include_once "../admin_backend/connect.php";

// Delete expired bookings (consider running this as a scheduled task instead)
$currentDateTime = date("Y-m-d H:i:s");
$deleteQuery = "DELETE FROM booking WHERE depature < '$currentDateTime'";
mysqli_query($conn, $deleteQuery);
?>

<div class="table-data">
    <div class="order">
        <h2>BOOKINGS</h2>
        <table class="table" border="2px">
            <thead>
                <tr>
                    <th class="text-center">Code</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Arrival</th>
                    <th class="text-center">Departure</th>
                    <th class="text-center">Number</th>
                    <th class="text-center">Room No</th>
                    
                    <th class="text-center">Status</th>
                    <th class="text-center">ID Card</th>
                    <th class="text-center">Room Type</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM booking";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row["code"]) ?></td>
                            <td><?= htmlspecialchars($row["email"]) ?></td>
                            <td><?= htmlspecialchars($row["arrival"]) ?></td>
                            <td><?= htmlspecialchars($row["depature"]) ?></td>
                            <td><?= htmlspecialchars($row["number"]) ?></td>
                            <td><?= htmlspecialchars($row["room_no"]) ?></td>
                           
                            <td><?= htmlspecialchars($row["status"]) ?></td>
                            <td>
                                <?php if (!empty($row['id_card'])) { ?>
                                    <!-- View ID Card -->
                                    <a href="../admin/upload/id_card/<?= htmlspecialchars($row['id_card']) ?>" target="_blank">View
                                        ID Card</a>

                                    <!-- Download ID Card -->
                                    <a href="../admin/upload/id_card/<?= htmlspecialchars($row['id_card']) ?>" download>
                                        <button style="
                                                background-color: #4CAF50; 
                                                color: white; 
                                                border: none;
                                                padding: 5px 10px;
                                                text-align: center;
                                                text-decoration: none;
                                                display: inline-block;
                                                font-size: 14px;
                                                margin-left: 8px; 
                                                cursor: pointer;
                                                border-radius: 4px;
                                            ">Download ID Card</button>
                                    </a>
                                <?php } else { ?>
                                    No ID Card
                                <?php } ?>
                            </td>
                            <td><?= htmlspecialchars($row["room_type"]) ?></td>
                            <td>
                                <select onchange="updateStatus(this.value, '<?= htmlspecialchars($row['room_no']); ?>')">
                                    <option value="Pending" <?= ($row["status"] == "Pending") ? "selected" : ""; ?>>Pending
                                    </option>
                                    <option value="Confirmed" <?= ($row["status"] == "Confirmed") ? "selected" : ""; ?>>Confirmed
                                    </option>
                                    <option value="Cancelled" <?= ($row["status"] == "Cancelled") ? "selected" : ""; ?>>Cancelled
                                    </option>
                                </select>
                            </td>
                           
                            <td>
                                <div class="action-icons">
                                    <!-- Delete Booking -->
                                    <a href="#"
                                        onclick="confirmAction('../admin_backend/delete_booking.php?room_no=<?= urlencode($row['room_no']) ?>', 'Are you sure you want to delete this booking?')"
                                        class="btn">
                                        <i class="bx bx-trash delete-icon"></i>
                                    </a>

                                    <a href="#"
                                        onclick="confirmAction('../admin_backend/history.php?email=<?= urlencode($row['email']) ?>', 'Are you sure you want to checkout this booking?')"
                                        class="btn">
                                        <i class="bx bx-check-circle checkout-icon"></i>
                                    </a>


                                </div>
                            </td>
                        </tr>
                        <?php
                    }
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

    function updateStatus(status, room_no) {
        if (status === "Cancelled") {
            if (confirm("Are you sure you want to cancel this booking? This action will delete the booking.")) {
                // If "Cancelled" is selected, delete the booking
                window.location.href = '../admin_backend/delete_booking.php?room_no=' + encodeURIComponent(room_no);
            } else {
                // If the user cancels, reset the dropdown to its previous value
                var table = document.querySelector('.table');
                var rows = table.getElementsByTagName('tr');
                for (var i = 1; i < rows.length; i++) {
                    var row = rows[i];
                    var rowRoomNo = row.cells[6].innerText; // Assuming 'Room No' is in the 7th column
                    if (rowRoomNo === room_no) {
                        var statusCell = row.cells[7]; // Status is in the 8th column
                        statusCell.querySelector('select').value = row.cells[7].innerText;
                        break;
                    }
                }
            }
        } else {
            // For other statuses (Pending, Confirmed), just update the status as usual
            var formData = new FormData();
            formData.append('status', status);
            formData.append('room_no', room_no);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../admin_backend/update_status.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var table = document.querySelector('.table');
                    var rows = table.getElementsByTagName('tr');
                    for (var i = 1; i < rows.length; i++) {
                        var row = rows[i];
                        var rowRoomNo = row.cells[6].innerText; // Assuming 'Room No' is in the 7th column
                        if (rowRoomNo === room_no) {
                            row.cells[7].innerText = status; // Update the status in the table
                            break;
                        }
                    }
                }
            };
            xhr.send(formData);
        }
    }

    function confirmAction(url, message) {
        if (confirm(message)) {
            window.location.href = url; // Redirect to the given URL if the user confirms
        }
    }
</script>
</body>

</html>