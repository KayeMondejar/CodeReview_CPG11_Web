<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Management</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo {
            width: 50px;  
            height: 50px;
            margin-right: 10px;  
            vertical-align: middle;  
        }

        .recyclelink-text {
            font-size: 22px; 
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
            background-color: #388e3c; /* Lighter green on hover */
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
            background: #f1f8e9; /* Light green hover */
        }

        table td {
            padding: 12px;
        }

        /* Status Dropdown */
        select {
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .status-pending {
            background-color: #FFD700; /* Yellow */
            color: black;
        }

        .status-approved {
            background-color: #2e7d32; /* Green */
            color: white;
        }

        .status-rejected {
            background-color: #FF4500; /* Red */
            color: white;
        }

        /* Success/Error Message */
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <?php
    // Database connection
    $conn = new mysqli('localhost:3306', 'kayemndjr11_123', 'Kayemondejar123!', 'kayemndjr11_123');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>

    <!-- Side Navigation Bar -->
    <div class="sidebar">
        <h2>
            <img src="logo.png" alt="Logo" class="logo"> <!-- Replace 'logo.png' with your logo file path -->
            <span class="recyclelink-text">RecycleLink</span>
        </h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="collector_admin.php" class="active">Collector Management</a></li>
            <li><a href="user_management.php">User Management</a></li>
            <li><a href="transaction_history.php">Transaction History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
		     
        <h1>Collector Management</h1>
        <!-- Success/Error Message -->
        <div id="message"></div>

        <div class="table-container">
            <!-- Applicants Table -->
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Police Clearance</th>
                        <th>Valid ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from collectors table
                    $sql = "SELECT collector_id, fullname, email, phone, address, police_clearance, valid_id, status FROM collectors";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = '';
                            switch ($row['status']) {
                                case 'Pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'Approved':
                                    $statusClass = 'status-approved';
                                    break;
                                case 'Rejected':
                                    $statusClass = 'status-rejected';
                                    break;
                            }

                            echo "<tr id='row-" . $row['collector_id'] . "'>
                                <td>" . $row['fullname'] . "</td>
                                <td>" . $row['email'] . "</td>
                                <td>" . $row['phone'] . "</td>
                                <td>" . $row['address'] . "</td>
                                <td><a href='/api/" . $row['police_clearance'] . "' target='_blank'>View Clearance</a></td>
                                <td><a href='/api/" . $row['valid_id'] . "' target='_blank'>View Valid ID</a></td>
                                <td>
                                    <select onchange='updateStatus(" . $row['collector_id'] . ", this.value)' 
                                            id='status-" . $row['collector_id'] . "' 
                                            class='" . $statusClass . "'>
                                        <option value='Pending' " . ($row['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                        <option value='Approved' " . ($row['status'] == 'Approved' ? 'selected' : '') . ">Approved</option>
                                        <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                                    </select>
                                </td>
                              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No pending applications</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateStatus(collectorId, status) {
            console.log("Updating ID: " + collectorId + ", Status: " + status);
            $.ajax({
                url: 'api/update_status.php',
                type: 'POST',
                data: {
                    id: collectorId,
                    status: status
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        var statusElement = $('#status-' + collectorId);
                        statusElement.removeClass('status-pending status-approved status-rejected');

                        switch (status) {
                            case 'Pending':
                                statusElement.addClass('status-pending');
                                break;
                            case 'Approved':
                                statusElement.addClass('status-approved');
                                break;
                            case 'Rejected':
                                statusElement.addClass('status-rejected');
                                break;
                        }

                        $('#message').text(response.message).addClass('success').show().delay(3000).fadeOut();
                    } else {
                        $('#message').text(response.message).addClass('error').show().delay(3000).fadeOut();
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error:", error);
                    $('#message').text("An error occurred while updating status.").addClass('error').show().delay(3000).fadeOut();
                }
            });
        }
    </script>
</body>
</html>
