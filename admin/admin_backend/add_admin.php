<?php
include("connect.php");

if ($_POST) {
    // $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $date = date('Y-m-d', strtotime($_POST['date']));
    $number = $_POST['number'];
    $password = "admin"; // Set the default password to "admin"

    // Insert the data into the admin_detail table
    $sql = "INSERT INTO admin_detail (name, email, date, number) VALUES ( '$name', '$email', '$date', '$number')";
    if (mysqli_query($conn, $sql)) {
        $id = mysqli_insert_id($conn);
        // Insert the name and default password into the another admin table
        $password_hashed = password_hash($password, PASSWORD_BCRYPT); // Hash the password for security
        $admin_sql = "INSERT INTO admin (id,name, password) VALUES ('$id','$name', '$password_hashed')";
        if (mysqli_query($conn, $admin_sql)) {
            echo "<script>alert('New Admin Added'); window.location='../admin/admin_index.php';</script>";
        } else {
            echo "Error: " . $admin_sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>







