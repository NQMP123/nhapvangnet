<?php
require_once 'api_config.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if (!checkIP()) {
    echo json_encode(['success' => false, 'message' => 'IP không được phép truy cập!']);
    exit;
}
$headers = getallheaders();
if (!isset($headers['X-API-Key']) || $headers['X-API-Key'] !== API_SECRET_KEY) {
    echo json_encode([
        'success' => false,
        'message' => 'API key không hợp lệ'
    ]);
    exit;
}

// Lấy danh sách đơn nạp vàng (có thể giới hạn số lượng, ví dụ 100 đơn mới nhất)
$limit = intval($_GET['limit'] ?? 100);
if ($limit < 1 || $limit > 500)
    $limit = 100;
$stmt = $pdo->prepare("SELECT * from napvang where status = 'pending' order by created_at DESC LIMIT ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

// Trả về JSON
echo json_encode([
    'success' => true,
    'data' => $orders
]);