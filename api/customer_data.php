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

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input."]);
    exit;
}

if (!isset($input["cus_phone"]) || empty(trim($input["cus_phone"]))) {
    echo json_encode(["status" => "error", "message" => "Missing or empty field: Customer Phone"]);
    exit;
}


$cus_phone = trim($input["cus_phone"]);

$stmt = $pdo->prepare("SELECT name, address FROM customers WHERE phone = ?");
$stmt->execute([$cus_phone]);
$customer_data = $stmt->fetch(PDO::FETCH_ASSOC);

if($customer_data){
    
echo json_encode([
    "status" => "success",
    "customer_data" => $customer_data,
    "response_code" => 1
]);
}else{

echo json_encode([
    "status" => "success",
    "response_code" => 0
]);
}
?>