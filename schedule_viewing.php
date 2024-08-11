<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['uid'])) {
    $property_id = filter_input(INPUT_POST, 'property_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['uid'];
    $selected_date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
    $viewing_time = filter_input(INPUT_POST, 'viewing_time', FILTER_SANITIZE_STRING);

    if ($property_id && $selected_date && $viewing_time) {
        $viewing_date = date('Y-m-d H:i:s', strtotime("$selected_date $viewing_time"));

        // Check for double bookings
        $check_query = "SELECT id FROM property_viewings WHERE property_id = ? AND viewing_date = ? AND status != 'rejected'";
        $check_stmt = $con->prepare($check_query);
        $check_stmt->bind_param("is", $property_id, $viewing_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            $query = "INSERT INTO property_viewings (property_id, user_id, viewing_date) VALUES (?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("iis", $property_id, $user_id, $viewing_date);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Viewing request submitted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error submitting viewing request.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'This time slot is no longer available. Please choose another.']);
        }

        $check_stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request or user not logged in.']);
}
?>