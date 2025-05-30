<?php
class Registro {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear un registro (Insert)
    public function create($data) {
        $sql = "INSERT INTO CLIENTE (CC_CLI, NOMBRE, TIPO_ID, LINEA, LUGAR_EXP, FECHA_EXP)
                VALUES (?, ?, ?, ?, ?, YEAR(NOW()))";
        $stmt = $this->conn->prepare($sql);
        $tipo_id = "CC";
        $stmt->bind_param("issss", $data['id'], $data['nombre'], $tipo_id, $data['numero'], $data['ciudad_expedicion']);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        // Insertar en EQUIPO
        $sqlEquipo = "INSERT INTO EQUIPO (IMEI, IMEI2, MARCA, MODELO, ESTADO, CC_CLI)
                      VALUES (?, NULL, ?, ?, ?, ?)";
        $stmtEquipo = $this->conn->prepare($sqlEquipo);
        $marca = "Desconocida";
        $estado = "Pendiente";
        $stmtEquipo->bind_param("iisssi", $data['imei'], $marca, $data['modelo'], $estado, $data['id']);
        if (!$stmtEquipo->execute()) {
            throw new Exception($stmtEquipo->error);
        }
        $stmtEquipo->close();

        // Insertar en TRABAJO
        $sqlTrabajo = "INSERT INTO TRABAJO (TIPO_JOB, OPERADOR, PAGO, PRECIO, IMEI, FECHA_JOB)
                       VALUES ('Registro', 'Movistar', FALSE, 6000, ?, NOW())";
        $stmtTrabajo = $this->conn->prepare($sqlTrabajo);
        $stmtTrabajo->bind_param("i", $data['imei']);
        if (!$stmtTrabajo->execute()) {
            throw new Exception($stmtTrabajo->error);
        }
        $stmtTrabajo->close();

        // Si existe otro IMEI
        if (!empty($data['otro_imei'])) {
            $stmtEquipo = $this->conn->prepare($sqlEquipo);
            $stmtEquipo->bind_param("iisssi", $data['otro_imei'], $marca, $data['modelo'], $estado, $data['id']);
            if (!$stmtEquipo->execute()) {
                throw new Exception($stmtEquipo->error);
            }
            $stmtEquipo->close();

            $stmtTrabajo = $this->conn->prepare($sqlTrabajo);
            $stmtTrabajo->bind_param("i", $data['otro_imei']);
            if (!$stmtTrabajo->execute()) {
                throw new Exception($stmtTrabajo->error);
            }
            $stmtTrabajo->close();
        }
        return true;
    }

    // Otros métodos: obtenerRegistros(), actualizarRegistro(), eliminarRegistro(), etc.
}
?>
