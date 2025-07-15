<?php
$host = 'localhost:3307';
$usuario = 'root';
$contraseña = '12345';
$baseDatos = 'ORION_BD';

$conn = new mysqli($host, $usuario, $contraseña, $baseDatos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
