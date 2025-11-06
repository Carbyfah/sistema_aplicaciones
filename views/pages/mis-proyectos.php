<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Mis Proyectos</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Proyectos Asignados a Mí</h3>
            </div>
            <div class="card-body">
                <div id="contenedorProyectos" class="row">
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                        <p class="mt-3 text-muted">Cargando proyectos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL DETALLES COMPLETOS -->
<div class="modal fade" id="modalDetallesProyecto" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-project-diagram text-primary"></i>
                    <span id="modalProyectoTitulo">Detalles del Proyecto</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalDetallesContenido">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">Cargando detalles...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .proyecto-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .proyecto-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        border-color: #3498db;
    }

    .proyecto-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .proyecto-codigo {
        font-size: 12px;
        color: #7f8c8d;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .proyecto-titulo {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin: 8px 0;
        line-height: 1.4;
    }

    .proyecto-estado {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .estado-pendiente {
        background-color: #fef3c7;
        color: #92400e;
    }

    .estado-en-proceso {
        background-color: #dbeafe;
        color: #1e3a8a;
    }

    .estado-completado {
        background-color: #d1fae5;
        color: #065f46;
    }

    .proyecto-fechas {
        display: flex;
        justify-content: space-between;
        margin: 15px 0;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .fecha-item {
        display: flex;
        flex-direction: column;
    }

    .fecha-label {
        font-size: 11px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .fecha-valor {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 500;
    }

    .proyecto-progreso {
        margin: 15px 0;
    }

    .progreso-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .progreso-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
    }

    .progreso-porcentaje {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 700;
    }

    .progreso-barra {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progreso-fill {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .progreso-fill.bajo {
        background: linear-gradient(90deg, #e74c3c, #c0392b);
    }

    .progreso-fill.medio {
        background: linear-gradient(90deg, #f39c12, #e67e22);
    }

    .progreso-fill.alto {
        background: linear-gradient(90deg, #3498db, #2980b9);
    }

    .progreso-fill.completo {
        background: linear-gradient(90deg, #2ecc71, #27ae60);
    }

    .proyecto-acciones {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }

    .btn-accion {
        flex: 1;
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: white;
        color: #495057;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-accion:hover {
        background: #f8f9fa;
        border-color: #3498db;
        color: #3498db;
        text-decoration: none;
    }

    .btn-accion i {
        font-size: 14px;
    }

    .sin-proyectos {
        text-align: center;
        padding: 60px 20px;
    }

    .sin-proyectos i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .sin-proyectos h4 {
        color: #6c757d;
        margin-bottom: 10px;
    }

    .sin-proyectos p {
        color: #adb5bd;
    }

    /* ESTILOS DEL MODAL */
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: 12px 20px;
    }

    .nav-tabs .nav-link.active {
        color: #3498db;
        border-bottom-color: #3498db;
        background: transparent;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #3498db;
        color: #3498db;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 15px;
        color: #2c3e50;
        font-weight: 500;
    }

    .team-member {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .team-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 18px;
        margin-right: 15px;
    }

    .team-info {
        flex: 1;
    }

    .team-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .team-role {
        font-size: 13px;
        color: #6c757d;
    }

    .tarea-item {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }

    .tarea-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .tarea-item.completada {
        background: #f0fdf4;
        border-color: #86efac;
    }

    .tarea-titulo {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tarea-prioridad {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .prioridad-alta {
        background: #fee2e2;
        color: #991b1b;
    }

    .prioridad-media {
        background: #fef3c7;
        color: #92400e;
    }

    .prioridad-baja {
        background: #dbeafe;
        color: #1e3a8a;
    }

    .documento-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .documento-item:hover {
        background: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .documento-icono {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e3f2fd;
        border-radius: 8px;
        margin-right: 12px;
        font-size: 18px;
        color: #1976d2;
    }

    .documento-info {
        flex: 1;
    }

    .documento-nombre {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 3px;
    }

    .documento-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .tabla-db {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
    }

    .tabla-db-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .tabla-db-nombre {
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .tabla-db-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .proyecto-fechas {
            flex-direction: column;
            gap: 10px;
        }

        .proyecto-acciones {
            flex-direction: column;
        }
    }
</style>

<script>
    const APP_URL = '/<?= $_ENV['APP_NAME'] ?>';

    document.addEventListener('DOMContentLoaded', function() {
        cargarMisProyectos();
    });

    function formatearFecha(fecha, conHora = true) {
        if (!fecha) return 'N/A';
        const date = new Date(fecha);
        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        const anio = date.getFullYear();

        if (conHora) {
            const hora = String(date.getHours()).padStart(2, '0');
            const min = String(date.getMinutes()).padStart(2, '0');
            return `${dia}/${mes}/${anio} ${hora}:${min}`;
        }
        return `${dia}/${mes}/${anio}`;
    }

    function getEstadoClass(estado) {
        if (estado.toLowerCase().includes('pendiente')) return 'estado-pendiente';
        if (estado.toLowerCase().includes('proceso')) return 'estado-en-proceso';
        if (estado.toLowerCase().includes('completado')) return 'estado-completado';
        return 'estado-pendiente';
    }

    function getProgresoClass(progreso) {
        if (progreso === 100) return 'completo';
        if (progreso >= 75) return 'alto';
        if (progreso >= 40) return 'medio';
        return 'bajo';
    }

    async function cargarMisProyectos() {
        try {
            const response = await fetch(`${APP_URL}/api/proyectos-asignados?usuario_actual=1`);
            const resultado = await response.json();

            const contenedor = document.getElementById('contenedorProyectos');
            contenedor.innerHTML = '';

            if (resultado.exito && resultado.data && resultado.data.length > 0) {
                resultado.data.forEach(proyecto => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4';

                    col.innerHTML = `
                    <div class="proyecto-card" onclick="verDetallesCompletos(${proyecto.id_ordenes_aplicaciones})">
                        <div class="proyecto-header">
                            <div>
                                <div class="proyecto-codigo">${proyecto.ordenes_aplicaciones_codigo || 'N/A'}</div>
                                <h4 class="proyecto-titulo">${proyecto.aplicacion_nombre || 'Sin nombre'}</h4>
                            </div>
                            <span class="proyecto-estado ${getEstadoClass(proyecto.estados_nombre || '')}">
                                ${proyecto.estados_nombre || 'Sin estado'}
                            </span>
                        </div>
                        
                        <div class="proyecto-fechas">
                            <div class="fecha-item">
                                <span class="fecha-label">Asignación</span>
                                <span class="fecha-valor">${formatearFecha(proyecto.ordenes_aplicaciones_fecha_asignacion, false)}</span>
                            </div>
                            <div class="fecha-item">
                                <span class="fecha-label">Entrega</span>
                                <span class="fecha-valor">${formatearFecha(proyecto.ordenes_aplicaciones_fecha_entrega, false)}</span>
                            </div>
                        </div>
                        
                        <div class="proyecto-progreso" data-proyecto-id="${proyecto.id_ordenes_aplicaciones}">
                            <div class="progreso-header">
                                <span class="progreso-label">Progreso</span>
                                <span class="progreso-porcentaje">0%</span>
                            </div>
                            <div class="progreso-barra">
                                <div class="progreso-fill" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="proyecto-acciones" onclick="event.stopPropagation()">
                            <a href="${APP_URL}/tareas?proyecto_id=${proyecto.id_ordenes_aplicaciones}" class="btn-accion">
                                <i class="fas fa-tasks"></i>
                                Tareas
                            </a>
                            <a href="${APP_URL}/personal-proyecto?proyecto_id=${proyecto.id_ordenes_aplicaciones}" class="btn-accion">
                                <i class="fas fa-users"></i>
                                Equipo
                            </a>
                            <a href="${APP_URL}/documentos?proyecto_id=${proyecto.id_ordenes_aplicaciones}" class="btn-accion">
                                <i class="fas fa-file-alt"></i>
                                Docs
                            </a>
                        </div>
                    </div>
                `;

                    contenedor.appendChild(col);
                });

                cargarProgresoProyectos();
            } else {
                contenedor.innerHTML = `
                <div class="col-12">
                    <div class="sin-proyectos">
                        <i class="fas fa-folder-open"></i>
                        <h4>No tienes proyectos asignados</h4>
                        <p>Cuando se te asignen proyectos, aparecerán aquí</p>
                    </div>
                </div>
            `;
            }
        } catch (error) {
            console.error('Error al cargar proyectos:', error);
            const contenedor = document.getElementById('contenedorProyectos');
            contenedor.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    Error al cargar los proyectos. Por favor, intenta de nuevo.
                </div>
            </div>
        `;
        }
    }

    async function cargarProgresoProyectos() {
        const progressElements = document.querySelectorAll('.proyecto-progreso[data-proyecto-id]');

        progressElements.forEach(async (element) => {
            const proyectoId = element.getAttribute('data-proyecto-id');
            if (!proyectoId) return;

            try {
                const response = await fetch(`${APP_URL}/api/tareas-aplicaciones/progreso?proyecto_id=${proyectoId}`);
                const resultado = await response.json();

                if (resultado.exito) {
                    const progreso = resultado.data.progreso || 0;
                    const totalTareas = resultado.data.total_tareas || 0;
                    const completadas = resultado.data.completadas || 0;

                    const porcentajeElement = element.querySelector('.progreso-porcentaje');
                    const fillElement = element.querySelector('.progreso-fill');

                    porcentajeElement.textContent = `${progreso}%`;
                    fillElement.style.width = `${progreso}%`;
                    fillElement.className = `progreso-fill ${getProgresoClass(progreso)}`;
                    fillElement.setAttribute('title', `${completadas} de ${totalTareas} tareas completadas`);
                }
            } catch (error) {
                console.error(`Error al cargar progreso del proyecto ${proyectoId}:`, error);
            }
        });
    }

    async function verDetallesCompletos(proyectoId) {
        $('#modalDetallesProyecto').modal('show');

        const contenido = document.getElementById('modalDetallesContenido');
        contenido.innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
            <p class="mt-3 text-muted">Cargando detalles completos...</p>
        </div>
    `;

        try {
            const response = await fetch(`${APP_URL}/api/proyectos-asignados/detalles-completos?id=${proyectoId}`);
            const resultado = await response.json();

            if (resultado.exito) {
                mostrarDetallesCompletos(resultado.data);
            } else {
                contenido.innerHTML = `
                <div class="alert alert-danger">
                    Error al cargar los detalles del proyecto.
                </div>
            `;
            }
        } catch (error) {
            console.error('Error:', error);
            contenido.innerHTML = `
            <div class="alert alert-danger">
                Error de conexión al servidor.
            </div>
        `;
        }
    }

    function mostrarDetallesCompletos(data) {
        const general = data.general;
        const equipo = data.equipo || [];
        const tareas = data.tareas.lista || [];
        const progreso = data.tareas.progreso || {
            total: 0,
            completadas: 0
        };
        const documentos = data.documentos || [];
        const costos = data.costos || null;
        const baseDatos = data.base_datos || [];

        document.getElementById('modalProyectoTitulo').textContent = general.aplicacion_nombre;

        const progresoCalculado = progreso.total > 0 ? Math.round((progreso.completadas / progreso.total) * 100) : 0;

        const contenido = document.getElementById('modalDetallesContenido');
        contenido.innerHTML = `
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabGeneral">
                        <i class="fas fa-info-circle"></i> General
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabEquipo">
                        <i class="fas fa-users"></i> Equipo (${equipo.length})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabTareas">
                        <i class="fas fa-tasks"></i> Tareas (${progreso.completadas}/${progreso.total})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabDocumentos">
                        <i class="fas fa-file-alt"></i> Documentos (${documentos.length})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabCostos">
                        <i class="fas fa-dollar-sign"></i> Costos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabBaseDatos">
                        <i class="fas fa-database"></i> Base de Datos (${baseDatos.length})
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- TAB GENERAL -->
                <div id="tabGeneral" class="tab-pane fade show active">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Código</div>
                                <div class="info-value">${general.ordenes_aplicaciones_codigo}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Estado</div>
                                <div class="info-value">
                                    <span class="proyecto-estado ${getEstadoClass(general.estados_nombre)}">
                                        ${general.estados_nombre}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Responsable</div>
                                <div class="info-value">${general.persona_nombres} ${general.persona_apellidos}</div>
                                <small class="text-muted">${general.persona_correo || 'Sin correo'}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Creado por</div>
                                <div class="info-value">${general.creado_por_nombre || 'N/A'} ${general.creado_por_apellido || ''}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Fecha Asignación</div>
                                <div class="info-value">${formatearFecha(general.ordenes_aplicaciones_fecha_asignacion)}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Fecha Entrega</div>
                                <div class="info-value">${formatearFecha(general.ordenes_aplicaciones_fecha_entrega, false)}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-label">Progreso General</div>
                                <div class="progreso-header">
                                    <span>${progreso.completadas} de ${progreso.total} tareas completadas</span>
                                    <span class="progreso-porcentaje">${progresoCalculado}%</span>
                                </div>
                                <div class="progreso-barra">
                                    <div class="progreso-fill ${getProgresoClass(progresoCalculado)}" style="width: ${progresoCalculado}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-label">Descripción del Proyecto</div>
                                <div class="info-value">${general.aplicacion_larga || general.aplicacion_desc_corta || 'Sin descripción'}</div>
                            </div>
                        </div>
                        ${general.ordenes_aplicaciones_notas ? `
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-label">Notas</div>
                                <div class="info-value">${general.ordenes_aplicaciones_notas}</div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <!-- TAB EQUIPO -->
                <div id="tabEquipo" class="tab-pane fade">
                    ${equipo.length > 0 ? equipo.map(miembro => `
                        <div class="team-member">
                            <div class="team-avatar">
                                ${miembro.persona_nombres.charAt(0)}${miembro.persona_apellidos.charAt(0)}
                            </div>
                            <div class="team-info">
                                <div class="team-name">${miembro.persona_nombres} ${miembro.persona_apellidos}</div>
                                <div class="team-role">${miembro.personal_proyecto_rol || miembro.roles_persona_nombre || 'Sin rol'}</div>
                                ${miembro.persona_correo ? `<small class="text-muted">${miembro.persona_correo}</small>` : ''}
                            </div>
                            <small class="text-muted">Desde ${formatearFecha(miembro.personal_proyecto_fecha_asignacion, false)}</small>
                        </div>
                    `).join('') : '<p class="text-center text-muted py-4">No hay equipo asignado</p>'}
                </div>

                <!-- TAB TAREAS -->
                <div id="tabTareas" class="tab-pane fade">
                    ${tareas.length > 0 ? tareas.map(tarea => `
                        <div class="tarea-item ${tarea.tareas_aplicaciones_completada == 1 ? 'completada' : ''}">
                            <div class="tarea-titulo">
                                ${tarea.tareas_aplicaciones_completada == 1 ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="far fa-circle text-muted"></i>'}
                                ${tarea.tareas_aplicaciones_titulo}
                                <span class="tarea-prioridad prioridad-${tarea.tareas_aplicaciones_prioridad.toLowerCase()}">
                                    ${tarea.tareas_aplicaciones_prioridad}
                                </span>
                            </div>
                            ${tarea.tareas_aplicaciones_descripcion ? `<p class="mb-2 text-muted">${tarea.tareas_aplicaciones_descripcion}</p>` : ''}
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> ${tarea.persona_nombres} ${tarea.persona_apellidos}
                                </small>
                                ${tarea.tareas_aplicaciones_fecha_limite ? `
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> ${formatearFecha(tarea.tareas_aplicaciones_fecha_limite, false)}
                                </small>
                                ` : ''}
                            </div>
                        </div>
                    `).join('') : '<p class="text-center text-muted py-4">No hay tareas asignadas</p>'}
                </div>

                <!-- TAB DOCUMENTOS -->
                <div id="tabDocumentos" class="tab-pane fade">
                    ${documentos.length > 0 ? documentos.map(doc => `
                        <div class="documento-item">
                            <div class="documento-icono">
                                <i class="fas fa-file-${doc.documentos_extension === 'pdf' ? 'pdf' : doc.documentos_extension === 'docx' ? 'word' : 'alt'}"></i>
                            </div>
                            <div class="documento-info">
                                <div class="documento-nombre">${doc.documentos_nombre}</div>
                                <div class="documento-meta">
                                    ${doc.categorias_documentos_nombre} | ${doc.persona_nombres} ${doc.persona_apellidos} | ${formatearFecha(doc.documentos_fecha_subida)}
                                </div>
                            </div>
                            <small class="text-muted">v${doc.documentos_version}</small>
                        </div>
                    `).join('') : '<p class="text-center text-muted py-4">No hay documentos</p>'}
                </div>

                <!-- TAB COSTOS -->
                <div id="tabCostos" class="tab-pane fade">
                    ${costos ? `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Complejidad</div>
                                    <div class="info-value">${costos.complejidad_nombre || 'N/A'} (Factor: ${costos.complejidad_factor || 'N/A'})</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Seguridad</div>
                                    <div class="info-value">${costos.seguridad_nombre || 'N/A'} (Factor: ${costos.seguridad_factor || 'N/A'})</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-label">Horas Estimadas</div>
                                    <div class="info-value">${costos.costos_horas_estimadas || 0} hrs</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-label">Tarifa por Hora</div>
                                    <div class="info-value">${costos.costos_moneda} ${costos.costos_tarifa_hora || 0}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-label">Costo Total</div>
                                    <div class="info-value text-primary">${costos.costos_moneda} ${costos.costos_total || 0}</div>
                                </div>
                            </div>
                            ${costos.costos_notas ? `
                            <div class="col-12">
                                <div class="info-card">
                                    <div class="info-label">Notas</div>
                                    <div class="info-value">${costos.costos_notas}</div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    ` : '<p class="text-center text-muted py-4">No hay información de costos</p>'}
                </div>

                <!-- TAB BASE DE DATOS -->
                <div id="tabBaseDatos" class="tab-pane fade">
                    ${baseDatos.length > 0 ? baseDatos.map(tabla => `
                        <div class="tabla-db">
                            <div class="tabla-db-header">
                                <div class="tabla-db-nombre">${tabla.tablas_nombre}</div>
                                <span class="tabla-db-badge">${tabla.total_campos} campos</span>
                            </div>
                            ${tabla.tablas_descripcion ? `<p class="text-muted mb-2">${tabla.tablas_descripcion}</p>` : ''}
                            <small class="text-muted">
                                Tipo: ${tabla.tipos_tabla_nombre || 'N/A'}
                            </small>
                        </div>
                    `).join('') : '<p class="text-center text-muted py-4">No hay tablas definidas</p>'}
                </div>
            </div>
        `;
    }

    // Vista Guiada - Mis Proyectos
    document.getElementById("btnVistaGuiada")?.addEventListener("click", function(e) {
        e.preventDefault();

        const steps = [{
                intro: "¡Bienvenido a Mis Proyectos! Aquí puedes ver y gestionar todos los proyectos asignados a ti.",
            },
            {
                element: "#contenedorProyectos",
                intro: "Aquí se muestran todos tus proyectos en formato de tarjetas para una mejor visualización.",
                position: "top",
            },
            {
                element: ".proyecto-card:first-child .proyecto-codigo",
                intro: "Código único identificador del proyecto.",
                position: "bottom",
            },
            {
                element: ".proyecto-card:first-child .proyecto-titulo",
                intro: "Nombre del proyecto o aplicación que estás desarrollando.",
                position: "bottom",
            },
            {
                element: ".proyecto-card:first-child .proyecto-estado",
                intro: "Estado actual del proyecto: Pendiente, En Proceso o Completado.",
                position: "left",
            },
            {
                element: ".proyecto-card:first-child .proyecto-fechas",
                intro: "Fechas importantes: cuándo se te asignó y cuándo debes entregarlo.",
                position: "top",
            },
            {
                element: ".proyecto-card:first-child .proyecto-progreso",
                intro: "Barra de progreso que muestra el avance basado en tareas completadas.",
                position: "top",
            },
            {
                element: ".proyecto-card:first-child .proyecto-acciones",
                intro: "Acciones rápidas para gestionar tareas, equipo y documentos del proyecto.",
                position: "top",
            },
            {
                element: ".proyecto-card:first-child",
                intro: "¡Haz clic en cualquier parte de la tarjeta para ver los detalles completos del proyecto!",
                position: "top",
            },
            {
                intro: "<strong>Consejo:</strong> Mantén tu progreso actualizado para tener una visión clara de tus proyectos.",
            },
            {
                intro: "<strong>Recordatorio:</strong> Revisa regularmente las fechas de entrega y coordina con tu equipo.",
            },
            {
                intro: "¡Listo! Ahora puedes gestionar eficientemente todos tus proyectos asignados.",
            }
        ];

        introJs()
            .setOptions({
                steps: steps,
                showProgress: true,
                showBullets: true,
                exitOnOverlayClick: false,
                nextLabel: "Siguiente →",
                prevLabel: "← Anterior",
                doneLabel: "Finalizar",
                skipLabel: "Salir",
            })
            .start();
    });
</script>