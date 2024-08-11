<?php
session_start();
include("config.php");

// Check if user is logged in and is an agent
if (!isset($_SESSION['uid']) || $_SESSION['utype'] != 'agent') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_STRING);
    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_STRING);

    if ($date && $start_time && $end_time) {
        $query = "INSERT INTO agent_availability (agent_id, available_date, start_time, end_time) 
                  VALUES (?, ?, ?, ?) 
                  ON DUPLICATE KEY UPDATE start_time = VALUES(start_time), end_time = VALUES(end_time)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("isss", $_SESSION['uid'], $date, $start_time, $end_time);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Availability updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating availability.";
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Invalid input data.";
    }
}

// Fetch current availability
$query = "SELECT * FROM agent_availability WHERE agent_id = ? ORDER BY available_date";
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
    <title>Set Availability</title>
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
    <h1>Set Your Availability</h1>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<p style='color: green;'>" . $_SESSION['success_message'] . "</p>";
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']);
    }
    ?>

    <form method="post" action="">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required>

        <button type="submit">Set Availability</button>
    </form>

    <h2>Current Availability</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['available_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>