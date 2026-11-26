<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<?php
$locale            = service('request')->getLocale();
$intlLocale        = str_starts_with($locale, 'es') ? 'es_ES' : 'en_US';
$formatter         = new IntlDateFormatter($intlLocale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$isOverviewDefault = ($defaultTabId === 0);
?>

<div class="container-fluid">

    <!-- Dashboard Tabs Bar (also acts as a drop zone) -->
    <div class="card card-primary card-outline card-outline-tabs shadow-sm mb-3 dashboard-tabs-wrapper">
        <div class="card-header p-0 border-bottom-0 d-flex align-items-center justify-content-between flex-wrap" id="dashboard-tab-dropzone">
            <ul class="nav nav-tabs" id="dashboard-main-tabs" role="tablist">
                <!-- Default Overview Tab -->
                <li class="nav-item">
                    <a class="nav-link <?= $isOverviewDefault ? 'active' : '' ?>"
                       id="tab-link-0"
                       data-toggle="pill"
                       href="#tab-pane-0"
                       role="tab"
                       aria-controls="tab-pane-0"
                       aria-selected="<?= $isOverviewDefault ? 'true' : 'false' ?>">
                        <i class="fas fa-tachometer-alt mr-1"></i>
                        <?= esc(lang('Dashboard.overviewTab')) ?>
                        <button type="button"
                                class="btn btn-xs btn-link p-0 ml-2 btn-set-default-tab <?= $isOverviewDefault ? 'text-warning' : 'text-muted' ?>"
                                data-id="0"
                                title="<?= esc(lang('Dashboard.setAsDefaultTab')) ?>">
                            <i class="<?= $isOverviewDefault ? 'fas' : 'far' ?> fa-star"></i>
                        </button>
                    </a>
                </li>

                <!-- User Custom Tabs -->
                <?php foreach ($customTabs as $tab): ?>
                    <?php $isActive = ((int) $tab['id'] === $defaultTabId); ?>
                    <li class="nav-item" id="nav-item-tab-<?= (int) $tab['id'] ?>">
                        <a class="nav-link dashboard-custom-tab-link <?= $isActive ? 'active' : '' ?>"
                           id="tab-link-<?= (int) $tab['id'] ?>"
                           data-toggle="pill"
                           href="#tab-pane-<?= (int) $tab['id'] ?>"
                           data-url="<?= esc($tab['resolved_url'], 'attr') ?>"
                           data-tab-id="<?= (int) $tab['id'] ?>"
                           role="tab"
                           aria-controls="tab-pane-<?= (int) $tab['id'] ?>"
                           aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                            <i class="<?= esc($tab['icon'], 'attr') ?> mr-1"></i>
                            <span><?= esc($tab['title']) ?></span>

                            <button type="button"
                                    class="btn btn-xs btn-link p-0 ml-2 btn-set-default-tab <?= $isActive ? 'text-warning' : 'text-muted' ?>"
                                    data-id="<?= (int) $tab['id'] ?>"
                                    title="<?= esc(lang('Dashboard.setAsDefaultTab')) ?>">
                                <i class="<?= $isActive ? 'fas' : 'far' ?> fa-star"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-xs btn-link p-0 ml-1 text-danger btn-delete-shortcut"
                                    data-id="<?= (int) $tab['id'] ?>"
                                    data-scope="tab"
                                    title="<?= esc(lang('Dashboard.removeItem')) ?>">
                                <i class="fas fa-times"></i>
                            </button>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="px-3 py-1 small text-muted d-none d-md-block">
                <i class="fas fa-hand-pointer mr-1"></i><?= esc(lang('Dashboard.dropHere')) ?>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboard-tabs-content">

        <!-- Main Overview Pane -->
        <div class="tab-pane fade <?= $isOverviewDefault ? 'show active' : '' ?>" id="tab-pane-0" role="tabpanel">

            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="jumbotron bg-gradient-primary text-white rounded-lg shadow-lg p-4 p-md-5 mb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h1 class="display-4"><?= esc(lang('Dashboard.welcome')) ?>, <span class="font-weight-bold"><?= esc($userName) ?></span>!</h1>
                                <p class="lead"><?= esc(lang('Dashboard.welcomeSubtitle')) ?></p>
                                <div class="d-flex mt-4">
                                    <div class="mr-4">
                                        <p class="mb-0"><?= esc(lang('Dashboard.todayIs')) ?></p>
                                        <h4 class="mb-0"><?= esc($formatter->format(new DateTime())) ?></h4>
                                    </div>
                                    <div>
                                        <p class="mb-0"><?= esc(lang('Dashboard.localTime')) ?></p>
                                        <h4 class="mb-0" id="current-time"><?= date('h:i:s A') ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="d-none d-md-block">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M0 0h24v24H0z' fill='none'/%3E%3Cpath d='M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z'/%3E%3C/svg%3E"
                                     alt="Welcome" style="height: 150px; opacity: 0.2;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Common Functions Drop Zone & Grid -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-lg" id="common-functions-dropzone">
                        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <h5 class="mb-0 font-weight-bold">
                                    <i class="fas fa-star text-warning mr-2"></i><?= esc(lang('Dashboard.commonFunctions')) ?>
                                </h5>
                                <small class="text-muted"><?= esc(lang('Dashboard.commonFunctionsHint')) ?></small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="drop-overlay-hint text-center py-3 mb-3 rounded d-none" id="drop-overlay-hint">
                                <i class="fas fa-plus-circle fa-2x text-primary mb-2"></i>
                                <p class="mb-0 font-weight-bold text-primary"><?= esc(lang('Dashboard.dropHere')) ?></p>
                            </div>

                            <div class="row" id="common-functions-list">
                                <?php if (empty($commonFunctions)): ?>
                                    <div class="col-12 text-center py-4 text-muted" id="empty-shortcuts-msg">
                                        <i class="fas fa-arrows-alt fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0"><?= esc(lang('Dashboard.noCommonFunctions')) ?></p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($commonFunctions as $shortcut): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3 shortcut-card-col" id="shortcut-card-<?= (int) $shortcut['id'] ?>">
                                            <div class="card shortcut-card h-100 shadow-sm border">
                                                <div class="card-body d-flex align-items-center justify-content-between p-3">
                                                    <a href="<?= esc($shortcut['resolved_url'], 'attr') ?>" class="d-flex align-items-center text-dark text-decoration-none flex-grow-1 overflow-hidden">
                                                        <div class="shortcut-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0">
                                                            <i class="<?= esc($shortcut['icon'], 'attr') ?>"></i>
                                                        </div>
                                                        <div class="text-truncate">
                                                            <h6 class="mb-0 font-weight-bold text-truncate"><?= esc($shortcut['title']) ?></h6>
                                                            <small class="text-muted text-truncate d-block"><?= esc($shortcut['relative_url']) ?></small>
                                                        </div>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-link text-danger ml-2 btn-delete-shortcut"
                                                            data-id="<?= (int) $shortcut['id'] ?>"
                                                            data-scope="shortcut"
                                                            title="<?= esc(lang('Dashboard.removeItem')) ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-gradient-info text-white shadow-lg border-0 rounded-lg">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 mr-3">
                                    <i class="fas fa-users fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1"><?= esc(lang('Dashboard.newUsers')) ?></h5>
                                    <h2 class="mb-0">24</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-gradient-success text-white shadow-lg border-0 rounded-lg">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 mr-3">
                                    <i class="fas fa-chart-line fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1"><?= esc(lang('Dashboard.performance')) ?></h5>
                                    <h2 class="mb-0">98%</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-gradient-warning text-white shadow-lg border-0 rounded-lg">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 mr-3">
                                    <i class="fas fa-tasks fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1"><?= esc(lang('Dashboard.tasks')) ?></h5>
                                    <h2 class="mb-0">12/18</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-gradient-danger text-white shadow-lg border-0 rounded-lg">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3 mr-3">
                                    <i class="fas fa-calendar-check fa-2x text-danger"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1"><?= esc(lang('Dashboard.events')) ?></h5>
                                    <h2 class="mb-0">5</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom User Tab Panes (Lazy-loaded iframes) -->
        <?php foreach ($customTabs as $tab): ?>
            <?php $isActive = ((int) $tab['id'] === $defaultTabId); ?>
            <div class="tab-pane fade <?= $isActive ? 'show active' : '' ?>"
                 id="tab-pane-<?= (int) $tab['id'] ?>"
                 role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">
                            <i class="<?= esc($tab['icon'], 'attr') ?> mr-1 text-primary"></i>
                            <?= esc($tab['title']) ?>
                        </span>
                        <div>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-reload-iframe mr-1"
                                    data-target="#iframe-tab-<?= (int) $tab['id'] ?>">
                                <i class="fas fa-sync-alt mr-1"></i><?= esc(lang('Dashboard.reloadTab')) ?>
                            </button>
                            <a href="<?= esc($tab['resolved_url'], 'attr') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt mr-1"></i><?= esc(lang('Dashboard.openFullPage')) ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <iframe id="iframe-tab-<?= (int) $tab['id'] ?>"
                                class="dashboard-tab-iframe"
                                data-src="<?= esc($tab['resolved_url'], 'attr') ?>"
                                src="<?= $isActive ? esc($tab['resolved_url'], 'attr') : 'about:blank' ?>"
                                frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<!-- Pin Menu Item Modal -->
<div class="modal fade" id="pinMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="pinMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="pinMenuItemForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pinMenuItemModalLabel">
                        <i class="fas fa-thumbtack mr-2"></i><?= esc(lang('Dashboard.modalTitle')) ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pin-item-icon" name="icon" value="fas fa-link">

                    <div class="form-group">
                        <label for="pin-item-title"><?= esc(lang('Dashboard.fieldTitle')) ?></label>
                        <input type="text" class="form-control" id="pin-item-title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="pin-item-url"><?= esc(lang('Dashboard.fieldUrl')) ?></label>
                        <input type="text" class="form-control" id="pin-item-url" name="url" readonly required>
                    </div>

                    <div class="form-group">
                        <label class="d-block"><?= esc(lang('Dashboard.fieldDisplayType')) ?></label>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="display-shortcut" name="display_type" value="shortcut" class="custom-control-input" checked>
                            <label class="custom-control-label" for="display-shortcut">
                                <i class="fas fa-th-large text-info mr-1"></i> <?= esc(lang('Dashboard.optionShortcut')) ?>
                            </label>
                        </div>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="display-tab" name="display_type" value="tab" class="custom-control-input">
                            <label class="custom-control-label" for="display-tab">
                                <i class="fas fa-folder text-primary mr-1"></i> <?= esc(lang('Dashboard.optionTab')) ?>
                            </label>
                        </div>

                        <div class="custom-control custom-radio">
                            <input type="radio" id="display-both" name="display_type" value="both" class="custom-control-input">
                            <label class="custom-control-label" for="display-both">
                                <i class="fas fa-layer-group text-success mr-1"></i> <?= esc(lang('Dashboard.optionBoth')) ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-0 mt-3" id="default-tab-checkbox-wrapper" style="display: none;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="pin-item-default" name="is_default_tab" value="1">
                            <label class="custom-control-label font-weight-bold text-primary" for="pin-item-default">
                                <i class="fas fa-star text-warning mr-1"></i> <?= esc(lang('Dashboard.fieldSetDefault')) ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= esc(lang('Dashboard.btnCancel')) ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i><?= esc(lang('Dashboard.btnSave')) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .jumbotron {
        background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
        position: relative;
        overflow: hidden;
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-gradient-info    { background: linear-gradient(135deg, #396afc 0%, #2948ff 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f46b45 0%, #eea849 100%); }
    .bg-gradient-danger  { background: linear-gradient(135deg, #c31432 0%, #240b36 100%); }

    .shortcut-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .shortcut-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
    }

    .shortcut-icon {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
    }

    .dropzone-active {
        border: 2px dashed #007bff !important;
        background-color: rgba(0, 123, 255, 0.05) !important;
    }

    .drop-overlay-hint {
        border: 2px dashed #007bff;
        background-color: rgba(0, 123, 255, 0.08);
    }

    .dashboard-tab-iframe {
        width: 100%;
        min-height: 75vh;
        border: none;
        display: block;
    }

    /* Visual hint that valid sidebar links are draggable */
    .main-sidebar .nav-sidebar a.nav-link[draggable="true"] {
        cursor: grab;
    }

    .main-sidebar .nav-sidebar a.nav-link[draggable="true"]:active {
        cursor: grabbing;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const config = {
        basePath:      '<?= esc($basePath, 'js') ?>',
        saveUrl:       '<?= route_to('dashboard.shortcuts.save') ?>',
        deleteBaseUrl: '<?= rtrim($basePath, '/') ?>/admin/dashboard/shortcuts/delete/',
        setDefaultUrl: '<?= route_to('dashboard.shortcuts.setDefault') ?>',
        csrfName:      '<?= csrf_token() ?>',
        csrfHash:      '<?= csrf_hash() ?>',
        i18n: {
            confirmDelete: '<?= esc(lang('Dashboard.msgConfirmDelete'), 'js') ?>',
            invalidItem:   '<?= esc(lang('Dashboard.msgInvalidItem'), 'js') ?>',
            error:         '<?= esc(lang('Dashboard.msgError'), 'js') ?>'
        }
    };

    // 1. Live Clock
    function updateTime() {
        const el = document.getElementById('current-time');
        if (!el) return;
        const now = new Date();
        el.textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
    setInterval(updateTime, 1000);

    // 2. URL Helpers
    function isValidHref(rawHref) {
        if (!rawHref) return false;
        const trimmed = rawHref.trim();
        if (trimmed === '' || trimmed === '#' || trimmed.startsWith('javascript:')) {
            return false;
        }
        return true;
    }

    // Extract only the relative path (e.g. "admin/reportMttoSAP") without domain, IP, or port
    function extractRelativePath(href) {
        try {
            const parsed = new URL(href, window.location.origin);
            let path = parsed.pathname + parsed.search;

            if (config.basePath !== '/' && path.startsWith(config.basePath)) {
                path = path.slice(config.basePath.length);
            }

            return path.replace(/^\/+/, '');
        } catch (err) {
            return String(href || '').replace(/^\/+/, '');
        }
    }

    // 3. Make valid AdminLTE sidebar menu items draggable (excluding '#')
    function initSidebarDraggables() {
        const sidebarLinks = document.querySelectorAll('.main-sidebar .nav-sidebar a.nav-link');

        sidebarLinks.forEach(function (link) {
            const rawHref = link.getAttribute('href');
            if (!isValidHref(rawHref)) {
                link.setAttribute('draggable', 'false');
                return;
            }

            link.setAttribute('draggable', 'true');

            link.addEventListener('dragstart', function (e) {
                const iconEl  = link.querySelector('i.nav-icon, i');
                const labelEl = link.querySelector('p');
                const title   = labelEl
                    ? labelEl.childNodes[0].textContent.trim()
                    : link.textContent.trim();

                let iconClass = 'fas fa-link';
                if (iconEl) {
                    iconClass = Array.from(iconEl.classList)
                        .filter(cls => cls !== 'nav-icon')
                        .join(' ') || 'fas fa-link';
                }

                const payload = {
                    title: title,
                    url:   extractRelativePath(link.href),
                    icon:  iconClass
                };

                e.dataTransfer.setData('application/json', JSON.stringify(payload));
                e.dataTransfer.effectAllowed = 'copy';

                showDropHighlights(true);
            });

            link.addEventListener('dragend', function () {
                showDropHighlights(false);
            });
        });
    }

    // 4. Configure Drop Zones (both Common Functions card & Top Tab Bar)
    const dropZones = [
        document.getElementById('common-functions-dropzone'),
        document.getElementById('dashboard-tab-dropzone')
    ].filter(Boolean);

    function showDropHighlights(active) {
        const hint = document.getElementById('drop-overlay-hint');
        dropZones.forEach(zone => zone.classList.toggle('dropzone-active', active));
        if (hint) {
            hint.classList.toggle('d-none', !active);
        }
    }

    dropZones.forEach(function (zone) {
        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            showDropHighlights(false);

            const rawData = e.dataTransfer.getData('application/json');
            if (!rawData) return;

            let item;
            try {
                item = JSON.parse(rawData);
            } catch (err) {
                return;
            }

            if (!isValidHref(item.url)) {
                alert(config.i18n.invalidItem);
                return;
            }

            const droppedOnTabBar = zone.id === 'dashboard-tab-dropzone';
            openPinModal(item, droppedOnTabBar ? 'tab' : 'shortcut');
        });
    });

    // 5. Pin Modal Logic
    const displayTypeRadios = document.querySelectorAll('input[name="display_type"]');
    const defaultTabWrapper = document.getElementById('default-tab-checkbox-wrapper');
    const defaultTabInput   = document.getElementById('pin-item-default');

    function toggleDefaultTabCheckbox() {
        const selected = document.querySelector('input[name="display_type"]:checked')?.value;
        const show = (selected === 'tab' || selected === 'both');
        defaultTabWrapper.style.display = show ? 'block' : 'none';
        if (!show) {
            defaultTabInput.checked = false;
        }
    }

    displayTypeRadios.forEach(radio => radio.addEventListener('change', toggleDefaultTabCheckbox));

    function openPinModal(item, defaultChoice) {
        document.getElementById('pin-item-title').value = item.title || '';
        document.getElementById('pin-item-url').value   = item.url || '';
        document.getElementById('pin-item-icon').value  = item.icon || 'fas fa-link';
        defaultTabInput.checked = false;

        const targetRadio = document.querySelector(`input[name="display_type"][value="${defaultChoice}"]`);
        if (targetRadio) targetRadio.checked = true;

        toggleDefaultTabCheckbox();
        $('#pinMenuItemModal').modal('show');
    }

    // 6. Save Shortcut / Tab via AJAX
    document.getElementById('pinMenuItemForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append(config.csrfName, config.csrfHash);

        fetch(config.saveUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.csrfHash) config.csrfHash = data.csrfHash;
            if (!data.success) {
                alert(data.message || config.i18n.error);
                return;
            }
            $('#pinMenuItemModal').modal('hide');
            window.location.reload();
        })
        .catch(() => alert(config.i18n.error));
    });

    // 7. Delete Shortcut / Tab
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-shortcut');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        if (!confirm(config.i18n.confirmDelete)) return;

        const id    = btn.dataset.id;
        const scope = btn.dataset.scope || '';
        const formData = new FormData();
        formData.append('scope', scope);
        formData.append(config.csrfName, config.csrfHash);

        fetch(config.deleteBaseUrl + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.csrfHash) config.csrfHash = data.csrfHash;
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || config.i18n.error);
            }
        })
        .catch(() => alert(config.i18n.error));
    });

    // 8. Set Default Tab
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-set-default-tab');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const shortcutId = btn.dataset.id;
        const formData = new FormData();
        formData.append('shortcut_id', shortcutId);
        formData.append(config.csrfName, config.csrfHash);

        fetch(config.setDefaultUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.csrfHash) config.csrfHash = data.csrfHash;
            if (!data.success) {
                alert(data.message || config.i18n.error);
                return;
            }

            document.querySelectorAll('.btn-set-default-tab').forEach(starBtn => {
                const isDefault = String(starBtn.dataset.id) === String(shortcutId);
                starBtn.classList.toggle('text-warning', isDefault);
                starBtn.classList.toggle('text-muted', !isDefault);
                const icon = starBtn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fas', isDefault);
                    icon.classList.toggle('far', !isDefault);
                }
            });
        })
        .catch(() => alert(config.i18n.error));
    });

    // 9. Lazy-load iframes on tab click & strip AdminLTE chrome inside embedded iframes
    function cleanEmbeddedAdminLTE(iframe) {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (!doc || !doc.head) return;

            const style = doc.createElement('style');
            style.textContent = `
                .main-header, .main-sidebar, .main-footer, .content-header .breadcrumb {
                    display: none !important;
                }
                .content-wrapper {
                    margin-left: 0 !important;
                    min-height: 100vh !important;
                }
            `;
            doc.head.appendChild(style);
        } catch (err) {
            // Cross-origin URL: leave iframe as-is
        }
    }

    document.querySelectorAll('.dashboard-tab-iframe').forEach(iframe => {
        iframe.addEventListener('load', function () {
            if (iframe.src && iframe.src !== 'about:blank') {
                cleanEmbeddedAdminLTE(iframe);
            }
        });
    });

    $('a.dashboard-custom-tab-link').on('shown.bs.tab', function (e) {
        const tabId  = e.target.dataset.tabId;
        const iframe = document.getElementById('iframe-tab-' + tabId);
        if (iframe && (!iframe.getAttribute('src') || iframe.getAttribute('src') === 'about:blank')) {
            iframe.setAttribute('src', iframe.dataset.src);
        }
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-reload-iframe');
        if (!btn) return;
        const iframe = document.querySelector(btn.dataset.target);
        if (iframe && iframe.dataset.src) {
            iframe.setAttribute('src', iframe.dataset.src);
        }
    });

    initSidebarDraggables();
});
</script>
<?= $this->endSection() ?>