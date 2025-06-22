<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method Use DELETE"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$required = ["deletable", "id"];
foreach ($required as $field) {
    if (!isset($data[$field])) {
        echo json_encode(["status" => "error", "message" => "$field is required"]);
        exit;
    }
}

$deletable = strtolower(htmlspecialchars(trim($data["deletable"])));
$id = $data["id"];

$allowedTables = ["suppliers", "brands", "service_invoice", "products", "expense", "categories", "invoices", "purchase"];

if (!in_array($deletable, $allowedTables)) {
    echo json_encode(["status" => "error", "message" => "Invalid table name"]);
    exit;
}

try {
    $pdo->beginTransaction();
    if($deletable == "invoices"){
        $stmt = $pdo->prepare("SELECT barcode, quantity FROM invoice_items WHERE invoice_id = ?");
        $stmt->execute([$id]);
        $invoice_items = $stmt->fetchAll();
        if($invoice_items){
            foreach ($invoice_items as $item) {
                $stmt = $pdo->prepare("UPDATE products SET in_stock = in_stock + ? WHERE barcode = ?");
                $stmt->execute([$item['quantity'], $item['barcode']]);
            }
        }
    }

    if($deletable == "purchase"){
        $stmt = $pdo->prepare("SELECT barcode, quantity , SUM(quantity*rate) AS total_amount FROM purchased_products WHERE purchase_id = ?");
        $stmt->execute([$id]);
        $purchase = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT supplier_id, paid FROM purchase WHERE id = ?");
        $stmt->execute([$id]);
        $supplier_id = $stmt->fetch();

        

        if($purchase){

            foreach($purchase as $item){

                $stmt = $pdo->prepare("UPDATE products SET in_stock = in_stock - ? WHERE barcode = ?");
                $stmt->execute([$item['quantity'], $item['barcode']]);

            }
 

        }

        if($supplier_id["supplier_id"]){

            $due = (float)$purchase["total_amount"] - (float)$supplier_id["paid"];

            $stmt = $pdo->prepare("UPDATE suppliers SET payable = payable - ? WHERE id = ?");
            $stmt->execute([$due, $supplier_id['supplier_id']]);
        }

    }

    $stmt = $pdo->prepare("DELETE FROM {$deletable} WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "Data deleted"]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>