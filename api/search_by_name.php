<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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

if (empty($input["product_name"]) || empty($input["category_id"])) {
    echo json_encode(["status" => "error", "message" => "Product name and Category ID are required."]);
    exit;
}

$product_name = trim($input["product_name"]);
$category_id = $input["category_id"];

try {
    $stmt = $pdo->prepare("
        SELECT p.name, p.barcode, p.sale_price, p.bulk_rate, p.purchase_price, p.in_stock, p.img_path,
               b.brand, c.category
        FROM products p
        INNER JOIN brands b ON p.brand_id = b.id
        INNER JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ? AND p.name LIKE ?
    ");

    $searchTerm = '%' . $product_name . '%'; 
    $stmt->execute([$category_id, $searchTerm]);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$products) {
        echo json_encode(["status" => "error", "message" => "No matching products found."]);
        exit;
    }

    $base_url = "https://www.mondolmotors.com/api/upload/products/";

    foreach ($products as &$product) {
        $product["img_path"] = !empty($product["img_path"])
            ? $base_url . ltrim(basename($product["img_path"]), '/')
            : null;
    }

    echo json_encode(["status" => "success", "products" => $products]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
