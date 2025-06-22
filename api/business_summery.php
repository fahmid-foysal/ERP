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

// if (!$input) {
//     echo json_encode(["status" => "error", "message" => "Invalid JSON input."]);
//     exit;
// }

$date = $input['date'] ?? null;
$till_date = $input['till_date'] ?? null;


try {
    if ($date && !$till_date) {
        $stmt = $pdo->prepare("
            SELECT 
                (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ?)) AS service_sale,
                (SELECT SUM(quantity * rate) FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ?)) AS product_sale,
                (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ?)) +
                (SELECT SUM(quantity * rate) FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ?)) AS total_amount,

                (SELECT SUM(rate * quantity) FROM purchased_products WHERE purchase_id IN (SELECT id FROM purchase WHERE date >= ?)) as total_purchase,

                (SELECT SUM(amount) FROM expense WHERE date >= ?) AS total_expense,
                (SELECT SUM(paid) FROM invoices WHERE date >= ?) AS total_paid
        ");
        $stmt->execute([$date, $date, $date, $date, $date, $date, $date]);
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);

        $amount["total_due"] = round($amount["total_amount"], 2) - round($amount["total_paid"], 2);
        $amount["total_profit"] = round($amount["total_amount"], 2) - round($amount["total_purchase"], 2);

    }elseif($date && $till_date){
            $stmt = $pdo->prepare("
            SELECT 
                (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ? AND date <= ?))AS service_sale,
                 (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ? AND date <= ?)) AS product_sale,
                (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ? AND date <= ?)) +
                (SELECT SUM(quantity * rate) FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE date >= ? AND date <= ?)) AS total_amount,



                (SELECT SUM(rate * quantity) FROM purchased_products WHERE purchase_id IN (SELECT id FROM purchase WHERE date >= ? AND date <= ?)) AS total_purchase,


                (SELECT SUM(amount) FROM expense WHERE date >= ? AND date <= ?) AS total_expense,
                (SELECT SUM(paid) FROM invoices WHERE date >= ? AND date <= ?) AS total_paid
        ");
        $stmt->execute([$date, $till_date, $date, $till_date, $date, $till_date, $date, $till_date, $date, $till_date, $date, $till_date, $date, $till_date]);
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);

        $amount["total_due"] = round($amount["total_amount"], 2) - round($amount["total_paid"], 2);
        $amount["total_profit"] = round($amount["total_amount"], 2) - round($amount["total_purchase"], 2);

    }elseif(!$date && $till_date){
        $stmt = $pdo->prepare("
        SELECT 
            (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date <= ?)) AS service_sale,
            (SELECT SUM(quantity * rate) FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE date <= ?)) AS product_sale,
            (SELECT SUM(service_price) FROM invoice_services WHERE invoice_id IN (SELECT id FROM invoices WHERE date <= ?)) +
            (SELECT SUM(quantity * rate) FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE date <= ?)) AS total_amount,



            (SELECT SUM(rate * quantity) FROM purchased_products WHERE purchase_id IN (SELECT id FROM purchase WHERE date <= ?)) AS total_purchase,


            (SELECT SUM(amount) FROM expense WHERE date <= ?) AS total_expense,
            (SELECT SUM(paid) FROM invoices WHERE date <= ?) AS total_paid
    ");
    $stmt->execute([$till_date, $till_date, $till_date, $till_date, $till_date, $till_date, $till_date]);
    $amount = $stmt->fetch(PDO::FETCH_ASSOC);

    $amount["total_due"] = round($amount["total_amount"], 2) - round($amount["total_paid"], 2);
    $amount["total_profit"] = round($amount["total_amount"], 2) - round($amount["total_purchase"], 2);

    }else {
        $stmt = $pdo->prepare("
            SELECT 
                (SELECT SUM(service_price) FROM invoice_services) AS service_sale,
                (SELECT SUM(quantity * rate) FROM invoice_items) AS product_sale,
                (SELECT SUM(service_price) FROM invoice_services) + 
                (SELECT SUM(quantity * rate) FROM invoice_items) AS total_amount,


                (SELECT SUM(rate * quantity) FROM purchased_products) AS total_purchase,


                (SELECT SUM(amount) FROM expense) AS total_expense,
                (SELECT SUM(paid) FROM invoices) AS total_paid
        ");
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);

        $amount["total_due"] = round($amount["total_amount"], 2) - round($amount["total_paid"], 2);
        $amount["total_profit"] = round($amount["total_amount"], 2) - round($amount["total_purchase"], 2);
    }

    $stmt = $pdo->prepare("SELECT SUM(payable) AS payable FROM suppliers");
    $stmt->execute();
    $payable = $stmt->fetch(PDO::FETCH_ASSOC);

    $amount["payable"] = round($payable["payable"], 2);

    echo json_encode(["status" => "success", "summery" => $amount]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>