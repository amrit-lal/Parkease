<?php include ('header.php'); ?>
<?php
include('includes/db_connect.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pass = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if user is blocked
        if ($user['status'] === 'blocked') {
            $error = "Your account has been blocked. Please contact support.";
        } elseif (password_verify($pass, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_img"] = $user["profile_img"];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="hero_area">
        <!-- Background Image -->
        <div class="bg-box">
            <img src="images/slider-bg.jpg" alt="background">
        </div>

        <div class="container mt-5">
            <div class="card p-4 mx-auto shadow-lg" style="max-width: 600px; background-color: rgba(21, 82, 99, 1);">
                <h3 class="text-center mb-4" style="max-width: 600px; background-color: #fd7e14; color: rgba(240, 243, 241, 0.95);">Login</h3>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><h6 style="color: rgba(240, 243, 241, 0.95)">Email</h6></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><h6 style="color: rgba(240, 243, 241, 0.95)">Password</h6></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php include ('footer.php'); ?>