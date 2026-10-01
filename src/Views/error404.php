<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>

<?php if ($tipo_error === 'permisos'): ?>
    <!-- 1. ERROR DE PERMISOS -->
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title text-warning">
                <i class="fas fa-user-lock mr-2"></i>Acceso Restringido (Falta de Permisos)
            </h3>
        </div>
        <div class="card-body">
            <p><?= esc($mensaje) ?></p>
            <p class="mb-0">
                <strong>Permiso requerido:</strong> 
                <span class="badge badge-warning"><?= esc($permiso) ?></span>
            </p>
        </div>
    </div>

<?php elseif ($tipo_error === 'ruta_no_encontrada'): ?>
    <!-- 2. RUTA INEXISTENTE -->
    <div class="card card-danger card-outline">
        <div class="card-header">
            <h3 class="card-title text-danger">
                <i class="fas fa-map-signs mr-2"></i>Ruta No Encontrada (Error 404)
            </h3>
        </div>
        <div class="card-body">
            <p>La URL que intentas visitar no está registrada en el sistema de rutas.</p>
            <p class="mb-0"><strong>Ruta consultada:</strong> <code><?= esc($uri ?: '/') ?></code></p>
        </div>
    </div>

<?php elseif ($tipo_error === 'controlador_faltante'): ?>
    <!-- 3A. RUTA EXISTE PERO FALTA EL CONTROLADOR -->
    <div class="card card-danger card-outline">
        <div class="card-header">
            <h3 class="card-title text-danger">
                <i class="fas fa-file-code mr-2"></i>Controlador no encontrado
            </h3>
        </div>
        <div class="card-body">
            <p>La ruta está registrada en <code>Routes.php</code>, pero el archivo o clase del controlador no existe.</p>
            <p class="mb-1"><strong>Ruta:</strong> <code><?= esc($uri) ?></code></p>
            <p class="mb-0"><strong>Clase faltante:</strong> <code><?= esc($controlador) ?></code></p>
        </div>
    </div>

<?php elseif ($tipo_error === 'metodo_faltante'): ?>
    <!-- 3B. RUTA Y CONTROLADOR EXISTEN PERO FALTA LA FUNCIÓN -->
    <div class="card card-danger card-outline">
        <div class="card-header">
            <h3 class="card-title text-danger">
                <i class="fas fa-code mr-2"></i>Método / Función no encontrada
            </h3>
        </div>
        <div class="card-body">
            <p>El controlador existe, pero la función indicada en la ruta no está declarada dentro de la clase.</p>
            <p class="mb-1"><strong>Controlador:</strong> <code><?= esc($controlador) ?></code></p>
            <p class="mb-0"><strong>Función faltante:</strong> <code><?= esc($metodo) ?>()</code></p>
        </div>
    </div>

<?php else: ?>
    <!-- 4. RECURSO GENÉRICO -->
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-2"></i>Registro no encontrado</h3>
        </div>
        <div class="card-body">
            El recurso o registro solicitado no se encuentra disponible.
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>