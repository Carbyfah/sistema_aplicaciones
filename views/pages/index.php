<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/<?= $_ENV['APP_NAME'] ?>/dashboard">Inicio</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <!-- Resumen de Proyectos -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3 id="total-proyectos">0</h3>
            <p>Proyectos Activos</p>
          </div>
          <div class="icon">
            <i class="fas fa-project-diagram"></i>
          </div>
          <a href="/<?= $_ENV['APP_NAME'] ?>/proyectos" class="small-box-footer">
            Más información <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3 id="total-completados">0</h3>
            <p>Proyectos Completados</p>
          </div>
          <div class="icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <a href="/<?= $_ENV['APP_NAME'] ?>/proyectos-asignados" class="small-box-footer">
            Más información <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3 id="total-documentos">0</h3>
            <p>Documentos</p>
          </div>
          <div class="icon">
            <i class="fas fa-file-alt"></i>
          </div>
          <a href="/<?= $_ENV['APP_NAME'] ?>/documentos" class="small-box-footer">
            Más información <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3 id="total-pendientes">0</h3>
            <p>Tareas Pendientes</p>
          </div>
          <div class="icon">
            <i class="fas fa-tasks"></i>
          </div>
          <a href="/<?= $_ENV['APP_NAME'] ?>/tareas" class="small-box-footer">
            Más información <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Proyectos Recientes y Estado -->
    <div class="row">
      <!-- Proyectos Recientes -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Proyectos Recientes</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table m-0" id="tabla-proyectos-recientes">
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Proyecto</th>
                    <th>Programador</th>
                    <th>Estado</th>
                    <th>Progreso</th>
                  </tr>
                </thead>
                <tbody id="lista-proyectos-recientes">
                  <tr>
                    <td colspan="5" class="text-center">Cargando datos...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer text-center">
            <a href="/<?= $_ENV['APP_NAME'] ?>/proyectos-asignados" class="btn btn-sm btn-info">
              Ver todos los proyectos
            </a>
          </div>
        </div>
      </div>

      <!-- Estado de Proyectos -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Estado de Proyectos</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <canvas id="grafico-estados" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Actividad Reciente</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm" id="tabla-actividad">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Tabla</th>
                    <th>Registro</th>
                  </tr>
                </thead>
                <tbody id="lista-actividad">
                  <tr>
                    <td colspan="5" class="text-center">Cargando actividad...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Script específico para el dashboard -->
<script src="build/js/inicio.js"></script>