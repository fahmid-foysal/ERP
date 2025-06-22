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

if (empty($input["brand_id"]) && empty($input["category_id"])) {
    echo json_encode(["status" => "error", "message" => "Brand Or Category is Required."]);
    exit;
}

$brand_id = $input['brand_id'] ?? null;
$category_id = $input['category_id'] ?? null;

try {
    $sql = "SELECT p.name, p.barcode, p.sale_price, p.bulk_rate, p.img_path, p.in_stock, p.purchase_price,
                   b.brand, c.category
            FROM products p
            INNER JOIN brands b ON p.brand_id = b.id
            INNER JOIN categories c ON p.category_id = c.id";

    $conditions = [];
    $params = [];

    if (!empty($brand_id)) {
        $conditions[] = "p.brand_id = ?";
        $params[] = $brand_id;
    }

    if (!empty($category_id)) {
        $conditions[] = "p.category_id = ?";
        $params[] = $category_id;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$products) {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }

    $base_url = "https://www.mondolmotors.com/api/upload/products/";

    foreach ($products as &$product) {
        $product["img_path"] = !empty($product["img_path"])
            ? $base_url . ltrim(basename($product["img_path"]), '/')
            : null;
    }

    echo json_encode(["status" => "success", "product" => $products]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
