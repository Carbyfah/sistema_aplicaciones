<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Tareas</h1>
            </div>
            <div class="col-sm-6 text-right">
                <div class="form-group mb-0">
                    <select class="form-control" id="selectProyecto">
                        <option value="">Seleccione un proyecto</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Listado de Tareas</h3>
                    <button type="button" class="btn btn-primary" id="btnNuevaTarea" disabled>
                        <i class="bi bi-plus-circle"></i> Nueva Tarea
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="toggleEliminados">
                            <label class="custom-control-label" for="toggleEliminados">
                                Mostrar tareas eliminadas
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="toggleCompletadas" checked>
                            <label class="custom-control-label" for="toggleCompletadas">
                                Mostrar tareas completadas
                            </label>
                        </div>
                    </div>
                </div>

                <div id="tareasContainer" class="table-responsive">
                    <div class="text-center p-5 text-muted">
                        <i class="fas fa-tasks fa-3x mb-3"></i>
                        <h5>Seleccione un proyecto para visualizar sus tareas</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTareaTitulo">Nueva Tarea</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTarea">
                <div class="modal-body">
                    <input type="hidden" id="id_tareas_aplicaciones" name="id_tareas_aplicaciones">
                    <input type="hidden" id="ordenes_aplicaciones_id_ordenes_aplicaciones" name="ordenes_aplicaciones_id_ordenes_aplicaciones">

                    <div class="form-group">
                        <label for="tareas_aplicaciones_titulo">Título de la Tarea *</label>
                        <input type="text" class="form-control" id="tareas_aplicaciones_titulo"
                            name="tareas_aplicaciones_titulo" required>
                    </div>

                    <div class="form-group">
                        <label for="tareas_aplicaciones_descripcion">Descripción</label>
                        <textarea class="form-control" id="tareas_aplicaciones_descripcion"
                            name="tareas_aplicaciones_descripcion" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tareas_aplicaciones_fecha_limite">Fecha Límite</label>
                        <input type="datetime-local" class="form-control" id="tareas_aplicaciones_fecha_limite"
                            name="tareas_aplicaciones_fecha_limite">
                    </div>

                    <div class="form-group">
                        <label for="tareas_aplicaciones_prioridad">Prioridad *</label>
                        <select class="form-control" id="tareas_aplicaciones_prioridad" name="tareas_aplicaciones_prioridad" required>
                            <option value="Baja">Baja</option>
                            <option value="Media" selected>Media</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER TAREA -->
<div class="modal fade" id="modalVerTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye text-info"></i>
                    <span id="modalVerTareaTitulo">Detalles de la Tarea</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalVerTareaContenido">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= asset('build/js/api/tareas_aplicaciones.js') ?>"></script>