<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/ORION_PROYECT/CONFIG/database.php';

// Obtener los datos del formulario
$imei = $_POST['imei'];
$modelo = $_POST['modelo'];
$numero = $_POST['numero'];
$nombre = $_POST['nombre'];
$ciudad_expedicion = $_POST['ciudad_expedicion'];
$id = $_POST['id'];
$otro_imei = $_POST['otro_imei'];

try {
    $conn->begin_transaction();

    // 1. Insertar en la tabla CLIENTE
    $sqlCliente = "INSERT INTO CLIENTE (CC_CLI, NOMBRE, TIPO_ID, LINEA, LUGAR_EXP, FECHA_EXP) 
                   VALUES (?, ?, ?, ?, ?, YEAR(NOW()))";
    $stmtCliente = $conn->prepare($sqlCliente);
    $tipo_id = "CC"; // Tipo de identificación por defecto
    $stmtCliente->bind_param("issss", $id, $nombre, $tipo_id, $numero, $ciudad_expedicion);
    $stmtCliente->execute();

    // 2. Insertar en la tabla EQUIPO (IMEI principal)
    $sqlEquipo = "INSERT INTO EQUIPO (IMEI, IMEI2, MARCA, MODELO, ESTADO, CC_CLI) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmtEquipo = $conn->prepare($sqlEquipo);
    $marca = "Desconocida"; // Marca por defecto
    $estado = "Pendiente";  // Estado por defecto
    $imei2_null = NULL;     // IMEI2 puede ser NULL

    // Bind de parámetros para EQUIPO
    $stmtEquipo->bind_param("iisssi", $imei, $imei2_null, $marca, $modelo, $estado, $id);
    $stmtEquipo->execute();

    // 3. Insertar un nuevo trabajo en la tabla TRABAJO para el IMEI principal
    $sqlTrabajo = "INSERT INTO TRABAJO (TIPO_JOB, OPERADOR, PAGO, PRECIO, IMEI, FECHA_JOB) 
                   VALUES ('Registro', 'Movistar', FALSE, 6000, ?, NOW())";
    $stmtTrabajo = $conn->prepare($sqlTrabajo);
    $stmtTrabajo->bind_param("i", $imei);
    $stmtTrabajo->execute();

    // 4. Si hay un IMEI adicional, insertarlo en EQUIPO y TRABAJO
    if (!empty($otro_imei)) {
        // Insertar el IMEI adicional en EQUIPO
        $stmtEquipo->bind_param("iisssi", $otro_imei, $imei2_null, $marca, $modelo, $estado, $id);
        $stmtEquipo->execute();

        // Insertar un nuevo trabajo en la tabla TRABAJO para el IMEI adicional
        $stmtTrabajo->bind_param("i", $otro_imei);
        $stmtTrabajo->execute();
    }

    // Confirmar la transacción
    $conn->commit();

    // Redirigir al usuario
    header("Location: /ORION_PROYECT/APP/VIEWS/registro.php");
    exit();
} catch (mysqli_sql_exception $exception) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo "Error al registrar los datos: " . $exception->getMessage();
}

// Cerrar las declaraciones y la conexión
$stmtCliente->close();
$stmtEquipo->close();
$stmtTrabajo->close();
$conn->close();
?>