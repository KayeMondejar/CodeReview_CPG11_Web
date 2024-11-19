<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
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
            background-color: #2e7d32; /* Dark green */
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

        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        .main-content h1 {
            color: #2e7d32;
            font-weight: bold;
            margin-bottom: 20px;
        }

        
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
    </style>
</head>
<body>

    <!-- Side Navigation Bar -->
    <div class="sidebar">
       <h2>
            <img src="logo.png" alt="Logo" class="logo"> 
            <span class="recyclelink-text">RecycleLink</span>
        </h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="collector_admin.php">Collector Management</a></li>
            <li><a href="user_management.php">User Management</a></li>
            <li><a href="transaction_history.php" class="active">Transaction History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    
    <div class="main-content">
        <h1>Transaction History</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Pickup ID</th>
                        <th>User ID</th>
                        <th>Collector ID</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>E-Waste Type</th> 
                        <th>Weight (kg)</th>  
                        <th>Status</th>
                        <th>Pickup Time</th>
                        <th>Pickup Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                    // Database connection
                    $conn = new mysqli('localhost', 'kayemndjr11_123', 'Kayemondejar123!', 'kayemndjr11_123');

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // SQL query to fetch data from the pickups table
                    $sql = "
                    SELECT 
                        pickup_id,
                        user_id,
                        collector_id,
                        address,
                        phone_number,
                        weight_kg,
                        status,
                        pickup_time,
                        pickup_date,
                        e_waste_type
                    FROM 
                        pickups
                    ORDER BY 
                        pickup_date DESC";

                    // Execute the query and handle potential errors
                    if ($result = $conn->query($sql)) {
                        if ($result->num_rows > 0) {
                            // display the data's from the pickup table
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row['pickup_id']) . "</td>
                                        <td>" . htmlspecialchars($row['user_id']) . "</td>
                                        <td>" . htmlspecialchars($row['collector_id']) . "</td>
                                        <td>" . htmlspecialchars($row['address']) . "</td>
                                        <td>" . htmlspecialchars($row['phone_number']) . "</td>
                                        <td>" . htmlspecialchars($row['e_waste_type']) . "</td>
                                        <td>" . htmlspecialchars($row['weight_kg']) . "</td>
                                        <td>" . htmlspecialchars($row['status']) . "</td>
                                        <td>" . htmlspecialchars($row['pickup_time']) . "</td>
                                        <td>" . htmlspecialchars($row['pickup_date']) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No pickups found</td></tr>";
                        }
                    } else {
                        // Output the SQL error for debugging
                        echo "<tr><td colspan='10'>Error: " . $conn->error . "</td></tr>";
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
