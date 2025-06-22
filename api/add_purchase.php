<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

$json_data = file_get_contents("php://input");
$input_data = json_decode($json_data, true);

if ($json_data === false || is_null($input_data)) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data or empty request."]);
    exit;
}

$required_fields = ["supplier_id", "products", "paid", "date"];

foreach ($required_fields as $field) {
    if (!isset($input_data[$field]) || $input_data[$field] === "") {
        echo json_encode(["status" => "error", "message" => "$field is required."]);
        exit;
    }
}

$supplier_id = trim($input_data["supplier_id"]);
$products = $input_data["products"]; 
$paid = filter_var($input_data["paid"], FILTER_VALIDATE_FLOAT);
$date = $input_data["date"];

if (!is_array($products) || empty($products)) {
    echo json_encode(["status" => "error", "message" => "Products list is empty or invalid."]);
    exit;
}

if ($paid === false || $paid < 0) {
    echo json_encode(["status" => "error", "message" => "Invalid paid amount."]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO purchase (supplier_id, paid, date) VALUES (?, ?, ?)");
    $stmt->execute([$supplier_id, $paid, $date]);
    $purchase_id = $pdo->lastInsertId();

    $total_cost = 0;

    foreach ($products as $product) {
        if (
            !isset($product["barcode"], $product["quantity"], $product["rate"]) ||
            $product["barcode"] === "" || $product["quantity"] <= 0 || $product["rate"] <= 0
        ) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "Each product must have valid barcode, quantity, and rate."]);
            exit;
        }

        $barcode = htmlspecialchars(strip_tags($product["barcode"]));
        $quantity = filter_var($product["quantity"], FILTER_VALIDATE_FLOAT);
        $rate = filter_var($product["rate"], FILTER_VALIDATE_FLOAT);

        $stmt = $pdo->prepare("SELECT id FROM products WHERE barcode = ?");
        $stmt->execute([$barcode]);
        $checkStock = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$checkStock) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "Invalid product barcode: $barcode"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO purchased_products (barcode, purchase_id, quantity, rate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$barcode, $purchase_id, $quantity, $rate]);

        $stmt = $pdo->prepare("UPDATE products SET in_stock = in_stock + ?, purchase_price = ? WHERE barcode = ?");
        $stmt->execute([$quantity, $rate, $barcode]);        

        $total_cost += $quantity * $rate;
    }

    $due = $total_cost - $paid;
    if ($due > 0) {
        $stmt = $pdo->prepare("UPDATE suppliers SET payable = payable + ? WHERE id = ?");
        $stmt->execute([$due, $supplier_id]);
    }

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Purchase recorded successfully."]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>