<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Proyectos Asignados</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoProyectoAsignado">
                    <i class="bi bi-plus-circle"></i> Nueva Asignación
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Proyectos Asignados</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="toggleEliminados">
                            <label class="custom-control-label" for="toggleEliminados">
                                Mostrar registros eliminados
                            </label>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tablaProyectosAsignados">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Proyecto</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th>Fecha Entrega</th>
                                <th width="100" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalProyectoAsignado" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProyectoAsignadoTitulo">Nuevo Proyecto Asignado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formProyectoAsignado">
                <div class="modal-body">
                    <input type="hidden" id="id_ordenes_aplicaciones" name="id_ordenes_aplicaciones">
                    <input type="hidden" id="ordenes_aplicaciones_codigo" name="ordenes_aplicaciones_codigo">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="aplicacion_id_aplicacion">Proyecto *</label>
                                <select class="form-control" id="aplicacion_id_aplicacion"
                                    name="aplicacion_id_aplicacion" required>
                                    <option value="">Seleccione un proyecto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuarios_id_usuarios">Programador Responsable *</label>
                                <select class="form-control" id="usuarios_id_usuarios"
                                    name="usuarios_id_usuarios" required>
                                    <option value="">Seleccione un programador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estados_id_estados">Estado *</label>
                                <select class="form-control" id="estados_id_estados"
                                    name="estados_id_estados" required>
                                    <option value="">Seleccione un estado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ordenes_aplicaciones_fecha_entrega">Fecha de Entrega *</label>
                                <input type="date" class="form-control" id="ordenes_aplicaciones_fecha_entrega"
                                    name="ordenes_aplicaciones_fecha_entrega" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ordenes_aplicaciones_notas">Notas / Instrucciones</label>
                        <textarea class="form-control" id="ordenes_aplicaciones_notas"
                            name="ordenes_aplicaciones_notas" rows="4"></textarea>
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

<script src="<?= asset('build/js/api/ordenes_aplicaciones.js') ?>"></script>