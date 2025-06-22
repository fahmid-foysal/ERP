<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db_config.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$required = ["cus_name", "cus_phone", "paid", "discount", "date"];

foreach ($required as $field) {
    if (!isset($data[$field])) {
        echo json_encode(["status" => "error", "message" => "$field is required"]);
        exit;
    }
}


if((!isset($data['items']) || empty($data["items"]) || !is_array($data["items"])) && 
            (!isset($data['services']) || empty($data["services"]) || !is_array($data["services"]))){
                echo json_encode(["status" => "error", "message" => "Array of service or item is required"]);
                exit;
            }

$cus_name = htmlspecialchars(trim($data["cus_name"]));
$cus_phone = htmlspecialchars(trim($data["cus_phone"]));
$cus_address = htmlspecialchars(trim($data["cus_address"] ?? ""));
$paid = floatval($data["paid"]);
$discount = floatval($data["discount"]);
$date = $data["date"];
$items = $data["items"] ?? [];
$services = $data["services"] ?? [];


try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM customers WHERE phone = ?");
    $stmt->execute([$cus_phone]);
    $customer = $stmt->fetch();

    if ($customer) {
        $cus_id = $customer["id"];
        $pdo->prepare("UPDATE customers SET name=?, address=? WHERE id=?")
            ->execute([$cus_name, $cus_address, $cus_id]);
    } else {
        $pdo->prepare("INSERT INTO customers (phone, name, address) VALUES (?, ?, ?)")
            ->execute([$cus_phone, $cus_name, $cus_address]);
        $cus_id = $pdo->lastInsertId();
    }

    $pdo->prepare("INSERT INTO invoices (cus_id, discount, paid, date) VALUES (?, ?, ?, ?)")
        ->execute([$cus_id, $discount, $paid, $date]);

    $invoice_id = $pdo->lastInsertId();


    foreach ($services as $service) {
        $service_name = htmlspecialchars($service["service_name"]);
        $service_price = floatval($service["service_price"]);
    
        if (empty($service_name) || $service_price <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid Service Name or Price"]);
            exit;
        }
    
        $pdo->prepare("INSERT INTO invoice_services (service_name, service_price, invoice_id) VALUES (?, ?, ?)")
            ->execute([$service_name, $service_price, $invoice_id]);
    }

    foreach ($items as $item) {
        $barcode = htmlspecialchars($item["barcode"]);
        $quantity = floatval($item["quantity"]);
        $rate = floatval($item["rate"]);

        if ($quantity <= 0 || $rate <= 0) {
            throw new Exception("Invalid Quantity or Rate");
        }

        $stmt = $pdo->prepare("SELECT in_stock FROM products WHERE barcode=?");
        $stmt->execute([$barcode]);
        $product = $stmt->fetch();

        if (!$product || $product["in_stock"] < $quantity) {
            throw new Exception("Insufficient Stock for $barcode");
        }

        $pdo->prepare("INSERT INTO invoice_items (invoice_id, barcode, quantity, rate) VALUES (?, ?, ?, ?)")
            ->execute([$invoice_id, $barcode, $quantity, $rate]);

        $pdo->prepare("UPDATE products SET in_stock = in_stock - ? WHERE barcode=?")
            ->execute([$quantity, $barcode]);
    }

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "Invoice Created"]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>