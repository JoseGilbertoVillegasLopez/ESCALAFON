document.addEventListener('DOMContentLoaded', () => {

    const initCollection = (selector, addBtnText) => {
        const collectionHolder = document.querySelector(selector);
        if (!collectionHolder) return;

        // ✅ índice inicial correcto
        collectionHolder.dataset.index = collectionHolder.querySelectorAll('div.form-group, fieldset, div.mb-2').length;

        // ✅ botón agregar
        const addButton = document.createElement('button');
        addButton.type = 'button';
        addButton.className = 'btn btn-success btn-sm mt-2';
        addButton.innerHTML = `<i class="bi bi-plus-circle"></i> ${addBtnText}`;
        collectionHolder.parentNode.insertBefore(addButton, collectionHolder.nextSibling);

        addButton.addEventListener('click', () => {
            const prototype = collectionHolder.dataset.prototype;

            // Seguridad: si prototype está vacío, abortar
            if (!prototype) {
                console.error('❌ data-prototype no encontrado en', selector);
                return;
            }

            const index = collectionHolder.dataset.index;
            const newForm = prototype.replace(/__name__/g, index);

            // Crear el nuevo bloque
            const item = document.createElement('div');
            item.innerHTML = newForm;
            item.classList.add('border', 'rounded', 'p-3', 'mb-2', 'bg-body-secondary');

            // Botón eliminar
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger btn-sm mt-2';
            removeButton.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
            removeButton.addEventListener('click', () => item.remove());

            // Agregar botón eliminar dentro del bloque
            item.appendChild(removeButton);

            // Agregar al contenedor
            collectionHolder.appendChild(item);

            // Incrementar índice
            collectionHolder.dataset.index++;
        });
    };

    // Inicializar ambas colecciones
    initCollection('.collection-contactos', 'Agregar contacto');
    initCollection('.collection-capacitacion', 'Agregar capacitación');
});
