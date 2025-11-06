<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Complejidad</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoComplejidad">
                    <i class="fas fa-plus-circle"></i> Nueva Complejidad
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Niveles de Complejidad</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaComplejidad">
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

<div class="modal fade" id="modalComplejidad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComplejidadTitulo">Nueva Complejidad</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formComplejidad">
                <div class="modal-body">
                    <input type="hidden" id="id_complejidad" name="id_complejidad">

                    <div class="form-group">
                        <label for="complejidad_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="complejidad_nombre"
                            name="complejidad_nombre" required placeholder="Ej: Baja, Media, Alta">
                    </div>

                    <div class="form-group">
                        <label for="complejidad_descripcion">Descripción</label>
                        <textarea class="form-control" id="complejidad_descripcion"
                            name="complejidad_descripcion" rows="3"
                            placeholder="Describe el nivel de complejidad"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="complejidad_factor">Factor Multiplicador *</label>
                        <input type="number" step="0.01" min="0.01" class="form-control"
                            id="complejidad_factor" name="complejidad_factor"
                            value="1.00" required>
                        <small class="form-text text-muted">
                            Factor por el que se multiplicará el costo base (Ej: 1.5 = +50%)
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

<script src="<?= asset('build/js/api/complejidad_opciones.js') ?>"></script>