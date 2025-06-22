<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Invoice ID is required"]);
    exit;
}

$invoice_id = intval($_GET['id']); 

try {
    $stmt = $pdo->prepare("
        SELECT i.id as invoice_id, i.date, i.discount, i.paid,
               c.name as cus_name, c.phone as cus_phone
        FROM invoices i
        INNER JOIN customers c ON c.id = i.cus_id
        WHERE i.id = ?  
    ");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        echo json_encode(["status" => "error", "message" => "Invoice not found"]);
        exit;
    }

    $itemStmt = $pdo->prepare("
        SELECT i.quantity, i.rate, i.barcode,
               p.name as product_name, c.category, b.brand
        FROM invoice_items i
        INNER JOIN products p ON p.barcode = i.barcode
        INNER JOIN categories c ON c.id = p.category_id
        INNER JOIN brands b ON b.id = p.brand_id
        WHERE invoice_id = ?
    ");
    $itemStmt->execute([$invoice_id]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    $serviceStmt = $pdo->prepare("
        SELECT service_price, service_name
        FROM invoice_services
        WHERE invoice_id = ?
    ");
    $serviceStmt->execute([$invoice_id]);
    $services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAmount = 0;
    foreach ($items as $item) {
        $totalAmount += $item['quantity'] * $item['rate'];
    }

    foreach ($services as $service) {
        $totalAmount += $service['service_price'];
    }

    $afterDiscount = $totalAmount * (1 - $invoice['discount'] / 100);
    $due = round($afterDiscount - $invoice['paid'], 2);

    $invoice['total_amount'] = round($afterDiscount, 2);
    $invoice['due'] = $due;

    $invoice['items'] = $items; 
    $invoice['services'] = $services; 

    echo json_encode(["status" => "success", "invoice" => $invoice]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>
