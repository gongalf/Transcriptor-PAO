document.addEventListener("DOMContentLoaded", function() {
  var btnTrans = document.getElementById("btn-transcribir");
  var btnCop = document.getElementById("btn-copiar");
  var origen = document.getElementById("texto-origen");
  var destino = document.getElementById("texto-destino");
  var realizationControl = document.getElementById("sibilant-realization");

  if (!origen || !destino) return;

  var debounceTimer = null;
  var currentAbortController = null;

  // --- Recuperar y guardar preferencia de sibilantes ---
  if (realizationControl) {
    var valorGuardado = localStorage.getItem("pao_sibilant_realization");
    if (valorGuardado) {
      realizationControl.value = valorGuardado;
    }

    realizationControl.addEventListener("change", function() {
      localStorage.setItem("pao_sibilant_realization", realizationControl.value);
      // Al cambiar sibilante, transcribir de inmediato
      ejecutarTranscripcion();
    });
  }

  // --- Función principal de transcripción ---
  function ejecutarTranscripcion(esBotonManual) {
    var texto = origen.value.trim();

    if (!texto) {
      destino.value = "";
      return;
    }

    // Cancelar cualquier petición anterior que siga en curso
    if (currentAbortController) {
      currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    var textoOriginalBtn = "";
    if (esBotonManual && btnTrans) {
      btnTrans.disabled = true;
      textoOriginalBtn = btnTrans.innerText;
      btnTrans.innerText = "Transcribiendo...";
    }

    fetch("/transcriptor-engine/transcribir.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      signal: currentAbortController.signal,
      body: JSON.stringify({
        texto: texto,
        sibilant_realization: realizationControl ? realizationControl.value : "distincion"
      })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data && data.resultado !== undefined) {
        destino.value = data.resultado;
      } else if (data && data.error) {
        destino.value = "Error: " + data.error;
      }
    })
    .catch(function(err) {
      // Ignorar el error si la petición fue cancelada a propósito por teclear de nuevo
      if (err.name !== "AbortError") {
        destino.value = "Error de conexión: " + err.message;
      }
    })
    .finally(function() {
      if (esBotonManual && btnTrans) {
        btnTrans.disabled = false;
        btnTrans.innerText = textoOriginalBtn;
      }
    });
  }

  // --- Transcripción en tiempo real al escribir o pegar ---
  origen.addEventListener("input", function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function() {
      ejecutarTranscripcion(false);
    }, 10); // Cambiado de 300 a 120
  });

  // --- Clic manual en el botón TRANSCRIBIR ---
  if (btnTrans) {
    btnTrans.addEventListener("click", function() {
      clearTimeout(debounceTimer);
      ejecutarTranscripcion(true);
    });
  }

  // --- Copiar resultado ---
  if (btnCop) {
    btnCop.addEventListener("click", function() {
      if (!destino.value) return;
      navigator.clipboard.writeText(destino.value).then(function() {
        var orig = btnCop.innerText;
        btnCop.innerText = "¡Copiado!";
        setTimeout(function() { btnCop.innerText = orig; }, 2000);
      });
    });
  }
});

/* document.addEventListener("DOMContentLoaded", function() {
  var btnTrans = document.getElementById("btn-transcribir");
  var btnCop = document.getElementById("btn-copiar");
  var origen = document.getElementById("texto-origen");
  var destino = document.getElementById("texto-destino");
  var realizationControl = document.getElementById("sibilant-realization");

  if (!btnTrans || !btnCop || !origen || !destino) return;

  // --- Recuperar la selección previa guardada ---
  if (realizationControl) {
    var valorGuardado = localStorage.getItem("pao_sibilant_realization");
    if (valorGuardado) {
      realizationControl.value = valorGuardado;
    }

    // --- Guardar automáticamente cada vez que el usuario cambie la opción ---
    realizationControl.addEventListener("change", function() {
      localStorage.setItem("pao_sibilant_realization", realizationControl.value);
    });
  }

  btnTrans.addEventListener("click", function() {
    var texto = origen.value.trim();
    if (!texto) {
      destino.value = "";
      return;
    }

    btnTrans.disabled = true;
    var textoOriginal = btnTrans.innerText;
    btnTrans.innerText = "Transcribiendo...";

    fetch("/transcriptor-engine/transcribir.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        texto: texto,
        sibilant_realization: realizationControl ? realizationControl.value : "distincion"
      })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data && data.resultado !== undefined) {
        destino.value = data.resultado;
      } else if (data && data.error) {
        destino.value = "Error: " + data.error;
      } else {
        destino.value = "Respuesta no esperada del servidor.";
      }
    })
    .catch(function(err) {
      destino.value = "Error de conexión: " + err.message;
    })
    .finally(function() {
      btnTrans.disabled = false;
      btnTrans.innerText = textoOriginal;
    });
  });

  btnCop.addEventListener("click", function() {
    if (!destino.value) return;
    navigator.clipboard.writeText(destino.value).then(function() {
      var orig = btnCop.innerText;
      btnCop.innerText = "¡Copiado!";
      setTimeout(function() { btnCop.innerText = orig; }, 2000);
    });
  });
}); */