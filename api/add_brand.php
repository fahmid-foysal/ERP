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

if (!$input || !isset($input["brand"]) || empty(trim($input["brand"]))) {
    echo json_encode(["status" => "error", "message" => "Missing or empty brand name."]);
    exit;
}

$brand = trim($input["brand"]);

try {
    $stmt = $pdo->prepare("SELECT id FROM brands WHERE brand = ?");
    $stmt->execute([$brand]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Brand already exists."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO brands (brand) VALUES (?)");
    $stmt->execute([$brand]);
    
    echo json_encode(["status" => "success", "message" => "Brand created successfully.", "brand_id" => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>