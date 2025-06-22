<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start(); 
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"]) || empty($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as admin."]);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET."]);
    exit;
}


try {

$stmt = $pdo->prepare("SELECT s.id, s.service, s.amount AS total_amount, s.paid AS total_paid, s.date,
                        c.name, c.phone
                        FROM service_invoice s
                        INNER JOIN customers c ON c.id = s.cus_id
                        ");

    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($invoices as &$invoice) {
        $invoice["total_due"] = (float)$invoice["total_amount"] - (float)$invoice["total_paid"];
        if((float)$invoice["total_due"]>0){
            $invoice["status"] = "due";
        }else{
            $invoice["status"] = "paid";
        }
    }



    echo json_encode(["status" => "success", "invoices" => $invoices]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>