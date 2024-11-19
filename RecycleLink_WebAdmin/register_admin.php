<?php
session_start();

// Database connection
 $conn = new mysqli('localhost:3306', 'kayemndjr11_123', 'Kayemondejar123!', 'kayemndjr11_123');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the registration form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new admin user into the database
    $sql = "INSERT INTO admin_users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit;
    } else {
        $error = "Error: " . $conn->error; // Show error if registration fails
    }
}

$conn->close();
?>

<!-- HTML Registration Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Admin Registration</title>
</head>
<body>
<div class="login-container">
    <h2>Admin Registration</h2>
    <form action="register_admin.php" method="POST">

        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Register</button>
    </form>
</div>

    <?php
    // Display error message if registration fails
    if (isset($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    ?>
</body>
</html>
