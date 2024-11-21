<?php
    session_start();
    include('connect.php');
    if(isset($_POST['name']) && isset($_POST['password'])){
            $name=$_POST['name'];
            $password=$_POST['password'];
            // $name=$_POST['name'];
    }
        $sql="select * from admin where name='$name'";
        $result= mysqli_query($conn, $sql);

        if(mysqli_num_rows($result)==1){
            $row=mysqli_fetch_assoc($result);
            $hash_password = $row['password'];
            
            if(password_verify($password, $hash_password)){
                $_SESSION['name']="$name";
                $_SESSION['password']="$password";
                header("location:../admin/admin_index.php");
            }else {
                $_SESSION['error']="Incorrect password";
                header("location:../admin/index.php");

            }
            
        }
        else{
            // echo"
            // <script> alert('No account found'); window.location='../admin/admin_index.php';</script>
            // ";
            $_SESSION['error']="Incorrect username or password";
            header("location:../admin/index.php");
        }
        mysqli_close($conn);
?>