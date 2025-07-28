<?php
// delete_testimonial.php
session_start();
include('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $testimonial_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Verify the testimonial belongs to the user before deleting
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $testimonial_id, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Testimonial deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting testimonial";
    }
}

header("Location: testimonial.php");
exit();
?>