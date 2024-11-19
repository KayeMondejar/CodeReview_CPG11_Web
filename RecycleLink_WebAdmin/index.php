<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Database connection
$conn = new mysqli('localhost:3306', 'kayemndjr11_123', 'Kayemondejar123!', 'kayemndjr11_123');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total users //
$user_query = "SELECT COUNT(*) AS total_users FROM users";
$user_result = $conn->query($user_query);
$total_users = $user_result->fetch_assoc()['total_users'];

// Get total collectors //
$collector_query = "SELECT COUNT(*) AS total_collectors FROM collectors";
$collector_result = $conn->query($collector_query);
$total_collectors = $collector_result->fetch_assoc()['total_collectors'];

// Get total waste collected //
$waste_query = "SELECT SUM(weight_kg) AS total_waste FROM pickups WHERE status = 'Completed'";
$waste_result = $conn->query($waste_query);
$total_waste = $waste_result->fetch_assoc()['total_waste'];

// Get counts of each type of e-waste //
$ewaste_query = "SELECT e_waste_type, COUNT(*) AS count FROM pickups WHERE status = 'Completed' AND e_waste_type IN ('Type 1', 'Type 2', 'Type 3') GROUP BY e_waste_type";
$ewaste_result = $conn->query($ewaste_query);

// Store e-waste data for chart
$ewaste_types = [];
$ewaste_counts = [];
$all_types = ["Type 1", "Type 2", "Type 3"];
$type_count = array_fill_keys($all_types, 0);

while ($row = $ewaste_result->fetch_assoc()) {
    $type_count[$row['e_waste_type']] = $row['count'];
}

$ewaste_types = array_keys($type_count);
$ewaste_counts = array_values($type_count);

$colors = [
    '#4CAF50', // Type 1 - Green
    '#FF9800', // Type 2 - Orange
    '#2196F3'  // Type 3 - Blue
];

// count of approved pickups by date
$approved_query = "SELECT pickup_date, COUNT(*) AS total_approved 
                   FROM pickups 
                   WHERE status = 'Approved' 
                   GROUP BY pickup_date 
                   ORDER BY pickup_date ASC";

$approved_result = $conn->query($approved_query);

$approved_dates = [];
$approved_counts = [];

while ($row = $approved_result->fetch_assoc()) {
    $approved_dates[] = $row['pickup_date'];
    $approved_counts[] = $row['total_approved'];
}

//  count of completed pickups by date //
$completed_query = "SELECT pickup_date, COUNT(*) AS total_completed 
                    FROM pickups 
                    WHERE status = 'Completed' 
                    GROUP BY pickup_date 
                    ORDER BY pickup_date ASC";

$completed_result = $conn->query($completed_query);

$completed_dates = [];
$completed_counts = [];

while ($row = $completed_result->fetch_assoc()) {
    $completed_dates[] = $row['pickup_date'];
    $completed_counts[] = $row['total_completed'];
}

// Pass PHP variables to JavaScript // 
$approvedDatesJS = json_encode($approved_dates);
$approvedCountsJS = json_encode($approved_counts);
$completedDatesJS = json_encode($completed_dates);
$completedCountsJS = json_encode($completed_counts);

// Fetch the admin's name from the session
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecycleLink Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js library -->
</head>
<body>
    <!-- Side Navigation Bar -->
    <div class="sidebar">
        <h2>
            <img src="logo.png" alt="Logo" class="logo"> <span class="recyclelink-text">RecycleLink</span>
        </h2>
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="collector_admin.php">Collector Management</a></li>
            <li><a href="user_management.php">User Management</a></li>
            <li><a href="transaction_history.php">Transaction History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="admin-welcome">
            <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h2>
        </div>

        <h1>Admin Dashboard</h1>
        <div class="dashboard-cards">
            <div class="card">
                <h2>Total Users</h2>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h2>Number of Collectors</h2>
                <p><?php echo $total_collectors; ?></p>
            </div>
            <div class="card">
                <h2>Waste Collected (kg)</h2>
                <p><?php echo $total_waste . " kg"; ?></p>
            </div>
        </div>

        <div class="chart-wrapper">
            <div class="ewaste-chart">
                <div class="card">
                    <h2>Types of E-Waste Collected</h2>
                    <canvas id="ewasteChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="line-chart-wrapper">
                <div class="card lineChart">
                    <h2>Total Approved and Completed Pickups Over Time</h2>
                    <canvas id="pickupsLineChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pie Chart for E-Waste Types
        const ewasteTypes = <?php echo json_encode($ewaste_types); ?>;
        const ewasteCounts = <?php echo json_encode($ewaste_counts); ?>;
        const colors = <?php echo json_encode($colors); ?>;

        const ewasteCtx = document.getElementById('ewasteChart').getContext('2d');
        const ewasteChart = new Chart(ewasteCtx, {
            type: 'pie',
            data: {
                labels: ewasteTypes,
                datasets: [{
                    data: ewasteCounts,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Line Chart for Approved and Completed Pickups
        const approvedDates = <?php echo $approvedDatesJS; ?>;
        const approvedCounts = <?php echo $approvedCountsJS; ?>;
        const completedDates = <?php echo $completedDatesJS; ?>;
        const completedCounts = <?php echo $completedCountsJS; ?>;

        const pickupsCtx = document.getElementById('pickupsLineChart').getContext('2d');
        const pickupsLineChart = new Chart(pickupsCtx, {
            type: 'line',
            data: {
                labels: approvedDates, // Assumes both datasets use the same labels
                datasets: [
                    {
                        label: 'Approved Pickups',
                        data: approvedCounts,
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Completed Pickups',
                        data: completedCounts,
                        borderColor: '#2196F3',
                        backgroundColor: 'rgba(33, 150, 243, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Pickups'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
