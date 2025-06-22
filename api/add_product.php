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

$required_fields = ["name", "barcode", "category_id", "brand_id", "unit", "sale_price", "bulk_rate", "purchase_price"];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            "status" => "error",
            "message" => "$field is required."
        ]);
        exit;
    }
}


$name = htmlspecialchars(strip_tags($_POST["name"]));
$barcode = htmlspecialchars(strip_tags($_POST["barcode"]));
$category_id = (int) $_POST["category_id"];
$brand_id = (int) $_POST["brand_id"];
$unit = htmlspecialchars(strip_tags($_POST["unit"]));
$description = htmlspecialchars(strip_tags($_POST["description"]));
$sale_price = filter_var($_POST["sale_price"], FILTER_VALIDATE_FLOAT);
$bulk_rate = filter_var($_POST["bulk_rate"], FILTER_VALIDATE_FLOAT);
$purchase_price = filter_var($_POST["purchase_price"], FILTER_VALIDATE_FLOAT);

if ($sale_price === false || $sale_price <= 0 || $purchase_price === false || $purchase_price <= 0 || $bulk_rate === false || $bulk_rate <= 0) {
    echo json_encode(["status" => "error", "message" => "Sale price and purchase price must be positive numbers."]);
    exit;
}

$upload_dir = "upload/products/";
$img_path = null;

if (isset($_FILES["img_path"]) && $_FILES["img_path"]["error"] === 0) {
    $file_extension = pathinfo($_FILES["img_path"]["name"], PATHINFO_EXTENSION);
    $unique_name = uniqid("product_", true) . "." . $file_extension;
    $upload_path = $upload_dir . $unique_name;

    $allowed_types = ["image/jpeg", "image/png", "image/gif"];
    if (!in_array($_FILES["img_path"]["type"], $allowed_types)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, PNG, or GIF are allowed."]);
        exit;
    }

    if (!move_uploaded_file($_FILES["img_path"]["tmp_name"], $upload_path)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload product image."]);
        exit;
    }

    $img_path = $upload_path;
}

try {
    $stmt = $pdo->prepare("INSERT INTO products (name, barcode, category_id, brand_id, unit, description, sale_price, bulk_rate, purchase_price, img_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $barcode, $category_id, $brand_id, $unit, $description, $sale_price, $bulk_rate, $purchase_price, $img_path]);

    echo json_encode(["status" => "success", "message" => "Product created successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
