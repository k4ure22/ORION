<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/ORION_PROYECT/CONFIG/database.php';

// Obtener el IMEI del registro a eliminar
$imei = $_POST['id'];

try {
    $conn->begin_transaction();

    // 1. Eliminar el registro de la tabla TRABAJO
    $sqlTrabajo = "DELETE FROM TRABAJO WHERE IMEI = ?";
    $stmtTrabajo = $conn->prepare($sqlTrabajo);
    $stmtTrabajo->bind_param("i", $imei);
    $stmtTrabajo->execute();

    // 2. Eliminar el registro de la tabla EQUIPO
    $sqlEquipo = "DELETE FROM EQUIPO WHERE IMEI = ?";
    $stmtEquipo = $conn->prepare($sqlEquipo);
    $stmtEquipo->bind_param("i", $imei);
    $stmtEquipo->execute();

    // Confirmar la transacción
    $conn->commit();

    // Redirigir al usuario
    header("Location: /ORION_PROYECT/APP/VIEWS/registro.php");
    exit();
} catch (mysqli_sql_exception $exception) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo "Error al eliminar el registro: " . $exception->getMessage();
}

// Cerrar las declaraciones y la conexión
$stmtTrabajo->close();
$stmtEquipo->close();
$conn->close();
?>