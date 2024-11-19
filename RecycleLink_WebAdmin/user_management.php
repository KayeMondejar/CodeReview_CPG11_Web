<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit;
}

// will get the whoever admin username will login
$admin_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background-color: #2e7d32; 
            padding-top: 20px;
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar h2 img {
            width: 50px; 
            height: 50px;
            margin-right: 10px;
			vertical-align: middle;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px;
            text-align: left;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            padding: 10px 15px;
            display: block;
        }

        .sidebar ul li a:hover {
            background-color: #388e3c; 
            border-radius: 5px;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        .main-content h1 {
            color: #2e7d32;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table thead {
            background: #2e7d32;
            color: white;
        }

        table thead th {
            padding: 15px;
            text-align: left;
        }

        table tbody tr {
            border-bottom: 1px solid #e5e5e5;
        }

        table tbody tr:hover {
            background: #f1f8e9; 
        }

        table td {
            padding: 12px;
        }

        /* Success and Error Message  */
        #message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            display: none;
        }

        #message.success {
            background-color: #d4edda;
            color: #155724;
        }

        #message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        
        .admin-welcome {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 18px;
            font-weight: bold;
            color: black; 
        }
    </style>
</head>
<body>
    <!-- Admin Welcome Message -->
  

   
    <div class="sidebar">
                <h2>
            <img src="logo.png" alt="Logo" class="logo"> 
            <span class="recyclelink-text">RecycleLink</span>
        </h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="collector_admin.php">Collector Management</a></li>
            <li><a href="user_management.php" class="active">User Management</a></li>
            <li><a href="transaction_history.php">Transaction History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Registered Users</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $conn = new mysqli('localhost:3306', 'kayemndjr11_123', 'Kayemondejar123!', 'kayemndjr11_123');

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // getting registered users from the database
                    $sql = "SELECT full_name, email, phone, address, registration_date FROM users";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['full_name']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['address']) . "</td>
                                    <td>" . htmlspecialchars($row['registration_date']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No registered users</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
