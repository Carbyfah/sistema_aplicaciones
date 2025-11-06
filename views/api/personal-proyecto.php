<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Personal por Proyecto</h1>
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
                    <h3 class="card-title">Personal Asignado</h3>
                    <button type="button" class="btn btn-primary" id="btnNuevoPersonalProyecto">
                        <i class="bi bi-plus-circle"></i> Nueva Asignación
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="toggleEliminados">
                            <label class="custom-control-label" for="toggleEliminados">
                                Mostrar asignaciones eliminadas
                            </label>
                        </div>
                    </div>
                </div>

                <div id="asignacionesContainer" class="table-responsive">
                    <div class="text-center p-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>Seleccione un proyecto para visualizar su equipo</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalPersonalProyecto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPersonalProyectoTitulo">Nueva Asignación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formPersonalProyecto">
                <div class="modal-body">
                    <input type="hidden" id="id_personal_proyecto" name="id_personal_proyecto">
                    <input type="hidden" name="personal_proyecto_situacion" value="1">

                    <div class="form-group">
                        <label for="ordenes_aplicaciones_id_ordenes_aplicaciones">Proyecto *</label>
                        <select class="form-control" id="ordenes_aplicaciones_id_ordenes_aplicaciones" name="ordenes_aplicaciones_id_ordenes_aplicaciones" required>
                            <option value="">Seleccione un proyecto</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="persona_id_persona">Personal *</label>
                        <select class="form-control" id="persona_id_persona" name="persona_id_persona" required>
                            <option value="">Seleccione una persona</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/api/personal_proyecto.js') ?>"></script>