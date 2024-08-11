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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <!-- Add your CSS links here -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Manage Property Viewings</h1>
    <table>
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
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['uname']); ?></td>
                    <td><?php echo htmlspecialchars($row['viewing_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'pending') { ?>
                            <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=approve">Approve</a>
                            <a href="update_booking.php?id=<?php echo $row['id']; ?>&action=reject">Reject</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>