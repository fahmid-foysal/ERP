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


try {

    $stmt = $pdo->prepare("SELECT p.id, p.name, p.barcode, p.sale_price, p.purchase_price, p.img_path, p.bulk_rate, p.in_stock, 
                                b.brand, c.category
                                  FROM    products p
                                INNER JOIN 
                                    brands b ON p.brand_id = b.id
                                INNER JOIN 
                                    categories c ON p.category_id = c.id
                                ");
    $stmt->execute();
    $deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $base_url = "https://www.mondolmotors.com/api/upload/products/";

foreach ($deposits as &$event) {
    if (!empty($event["img_path"])) {
        $event["img_path"] = $base_url . ltrim(basename($event["img_path"]), '/'); 
    } else {
        $event["img_path"] = null; 
    }
}

    echo json_encode(["status" => "success", "products" => $deposits]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>