<?php
session_start();

// Database connection
$conn = new mysqli('localhost:3306', 'kayemndjr11_123', 'Kayemondejar123!', 'kayemndjr11_123');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message variable
$error = "";

// Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    // Bind the parameter (the username) to the statement
    $stmt->bind_param("s", $username);
    // Execute the statement
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();

    // Check if the user exists and verify the password
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables for logged-in user
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['username']; // Store the admin's username in the session
            $_SESSION['is_admin'] = true;

            // Redirect to index.php
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!-- HTML Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="login.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2> 
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>

        <!-- Display the error message if there is any -->
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Registration link below the form -->
        <p class="register-link">Don't have an account? <a href="register_admin.php">Register here</a></p>
    </div>
</body>
</html>
