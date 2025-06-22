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

$required_fields = ["expense_name", "amount", "option", "note", "date"];

foreach ($required_fields as $field) {
    if (!isset($input_data[$field]) || $input_data[$field] === "") {
        echo json_encode(["status" => "error", "message" => "$field is required."]);
        exit;
    }
}

$expense_name = trim($input_data["expense_name"]);
$amount = filter_var($input_data["amount"], FILTER_VALIDATE_FLOAT);
$option = trim($input_data["option"]);
$note = trim($input_data["note"]);
$date = $input_data["date"];


if ($amount === false || $amount <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid numerical value."]);
    exit;
}

try {

    $stmt = $pdo->prepare("INSERT INTO expense (expense_name, amount, option, note, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$expense_name, $amount, $option, $note, $date]);

    echo json_encode(["status" => "success", "message" => "Expense created successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>