<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auser = mysqli_real_escape_string($con, $_POST['auser']);
    $aemail = mysqli_real_escape_string($con, $_POST['aemail']);
    $apass = sha1($_POST['apass']); // Using SHA1 for password hashing
    $adob = mysqli_real_escape_string($con, $_POST['adob']);
    $aphone = mysqli_real_escape_string($con, $_POST['aphone']);

    $query = "INSERT INTO admin (auser, aemail, apass, adob, aphone) VALUES ('$auser', '$aemail', '$apass', '$adob', '$aphone')";
    
    if (mysqli_query($con, $query)) {
        $msg="<p class='alert alert-success'>Admin added successfully</p>";
        header("location:adminlist.php?msg=$msg");
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
    <title>Add Admin</title>

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
                        <h3 class="page-title">Add Admin</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="adminlist.php">Admin List</a></li>
                            <li class="breadcrumb-item active">Add Admin</li>
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
                            <form method="post">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="auser" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="aemail" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="apass" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="adob" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="aphone" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Add Admin</button>
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