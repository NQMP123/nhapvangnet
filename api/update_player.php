<?php
require_once 'api_config.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    error_log("PDO Connection Error in update_gold_delivery_persons.php: " . $e->getMessage());
    exit;
}
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
header('Content-Type: application/json');

// Get the data from the request body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['player']) || !is_array($input['player'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input. \"player\" array is required.']);
    http_response_code(400);
    exit;
}

$gold_receivers_data = $input['player'];

try {
    // Start transaction
    $pdo->beginTransaction();

    // Clear the gold_receivers table
    $stmt = $pdo->prepare("DELETE FROM player");
    $stmt->execute();

    // Prepare insert statement
    // `id`, `created_at`, `updated_at` are auto-managed by the database.
    // `is_active` defaults to 1, but can be overridden if provided in the input.
    $sql = "INSERT INTO player (name, location, server_id, region,gold_balance,gold_bar, is_active) VALUES (:character_name, :location, :server_id, :region, :amount,:gold_bar, :is_active)";
    $stmt = $pdo->prepare($sql);

    foreach ($gold_receivers_data as $receiver) {
        // Validate required fields for each receiver
        if (!isset($receiver['character_name']) || !isset($receiver['location']) || !isset($receiver['server_id']) || !isset($receiver['region']) || !isset($receiver['amount']) || !isset($receiver['gold_bar'])) {
            $pdo->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid data for a receiver. Each receiver must have character_name, location, server_id, region, and amount.',
                'offending_receiver' => $receiver // Helps identify the problematic entry
            ]);
            http_response_code(400);
            exit;
        }

        // Set is_active: defaults to 1 if not provided or if provided value is not 0
        $is_active = isset($receiver['is_active']) && $receiver['is_active'] === 0 ? 0 : 1;

        $stmt->execute([
            ':character_name' => $receiver['character_name'],
            ':location' => $receiver['location'],
            ':server_id' => $receiver['server_id'],
            ':region' => $receiver['region'],
            ':amount' => $receiver['amount'],
            ':gold_bar' => $receiver['gold_bar'],
            ':is_active' => $is_active
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode(['status' => 'success', 'message' => 'Gold receivers updated successfully.']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database error in update_gold_receivers: " . $e->getMessage()); // Log detailed error
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Unexpected error in update_gold_receivers: " . $e->getMessage()); // Log detailed error
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
    http_response_code(500);
}

?>