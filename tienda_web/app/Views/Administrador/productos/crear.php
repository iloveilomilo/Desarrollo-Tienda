<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= isset($producto) ? 'Editar Producto' : 'Nuevo Producto' ?></h3>
    <a href="<?= base_url('admin/productos') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-info border-0 shadow-sm"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<form action="<?= isset($producto) ? base_url('admin/productos/actualizar/' . $producto['id']) : base_url('admin/productos/guardar') ?>"
    method="post" enctype="multipart/form-data">

    <div class="row">

        <!-- ── Columna izquierda (Información + Imágenes + Filtros) ── -->
        <div class="col-md-8">

            <div class="card p-4 shadow-sm mb-4 border-0">
                <h5 class="card-title text-primary mb-3">Información General</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" required
                            placeholder="Ej: iPhone 15 Pro"
                            value="<?= esc($producto['nombre'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Marca</label>
                        <input type="text" name="marca" class="form-control" required
                            placeholder="Ej: Apple"
                            value="<?= esc($producto['marca'] ?? '') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="categoria_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= (isset($producto) && $producto['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= esc($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SKU (Código único)</label>
                        <input type="text" class="form-control bg-light text-muted"
                            value="<?= esc($producto['sku'] ?? 'Automático (Ej: NP-APP-N-0001)') ?>" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" required rows="4"><?= esc($producto['descripcion'] ?? '') ?></textarea>
                </div>

                <!-- Gestión de Imágenes -->
                <div class="row mt-4 bg-light p-3 rounded mx-1">
                    <h6 class="text-primary mb-3"><i class="fas fa-camera me-2"></i>Gestión de Imágenes</h6>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Imagen Principal (Portada)</label>
                        <input type="file" name="imagen" id="imagenPrincipalInput" class="form-control" accept="image/*"
                            <?= isset($producto) ? '' : 'required' ?>>
                        <div class="form-text">
                            <?= isset($producto) ? 'Deja en blanco para conservar la imagen actual.' : 'Esta imagen siempre es obligatoria.' ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Agregar a Galería (Máx 15)</label>
                        <input type="file" name="galeria[]" id="galeriaInput" class="form-control" accept="image/*" multiple>
                        <div class="form-text">Selecciona varias con la tecla Ctrl.</div>
                    </div>

                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold mb-2">Previsualización:</label>
                        <div class="d-flex flex-wrap gap-3 p-3 bg-white border rounded" id="previewContainerGlobal">

                            <!-- Portada actual -->
                            <div class="position-relative preview-wrapper" style="width:100px; height:100px;">
                                <span class="badge bg-primary position-absolute top-0 start-50 translate-middle-x shadow-sm" style="z-index:10; margin-top:-8px;">Portada</span>
                                <?php if (isset($producto) && !empty($producto['imagen_principal'])): ?>
                                    <img id="previewPortada"
                                        src="<?= base_url('uploads/productos/' . $producto['imagen_principal']) ?>"
                                        class="img-thumbnail border-primary border-2 shadow-sm w-100 h-100"
                                        style="object-fit:cover;">
                                <?php else: ?>
                                    <img id="previewPortada"
                                        src="https://via.placeholder.com/100?text=Sin+Portada"
                                        class="img-thumbnail border-primary border-2 shadow-sm w-100 h-100 text-muted"
                                        style="object-fit:cover;">
                                <?php endif; ?>
                            </div>

                            <!-- Fotos existentes de galería (modo edición) -->
                            <?php if (isset($imagenesGaleria)): ?>
                                <?php foreach ($imagenesGaleria as $img): ?>
                                    <div class="position-relative preview-wrapper old-gallery-preview"
                                        style="width:100px; height:100px;" id="old-img-<?= $img['id'] ?>">
                                        <img src="<?= base_url('uploads/productos/' . $img['nombre_archivo']) ?>"
                                            class="img-thumbnail shadow-sm w-100 h-100" style="object-fit:cover;">
                                        <button type="button"
                                            class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-0 fw-bold shadow"
                                            style="width:22px; height:22px; line-height:1;"
                                            onclick="eliminarFotoVieja(<?= $img['id'] ?>, this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Nuevas fotos seleccionadas -->
                            <div id="nuevasGaleriaContainer" class="d-flex flex-wrap gap-3"></div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Especificaciones técnicas (filtros dinámicos) -->
            <div class="card p-4 shadow-sm border-0">
                <h5 class="card-title text-primary mb-3">Especificaciones Técnicas</h5>
                <p class="text-muted small border-bottom pb-2">Llena solo las que apliquen al equipo.</p>
                <div class="row">
                    <?php foreach ($filtros as $filtro): ?>
                        <?php
                        $valorGuardado = '';
                        if (isset($valoresFiltros)) {
                            foreach ($valoresFiltros as $vf) {
                                if ($vf['filtro_id'] == $filtro['id']) {
                                    $valorGuardado = $vf['valor'];
                                    break;
                                }
                            }
                        }
                        ?>
                        <div class="col-md-6 mb-3 contenedor-filtro" data-nombre="<?= strtolower($filtro['nombre']) ?>">
                            <label class="form-label fw-bold text-secondary"><?= esc($filtro['nombre']) ?></label>
                            <input type="text" name="filtro_<?= $filtro['id'] ?>" class="form-control"
                                placeholder="Valor..." value="<?= esc($valorGuardado) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── Columna derecha (Inventario y Ventas) ── -->
        <div class="col-md-4">
            <div class="card p-4 shadow-sm bg-light border-0">
                <h5 class="card-title text-primary">Inventario y Ventas</h5>
                <hr>

                <div class="mb-3">
                    <label class="form-label fw-bold">Condición</label>
                    <select name="condicion" id="selectCondicion" class="form-select" required onchange="toggleReacondicionado()">
                        <option value="nuevo" <?= (isset($producto) && $producto['condicion'] == 'nuevo') ? 'selected' : '' ?>>Nuevo</option>
                        <option value="reacondicionado" <?= (isset($producto) && $producto['condicion'] == 'reacondicionado') ? 'selected' : '' ?>>Reacondicionado</option>
                    </select>
                </div>

                <div class="mb-4 bg-white p-3 border rounded">
                    <label class="form-label fw-bold text-primary mb-2">¿Qué incluye?</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="caja_original" value="1" id="cajaCheck"
                            <?= (isset($producto) && $producto['caja_original'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cajaCheck">Caja original</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="cable_cargador" value="1" id="cableCheck"
                            <?= (isset($producto) && $producto['cable_cargador'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cableCheck">Cable cargador</label>
                    </div>
                </div>

                <div class="mb-4 bg-white p-3 border rounded">
                    <label class="form-label fw-bold text-primary mb-2">Conectividad</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="esim" value="1" id="esimCheck"
                            <?= (isset($producto) && $producto['esim'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="esimCheck">Solo eSIM (Sin chip físico)</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Precio ($)</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required
                        value="<?= $producto['precio'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descuento (%)</label>
                    <input type="number" name="descuento" class="form-control"
                        value="<?= $producto['descuento'] ?? '0' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Stock Disponible</label>
                    <input type="number" name="stock" class="form-control" required
                        value="<?= $producto['stock'] ?? '' ?>">
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>
                        <?= isset($producto) ? 'Guardar Cambios' : 'Publicar Producto' ?>
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
    // Ocultar filtros de batería/estética en productos nuevos
    function toggleReacondicionado() {
        const condicion = document.getElementById('selectCondicion').value;
        document.querySelectorAll('.contenedor-filtro').forEach(div => {
            const nombre = div.getAttribute('data-nombre');
            const esFiltroReacondicionado = nombre.includes('estética') || nombre.includes('estetica') ||
                nombre.includes('batería') || nombre.includes('bateria');
            if (esFiltroReacondicionado && condicion === 'nuevo') {
                div.style.display = 'none';
                if (!<?= isset($producto) ? 'true' : 'false' ?>) {
                    const input = div.querySelector('input');
                    if (input) input.value = '';
                }
            } else {
                div.style.display = 'block';
            }
        });
    }

    // Previsualización portada
    document.getElementById('imagenPrincipalInput').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = evt => document.getElementById('previewPortada').src = evt.target.result;
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Galería: acumular archivos y previsualizar
    const galeriaInput = document.getElementById('galeriaInput');
    const nuevasGaleriaContainer = document.getElementById('nuevasGaleriaContainer');
    let archivosSeleccionados = new DataTransfer();

    galeriaInput.addEventListener('change', function(e) {
        Array.from(e.target.files).forEach(file => {
            if (archivosSeleccionados.items.length < 15) {
                archivosSeleccionados.items.add(file);
            }
        });
        galeriaInput.files = archivosSeleccionados.files;
        renderizarNuevas();
    });

    function renderizarNuevas() {
        nuevasGaleriaContainer.innerHTML = '';
        Array.from(archivosSeleccionados.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'position-relative preview-wrapper';
                div.style.cssText = 'width:100px; height:100px;';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail shadow-sm w-100 h-100 border-success';
                img.style.objectFit = 'cover';

                const btn = document.createElement('button');
                btn.innerHTML = '&times;';
                btn.className = 'btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-0 fw-bold shadow';
                btn.style.cssText = 'width:22px; height:22px; line-height:1;';
                btn.onclick = e => { e.preventDefault(); eliminarArchivoNuevo(index); };

                div.appendChild(img);
                div.appendChild(btn);
                nuevasGaleriaContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    function eliminarArchivoNuevo(index) {
        const nuevaLista = new DataTransfer();
        Array.from(archivosSeleccionados.files).forEach((f, i) => { if (i !== index) nuevaLista.items.add(f); });
        archivosSeleccionados = nuevaLista;
        galeriaInput.files = archivosSeleccionados.files;
        renderizarNuevas();
    }

    // Eliminar foto existente de galería (modo edición)
    function eliminarFotoVieja(idImagenBD, btnElement) {
        if (confirm('¿Seguro que deseas quitar esta imagen de la galería?')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'eliminar_imagenes[]';
            input.value = idImagenBD;
            document.querySelector('form').appendChild(input);
            btnElement.closest('.preview-wrapper').style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', toggleReacondicionado);
</script>

<?= $this->endSection() ?>
