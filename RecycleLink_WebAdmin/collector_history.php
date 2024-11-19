<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$host = 'localhost:3306';
$user = 'kayemndjr11_123';
$password = 'Kayemondejar123!';
$database = 'kayemndjr11_123';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to connect to the database.'
    ]);
    exit();
}

$collector_id = isset($_GET['collector_id']) ? filter_var($_GET['collector_id'], FILTER_VALIDATE_INT) : 0;

if (!$collector_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid user ID.'
    ]);
    exit();
}

$sql = "SELECT pickup_id, weight_kg, status, pickup_date, pickup_time, address, phone_number, 
               e_waste_type, IFNULL(reason, 'N/A') AS cancellation_reason
        FROM pickups
        WHERE user_id = ?
        ORDER BY pickup_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $collector_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = [
            'waste_type' => $row['e_waste_type'] ?? 'Unknown',
            'weight_kg' => $row['weight_kg'] ?? 'Unknown',
            'address' => $row['address'],
            'contact_num' => $row['phone_number'],
            'pickup_date' => $row['pickup_date'],
            'pickup_time' => $row['pickup_time'],
            'status' => $row['status'],
            'cancellation_reason' => $row['cancellation_reason'] // Fetching from 'reason' column
        ];
    }
    echo json_encode([
        'status' => 'success',
        'transactions' => $transactions
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No transaction history found for this user.'
    ]);
}

$stmt->close();
$conn->close();
?>