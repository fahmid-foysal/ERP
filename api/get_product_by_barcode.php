<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start(); 
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as admin."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET."]);
    exit;
}

if (!isset($_GET["barcode"]) || empty(trim($_GET["barcode"]))) {
    echo json_encode(["status" => "error", "message" => "Missing or empty barcode parameter."]);
    exit;
}

$barcode = trim($_GET["barcode"]);

try {
    $stmt = $pdo->prepare("
        SELECT p.name, p.barcode, p.sale_price, p.bulk_rate, p.img_path, p.purchase_price,
               b.brand, c.category
        FROM products p
        INNER JOIN brands b ON p.brand_id = b.id
        INNER JOIN categories c ON p.category_id = c.id
        WHERE p.barcode = ?
    ");
    $stmt->execute([$barcode]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }

    $base_url = "https://www.mondolmotors.com/api/upload/products/";
    if (!empty($product["img_path"])) {
        $product["img_path"] = $base_url . ltrim(basename($product["img_path"]), '/'); 
    } else {
        $product["img_path"] = null;
    }

    echo json_encode(["status" => "success", "product" => $product]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
