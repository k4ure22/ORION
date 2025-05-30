<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/ORION_PROYECT/CONFIG/database.php';

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$imei = $_GET['imei'];

// Consulta SQL con JOIN para obtener información relacionada
$sql = "
    SELECT 
        EQUIPO.IMEI,
        EQUIPO.IMEI2,
        EQUIPO.MARCA,
        EQUIPO.MODELO,
        EQUIPO.ESTADO,
        CLIENTE.NOMBRE,
        CLIENTE.LINEA,
        CLIENTE.LUGAR_EXP,
        CLIENTE.CC_CLI,
        TRABAJO.TIPO_JOB,
        TRABAJO.FECHA_JOB
    FROM EQUIPO
    LEFT JOIN CLIENTE ON EQUIPO.CC_CLI = CLIENTE.CC_CLI
    LEFT JOIN TRABAJO ON EQUIPO.IMEI = TRABAJO.IMEI
    WHERE EQUIPO.IMEI = '$imei'
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'imei' => $row['IMEI'],
        'imei2' => $row['IMEI2'],
        'equipo' => $row['MARCA'] . ' ' . $row['MODELO'], // Combina marca y modelo
        'numero' => $row['LINEA'],
        'nombre' => $row['NOMBRE'],
        'ciudad_expedicion' => $row['LUGAR_EXP'],
        'id' => $row['CC_CLI'],
        'tipo_trabajo' => $row['TIPO_JOB'],
        'fecha_registro' => $row['FECHA_JOB'],
        'estado' => $row['ESTADO']
    ]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>