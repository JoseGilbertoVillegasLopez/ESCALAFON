document.addEventListener('DOMContentLoaded', () => {
  const collectionHolder = document.querySelector('.collection-requisitos');
  const addButton = document.querySelector('.add-requisito');

  if (!collectionHolder || !addButton) return;

  // 🔒 Evitar duplicar eventos si el script se recarga
  if (addButton.dataset.bound === 'true') return;
  addButton.dataset.bound = 'true';

  // Inicializa el índice según los ítems existentes
  collectionHolder.dataset.index = collectionHolder.querySelectorAll('.requisito-item').length;

  // 🧱 Función para crear un nuevo bloque de requisito
  function createRequisitoForm() {
    const prototype = collectionHolder.dataset.prototype;
    const index = collectionHolder.dataset.index;
    const newForm = prototype.replace(/__name__/g, index);
    collectionHolder.dataset.index++;

    const newItem = document.createElement('div');
    newItem.classList.add('requisito-item', 'p-3', 'rounded', 'shadow-sm', 'mb-3');
    newItem.style.backgroundColor = '#2a323c';
    newItem.innerHTML = `
      <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="flex-fill">${newForm}</div>
        <button type="button" class="btn btn-outline-danger btn-sm remove-requisito">
          <i class="bi bi-trash"></i> Eliminar
        </button>
      </div>
    `;

    addRemoveListener(newItem.querySelector('.remove-requisito'));
    return newItem;
  }

  // 🔹 Listener para agregar
  addButton.addEventListener('click', () => {
    const newItem = createRequisitoForm();
    collectionHolder.appendChild(newItem);
  });

  // 🔹 Listener para eliminar
  function addRemoveListener(button) {
    button.addEventListener('click', () => {
      const item = button.closest('.requisito-item');
      if (item) item.remove();
    });
  }

  // 🔹 Aplica listener a los existentes
  document.querySelectorAll('.remove-requisito').forEach(addRemoveListener);
});
