<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as admin."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
    exit;
}

$json_data = file_get_contents("php://input");
$input_data = json_decode($json_data, true);

if ($json_data === false || is_null($input_data)) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data or empty request."]);
    exit;
}

$required_fields = ["cus_name", "cus_phone", "amount", "paid", "date"];

foreach ($required_fields as $field) {
    if (!isset($input_data[$field]) || $input_data[$field] === "") {
        echo json_encode(["status" => "error", "message" => "$field is required."]);
        exit;
    }
}

$cus_name = htmlspecialchars(trim($input_data["cus_name"]));
$cus_phone = htmlspecialchars(trim($input_data["cus_phone"]));
$cus_address = htmlspecialchars(trim($input_data["cus_address"] ?? ""));
$service_name = htmlspecialchars(trim($input_data["service_name"]));
$amount = filter_var($input_data["amount"], FILTER_VALIDATE_FLOAT);
$paid = filter_var($input_data["paid"], FILTER_VALIDATE_FLOAT);
$date = $input_data["date"];

if ($amount === false || $amount <= 0 || $paid === false || $paid < 0) {
    echo json_encode(["status" => "error", "message" => "Invalid numerical values."]);
    exit;
}

$cus_id = 0;

try {

    $stmt = $pdo->prepare("SELECT * FROM customers WHERE phone = ?");
    $stmt->execute([$cus_phone]);
    $existingRecord = $stmt->fetch();

    if ($existingRecord) {
        $stmt = $pdo->prepare("UPDATE customers SET address = ?, name = ? WHERE id = ?");
        $stmt->execute([$cus_address, $cus_name, $existingRecord['id']]);
        $cus_id = $existingRecord['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (phone, name, address) VALUES (?, ?, ?)");
        $stmt->execute([$cus_phone, $cus_name, $cus_address]);
        $cus_id = $pdo->lastInsertId();

        if (!$cus_id) {
            echo json_encode(["status" => "error", "message" => "Failed to create customer record."]);
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO service_invoice (cus_id, service, amount, paid, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$cus_id, $service_name, $amount, $paid, $date]);


    echo json_encode(["status" => "success", "message" => "Invoice created successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>