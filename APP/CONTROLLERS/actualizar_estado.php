<?php
header('Content-Type: application/json');
include_once $_SERVER['DOCUMENT_ROOT'] . '/ORION_PROYECT/CONFIG/database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validación adicional
if (!isset($data['imei']) || !is_numeric($data['imei']) || strlen($data['imei']) != 15) {
    echo json_encode(['success' => false, 'error' => 'IMEI inválido']);
    exit;
}

try {
    $sql = "UPDATE EQUIPO SET ESTADO = ? WHERE IMEI = ?";
    $stmt = $conn->prepare($sql);
    
    // Asegurar tipos de datos correctos (BIGINT = string)
    $imei = (string)$data['imei'];
    $estado = $data['estado'];
    
    $stmt->bind_param("ss", $estado, $imei);
    $stmt->execute();

    echo json_encode(['success' => $stmt->affected_rows > 0]);
    
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>