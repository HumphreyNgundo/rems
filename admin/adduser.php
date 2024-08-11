<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = mysqli_real_escape_string($con, $_POST['uname']);
    $uemail = mysqli_real_escape_string($con, $_POST['uemail']);
    $uphone = mysqli_real_escape_string($con, $_POST['uphone']);
    $upass = sha1($_POST['upass']); 
    $utype = mysqli_real_escape_string($con, $_POST['utype']);

    // Handle file upload
    $uimage = '';
    if(isset($_FILES['uimage']) && $_FILES['uimage']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["uimage"]["name"];
        $filetype = $_FILES["uimage"]["type"];
        $filesize = $_FILES["uimage"]["size"];
    
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
    
        $maxsize = 5 * 1024 * 1024; // 5MB
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
    
        if(in_array($filetype, $allowed)) {
            if(file_exists("user_images/" . $filename)) {
                $filename = uniqid() . $filename;
            }
            move_uploaded_file($_FILES["uimage"]["tmp_name"], "user_images/" . $filename);
            $uimage = $filename;
        } else {
            $error = "Error: There was a problem uploading your file. Please try again.";
        }
    }

    $query = "INSERT INTO user (uname, uemail, uphone, upass, utype, uimage) VALUES ('$uname', '$uemail', '$uphone', '$upass', '$utype', '$uimage')";
    
    if (mysqli_query($con, $query)) {
        $msg="<p class='alert alert-success'>User added successfully</p>";
        header("location:userlist.php?msg=$msg");
        exit();
    } else {
        $error = "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add User</title>

    <!-- Include your CSS files here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include your header here -->
    <?php include("header.php"); ?>

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Add User</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="userlist.php">User List</a></li>
                            <li class="breadcrumb-item active">Add User</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($error)) { ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php } ?>
                            <form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="uname" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="uemail" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Phone</label>
        <input type="tel" name="uphone" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="upass" class="form-control" required>
    </div>
    <div class="form-group">
        <label>User Type</label>
        <select name="utype" class="form-control" required>
            <option value="user">User</option>
            <option value="agent">Agent</option>
        </select>
    </div>
    <div class="form-group">
        <label>User Image</label>
        <input type="file" name="uimage" class="form-control-file">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Add User</button>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your JavaScript files here -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>