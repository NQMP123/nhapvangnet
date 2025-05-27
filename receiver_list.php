<?php
require_once 'includes/db.php';
$server_id = intval($_GET['server_id'] ?? 0);
if (!$server_id) {
    echo '<tr><td colspan="5" class="text-danger text-center">Không xác định máy chủ.</td></tr>';
    exit;
}
$stmt = $pdo->prepare("SELECT p.*, s.name as server_name FROM player p JOIN server s ON p.server_id = s.id WHERE p.server_id = ?");
$stmt->execute([$server_id]);
$list = $stmt->fetchAll();
if (!$list) {
    echo '<tr><td colspan="5" class="text-danger text-center">Không có nhân vật nhận vàng cho server này.</td></tr>';
    exit;
}
foreach ($list as $row) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['server_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
    echo '<td>' . (isset($row['location']) ? htmlspecialchars($row['location']) : '---') . '</td>';
    echo '<td>' . (isset($row['zone']) ? htmlspecialchars($row['zone']) : '---') . '</td>';
    echo '<td>' . (isset($row['gold_balance']) ? number_format($row['gold_balance']) : '---') . '</td>';
    echo '</tr>';
}