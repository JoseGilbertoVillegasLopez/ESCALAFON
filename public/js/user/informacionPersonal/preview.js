 //JS para previsualizar la imagen sin subirla aún 
function previewImage(event) {
  // obtenemos el input que disparó el evento y el <img> de previsualización
    const input = event.target;
    const preview = document.getElementById('preview');

  // si hay al menos un archivo seleccionado
    if (input.files && input.files[0]) {
        const reader = new FileReader(); // lector de archivos del navegador

    // cuando termine de leer, asignamos el resultado (base64) al src del <img>
    reader.onload = function(e) {
      preview.src = e.target.result;       // contenido base64 para mostrar
      preview.classList.remove('d-none');  // hacemos visible la imagen
    };

    // leemos el archivo como DataURL (base64) para poder mostrarlo
    reader.readAsDataURL(input.files[0]);
    } else {
    // si no hay archivo, ocultamos la vista previa
    preview.src = '#';
    preview.classList.add('d-none');
    }
}

