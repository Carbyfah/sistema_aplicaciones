<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Costos de Aplicaciones</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoCosto">
                    <i class="fas fa-plus-circle"></i> Nuevo Costo
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Costos por Aplicación</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaCostos">
                        <thead>
                            <tr>
                                <th>Aplicación</th>
                                <th>Horas</th>
                                <th>Tarifa/Hora</th>
                                <th>Complejidad</th>
                                <th>Seguridad</th>
                                <th>Total</th>
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

<div class="modal fade" id="modalCosto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCostoTitulo">Nuevo Costo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formCosto">
                <div class="modal-body">
                    <input type="hidden" id="id_aplicacion_costos" name="id_aplicacion_costos">

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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="costos_horas_estimadas">Horas Estimadas *</label>
                                <input type="number" step="0.01" min="0.01" class="form-control"
                                    id="costos_horas_estimadas" name="costos_horas_estimadas"
                                    required placeholder="120.50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="costos_tarifa_hora">Tarifa por Hora *</label>
                                <input type="number" step="0.01" min="0.01" class="form-control"
                                    id="costos_tarifa_hora" name="costos_tarifa_hora"
                                    required placeholder="50.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="complejidad_id">Complejidad</label>
                                <select class="form-control" id="complejidad_id" name="complejidad_id">
                                    <option value="">Sin complejidad</option>
                                    <?php foreach ($complejidades as $comp): ?>
                                        <option value="<?= $comp->id_complejidad ?>"
                                            data-factor="<?= $comp->complejidad_factor ?>">
                                            <?= $comp->complejidad_nombre ?>
                                            (<?= $comp->complejidad_factor ?>x)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="seguridad_id">Seguridad</label>
                                <select class="form-control" id="seguridad_id" name="seguridad_id">
                                    <option value="">Sin seguridad</option>
                                    <?php foreach ($seguridades as $seg): ?>
                                        <option value="<?= $seg->id_seguridad ?>"
                                            data-factor="<?= $seg->seguridad_factor ?>">
                                            <?= $seg->seguridad_nombre ?>
                                            (<?= $seg->seguridad_factor ?>x)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="costos_moneda">Moneda</label>
                                <input type="text" class="form-control" id="costos_moneda"
                                    name="costos_moneda" value="USD" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Costo Total Estimado</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="monedaSymbol">$</span>
                                    </div>
                                    <input type="text" class="form-control" id="costos_total_preview"
                                        readonly value="0.00">
                                </div>
                                <small class="form-text text-muted">
                                    Calculado automáticamente: Horas × Tarifa × Complejidad × Seguridad
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="costos_notas">Notas</label>
                        <textarea class="form-control" id="costos_notas"
                            name="costos_notas" rows="3"
                            placeholder="Observaciones o detalles adicionales"></textarea>
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

<script src="<?= asset('build/js/api/aplicacion_costos.js') ?>"></script>