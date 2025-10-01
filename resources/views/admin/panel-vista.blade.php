<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Vista - CSDT</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .page-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        .page-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .page-card.libre { border-left-color: #28a745; }
        .page-card.compartida { border-left-color: #ffc107; }
        .page-card.privada { border-left-color: #dc3545; }
        .page-card.administrativa { border-left-color: #6f42c1; }
        
        .status-badge {
            font-size: 0.75rem;
        }
        
        .role-toggle {
            cursor: pointer;
        }
        
        .permission-item {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 8px 12px;
            margin: 2px;
            display: inline-block;
            font-size: 0.85rem;
        }
        
        .folder-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Panel de Vista</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-bs-toggle="tab" data-bs-target="#paginas">
                                <i class="fas fa-eye me-2"></i>Páginas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="tab" data-bs-target="#roles">
                                <i class="fas fa-users me-2"></i>Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="tab" data-bs-target="#estadisticas">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Panel de Vista</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaPaginaModal">
                            <i class="fas fa-plus me-2"></i>Nueva Página
                        </button>
                    </div>
                </div>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Páginas Tab -->
                    <div class="tab-pane fade show active" id="paginas">
                        <div class="row">
                            <div class="col-12">
                                <div class="stats-card">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <h4 id="totalPaginas">0</h4>
                                            <p class="mb-0">Total Páginas</p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 id="paginasActivas">0</h4>
                                            <p class="mb-0">Activas</p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 id="paginasPublicas">0</h4>
                                            <p class="mb-0">Públicas</p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 id="totalRoles">0</h4>
                                            <p class="mb-0">Roles</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Páginas por carpeta -->
                        <div id="paginasContainer">
                            <!-- Las páginas se cargarán aquí dinámicamente -->
                        </div>
                    </div>

                    <!-- Roles Tab -->
                    <div class="tab-pane fade" id="roles">
                        <div class="row">
                            <div class="col-12">
                                <h3>Gestión de Roles y Permisos</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Rol</th>
                                                <th>Páginas Asignadas</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rolesTableBody">
                                            <!-- Los roles se cargarán aquí -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Tab -->
                    <div class="tab-pane fade" id="estadisticas">
                        <div class="row">
                            <div class="col-12">
                                <h3>Estadísticas del Sistema</h3>
                                <div id="estadisticasContainer">
                                    <!-- Las estadísticas se cargarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Nueva Página -->
    <div class="modal fade" id="nuevaPaginaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Página</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="nuevaPaginaForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ruta" class="form-label">Ruta</label>
                                    <input type="text" class="form-control" id="ruta" name="ruta" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="carpeta" class="form-label">Carpeta</label>
                                    <select class="form-select" id="carpeta" name="carpeta" required>
                                        <option value="01">01 - Páginas Libres</option>
                                        <option value="02">02 - Páginas Libres</option>
                                        <option value="03">03 - Páginas Libres</option>
                                        <option value="04">04 - Páginas Libres</option>
                                        <option value="05">05 - Páginas Libres</option>
                                        <option value="06">06 - Páginas Libres</option>
                                        <option value="07">07 - Páginas Libres</option>
                                        <option value="08">08 - Páginas Libres</option>
                                        <option value="09">09 - Páginas Libres</option>
                                        <option value="10">10 - Páginas Libres</option>
                                        <option value="11-cliente">11 - Cliente</option>
                                        <option value="12-operador">12 - Operador</option>
                                        <option value="13-administrador">13 - Administrador</option>
                                        <option value="14-administrador-general">14 - Administrador General</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="libre">Libre</option>
                                        <option value="compartida">Compartida</option>
                                        <option value="privada">Privada</option>
                                        <option value="administrativa">Administrativa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="es_publica" name="es_publica">
                                    <label class="form-check-label" for="es_publica">
                                        Es Pública
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="requiere_autenticacion" name="requiere_autenticacion">
                                    <label class="form-check-label" for="requiere_autenticacion">
                                        Requiere Autenticación
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="icono" class="form-label">Icono</label>
                                    <input type="text" class="form-control" id="icono" name="icono" placeholder="fas fa-home">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="crearPagina()">Crear Página</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Página -->
    <div class="modal fade" id="editarPaginaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Página</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editarPaginaForm">
                        <input type="hidden" id="edit_id" name="id">
                        <!-- Los campos se llenarán dinámicamente -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarPagina()">Actualizar Página</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let paginas = {};
        let roles = [];
        let permisos = [];

        // Cargar datos iniciales
        document.addEventListener('DOMContentLoaded', function() {
            cargarPaginas();
            cargarRoles();
            cargarEstadisticas();
        });

        // Cargar páginas
        async function cargarPaginas() {
            try {
                const response = await fetch('/api/panel-vista/paginas');
                const data = await response.json();
                
                if (data.success) {
                    paginas = data.data;
                    mostrarPaginas();
                }
            } catch (error) {
                console.error('Error al cargar páginas:', error);
            }
        }

        // Mostrar páginas
        function mostrarPaginas() {
            const container = document.getElementById('paginasContainer');
            container.innerHTML = '';

            Object.keys(paginas).forEach(carpeta => {
                const paginasCarpeta = paginas[carpeta];
                
                const folderDiv = document.createElement('div');
                folderDiv.className = 'mb-4';
                
                const folderHeader = document.createElement('div');
                folderHeader.className = 'folder-header';
                folderHeader.innerHTML = `
                    <h4><i class="fas fa-folder me-2"></i>Carpeta ${carpeta}</h4>
                    <p class="mb-0">${paginasCarpeta.length} páginas</p>
                `;
                
                const pagesGrid = document.createElement('div');
                pagesGrid.className = 'row';
                
                paginasCarpeta.forEach(pagina => {
                    const pageCard = document.createElement('div');
                    pageCard.className = 'col-md-6 col-lg-4 mb-3';
                    pageCard.innerHTML = `
                        <div class="card page-card ${pagina.tipo}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title">
                                        <i class="fas fa-${pagina.icono || 'file'} me-2"></i>
                                        ${pagina.nombre}
                                    </h6>
                                    <span class="badge status-badge ${pagina.estado === 'activa' ? 'bg-success' : pagina.estado === 'inactiva' ? 'bg-warning' : 'bg-danger'}">
                                        ${pagina.estado}
                                    </span>
                                </div>
                                <p class="card-text text-muted small">${pagina.descripcion || 'Sin descripción'}</p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-link me-1"></i>${pagina.ruta}
                                    </small>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        ${pagina.es_publica ? '<span class="badge bg-info me-1">Pública</span>' : ''}
                                        ${pagina.requiere_autenticacion ? '<span class="badge bg-warning me-1">Auth</span>' : ''}
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editarPagina(${pagina.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarPagina(${pagina.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    pagesGrid.appendChild(pageCard);
                });
                
                folderDiv.appendChild(folderHeader);
                folderDiv.appendChild(pagesGrid);
                container.appendChild(folderDiv);
            });
        }

        // Cargar roles
        async function cargarRoles() {
            try {
                const response = await fetch('/api/roles');
                const data = await response.json();
                
                if (data.success) {
                    roles = data.data;
                    mostrarRoles();
                }
            } catch (error) {
                console.error('Error al cargar roles:', error);
            }
        }

        // Mostrar roles
        function mostrarRoles() {
            const tbody = document.getElementById('rolesTableBody');
            tbody.innerHTML = '';

            roles.forEach(rol => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${rol.nom}</td>
                    <td>
                        <span class="badge bg-primary">${rol.paginas_count || 0} páginas</span>
                    </td>
                    <td>
                        <span class="badge ${rol.est === 'act' ? 'bg-success' : 'bg-danger'}">
                            ${rol.est === 'act' ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="gestionarPermisosRol(${rol.id})">
                            <i class="fas fa-cog"></i> Gestionar
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Cargar estadísticas
        async function cargarEstadisticas() {
            try {
                const response = await fetch('/api/panel-vista/estadisticas');
                const data = await response.json();
                
                if (data.success) {
                    mostrarEstadisticas(data.data);
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        // Mostrar estadísticas
        function mostrarEstadisticas(stats) {
            document.getElementById('totalPaginas').textContent = stats.total_paginas;
            document.getElementById('paginasActivas').textContent = stats.paginas_activas;
            document.getElementById('paginasPublicas').textContent = stats.paginas_publicas;
            document.getElementById('totalRoles').textContent = stats.total_roles;

            const container = document.getElementById('estadisticasContainer');
            container.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>Páginas por Carpeta</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Carpeta</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${stats.paginas_por_carpeta.map(item => `
                                        <tr>
                                            <td>${item.carpeta}</td>
                                            <td><span class="badge bg-primary">${item.total}</span></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Páginas por Tipo</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${stats.paginas_por_tipo.map(item => `
                                        <tr>
                                            <td>${item.tipo}</td>
                                            <td><span class="badge bg-info">${item.total}</span></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        // Crear nueva página
        async function crearPagina() {
            const form = document.getElementById('nuevaPaginaForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Convertir checkboxes a boolean
            data.es_publica = formData.has('es_publica');
            data.requiere_autenticacion = formData.has('requiere_autenticacion');

            try {
                const response = await fetch('/api/panel-vista/paginas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Página creada correctamente');
                    bootstrap.Modal.getInstance(document.getElementById('nuevaPaginaModal')).hide();
                    form.reset();
                    cargarPaginas();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error al crear página:', error);
                alert('Error al crear la página');
            }
        }

        // Editar página
        function editarPagina(id) {
            // Buscar la página en los datos cargados
            let pagina = null;
            Object.values(paginas).forEach(paginasCarpeta => {
                const encontrada = paginasCarpeta.find(p => p.id === id);
                if (encontrada) pagina = encontrada;
            });

            if (pagina) {
                // Llenar el formulario de edición
                document.getElementById('edit_id').value = pagina.id;
                // Aquí se llenarían los demás campos del formulario
                
                const modal = new bootstrap.Modal(document.getElementById('editarPaginaModal'));
                modal.show();
            }
        }

        // Actualizar página
        async function actualizarPagina() {
            // Implementar lógica de actualización
            console.log('Actualizar página');
        }

        // Eliminar página
        async function eliminarPagina(id) {
            if (confirm('¿Está seguro de que desea eliminar esta página?')) {
                try {
                    const response = await fetch(`/api/panel-vista/paginas/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Página eliminada correctamente');
                        cargarPaginas();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al eliminar página:', error);
                    alert('Error al eliminar la página');
                }
            }
        }

        // Gestionar permisos de rol
        function gestionarPermisosRol(rolId) {
            console.log('Gestionar permisos para rol:', rolId);
            // Implementar modal de gestión de permisos
        }
    </script>
</body>
</html>
