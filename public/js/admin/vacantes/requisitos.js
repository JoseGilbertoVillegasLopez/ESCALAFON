document.addEventListener('DOMContentLoaded', () => {
    const collectionHolder = document.querySelector('.collection-requisitos');
    const addButton = document.querySelector('.add-requisito');

    collectionHolder.dataset.index = collectionHolder.querySelectorAll('.requisito-item').length;

    addButton.addEventListener('click', () => {
        const prototype = collectionHolder.dataset.prototype;
        const index = collectionHolder.dataset.index;
        const newForm = prototype.replace(/__name__/g, index);
        collectionHolder.dataset.index++;

        const requisitoDiv = document.createElement('div');
        requisitoDiv.classList.add('requisito-item', 'border', 'p-3', 'rounded', 'mb-2');
        requisitoDiv.innerHTML = newForm + '<button type="button" class="btn btn-danger btn-sm remove-requisito mt-2">Eliminar</button>';

        collectionHolder.appendChild(requisitoDiv);

        requisitoDiv.querySelector('.remove-requisito').addEventListener('click', () => {
            requisitoDiv.remove();
        });
    });

    document.querySelectorAll('.remove-requisito').forEach(button => {
        button.addEventListener('click', (e) => e.target.closest('.requisito-item').remove());
    });
});
