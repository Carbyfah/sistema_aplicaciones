<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Seguridad</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoSeguridad">
                    <i class="fas fa-plus-circle"></i> Nuevo Nivel de Seguridad
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Niveles de Seguridad</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaSeguridad">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Factor</th>
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

<div class="modal fade" id="modalSeguridad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSeguridadTitulo">Nuevo Nivel de Seguridad</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formSeguridad">
                <div class="modal-body">
                    <input type="hidden" id="id_seguridad" name="id_seguridad">

                    <div class="form-group">
                        <label for="seguridad_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="seguridad_nombre"
                            name="seguridad_nombre" required placeholder="Ej: Básica, Media, Alta, Crítica">
                    </div>

                    <div class="form-group">
                        <label for="seguridad_descripcion">Descripción</label>
                        <textarea class="form-control" id="seguridad_descripcion"
                            name="seguridad_descripcion" rows="3"
                            placeholder="Describe las características de este nivel de seguridad"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="seguridad_factor">Factor Multiplicador *</label>
                        <input type="number" step="0.01" min="0.01" class="form-control"
                            id="seguridad_factor" name="seguridad_factor"
                            value="1.00" required>
                        <small class="form-text text-muted">
                            Factor por el que se multiplicará el costo (Ej: 1.3 = +30% por seguridad)
                        </small>
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

<script src="<?= asset('build/js/api/seguridad_opciones.js') ?>"></script>