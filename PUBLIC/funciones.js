document.addEventListener('DOMContentLoaded', function() {
  // Variables globales
  let imeiActual = '';
  const modal = document.getElementById('infoModal');
  const backdrop = document.getElementById('modalBackdrop');
  const closeBtn = document.querySelector('.close');

  // Función mejorada para mostrar modal con información
  function mostrarInfoModal(imei) {
    imeiActual = imei;
    document.getElementById('modalImei').value = imei;
    
    fetch(`/ORION_PROYECT/APP/CONTROLLERS/obtener_info.php?imei=${imei}`)
      .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        return response.json();
      })
      .then(data => {
        const modalInfo = document.getElementById('modal-info');
        if (data.success) {
          // Formatear la fecha si existe
          const fechaRegistro = data.fecha_registro ? 
            new Date(data.fecha_registro).toLocaleString() : 'No registrada';
          
          // Mostrar información completa en el modal
          modalInfo.innerHTML = `
            <div class="info-row">
              <span class="info-label">IMEI:</span>
              <span class="info-value">${data.imei || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">IMEI 2:</span>
              <span class="info-value">${data.imei2 || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Equipo:</span>
              <span class="info-value">${data.equipo || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Número:</span>
              <span class="info-value">${data.numero || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Cliente:</span>
              <span class="info-value">${data.nombre || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Ciudad:</span>
              <span class="info-value">${data.ciudad_expedicion || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">ID:</span>
              <span class="info-value">${data.id || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Trabajo:</span>
              <span class="info-value">${data.tipo_trabajo || 'N/A'}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Fecha Registro:</span>
              <span class="info-value">${fechaRegistro}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Estado:</span>
              <span class="info-value">${data.estado || 'N/A'}</span>
            </div>
          `;
        } else {
          throw new Error(data.error || 'No se encontró información para este IMEI');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('modal-info').innerHTML = `
          <div class="error-message">${error.message}</div>
        `;
      });

    modal.style.display = 'block';
    backdrop.style.display = 'block';
  }

  // Función para cerrar modal
  function cerrarModal() {
    modal.style.display = 'none';
    backdrop.style.display = 'none';
  }

  // Delegación de eventos para toda la tabla
  document.addEventListener('click', function(e) {
    const target = e.target;
    const imei = target.dataset.imei || target.closest('[data-imei]')?.dataset.imei;

    if (!imei) return;

    if (target.classList.contains('btn-info') || target.classList.contains('info-btn')) {
      mostrarInfoModal(imei);
    }
    else if (target.classList.contains('checar-imei')) {
      checarIMEI(imei);
    }
    else if (target.classList.contains('registrar-ahora')) {
      registrarAhora(imei);
    }
  });

  // Event listeners para cerrar modal
  closeBtn.addEventListener('click', cerrarModal);
  backdrop.addEventListener('click', cerrarModal);

  // Prevenir que el modal se cierre al hacer clic dentro
  document.querySelector('.modal-content').addEventListener('click', function(e) {
    e.stopPropagation();
  });

  // Confirmación para eliminar registro
  document.getElementById('eliminarForm').addEventListener('submit', function(e) {
    if (!confirm('¿Estás seguro de eliminar este registro?')) {
      e.preventDefault();
    }
  });

  // Función para editar registro
  document.getElementById('btnEditar').addEventListener('click', function() {
    alert(`Editar registro con IMEI: ${imeiActual}`);
    // Implementar lógica de edición aquí
  });

  // Funciones auxiliares
  function checarIMEI(imei) {
    navigator.clipboard.writeText(imei)
      .then(() => window.open('https://www.imeicolombia.com.co/', '_blank'))
      .catch(() => alert('No se pudo copiar el IMEI'));
  }

  function registrarAhora(imei) {
    window.location.href = `https://atencionalcliente.movistar.co/Proteccion_al_usuario/No_al_hurto_de_terminales/?imei=${imei}`;
  }

  // Validación de campos del formulario
  function validarCampo(inputId, warningId, maxLength) {
    const input = document.getElementById(inputId);
    const warning = document.getElementById(warningId);
    
    input.addEventListener('input', function() {
      warning.style.display = this.value.length > maxLength ? 'block' : 'none';
    });
  }

  validarCampo('imei', 'imeiWarning', 15);
  validarCampo('numero', 'numeroWarning', 10);
  validarCampo('otro_imei', 'otroImeiWarning', 15);
});document.addEventListener('click', function (e) {
  if (e.target.classList.contains('cambiar-estado')) {
    const nuevoEstado = e.target.dataset.estado;
    const imei = e.target.dataset.imei;

    fetch('/ORION_PROYECT/CONTROLLER/actualizar_estado.php', {
      method: 'POST',
      body: JSON.stringify({ imei, estado: nuevoEstado }),
      headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const btn = document.querySelector(`.estado-btn[data-imei="${imei}"]`);
        const fila = btn.closest('tr');

        // ACTUALIZA TEXTO
        btn.textContent = nuevoEstado;

        // ACTUALIZA CLASES
        const clasesEstado = ['estado-registrado', 'estado-pendiente', 'estado-bloqueado'];

        // QUITA clases anteriores
        clasesEstado.forEach(c => {
          btn.classList.remove(c);
          fila.classList.remove(c);
        });

        // AGREGA nueva clase
        const nuevaClase = `estado-${nuevoEstado.toLowerCase().replace(/\s/g, '')}`;
        btn.classList.add(nuevaClase);
        fila.classList.add(nuevaClase);

        console.log(`✅ Estado actualizado a ${nuevoEstado}, clase aplicada: ${nuevaClase}`);
      } else {
        console.error('⚠️ Error al actualizar:', data.error);
      }
    })
    .catch(err => console.error('❌ Error de red:', err));
  }
});
