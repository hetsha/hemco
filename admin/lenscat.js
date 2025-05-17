// lenscat.js - JS for lens category CRUD

document.addEventListener('DOMContentLoaded', () => {
  loadLensCategories();
  document.getElementById('addLensCatBtn').onclick = () => {
    const name = prompt('Enter new category name:');
    if (name) {
      fetch('lenscat_actions.php', { method: 'POST', body: new URLSearchParams({action:'add', name}) })
        .then(() => loadLensCategories());
    }
  };
});

function loadLensCategories() {
  fetch('lenscat_actions.php', { method: 'POST', body: new URLSearchParams({action:'get'}) })
    .then(r => r.json())
    .then(data => {
      const table = document.getElementById('lenscatTable');
      table.innerHTML = '<table><tr><th>ID</th><th>Name</th><th>Action</th></tr>' +
        data.map(cat => `<tr><td>${cat.id}</td><td>${cat.name}</td><td><button onclick="editCat(${cat.id},'${cat.name}')">Edit</button> <button onclick="deleteCat(${cat.id})">Delete</button></td></tr>`).join('') + '</table>';
    });
}

function editCat(id, oldName) {
  const name = prompt('Edit category name:', oldName);
  if (name && name !== oldName) {
    fetch('lenscat_actions.php', { method: 'POST', body: new URLSearchParams({action:'edit', id, name}) })
      .then(() => loadLensCategories());
  }
}

function deleteCat(id) {
  if (confirm('Delete this category?')) {
    fetch('lenscat_actions.php', { method: 'POST', body: new URLSearchParams({action:'delete', id}) })
      .then(() => loadLensCategories());
  }
}
