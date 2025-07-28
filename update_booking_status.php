<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include('includes/db_connect.php');

// Debug logging
error_log("Cancellation request received: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status
    $allowed_statuses = ['Cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error'] = "Invalid status update";
        error_log("Invalid status attempt: $new_status");
        header("Location: view_bookings.php");
        exit();
    }
    
    // Get complete booking details with transaction
    $conn->begin_transaction();
    
    try {
        // First get slot_id before updating
        $booking_query = $conn->query("SELECT * FROM bookings WHERE id = $booking_id FOR UPDATE");
        if ($booking_query->num_rows === 0) {
            throw new Exception("Booking not found");
        }
        
        $booking = $booking_query->fetch_assoc();
        $slot_id = $booking['slot_id'];
        $current_status = $booking['status'];
        
        // Update booking status
        $update_stmt = $conn->prepare("UPDATE bookings SET 
            status = ?,
            cancelled_at = CASE WHEN ? = 'Cancelled' THEN NOW() ELSE NULL END,
            updated_at = NOW()
            WHERE id = ?");
        $update_stmt->bind_param("ssi", $new_status, $new_status, $booking_id);
        $update_stmt->execute();
        
        if ($update_stmt->affected_rows === 0) {
            throw new Exception("No rows affected - booking update failed");
        }
        
        // Only make slot available if cancelling an active booking
        if (in_array($current_status, ['Booked', 'Occupied'])) {
            $update_slot = $conn->query("UPDATE slots SET status = 'Available' WHERE id = $slot_id");
            
            if (!$update_slot) {
                throw new Exception("Slot update failed: " . $conn->error);
            }
        }
        
        $conn->commit();
        $_SESSION['success'] = "Booking #$booking_id status updated to $new_status successfully";
        error_log("Successfully cancelled booking $booking_id");
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error updating booking: " . $e->getMessage();
        error_log("Error cancelling booking: " . $e->getMessage());
    }
    
    header("Location: view_bookings.php");
    exit();
} else {
    header("Location: view_bookings.php");
    exit();
}
?>