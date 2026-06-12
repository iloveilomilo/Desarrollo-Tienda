<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\ProductoModel;

class Productos extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $productoModel = new ProductoModel();
        $data = [
            'productos' => $productoModel->obtenerInventarioCompleto()
        ];
        return view('Administrador/productos/index', $data);
    }

    public function create()
    {
        $data['categorias'] = $this->db->table('categorias')->where('activo', 1)->get()->getResultArray();
        $data['filtros']    = $this->db->table('filtros')->where('activo', 1)->get()->getResultArray();
        return view('Administrador/productos/crear', $data);
    }

    // =================================================================
    // EDITAR PRODUCTO
    // =================================================================
    public function edit($id)
    {
        $productoModel = new \App\Models\ProductoModel();

        $data['producto'] = $productoModel->obtenerProductoCompletoPorId($id);

        if (!$data['producto']) {
            return redirect()->to('/admin/productos')->with('msg', 'El producto no existe.');
        }

        $data['categorias'] = $this->db->table('categorias')->where('activo', 1)->get()->getResultArray();
        $data['filtros']    = $this->db->table('filtros')->where('activo', 1)->get()->getResultArray();
        $data['valoresFiltros'] = $this->db->table('valores_filtros')->where('inventario_id', $data['producto']['inventario_id'])->get()->getResultArray();
        $data['imagenesGaleria'] = $this->db->table('imagenes_producto')->where('producto_id', $id)->get()->getResultArray();

        return view('Administrador/productos/crear', $data);
    }

    public function store()
    {
        // 1. Manejo de la Imagen Principal
        $img = $this->request->getFile('imagen');
        if (!$img->isValid()) {
            return redirect()->back()->with('msg', 'Debes subir una imagen de portada válida.');
        }

        $nuevoNombre = $img->getRandomName();
        $img->move(ROOTPATH . 'public/uploads/productos', $nuevoNombre);

        try {
            $datosProducto = [
                'categoria_id'     => $this->request->getPost('categoria_id'),
                'nombre'           => $this->request->getPost('nombre'),
                'marca'            => $this->request->getPost('marca'),
                'descripcion'      => $this->request->getPost('descripcion'),
                'imagen_principal' => $nuevoNombre
            ];

            $postData = $this->request->getPost();
            $productoModel = new ProductoModel();
            $skuGenerado = $productoModel->guardarProductoCompleto($datosProducto, $postData);

            if ($files = $this->request->getFiles()) {

                $productoRecienCreado = $productoModel->orderBy('id', 'DESC')->first();
                $nuevoProductoID = $productoRecienCreado['id'];

                if (isset($files['galeria'])) {
                    foreach ($files['galeria'] as $fotoGaleria) {
                        if ($fotoGaleria->isValid() && !$fotoGaleria->hasMoved()) {
                            $nombreGaleria = $fotoGaleria->getRandomName();
                            $fotoGaleria->move(ROOTPATH . 'public/uploads/productos', $nombreGaleria);

                            $this->db->table('imagenes_producto')->insert([
                                'producto_id' => $nuevoProductoID,
                                'nombre_archivo' => $nombreGaleria
                            ]);
                        }
                    }
                }
            }

            return redirect()->to('/admin/productos')->with('msg', '¡Éxito! Producto y galería publicados. Código: <strong>' . $skuGenerado . '</strong>');
        } catch (\Throwable $e) {
            // Si algo sale mal se borra la imagen que se subió
            if (file_exists(ROOTPATH . 'public/uploads/productos/' . $nuevoNombre)) {
                unlink(ROOTPATH . 'public/uploads/productos/' . $nuevoNombre);
            }
            return redirect()->back()->with('msg', 'Error al guardar. Detalle: ' . $e->getMessage())->withInput();
        }
    }

    // =================================================================
    // PROCESAR LA ACTUALIZACIÓN EN BASE DE DATOS
    // =================================================================
    public function actualizar($id)
    {
        $productoModel = new \App\Models\ProductoModel();

        $productoActual = $productoModel->find($id);

        $postData = $this->request->getPost();

        $datosProducto = [
            'categoria_id' => $postData['categoria_id'],
            'nombre'       => $postData['nombre'],
            'marca'        => $postData['marca'],
            'descripcion'  => $postData['descripcion']
        ];

        $img = $this->request->getFile('imagen');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $nuevoNombre = $img->getRandomName();
            $img->move(ROOTPATH . 'public/uploads/productos', $nuevoNombre);
            $datosProducto['imagen_principal'] = $nuevoNombre;

            if (!empty($productoActual['imagen_principal'])) {
                $rutaVieja = ROOTPATH . 'public/uploads/productos/' . $productoActual['imagen_principal'];
                if (file_exists($rutaVieja)) {
                    unlink($rutaVieja);
                }
            }
        }

        try {
            $productoModel->actualizarProductoCompleto($id, $datosProducto, $postData);

            // =========================================================
            // Se elmiminaron fotos viejas de la galeria?
            // =========================================================
            if ($imagenesAEliminar = $this->request->getPost('eliminar_imagenes')) {
                foreach ($imagenesAEliminar as $img_id) {
                    $imgData = $this->db->table('imagenes_producto')->where('id', $img_id)->get()->getRow();
                    if ($imgData) {
                        // se borra el archivo físico
                        $rutaFisica = ROOTPATH . 'public/uploads/productos/' . $imgData->nombre_archivo;
                        if (file_exists($rutaFisica)) {
                            unlink($rutaFisica);
                        }
                        // y se borra de la bd
                        $this->db->table('imagenes_producto')->where('id', $img_id)->delete();
                    }
                }
            }

            // =========================================================
            // Se subieron fotos nuevas a la galeria?
            // =========================================================
            if ($files = $this->request->getFiles()) {
                if (isset($files['galeria'])) {
                    foreach ($files['galeria'] as $fotoGaleria) {
                        if ($fotoGaleria->isValid() && !$fotoGaleria->hasMoved()) {
                            $nombreGaleria = $fotoGaleria->getRandomName();
                            $fotoGaleria->move(ROOTPATH . 'public/uploads/productos', $nombreGaleria);

                            $this->db->table('imagenes_producto')->insert([
                                'producto_id' => $id,
                                'nombre_archivo' => $nombreGaleria
                            ]);
                        }
                    }
                }
            }

            return redirect()->to('/admin/productos')->with('msg', '¡Producto actualizado correctamente!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('msg', 'Error al actualizar. Detalle: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $productoModel = new ProductoModel();
        $productoModel->bajaLogica($id);
        return redirect()->to('/admin/productos')->with('msg', 'Producto Dado de Baja.');
    }
}
