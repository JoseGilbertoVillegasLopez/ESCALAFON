document.addEventListener('DOMContentLoaded', () => {
  const initCollection = (selector, addBtnText) => {
    const collectionHolder = document.querySelector(selector);
    if (!collectionHolder) return;

    // Clase de item según la colección
    const itemClass = selector.includes('capacitacion')
      ? 'capacitacion-item'
      : 'contacto-item';

    // Índice inicial correcto
    collectionHolder.dataset.index =
      collectionHolder.querySelectorAll(`.${itemClass}`).length;

    // Botón "Agregar"
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'btn btn-success btn-sm mt-2';
    addButton.innerHTML = `<i class="bi bi-plus-circle"></i> ${addBtnText}`;
    collectionHolder.parentNode.insertBefore(addButton, collectionHolder.nextSibling);

    // Click en "Agregar"
    addButton.addEventListener('click', () => {
      const prototype = collectionHolder.dataset.prototype;
      if (!prototype) {
        console.error('❌ data-prototype no encontrado en', selector);
        return;
      }

      const index = collectionHolder.dataset.index;
      const newForm = prototype.replace(/__name__/g, index);

      // Crear bloque
      const item = document.createElement('div');
      item.classList.add(itemClass);
      item.innerHTML = newForm;

      // Crear botón eliminar
      const removeButton = document.createElement('button');
      removeButton.type = 'button';
      removeButton.className = 'btn btn-danger btn-sm remove-item';
      removeButton.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
      removeButton.addEventListener('click', () => item.remove());

      // ⬅️ INSERTAR EL BOTÓN DENTRO DEL HUECO DEL GRID
      const slot = item.querySelector('.eliminar-container');
      if (slot) {
        slot.appendChild(removeButton);
      } else {
        // Fallback por si en algún prototipo faltara el hueco
        item.appendChild(removeButton);
      }

      collectionHolder.appendChild(item);
      collectionHolder.dataset.index++;
    });

    // Activar eliminación para los existentes
    collectionHolder.querySelectorAll('.remove-item').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const container = e.target.closest(`.${itemClass}`);
        if (container) container.remove();
      });
    });
  };

  // Inicializar colecciones
  initCollection('.collection-capacitacion', 'Agregar capacitación');
  initCollection('.collection-contactos', 'Agregar contacto');
});
