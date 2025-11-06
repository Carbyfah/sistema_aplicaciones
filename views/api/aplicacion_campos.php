<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Campos</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoCampo">
                    <i class="fas fa-plus-circle"></i> Nuevo Campo
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Campos</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaCampos">
                        <thead>
                            <tr>
                                <th>Tabla</th>
                                <th>Campo</th>
                                <th>Tipo Dato</th>
                                <th>Longitud</th>
                                <th>Nulo</th>
                                <th>Clave</th>
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

<div class="modal fade" id="modalCampo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCampoTitulo">Nuevo Campo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formCampo">
                <div class="modal-body">
                    <input type="hidden" id="id_aplicacion_campos" name="id_aplicacion_campos">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="aplicacion_tablas_id">Tabla *</label>
                                <select class="form-control" id="aplicacion_tablas_id"
                                    name="aplicacion_tablas_id" required>
                                    <option value="">Seleccione una tabla</option>
                                    <?php foreach ($tablas as $tabla): ?>
                                        <option value="<?= $tabla->id_aplicacion_tablas ?>">
                                            <?= $tabla->tablas_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="campos_nombre">Nombre del Campo *</label>
                                <input type="text" class="form-control" id="campos_nombre"
                                    name="campos_nombre" required placeholder="Ej: id_usuario, nombre">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_dato_id">Tipo de Dato *</label>
                                <select class="form-control" id="tipo_dato_id" name="tipo_dato_id" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($tipos_dato as $tipo): ?>
                                        <option value="<?= $tipo->id_tipo_dato ?>">
                                            <?= $tipo->tipos_dato_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="campos_longitud">Longitud</label>
                                <input type="number" class="form-control" id="campos_longitud"
                                    name="campos_longitud" placeholder="Ej: 100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_clave_id">Tipo de Clave</label>
                                <select class="form-control" id="tipo_clave_id" name="tipo_clave_id">
                                    <option value="">Sin clave</option>
                                    <?php foreach ($tipos_clave as $clave): ?>
                                        <option value="<?= $clave->id_tipo_clave ?>">
                                            <?= $clave->tipos_clave_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                        id="campos_nulo" name="campos_nulo" value="1">
                                    <label class="custom-control-label" for="campos_nulo">
                                        Permite valores NULL
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="campos_descripcion">Descripción</label>
                        <textarea class="form-control" id="campos_descripcion"
                            name="campos_descripcion" rows="2"
                            placeholder="Descripción del campo"></textarea>
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

<script src="<?= asset('build/js/api/aplicacion_campos.js') ?>"></script>