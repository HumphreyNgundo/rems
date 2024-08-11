<?php
include("config.php");

$date = $_POST['date'];
$property_id = $_POST['property_id'];

// Get the agent for this property
$agent_query = "SELECT agent_id FROM property WHERE pid = ?";
$agent_stmt = $con->prepare($agent_query);
$agent_stmt->bind_param("i", $property_id);
$agent_stmt->execute();
$agent_result = $agent_stmt->get_result();
$agent_id = $agent_result->fetch_assoc()['agent_id'];

// Initialize array for available slots
$available_slots = ["08:00", "09:00", "10:00", "11:00", "12:00", 
                    "13:00", "14:00", "15:00", "16:00", "17:00"];

// Check agent availability for the selected date
$availability_query = "SELECT start_time, end_time FROM agent_availability 
                       WHERE agent_id = ? AND available_date = ?";
$availability_stmt = $con->prepare($availability_query);
$availability_stmt->bind_param("is", $agent_id, $date);
$availability_stmt->execute();
$availability_result = $availability_stmt->get_result();

if ($availability_result->num_rows > 0) {
    $row = $availability_result->fetch_assoc();
    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);

    // Keep only slots within agent's availability
    $available_slots = array_filter($available_slots, function($slot) use ($start_time, $end_time) {
        $slot_time = strtotime($slot);
        return $slot_time >= $start_time && $slot_time < $end_time;
    });
}

// Get booked slots
$booked_query = "SELECT TIME_FORMAT(viewing_date, '%H:%i') as booked_time 
                 FROM property_viewings 
                 WHERE property_id = ? AND DATE(viewing_date) = ? AND status != 'rejected'";
$booked_stmt = $con->prepare($booked_query);
$booked_stmt->bind_param("is", $property_id, $date);
$booked_stmt->execute();
$booked_result = $booked_stmt->get_result();

$booked_slots = [];
while ($row = $booked_result->fetch_assoc()) {
    $booked_slots[] = $row['booked_time'];
}

// Remove booked slots from available slots
$available_slots = array_diff($available_slots, $booked_slots);

// Re-index the array
$available_slots = array_values($available_slots);

echo json_encode($available_slots);

$agent_stmt->close();
$availability_stmt->close();
$booked_stmt->close();
?>