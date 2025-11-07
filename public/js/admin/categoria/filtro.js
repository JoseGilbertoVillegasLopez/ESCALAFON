document.addEventListener('DOMContentLoaded', () => {
  const inputFiltro = document.querySelector('#filtro-nombre');
  const filas = document.querySelectorAll('tbody tr');

  if (!inputFiltro) return;

  inputFiltro.addEventListener('input', () => {
    const texto = inputFiltro.value.toLowerCase();

    filas.forEach(fila => {
      const nombre = fila.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
      fila.style.display = nombre.includes(texto) ? '' : 'none';
    });
  });
});
