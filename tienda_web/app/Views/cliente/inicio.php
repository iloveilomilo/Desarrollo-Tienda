<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">

        
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body p-3">
            <div class="mb-2 mt-3">
        <h2 class="fw-bold text-dark mb-0">Tienda / Catálogo</h2>
        <p class="text-muted small">Celulares disponibles para entrega inmediata.</p>
    </div>
        
        <form action="<?= base_url('dashboard/cliente') ?>" method="get" class="mb-4 mt-2">
            
            <div class="row g-2 align-items-center mb-1">
                <div class="col-md-9 col-sm-8">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                        <span class="input-group-text bg-white border-0 text-primary ps-3 py-1"><i class="fas fa-search"></i></span>
                        <input class="form-control border-0 shadow-none bg-white ps-2 py-1" type="search" name="q" value="<?= esc($busqueda ?? '') ?>" placeholder="Busca equipos (Ej. OLED, 8GB)...">
                        <button class="btn btn-primary px-3 fw-bold py-1" type="submit" style="border-radius: 0 50rem 50rem 0;">Buscar</button>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4 text-end">
                    <button class="btn bg-white border shadow-sm text-primary fw-bold rounded-pill w-100 py-1" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados" aria-expanded="false" aria-controls="filtrosAvanzados">
                        <i class="fas fa-sliders-h me-1"></i> Filtros
                    </button>
                </div>
            </div>

            <?php $filtrosActivos = (!empty($filtros['categoria']) || !empty($filtros['marca']) || !empty($filtros['condicion']) || !empty($filtros['precio_min']) || !empty($filtros['precio_max'])); ?>
            
            <div class="collapse <?= $filtrosActivos ? 'show' : '' ?>" id="filtrosAvanzados">
                <div class="card card-body border border-light shadow-sm rounded-4 bg-white p-3 mt-2">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-filter me-2 text-primary"></i>Refina tu búsqueda</h6>
                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold mb-1">Categoría</label>
                            <select name="categoria" class="form-select form-select-sm border-light bg-light rounded-3">
                                <option value="">Todas</option>
                                <?php if(isset($categorias)): foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($filtros['categoria']) && $filtros['categoria'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= esc($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold mb-1">Marca</label>
                            <select name="marca" class="form-select form-select-sm border-light bg-light rounded-3">
                                <option value="">Todas</option>
                                <?php if(isset($marcas)): foreach ($marcas as $m): ?>
                                    <option value="<?= $m['marca'] ?>" <?= (isset($filtros['marca']) && $filtros['marca'] == $m['marca']) ? 'selected' : '' ?>>
                                        <?= strtoupper($m['marca']) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold mb-1">Condición</label>
                            <select name="condicion" class="form-select form-select-sm border-light bg-light rounded-3">
                                <option value="">Cualquier Estado</option>
                                <option value="nuevo" <?= (isset($filtros['condicion']) && $filtros['condicion'] == 'nuevo') ? 'selected' : '' ?>>Nuevo</option>
                                <option value="reacondicionado" <?= (isset($filtros['condicion']) && $filtros['condicion'] == 'reacondicionado') ? 'selected' : '' ?>>Reacondicionado</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold mb-1">Rango de Precio</label>
                            <div class="d-flex align-items-center">
                                <input type="number" name="precio_min" class="form-control form-control-sm border-light bg-light rounded-3" placeholder="Min" value="<?= esc($filtros['precio_min'] ?? '') ?>">
                                <span class="mx-2 text-muted">-</span>
                                <input type="number" name="precio_max" class="form-control form-control-sm border-light bg-light rounded-3" placeholder="Max" value="<?= esc($filtros['precio_max'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end mt-3 pt-3 border-top">
                            <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-light btn-sm text-muted rounded-pill px-3 me-2"><i class="fas fa-eraser me-2"></i>Limpiar</a>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" type="submit">Aplicar Filtros</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
    <div class="row">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $prod): ?>
                <?php 
                    // Cálculo de precios
                    $precioOriginal = $prod['precio'];
                    $descuento = $prod['descuento'] ?? 0;
                    $precioFinal = $precioOriginal;
                    $tieneDescuento = $descuento > 0;

                    if ($tieneDescuento) {
                        $precioFinal = $precioOriginal - ($precioOriginal * ($descuento / 100));
                    }
                ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-effect">
                        
                        <div class="position-absolute top-0 end-0 m-2 d-flex flex-column align-items-end">
                            <span class="badge <?= strtolower($prod['condicion']) == 'nuevo' ? 'bg-success' : 'bg-warning text-dark' ?> mb-1">
                                <?= strtoupper($prod['condicion']) ?>
                            </span>
                            <?php if ($tieneDescuento): ?>
                                <span class="badge bg-danger">
                                    -<?= $descuento ?>%
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="text-center p-3 bg-light rounded-top">
                            <img src="<?= base_url('uploads/productos/' . $prod['imagen_principal']) ?>" 
                                 class="img-fluid" 
                                 style="height: 200px; object-fit: contain;">
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted text-uppercase fw-bold"><?= $prod['marca'] ?> | SKU: <?= $prod['sku'] ?></small>
                            <h5 class="card-title fw-bold text-dark"><?= $prod['nombre'] ?></h5>
                            
                            <div class="mt-auto">
                                <?php if ($tieneDescuento): ?>
                                    <small class="text-muted text-decoration-line-through">$<?= number_format($precioOriginal, 2) ?></small>
                                    <h4 class="text-success fw-bold mb-3">$<?= number_format($precioFinal, 2) ?></h4>
                                <?php else: ?>
                                    <h4 class="text-primary fw-bold mb-3">$<?= number_format($precioFinal, 2) ?></h4>
                                <?php endif; ?>
                                
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-detalle"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#productoModal"
                                            data-id="<?= $prod['inventario_id'] ?>" 
                                            data-nombre="<?= $prod['nombre'] ?>"
                                            data-marca="<?= $prod['marca'] ?>"
                                            data-sku="<?= $prod['sku'] ?>"
                                            data-precio-original="<?= number_format($precioOriginal, 2) ?>"
                                            data-precio-final="<?= number_format($precioFinal, 2, '.', '') ?>"
                                            data-descuento="<?= $descuento ?>"
                                            data-desc="<?= $prod['descripcion'] ?>"
                                            data-condicion="<?= $prod['condicion'] ?>"
                                            data-stock="<?= $prod['stock'] ?>"
                                            data-img="<?= $prod['imagen_principal'] ?>"
                                            data-galeria='<?= $prod['galeria'] ?>'> Ver Detalles
                                    </button>
                                    
                                    <?php if ($prod['stock'] > 0): ?>
                                        <button class="btn btn-primary btn-sm btn-agregar-carrito" 
                                                data-id="<?= $prod['inventario_id'] ?>" 
                                                data-nombre="<?= $prod['nombre'] ?>" 
                                                data-precio="<?= number_format($precioFinal, 2, '.', '') ?>" 
                                                data-img="<?= $prod['imagen_principal'] ?>"
                                                data-stock="<?= $prod['stock'] ?>"> <i class="fas fa-cart-plus me-2"></i>Agregar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-times-circle me-2"></i>Agotado
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-5">
        <div class="row">
            <div class="col-md-6 text-center mb-3 mb-md-0">
                <div id="modalCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded" id="modalCarouselInner">
                        </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <small id="modalMarca" class="text-uppercase text-muted fw-bold"></small> <small class="text-muted">| SKU: <span id="modalSku"></span></small>
                <h2 id="modalNombre" class="fw-bold mb-2"></h2>
                
                <div class="mb-3">
                    <span id="modalCondicion" class="badge bg-light text-dark border"></span>
                    <span class="text-success fw-bold ms-2"><i class="fas fa-check-circle"></i> Stock: <span id="modalStock"></span></span>
                </div>

                <div id="modalPriceContainer"></div>
                
                <h6 class="fw-bold mt-3">Descripción:</h6>
                <p id="modalDesc" class="text-muted small" style="line-height: 1.6;"></p>

                <div class="mt-4 border-top pt-3">
                    <?php if(session('id')): ?>
                        <button class="btn btn-outline-info btn-sm w-100 mb-2 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDuda" aria-expanded="false" aria-controls="collapseDuda">
                            <i class="fas fa-question-circle me-2"></i>¿Tienes una duda sobre este equipo?
                        </button>
                        <div class="collapse" id="collapseDuda">
                            <div class="card card-body bg-light border-info p-3">
                                <form id="formDudaSoporte">
                                    <input type="hidden" id="dudaProductoId" name="producto_id">
                                    <label class="form-label small fw-bold text-dark mb-1">Escribe tu pregunta:</label>
                                    <textarea class="form-control form-control-sm mb-2" id="dudaMensaje" name="mensaje" rows="2" placeholder="Ej. ¿Qué accesorios incluye en la caja?" required></textarea>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-info btn-sm text-white fw-bold px-3">
                                            <i class="fas fa-paper-plane me-1"></i>Enviar a Soporte
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center py-2 mb-0 small" role="alert">
                            <i class="fas fa-info-circle me-1"></i> 
                            <a href="<?= base_url('login') ?>" class="alert-link text-info">Inicia sesión</a> o 
                            <a href="<?= base_url('registro') ?>" class="alert-link text-info">crea una cuenta</a> para hacerle preguntas al soporte.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button id="btnModalAgregar" class="btn btn-primary btn-lg btn-agregar-carrito">
                        <i class="fas fa-shopping-cart me-2"></i> Añadir al Carrito
                    </button>
                    <small id="modalStockAviso" class="text-danger fw-bold d-none mt-2 text-center">Este producto no tiene stock disponible.</small>
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Seguir viendo</button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            // abrir modal si venimos del carrito
        const productoParaAbrir = <?= isset($_GET['ver_producto']) ? esc($_GET['ver_producto']) : 'null' ?>;
        
        if (productoParaAbrir) {
            // Le damos 100 milisegundos de respiro al navegador para que cargue los datos
            setTimeout(() => {
                const botonDetalle = document.querySelector('.btn-detalle[data-id="' + productoParaAbrir + '"]');
                if (botonDetalle) {
                    botonDetalle.click();
                    window.history.replaceState({}, document.title, "<?= base_url('dashboard/cliente') ?>");
                }
            }, 100);
        }
        const botonesDetalle = document.querySelectorAll('.btn-detalle');
        
        botonesDetalle.forEach(boton => {
            boton.addEventListener('click', function() {
                const nombre = this.getAttribute('data-nombre');
                const marca = this.getAttribute('data-marca');
                const sku = this.getAttribute('data-sku');
                const precioOriginal = this.getAttribute('data-precio-original');
                const precioFinal = this.getAttribute('data-precio-final');
                const descuento = parseInt(this.getAttribute('data-descuento'));
                const desc = this.getAttribute('data-desc');
                const condicion = this.getAttribute('data-condicion');
                const stock = this.getAttribute('data-stock');
                const img = this.getAttribute('data-img');
                const id = this.getAttribute('data-id'); 

                // si el cliente tiene sesión
                const inputDuda = document.getElementById('dudaProductoId');
                if (inputDuda) {
                    inputDuda.value = id;
                }
                document.getElementById('modalNombre').textContent = nombre;
                document.getElementById('modalMarca').textContent = marca;
                document.getElementById('modalSku').textContent = sku;
                document.getElementById('modalDesc').textContent = desc;
                document.getElementById('modalStock').textContent = stock;
                const galeria = JSON.parse(this.getAttribute('data-galeria'));
                const carouselInner = document.getElementById('modalCarouselInner');
                carouselInner.innerHTML = ''; 

                galeria.forEach((imgArchivo, index) => {
                    const activeClass = index === 0 ? 'active' : '';
                    const imgUrl = '<?= base_url("uploads/productos/") ?>' + imgArchivo;
                    
                    carouselInner.innerHTML += `
                        <div class="carousel-item ${activeClass}">
                            <img src="${imgUrl}" class="d-block w-100 img-fluid" style="height: 350px; object-fit: contain;">
                        </div>
                    `;
                });
                const priceContainer = document.getElementById('modalPriceContainer');
                if (descuento > 0) {
                    priceContainer.innerHTML = `
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="text-success fw-bold mb-0 me-2">$${precioFinal}</h3>
                            <h5 class="text-muted text-decoration-line-through mb-0">$${precioOriginal}</h5>
                            <span class="badge bg-danger ms-2">-${descuento}%</span>
                        </div>
                    `;
                } else {
                    priceContainer.innerHTML = `<h3 class="text-primary fw-bold mb-4">$${precioFinal}</h3>`;
                }
                
                // Ajustar badge de condición
                const badge = document.getElementById('modalCondicion');
                badge.textContent = condicion.toUpperCase();
                badge.className = condicion.toLowerCase() === 'nuevo' 
                    ? 'badge bg-success' 
                    : 'badge bg-warning text-dark';

                const stockDisponible = parseInt(stock);
                const btnModal = document.getElementById('btnModalAgregar');
                const avisoStock = document.getElementById('modalStockAviso');

                if (stockDisponible <= 0) {
                    btnModal.disabled = true;
                    btnModal.classList.replace('btn-primary', 'btn-secondary');
                    btnModal.innerHTML = '<i class="fas fa-times-circle me-2"></i> Agotado';
                    if(avisoStock) avisoStock.classList.remove('d-none');
                } else {
                    btnModal.disabled = false;
                    btnModal.classList.replace('btn-secondary', 'btn-primary');
                    btnModal.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> Añadir al Carrito';
                    if(avisoStock) avisoStock.classList.add('d-none');
                }
                // Pasar los datos al botón "Agregar"
                btnModal.setAttribute('data-id', id);
                btnModal.setAttribute('data-nombre', nombre);
                btnModal.setAttribute('data-precio', precioFinal);
                btnModal.setAttribute('data-img', img);
                btnModal.setAttribute('data-stock', stock);
            });
        });

        // Agregar al Carrito
        const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');
        
        botonesAgregar.forEach(boton => {
            boton.addEventListener('click', function() {
                
                // Verificamos si el usuario tiene sesión iniciada
                const logueado = <?= session('id') ? 'true' : 'false' ?>;
                
                if(!logueado) {
                    // Si NO está logueado, mostramos mensaje y lo mandamos al login
                    Swal.fire({
                        title: '¡Atención!',
                        text: 'Debes iniciar sesión o crear una cuenta para agregar equipos al carrito.',
                        icon: 'warning',
                        confirmButtonText: 'Ir a Iniciar Sesión',
                        confirmButtonColor: '#764ba2'
                    }).then(() => {
                        window.location.href = '<?= base_url("login") ?>';
                    });
                    
                    return; 
                }
                const stockMaximo = parseInt(this.getAttribute('data-stock'));
                if (stockMaximo <= 0) {
                    Swal.fire({
                        title: '¡Producto Agotado!',
                        text: 'Lo sentimos, este equipo ya no tiene unidades disponibles.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                    return; 
                }
                // Si SÍ está logueado, procedemos a agregarlo
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const precio = this.getAttribute('data-precio');
                const img = this.getAttribute('data-img');

                if(!id) return;

                const formData = new FormData();
                formData.append('id', id);
                formData.append('nombre', nombre);
                formData.append('precio', precio);
                formData.append('imagen', img);

                fetch('<?= base_url('carrito/agregar') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: '¡Agregado!',
                            text: nombre + ' se añadió a tu carrito.',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
    //ENVÍO DE DUDAS A SOPORTE 
        const formDuda = document.getElementById('formDudaSoporte');
        if(formDuda) {
            formDuda.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Verificamos si tiene sesión 
                const logueado = <?= session('id') ? 'true' : 'false' ?>;
                if(!logueado) {
                    Swal.fire({
                        title: '¡Inicia Sesión!',
                        text: 'Debes iniciar sesión para poder enviar preguntas a soporte.',
                        icon: 'info',
                        confirmButtonText: 'Ir al Login',
                        confirmButtonColor: '#764ba2'
                    }).then(() => { window.location.href = '<?= base_url("login") ?>'; });
                    return;
                }

                // Enviamos la duda al servidor
                const formData = new FormData(this);
                
                fetch('<?= base_url('soporte/enviar_duda') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire('¡Pregunta Enviada!', 'El equipo de soporte te responderá pronto. Podrás ver la respuesta en tu panel.', 'success');
                        this.reset(); 
                        document.getElementById('collapseDuda').classList.remove('show'); 
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
</script>

<style>
    .hover-effect { transition: all 0.3s ease; }
    .hover-effect:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
    .modal-content { border-radius: 15px; overflow: hidden; }
</style>

<?= $this->endSection() ?>