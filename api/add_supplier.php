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

$required_fields = ["name", "phone", "address"];

foreach ($required_fields as $field) {
    if (!isset($input_data[$field]) || $input_data[$field] === "") {
        echo json_encode(["status" => "error", "message" => "$field is required."]);
        exit;
    }
}

$name = htmlspecialchars(trim($input_data["name"]));
$phone = htmlspecialchars(trim($input_data["phone"]));
$address = htmlspecialchars(trim($input_data["address"] ?? ""));


try {

    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE phone = ?");
    $stmt->execute([$phone]);
    $existingRecord = $stmt->fetch();

    if ($existingRecord) {
        echo json_encode(["status" => "error", "message" => "Supplier already exists."]);
        exit;
    } 

    $stmt = $pdo->prepare("INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $address]);


    echo json_encode(["status" => "success", "message" => "Supplier created successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>