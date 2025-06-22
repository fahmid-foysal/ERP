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

try {
    $stmt = $pdo->prepare("
        SELECT i.id as invoice_id, i.date, i.discount, i.paid,
               c.name as cus_name, c.phone as cus_phone
        FROM invoices i
        INNER JOIN customers c ON c.id = i.cus_id
        ORDER BY i.id DESC
    ");
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($invoices as &$invoice) {
        $itemStmt = $pdo->prepare("
            SELECT quantity, rate
            FROM invoice_items
            WHERE invoice_id = ?
        ");
        $itemStmt->execute([$invoice['invoice_id']]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        $serviceStmt = $pdo->prepare("SELECT service_price FROM invoice_services WHERE invoice_id = ?");
        $serviceStmt->execute([$invoice['invoice_id']]);
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
    }

    echo json_encode(["status" => "success", "invoices" => $invoices]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>