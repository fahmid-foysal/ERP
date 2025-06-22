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
        SELECT 
            c.name AS cus_name,
            c.phone AS cus_phone,
            c.address AS cus_address,
            SUM(
                (
                    SELECT IFNULL(SUM(ii.quantity * ii.rate), 0)
                    FROM invoice_items ii
                    WHERE ii.invoice_id = i.id
                ) +
                (
                    SELECT IFNULL(SUM(isv.service_price), 0)
                    FROM invoice_services isv
                    WHERE isv.invoice_id = i.id
                )
            ) * (1 - i.discount / 100) AS total_amount,
            SUM(i.paid) AS total_paid,
            SUM(
                (
                    (
                        (
                            SELECT IFNULL(SUM(ii.quantity * ii.rate), 0)
                            FROM invoice_items ii
                            WHERE ii.invoice_id = i.id
                        ) +
                        (
                            SELECT IFNULL(SUM(isv.service_price), 0)
                            FROM invoice_services isv
                            WHERE isv.invoice_id = i.id
                        )
                    ) * (1 - i.discount / 100)
                ) - i.paid
            ) AS total_due
        FROM invoices i
        INNER JOIN customers c ON c.id = i.cus_id
        GROUP BY i.cus_id
        ORDER BY c.name ASC
    ");

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "customers" => $result]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>
