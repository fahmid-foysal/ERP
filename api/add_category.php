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

if (!$input || !isset($input["category"]) || empty(trim($input["category"]))) {
    echo json_encode(["status" => "error", "message" => "Missing or empty brand name."]);
    exit;
}

$category = trim($input["category"]);

try {
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE category = ?");
    $stmt->execute([$brand]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "category already exists."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO categories (category) VALUES (?)");
    $stmt->execute([$category]);
    
    echo json_encode(["status" => "success", "message" => "category created successfully.", "category_id" => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
