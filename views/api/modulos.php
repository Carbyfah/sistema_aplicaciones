<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Módulos</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoModulo">
                    <i class="fas fa-plus"></i> Nuevo Módulo
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Módulos</h3>
                <div class="card-tools">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="toggleEliminados">
                        <label class="custom-control-label" for="toggleEliminados">Ver Eliminados</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaModulos">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Módulo Padre</th>
                                <th width="150" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalModulo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalModuloTitulo">Nuevo Módulo</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formModulo">
                <div class="modal-body">
                    <input type="hidden" id="id_modulos" name="id_modulos">

                    <div class="form-group">
                        <label for="modulos_nombre">Código del Módulo *</label>
                        <input type="text" class="form-control" id="modulos_nombre" name="modulos_nombre" required>
                        <small class="form-text text-muted">Ejemplo: usuarios, proyectos, dashboard</small>
                    </div>

                    <div class="form-group">
                        <label for="modulos_descripcion">Descripción</label>
                        <textarea class="form-control" id="modulos_descripcion" name="modulos_descripcion" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="modulo_padre_id">Módulo Padre (Opcional)</label>
                        <select class="form-control" id="modulo_padre_id" name="modulo_padre_id">
                            <option value="">-- Sin Padre --</option>
                        </select>
                        <small class="form-text text-muted">Si es un submódulo, selecciona su módulo padre</small>
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

<script src="<?= asset('build/js/api/modulos.js') ?>"></script>