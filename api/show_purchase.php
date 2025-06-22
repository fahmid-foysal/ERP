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

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing purchase ID."]);
    exit;
}
$purchase_id = (int) $_GET["id"];

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.date, 
            p.paid AS total_paid, 
            s.name AS supplier_name, 
            s.phone AS supplier_phone
        FROM purchase p
        INNER JOIN suppliers s ON s.id = p.supplier_id
        WHERE p.id = ?
    ");
    $stmt->execute([$purchase_id]);
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$purchase) {
        echo json_encode(["status" => "error", "message" => "Purchase not found."]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            pp.barcode,
            pp.quantity,
            pp.rate,
            pr.name AS product_name,
            b.brand,
            c.category
        FROM purchased_products pp
        INNER JOIN products pr ON pr.barcode = pp.barcode
        INNER JOIN brands b ON b.id = pr.brand_id
        INNER JOIN categories c ON c.id = pr.category_id
        WHERE pp.purchase_id = ?
    ");
    $stmt->execute([$purchase_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_amount = 0;
    foreach ($products as $product) {
        $total_amount += $product["quantity"] * $product["rate"];
    }

    $total_due = $total_amount - $purchase["total_paid"];

    $response = [
        "status" => "success",
        "purchase_details" => [
            "date" => $purchase["date"],
            "supplier_name" => $purchase["supplier_name"],
            "supplier_phone" => $purchase["supplier_phone"],
            "total_amount" => number_format($total_amount, 2, '.', ''),
            "total_paid" => number_format($purchase["total_paid"], 2, '.', ''),
            "total_due" => number_format($total_due, 2, '.', ''),
            "products" => $products
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
