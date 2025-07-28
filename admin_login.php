<?php 
include('h1.php');
include('includes/db_connect.php'); // Include the database connection file

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $error = "";

    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password!";
    } else {
        try {
            // Using PDO for database operations (as defined in db_connect.php)
            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // Prepare statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header("Location: admin_dashboard.php");
                exit();
            } else {
                // Generic error message (don't reveal whether username or password was wrong)
                $error = "Invalid login credentials!";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "A system error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <!-- Background Image -->
    <div class="bg-box">
        <img src="images/slider-bg.jpg" alt="background">
    </div>
    
    <div class="container mt-5">
        <div class="card mx-auto shadow-sm p-4" style="max-width: 400px; background-color: rgba(21, 82, 99, 1)">
            <h3 class="text-center mb-4" style="background-color: #fd7e14; color: rgba(240, 243, 241, 0.95)">Admin Login</h3>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><h6 style="color: rgba(240, 243, 241, 0.95)">Username</h6></label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label"><h6 style="color: rgba(240, 243, 241, 0.95)">Password</h6></label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include('footer.php'); ?>