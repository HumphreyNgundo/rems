<?php
session_start();
include("config.php");

// Check if user is logged in and is an admin/agent
if (!isset($_SESSION['uid']) || $_SESSION['utype'] != 'agent') {
    header("Location: login.php");
    exit();
}

function sendBookingEmail($to, $propertyTitle, $viewingDate, $status) {
    $subject = "Property Viewing Update - " . ucfirst($status);
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            h1 { color: #28a745; }
            .details { background-color: #f4f4f4; padding: 15px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Property Viewing " . ucfirst($status) . "</h1>
            <p>Dear User,</p>
            <p>Your viewing request has been " . $status . ".</p>
            <div class='details'>
                <p><strong>Property:</strong> " . htmlspecialchars($propertyTitle) . "</p>
                <p><strong>Viewing Date:</strong> " . $viewingDate . "</p>
                <p><strong>Status:</strong> " . ucfirst($status) . "</p>
            </div>
            <p>If you have any questions, please contact us.</p>
            <p>Best regards,<br>Your Real Estate Team</p>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: youremail@example.com" . "\r\n";

    return mail($to, $subject, $message, $headers);
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

    if ($id && ($action == 'approve' || $action == 'reject')) {
        // Verify that the booking belongs to a property managed by this agent
        $verify_query = "SELECT pv.id FROM property_viewings pv 
                         JOIN property p ON pv.property_id = p.pid 
                         WHERE pv.id = ? AND p.agent_id = ?";
        $verify_stmt = $con->prepare($verify_query);
        $verify_stmt->bind_param("ii", $id, $_SESSION['uid']);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();

        if ($verify_result->num_rows > 0) {
            $query = "UPDATE property_viewings SET status = ? WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("si", $action, $id);

            if ($stmt->execute()) {
                // Fetch user email and booking details
                $emailQuery = "SELECT u.uemail, p.title, pv.viewing_date 
                               FROM property_viewings pv 
                               JOIN user u ON pv.user_id = u.uid 
                               JOIN property p ON pv.property_id = p.pid 
                               WHERE pv.id = ?";
                $emailStmt = $con->prepare($emailQuery);
                $emailStmt->bind_param("i", $id);
                $emailStmt->execute();
                $emailResult = $emailStmt->get_result();
                $emailData = $emailResult->fetch_assoc();

                // Send email
                if (sendBookingEmail($emailData['uemail'], $emailData['title'], $emailData['viewing_date'], $action)) {
                    $_SESSION['success_message'] = "Booking updated successfully and email sent.";
                } else {
                    $_SESSION['error_message'] = "Booking updated but there was an error sending the email.";
                }

                $emailStmt->close();
            } else {
                $_SESSION['error_message'] = "Error updating booking.";
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = "You don't have permission to update this booking.";
        }

        $verify_stmt->close();
    } else {
        $_SESSION['error_message'] = "Invalid request.";
    }
} else {
    $_SESSION['error_message'] = "Missing parameters.";
}

// Redirect back to admin_bookings.php
header("Location: admin_bookings.php");
exit();
?>