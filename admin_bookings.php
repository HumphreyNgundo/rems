<?php
session_start();
include("config.php");

// Check if user is logged in and is an admin/agent
if (!isset($_SESSION['uid']) || $_SESSION['utype'] != 'agent') {
    header("Location: login.php");
    exit();
}

$query = "SELECT pv.*, p.title, u.uname, u.uemail 
          FROM property_viewings pv 
          JOIN property p ON pv.property_id = p.pid 
          JOIN user u ON pv.user_id = u.uid 
          WHERE p.agent_id = ?
          ORDER BY pv.viewing_date";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta Tags -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Real Estate PHP">
    <meta name="keywords" content="">
    <meta name="author" content="Unicoder">
    <link rel="shortcut icon" href="images/favicon.ico">

    <!--	Fonts
	========================================================-->
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">

    <!--	Css Link
	========================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/layerslider.css">
    <link rel="stylesheet" type="text/css" href="css/color.css" id="color-change">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/flaticon/flaticon.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">


    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
        }

        .table thead th {
            background-color: #218838;
            color: #fff;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .btn {
            margin-right: 5px;
        }

        .header {
            margin-bottom: 20px;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .action-links a {
            margin-right: 10px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>

<body>
    <div id="page-wrapper">
        <!--	Header start  -->
    <?php include("include/header.php"); ?>
            <!--	Header end  -->
        <div class="container">
        <div class="header">
            <h1>Manage Property Viewings</h1>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>User</th>
                    <th>Viewing Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { 
                    while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['uname']); ?></td>
                        <td><?php echo htmlspecialchars($row['viewing_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td class="action-links">
                            <?php if ($row['status'] == 'pending') { ?>
                                <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
                                <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
                            <?php } else { 
                                echo htmlspecialchars(ucfirst($row['status'])); 
                            } ?>
                        </td>
                    </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No bookings found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    </div>

    
</body>

</html>
