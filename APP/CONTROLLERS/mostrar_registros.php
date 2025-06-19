<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/ORION_PROYECT/CONFIG/database.php';

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT EQUIPO.IMEI, CLIENTE.NOMBRE, EQUIPO.ESTADO, MAX(TRABAJO.FECHA_JOB) AS ULTIMA_FECHA 
        FROM EQUIPO 
        JOIN CLIENTE ON EQUIPO.CC_CLI = CLIENTE.CC_CLI
        LEFT JOIN TRABAJO ON EQUIPO.IMEI = TRABAJO.IMEI
        GROUP BY EQUIPO.IMEI
        ORDER BY CASE WHEN EQUIPO.ESTADO = 'Pendiente' THEN 1 ELSE 2 END, ULTIMA_FECHA ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

function calcularTiempoTranscurrido($fechaJob) {
    if (empty($fechaJob)) {
        return "No registrado";
    }
    try {
        $fechaActual = new DateTime();
        $fechaTrabajo = new DateTime($fechaJob);
        $diferencia = $fechaActual->diff($fechaTrabajo);
        if ($diferencia->d == 0) {
            return "Hoy";
        } elseif ($diferencia->d == 1) {
            return "Hace 1 día";
        } else {
            return "Hace " . $diferencia->d . " días";
        }
    } catch (Exception $e) {
        return "Fecha inválida";
    }
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $estadoClase = strtolower(str_replace(' ', '', $row['ESTADO']));
        $tiempoTranscurrido = calcularTiempoTranscurrido($row['ULTIMA_FECHA']);
        $imei = htmlspecialchars($row['IMEI'], ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars($row['NOMBRE'], ENT_QUOTES, 'UTF-8');
        $estado = htmlspecialchars($row['ESTADO'], ENT_QUOTES, 'UTF-8');
        
        echo "<tr class='estado-$estadoClase'>
                <td>$imei</td>
                <td>$nombre</td>
                <td>
                  <div class='estado-container'>
                    <button class='estado-btn' data-imei='$imei'>$estado</button>
                    <button class='confirmar-btn' data-imei='$imei'>✓</button>
                    <div class='estado-menu' style='display: none;'>
                      <button class='cambiar-estado' data-estado='Pendiente' data-imei='$imei'>Pendiente</button>
                      <button class='cambiar-estado' data-estado='Registrado' data-imei='$imei'>Registrado</button>
                      <button class='cambiar-estado' data-estado='Bloqueado' data-imei='$imei'>Bloqueado</button>
                      <button class='mostrar-otro' data-imei='$imei'>Otro</button>
                      <input type='text' class='otro-input' placeholder='Ingrese otro estado' style='display: none;'>
                    </div>
                  </div>
                </td>
                <td>$tiempoTranscurrido</td>
                <td>
          <button class='registrar-ahora' data-imei='$imei'>Registrar Ahora</button>  
          <button class='btn-info info-btn' data-imei='$imei'>Info</button>
          <button class='checar-imei' data-imei='$imei'>Checar IMEI</button>
        </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No hay registros</td></tr>";
}

$conn->close();
?>