<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/ORION_PROYECT/PUBLIC/style.css">
  <title>Formulario de Registro</title>
</head>
<body>
  <div class="main-layout">
    <form action="/ORION_PROYECT/APP/CONTROLLERS/procesar_registro.php" method="POST">
      <h1 class="titulo-principal">REGISTRO DE IMEI</h1>
      <div class="input-container">
        <label for="imei">IMEI:</label>
        <input type="text" id="imei" name="imei" maxlength="15" inputmode="numeric" required>
        <span id="imeiWarning" class="warning">Máximo 15 caracteres</span>
      </div>
      <div class="input-container">
        <label for="modelo">Equipo:</label>
        <input type="text" id="modelo" name="modelo">
      </div>
      <div class="input-container">
        <label for="numero">Número de teléfono:</label>
        <input type="tel" id="numero" name="numero" maxlength="10" inputmode="numeric">
        <span id="numeroWarning" class="warning">Máximo 10 caracteres</span>
      </div>
      <div class="input-container">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre">
      </div>
      <div class="input-container">
        <label for="ciudad_expedicion">Ciudad de Expedición (ID):</label>
        <input type="text" id="ciudad_expedicion" name="ciudad_expedicion">
      </div>
      <div class="input-container">
        <label for="id">Número de ID (CC):</label>
        <input type="text" id="id" name="id">
      </div>
      <div class="input-container">
        <label for="otro_imei">Otro IMEI:</label>
        <input type="text" id="otro_imei" name="otro_imei" maxlength="15" inputmode="numeric">
        <span id="otroImeiWarning" class="warning">Máximo 15 caracteres</span>
      </div>
      <button type="submit">Registrar</button>
    </form>

    <div class="table-container">
      <h2 class="titulo-secundario">Registros</h2>
      <table>
        <thead>
          <tr>
            <th>IMEI</th>
            <th>Nombre del Cliente</th>
            <th>Estado</th>
            <th>Tiempo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php include $_SERVER['DOCUMENT_ROOT'] . '/ORION_PROYECT/APP/CONTROLLERS/mostrar_registros.php'; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Ventana modal -->
  <div id="infoModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Información Completa</h2>
      <div id="modal-info"></div>
      <div class="modal-buttons">
        <button id="btnEditar">Editar</button>
        <form id="eliminarForm" action="/ORION_PROYECT/APP/CONTROLLERS/eliminar_registro.php" method="POST">
          <input type="hidden" name="id" id="modalImei">
          <button type="submit" class="eliminar-btn">Eliminar</button>
        </form>
      </div>
    </div>
  </div>

  <div id="modalBackdrop" class="modal-backdrop"></div>

  <script src="/ORION_PROYECT/PUBLIC/funciones.js"></script>
</body>
</html>