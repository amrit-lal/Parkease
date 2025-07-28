<?php
session_start();
include('includes/db_connect.php');

// Set content type header first
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate input
        if (!isset($_POST['booking_id'], $_POST['amount'], $_POST['additional'], $_POST['discount'])) {
            throw new Exception('Missing required parameters');
        }

        $booking_id = intval($_POST['booking_id']);
        $amount = floatval($_POST['amount']);
        $additional = floatval($_POST['additional']);
        $discount = floatval($_POST['discount']);

        // Validate amounts
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        // Calculate total amount
        $total_amount = $amount + $additional - $discount;

        // Update booking with bill information
        $stmt = $conn->prepare("UPDATE bookings SET 
            bill_amount = ?, 
            additional_charges = ?,
            discount = ?,
            total_amount = ?,
            bill_generated_at = NOW(),
            payment_status = 'Pending'
            WHERE id = ? AND status IN ('Occupied', 'Booked')");
        
        if (!$stmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }

        $stmt->bind_param("dddds", $amount, $additional, $discount, $total_amount, $booking_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows === 0) {
                throw new Exception('No booking found or booking not in valid status');
            }

            $response = [
                'success' => true,
                'amount' => $amount,
                'additional' => $additional,
                'discount' => $discount,
                'total_amount' => $total_amount,
                'booking_id' => $booking_id
            ];
        } else {
            throw new Exception('Database error: ' . $stmt->error);
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit();
?>