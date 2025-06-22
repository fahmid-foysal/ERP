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
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.date,
            p.paid,
            s.name
        FROM purchase p
        INNER JOIN suppliers s ON s.id = p.supplier_id
        ORDER BY p.id DESC
    ");
    $stmt->execute();
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $final_data = [];

    foreach ($purchases as $purchase) {
        $stmt = $pdo->prepare("
            SELECT 
                quantity,
                rate
            FROM purchased_products
            WHERE purchase_id = ?
        ");
        $stmt->execute([$purchase["id"]]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_amount = 0;
        foreach ($products as $product) {
            $total_amount += $product["quantity"] * $product["rate"];
        }

        $total_paid = (float)$purchase["paid"];
        $total_due = $total_amount - $total_paid;

        $final_data[] = [
            "id" => $purchase["id"],
            "date" => $purchase["date"],
            "total_amount" => number_format($total_amount, 2, '.', ''),
            "total_paid" => number_format($total_paid, 2, '.', ''),
            "total_due" => number_format($total_due, 2, '.', ''),
            "name" => $purchase["name"],
            "status" => $total_due > 0 ? "due" : "paid"
        ];
    }

    echo json_encode(["status" => "success", "purchases" => $final_data]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
