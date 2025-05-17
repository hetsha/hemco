// lens.js - JS for lens CRUD

document.addEventListener('DOMContentLoaded', () => {
  loadLenses();
  document.getElementById('addLensBtn').onclick = () => {
    const name = prompt('Enter lens name:');
    if (name) {
      fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'add', name}) })
        .then(() => loadLenses());
    }
  };
});

function loadLenses() {
  fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'get'}) })
    .then(r => r.json())
    .then(data => {
      const table = document.getElementById('lensTable');
      table.innerHTML = '<table><tr><th>ID</th><th>Name</th><th>Action</th></tr>' +
        data.map(lens => `<tr><td>${lens.id}</td><td>${lens.name}</td><td><button onclick="editLens(${lens.id},'${lens.name}')">Edit</button> <button onclick="deleteLens(${lens.id})">Delete</button></td></tr>`).join('') + '</table>';
    });
}

function editLens(id, oldName) {
  const name = prompt('Edit lens name:', oldName);
  if (name && name !== oldName) {
    fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'edit', id, name}) })
      .then(() => loadLenses());
  }
}

function deleteLens(id) {
  if (confirm('Delete this lens?')) {
    fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'delete', id}) })
      .then(() => loadLenses());
  }
}
