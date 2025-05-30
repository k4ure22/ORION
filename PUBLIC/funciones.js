// funciones.js - Versión Corregida y Actualizada

document.addEventListener('DOMContentLoaded', () => {
  // Función para cargar el IMEI desde la URL
  const params = new URLSearchParams(window.location.search);
  const imei = params.get('imei');

  if (imei) {
    const imeiInput = document.querySelector('input[name="imeiNum"]');
    if (imeiInput) imeiInput.value = imei;
  }

  // Función para mostrar/ocultar el menú de estados
  function toggleMenu(elemento) {
    const menu = elemento.nextElementSibling.nextElementSibling;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  }

  // Función para cambiar el estado
  function cambiarEstado(imei, nuevoEstado) {
    fetch('/ORION_PROYECT/APP/CONTROLLERS/actualizar_estado.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ imei: imei, estado: nuevoEstado }),
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Actualizar solo la fila afectada sin recargar la página
          const fila = document.querySelector(`button[data-imei="${imei}"]`)?.closest('tr');
          if (fila) {
            fila.querySelector('.estado-btn').textContent = nuevoEstado;
            fila.className = `estado-${nuevoEstado.toLowerCase().replace(/\s+/g, '')}`;
          }
        } else {
          alert("Error al actualizar el estado: " + (data.error || "Desconocido"));
        }
      })
      .catch(error => console.error('Error:', error));
  }

  // Función para confirmar el estado actual
  function confirmarEstado(elemento) {
    const imei = elemento.dataset.imei; // Obtener el IMEI del atributo data-imei
    if (imei) {
      cambiarEstado(imei, 'Registrado'); // Cambiar el estado a "Registrado"
    } else {
      console.error('No se encontró el IMEI en el botón.');
    }
  }

  // Función para mostrar el campo "Otro"
  function mostrarCampoOtro(elemento) {
    const inputOtro = elemento.parentElement.querySelector('.otro-input');
    inputOtro.style.display = 'block';
    inputOtro.focus();
  }

  // Función para abrir la ventana modal de información
  function info(imei) {
    fetch('/ORION_PROYECT/APP/MODELS/obtener_info.php?imei=' + imei)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const modal = document.getElementById('infoModal');
          const modalInfo = document.getElementById('modal-info');

          // Mostrar la información en la ventana modal
          modalInfo.innerHTML = `
            <p><strong>IMEI:</strong> ${data.imei}</p>
            <p><strong>IMEI2:</strong> ${data.imei2 || 'No registrado'}</p>
            <p><strong>Equipo:</strong> ${data.equipo}</p>
            <p><strong>Número de teléfono:</strong> ${data.numero}</p>
            <p><strong>Nombre del cliente:</strong> ${data.nombre}</p>
            <p><strong>Ciudad de expedición:</strong> ${data.ciudad_expedicion}</p>
            <p><strong>ID:</strong> ${data.id}</p>
            <p><strong>Tipo de trabajo:</strong> ${data.tipo_trabajo}</p>
            <p><strong>Fecha de registro:</strong> ${data.fecha_registro}</p>
            <p><strong>Estado del registro:</strong> ${data.estado}</p>
          `;

          // Mostrar la ventana modal
          modal.style.display = 'block';
        } else {
          alert("Error al obtener la información del registro.");
        }
      })
      .catch(error => console.error('Error:', error));
  }

  // Función para cerrar la ventana modal
  function cerrarModal() {
    const modal = document.getElementById('infoModal');
    const backdrop = document.getElementById('modalBackdrop');
    if (modal && backdrop) {
      modal.style.display = 'none';
      backdrop.style.display = 'none';
    } else {
      console.error('No se encontró la ventana modal o el fondo oscuro.');
    }
  }

  // Cerrar la ventana modal al hacer clic en la "X"
  const closeButton = document.querySelector('.close');
  if (closeButton) {
    closeButton.addEventListener('click', cerrarModal);
  } else {
    console.error('El elemento .close no existe en el DOM.');
  }

  // Cerrar la ventana modal al hacer clic fuera de ella
  window.addEventListener('click', (event) => {
    const modal = document.getElementById('infoModal');
    if (event.target === modal) {
      cerrarModal();
    }
  });

  // Función para redirigir a la página de registro de IMEI
  function registrarAhora(imei) {
    window.location.href = `https://atencionalcliente.movistar.co/Proteccion_al_usuario/No_al_hurto_de_terminales/?imei=${imei}`;
  }

  // Función para checar el IMEI
  function checarIMEI(imei) {
    // Copiar el IMEI al portapapeles
    navigator.clipboard.writeText(imei)
      .then(() => {
        console.log('IMEI copiado:', imei);
        
        // Redirigir a la página de verificación de IMEI
        window.open('https://www.imeicolombia.com.co/', '_blank');
      })
      .catch((error) => {
        console.error('Error al copiar el IMEI:', error);
        alert('No se pudo copiar el IMEI. Inténtalo manualmente.');
      });
  }

  // Función para copiar el IMEI
  function copiarIMEI() {
    navigator.clipboard.writeText(imeiActual)
      .then(() => {
        window.open('https://www.imeicolombia.com.co/', '_blank');
        cerrarModal();
      })
      .catch(() => alert('No se pudo copiar el IMEI. Inténtalo manualmente.'));
  }

  // Función para mostrar una alerta
  function mostrarAlerta(mensaje) {
    alert(mensaje);
  }

  // Validación de longitud de los campos de entrada
  function validarLongitud(inputId, minLength, maxLength, warningId) {
    const input = document.getElementById(inputId);
    const warning = document.getElementById(warningId);

    if (!input || !warning) {
      console.error(`Error: No se encontró el input ${inputId} o el warning ${warningId}`);
      return;
    }

    warning.style.display = "none";

    input.addEventListener("input", function () {
      if (this.value.length < minLength) {
        warning.textContent = `Debe contener al menos ${minLength} caracteres.`;
        warning.style.display = "inline-block";
      } else if (this.value.length >= maxLength) {
        warning.textContent = `No puede superar los ${minLength} caracteres.`;
        warning.style.display = "inline-block";
      } else {
        warning.style.display = "none";
      }
    });
  }

  validarLongitud("imei", 15, 16, "imeiWarning");
  validarLongitud("numero", 10, 11, "numeroWarning");
  validarLongitud("otro_imei", 15, 16, "otroImeiWarning");

  // Delegación de eventos para elementos dinámicos
  document.addEventListener('click', function(event) {
    const target = event.target;

    // Alternar menú de estados
    if (target.classList.contains('estado-btn')) {
      event.stopPropagation(); // Evita que el evento se propague
      toggleMenu(target);
    }

    // Cambiar estado
    if (target.classList.contains('cambiar-estado')) {
      const imei = target.dataset.imei;
      const nuevoEstado = target.dataset.estado;
      cambiarEstado(imei, nuevoEstado);
    }

    // Mostrar campo "Otro"
    if (target.classList.contains('mostrar-otro')) {
      mostrarCampoOtro(target);
    }

    // Confirmar estado
    if (target.classList.contains('confirmar-btn')) {
      confirmarEstado(target);
    }

    // Registrar ahora
    if (target.classList.contains('registrar-ahora')) {
      const imei = target.dataset.imei;
      registrarAhora(imei);
    }

    // Info
    if (target.classList.contains('info-btn')) {
      const imei = target.dataset.imei;
      info(imei);
    }

    // Checar IMEI
    if (target.classList.contains('checar-imei')) {
      const imei = target.dataset.imei;
      checarIMEI(imei);
    }
  });
}); 

function eliminarEquipo(imei) {
  if (!confirm("¿Estás seguro de eliminar este equipo y su trabajo asociado?")) return;

  fetch('/ORION_PROYECT/APP/CONTROLLERS/eliminar_equipo_trabajo.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({ imei: imei })
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          alert("Equipo y trabajo eliminados correctamente");
          location.reload();
      } else {
          alert("Error al eliminar: " + (data.error || "Desconocido"));
      }
  })
  .catch(error => console.error('Error:', error));
}
