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
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
}

$email = filter_var($data["email"], FILTER_SANITIZE_EMAIL);
$password = password_hash($data["password"], PASSWORD_BCRYPT); 
$name = $data["name"];

try {
    $stmt = $pdo->prepare("SELECT id FROM admin WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists."]);
        exit;
    }

   
    $stmt = $pdo->prepare("INSERT INTO admin (email, password, name) VALUES (?, ?, ?)");
    $stmt->execute([$email, $password, $name]);

    echo json_encode(["status" => "success", "message" => "API CHECK successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>