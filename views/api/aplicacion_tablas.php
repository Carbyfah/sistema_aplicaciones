<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Tablas de Aplicación</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevaTabla">
                    <i class="fas fa-plus-circle"></i> Nueva Tabla
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Tablas</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaAplicacionTablas">
                        <thead>
                            <tr>
                                <th>Aplicación</th>
                                <th>Nombre de Tabla</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
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

<div class="modal fade" id="modalTabla" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTablaTitulo">Nueva Tabla</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTabla">
                <div class="modal-body">
                    <input type="hidden" id="id_aplicacion_tablas" name="id_aplicacion_tablas">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="aplicacion_id_aplicacion">Aplicación *</label>
                                <select class="form-control" id="aplicacion_id_aplicacion"
                                    name="aplicacion_id_aplicacion" required>
                                    <option value="">Seleccione una aplicación</option>
                                    <?php foreach ($aplicaciones as $app): ?>
                                        <option value="<?= $app->id_aplicacion ?>">
                                            <?= $app->aplicacion_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="tablas_nombre">Nombre de la Tabla *</label>
                                <input type="text" class="form-control" id="tablas_nombre"
                                    name="tablas_nombre" required placeholder="Ej: usuarios, productos">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_tabla_id">Tipo de Tabla</label>
                                <select class="form-control" id="tipo_tabla_id" name="tipo_tabla_id">
                                    <option value="">Sin tipo</option>
                                    <?php foreach ($tipos_tabla as $tipo): ?>
                                        <option value="<?= $tipo->id_tipo_tabla ?>">
                                            <?= $tipo->tipos_tabla_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tablas_descripcion">Descripción</label>
                        <textarea class="form-control" id="tablas_descripcion"
                            name="tablas_descripcion" rows="3"
                            placeholder="Descripción de la tabla y su propósito"></textarea>
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

<script src="<?= asset('build/js/api/aplicacion_tablas.js') ?>"></script>