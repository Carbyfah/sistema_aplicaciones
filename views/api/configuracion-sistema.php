<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Configuración del Sistema</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevaConfiguracion">
                    <i class="bi bi-plus-circle"></i> Nueva Configuración
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Parámetros de Configuración</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaConfiguraciones">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Valor</th>
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

<div class="modal fade" id="modalConfiguracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfiguracionTitulo">Nueva Configuración</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formConfiguracion">
                <div class="modal-body">
                    <input type="hidden" id="id_configuracion_sistema" name="id_configuracion_sistema">

                    <div class="form-group">
                        <label for="configuracion_sistema_clave">Clave *</label>
                        <input type="text" class="form-control" id="configuracion_sistema_clave"
                            name="configuracion_sistema_clave" required>
                        <small class="form-text text-muted">
                            Formato: SECCION_PARAMETRO (ej: SISTEMA_NOMBRE)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="configuracion_sistema_valor">Valor *</label>
                        <input type="text" class="form-control" id="configuracion_sistema_valor"
                            name="configuracion_sistema_valor" required>
                    </div>

                    <div class="form-group">
                        <label for="configuracion_sistema_tipo">Tipo *</label>
                        <select class="form-control" id="configuracion_sistema_tipo"
                            name="configuracion_sistema_tipo" required>
                            <option value="string">Texto (string)</option>
                            <option value="int">Entero (int)</option>
                            <option value="float">Decimal (float)</option>
                            <option value="bool">Booleano (bool)</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="configuracion_sistema_descripcion">Descripción</label>
                        <textarea class="form-control" id="configuracion_sistema_descripcion"
                            name="configuracion_sistema_descripcion" rows="3"></textarea>
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

<script src="<?= asset('build/js/api/configuracion-sistema.js') ?>"></script>