<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as admin."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use PUT."]);
    exit;
}

$json_data = file_get_contents("php://input");
$input_data = json_decode($json_data, true);

if ($json_data === false || is_null($input_data)) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data or empty request."]);
    exit;
}

$required_fields = ["purchase_id", "amount"];

foreach ($required_fields as $field) {
    if (!isset($input_data[$field]) || $input_data[$field] === "") {
        echo json_encode(["status" => "error", "message" => "$field is required."]);
        exit;
    }
}

$purchase_id = trim($input_data["purchase_id"]);
$amount = filter_var($input_data["amount"], FILTER_VALIDATE_FLOAT);




if ($amount === false || $amount <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid numerical value."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT supplier_id FROM purchase WHERE id = ?");
    $stmt->execute([$purchase_id]);
    $check = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$check) {
        echo json_encode(["status" => "error", "message" => "Invalid purchase_id."]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE purchase SET paid = paid + ? WHERE id = ?");
    $stmt->execute([$amount, $purchase_id]);



    $stmt = $pdo->prepare("UPDATE suppliers SET payable = payable - ? WHERE id = ?");
    $stmt->execute([$due, $check["supplier_id"]]);
    

    echo json_encode(["status" => "success", "message" => "Purchase updated successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>