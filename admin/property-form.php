<?php
/**
 * Formulario de Propiedades - Ibron Inmobiliaria
 * Crear y editar propiedades
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth();

$page_title = 'Agregar Propiedad';
$error = '';
$success = '';
$property = null;

// Verificar si estamos editando
$property_id = $_GET['id'] ?? null;
if ($property_id) {
    $properties = supabase_get('properties', ['id' => 'eq.' . $property_id]);
    if ($properties && count($properties) > 0) {
        $property = $properties[0];
        $page_title = 'Editar Propiedad';
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        // Limpiar precio de formato (RD$ 1,000.00 -> 1000.00)
        $clean_price = preg_replace('/[^\d.]/', '', $_POST['price']);
        $data = [
            'title' => sanitize_input($_POST['title']),
            'description' => sanitize_input($_POST['description']),
            'type' => sanitize_input($_POST['type']),
            'price' => (float)$clean_price,
            'bedrooms' => (int)$_POST['bedrooms'],
            'bathrooms' => (int)$_POST['bathrooms'],
            'area' => (float)$_POST['area'],
            'location' => sanitize_input($_POST['location']),
            'address' => sanitize_input($_POST['address']),
            'status' => sanitize_input($_POST['status']),
            'featured' => isset($_POST['featured']) ? true : false,
            'created_by' => $_SESSION['user_id']
        ];

        // Procesar características (features)
        if (!empty($_POST['features'])) {
            $features = array_map('sanitize_input', $_POST['features']);
            $data['features'] = '{' . implode(',', array_map(fn($f) => '"' . $f . '"', $features)) . '}';
        }

        // Procesar amenidades
        if (!empty($_POST['amenities'])) {
            $amenities = array_map('sanitize_input', $_POST['amenities']);
            $data['amenities'] = '{' . implode(',', array_map(fn($a) => '"' . $a . '"', $amenities)) . '}';
        }

        // Generar ID para la propiedad (si es nueva)
        $prop_id = $property_id ?? uniqid();

        // ==========================================
        // NUEVO: PROCESAR IMÁGENES RECORTADAS/COMPRIMIDAS (BASE64)
        // ==========================================
        
        // 1. Imagen Principal Recortada
        if (!empty($_POST['cropped_main'])) {
            $base64_img = $_POST['cropped_main'];
            $file_data = decode_base64_image($base64_img);
            
            if ($file_data) {
                $temp_file = tempnam(sys_get_temp_dir(), 'prop_main_');
                file_put_contents($temp_file, $file_data['data']);
                
                $file_array = [
                    'name' => 'main_optimized.jpg',
                    'type' => $file_data['type'],
                    'tmp_name' => $temp_file,
                    'error' => UPLOAD_ERR_OK,
                    'size' => strlen($file_data['data'])
                ];
                
                $upload_result = upload_property_image($file_array, $prop_id, 'main');
                if ($upload_result) {
                    $data['image_main'] = $upload_result;
                }
                unlink($temp_file);
            }
        }
        
        // 2. Galería Recortada (Múltiples)
        if (!empty($_POST['cropped_gallery']) && is_array($_POST['cropped_gallery'])) {
            $gallery_urls = !empty($property['image_gallery']) 
                ? (is_array($property['image_gallery']) ? $property['image_gallery'] : pg_array_to_php_array($property['image_gallery'])) 
                : [];

            foreach ($_POST['cropped_gallery'] as $index => $base64_img) {
                $file_data = decode_base64_image($base64_img);
                if ($file_data) {
                    $temp_file = tempnam(sys_get_temp_dir(), 'prop_gal_');
                    file_put_contents($temp_file, $file_data['data']);
                    
                    $file_array = [
                        'name' => 'gallery_optimized_' . $index . '.jpg',
                        'type' => $file_data['type'],
                        'tmp_name' => $temp_file,
                        'error' => UPLOAD_ERR_OK,
                        'size' => strlen($file_data['data'])
                    ];
                    
                    $upload_result = upload_property_image($file_array, $prop_id, 'gallery');
                    if ($upload_result) {
                        $gallery_urls[] = $upload_result;
                    }
                    unlink($temp_file);
                }
            }
            
            if (!empty($gallery_urls)) {
                $data['image_gallery'] = php_array_to_pg_array($gallery_urls);
            }
        }
        // ==========================================
        
        // Subir imagen principal si se proporcionó
        if (isset($_FILES['image_main']) && $_FILES['image_main']['error'] === UPLOAD_ERR_OK && empty($_POST['cropped_main'])) {
            $upload_result = upload_property_image($_FILES['image_main'], $prop_id, 'main');
            if ($upload_result) {
                $data['image_main'] = $upload_result;
            } else {
                $error = 'Error al subir la imagen principal';
            }
        }

        // Subir imágenes de galería si se proporcionaron
        $gallery_urls = [];
        if (isset($_FILES['image_gallery']) && !empty($_FILES['image_gallery']['name'][0]) && empty($_POST['cropped_gallery'])) {
            $files = $_FILES['image_gallery'];
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    // Crear array temporal para cada archivo
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                    
                    $upload_result = upload_property_image($file, $prop_id, 'gallery');
                    if ($upload_result) {
                        $gallery_urls[] = $upload_result;
                    }
                }
            }
            
            // Guardar URLs en formato PostgreSQL array
            if (!empty($gallery_urls)) {
                $data['image_gallery'] = '{' . implode(',', array_map(fn($url) => '"' . $url . '"', $gallery_urls)) . '}';
            }
        }

        // ==========================================
        // NUEVO: PROCESAR ELIMINACIÓN DE IMÁGENES
        // ==========================================
        if (isset($_POST['removed_images']) && is_array($_POST['removed_images'])) {
            foreach ($_POST['removed_images'] as $img_url) {
                // 1. Extraer la ruta del bucket de la URL pública
                // URL: https://.../storage/v1/object/public/property-images/main/prop_id/img.jpg
                $path_parts = explode('/public/property-images/', $img_url);
                if (count($path_parts) > 1) {
                    $storage_path = $path_parts[1];
                    
                    // 2. Eliminar físicamente de Supabase Storage
                    supabase_storage_delete('property-images', $storage_path);
                    
                    // 3. Quitar de la base de datos si es imagen principal
                    if ($img_url === ($property['image_main'] ?? '')) {
                        $data['image_main'] = null;
                    }
                    
                    // 4. Quitar de la galería (si existe)
                    if (!empty($property['image_gallery'])) {
                        $current_gallery = is_array($property['image_gallery']) 
                            ? $property['image_gallery'] 
                            : pg_array_to_php_array($property['image_gallery']);
                        
                        $index = array_search($img_url, $current_gallery);
                        if ($index !== false) {
                            unset($current_gallery[$index]);
                            $data['image_gallery'] = php_array_to_pg_array(array_values($current_gallery));
                        }
                    }
                }
            }
        }
        // ==========================================

        if (empty($error)) {
            if ($property_id) {
                // Actualizar propiedad existente
                $result = supabase_update('properties', $property_id, $data);
                if ($result) {
                    $success = 'Propiedad actualizada exitosamente';
                } else {
                    $error = 'Error al actualizar la propiedad';
                }
            } else {
                // Crear nueva propiedad
                $result = supabase_insert('properties', $data);
                if ($result) {
                    $success = 'Propiedad creada exitosamente';
                    header('Location: ' . SITE_URL . '/admin/properties-manage.php');
                    exit;
                } else {
                    $error = 'Error al crear la propiedad';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-item .remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #map-selector {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        /* Estilos para el Cropper */
        .cropper-container-wrapper {
            max-height: 500px;
            overflow: hidden;
            background: #f8f9fa;
        }
        #cropper-image {
            max-width: 100%;
            display: block;
        }
        .filter-controls {
            padding: 15px;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }
        .filter-group {
            margin-bottom: 10px;
        }
        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .image-size-badge {
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 10px;
            position: absolute;
            bottom: 2px;
            left: 2px;
        }
        .edit-existing-btn {
            position: absolute;
            top: 2px;
            left: 2px;
            background: rgba(0, 123, 255, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* FIX: Asegurar que los filtros se apliquen al cropper */
        .cropper-view-box img, 
        .cropper-canvas img {
            filter: var(--cropper-filter, none);
        }
        /* FIX: Visibilidad cabecera modal */
        #cropperModal .modal-header .modal-title {
            color: #fff !important;
        }
        #cropperModal .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar simple -->
            <nav class="col-md-2 bg-dark text-white p-3">
                <h5>Admin Panel</h5>
                <hr>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-sm btn-outline-light w-100 mb-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 p-4">
                <h2 class="mb-4"><?php echo $page_title; ?></h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo escape_output($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo escape_output($success); ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form id="property-form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                            <div class="row g-3">
                                <!-- Título -->
                                <div class="col-md-8">
                                    <label class="form-label">Título *</label>
                                    <input type="text" class="form-control" name="title" id="title"
                                           value="<?php echo escape_output($property['title'] ?? ''); ?>" required>
                                </div>

                                <!-- Tipo -->
                                <div class="col-md-4">
                                    <label class="form-label">Tipo *</label>
                                    <select class="form-select" name="type" required>
                                        <option value="">Seleccionar...</option>
                                        <?php
                                        $types = ['Casa', 'Apartamento', 'Villa', 'Solar', 'Oficina', 'Local Comercial', 'Penthouse', 'Terreno'];
                                        foreach ($types as $type) {
                                            $selected = ($property['type'] ?? '') === $type ? 'selected' : '';
                                            echo "<option value=\"$type\" $selected>$type</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Descripción -->
                                <div class="col-12">
                                    <label class="form-label">Descripción *</label>
                                    <textarea class="form-control" name="description" id="description" rows="4" required><?php echo escape_output($property['description'] ?? ''); ?></textarea>
                                </div>

                                <!-- Precio -->
                                <div class="col-md-4">
                                    <label class="form-label">Precio (RD$) *</label>
                                    <input type="text" class="form-control" name="price" id="price" autocomplete="off"
                                           value="<?php echo $property['price'] ?? ''; ?>" required>
                                </div>

                                <!-- Habitaciones -->
                                <div class="col-md-2">
                                    <label class="form-label">Habitaciones</label>
                                    <input type="number" class="form-control" name="bedrooms" min="0" 
                                           value="<?php echo $property['bedrooms'] ?? 0; ?>">
                                </div>

                                <!-- Baños -->
                                <div class="col-md-2">
                                    <label class="form-label">Baños</label>
                                    <input type="number" class="form-control" name="bathrooms" min="0" 
                                           value="<?php echo $property['bathrooms'] ?? 0; ?>">
                                </div>

                                <!-- Área -->
                                <div class="col-md-4">
                                    <label class="form-label">Área (m²)</label>
                                    <input type="number" class="form-control" name="area" step="0.01" 
                                           value="<?php echo $property['area'] ?? ''; ?>">
                                </div>

                                <!-- Ubicación -->
                                <div class="col-md-6">
                                    <label class="form-label">Ubicación *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="location" id="location"
                                               value="<?php echo escape_output($property['location'] ?? ''); ?>" required>
                                        <button class="btn btn-outline-primary" type="button" id="open-map-btn" data-bs-toggle="modal" data-bs-target="#mapModal">
                                            <i class="fas fa-map-marked-alt me-2"></i>Elegir en Mapa
                                        </button>
                                    </div>
                                    <small class="text-muted">Ciudad, sector o coordenadas</small>
                                </div>

                                <!-- Dirección -->
                                <div class="col-md-6">
                                    <label class="form-label">Dirección Completa</label>
                                    <input type="text" class="form-control" name="address" id="address"
                                           value="<?php echo escape_output($property['address'] ?? ''); ?>">
                                </div>

                                <!-- Estado -->
                                <div class="col-md-4">
                                    <label class="form-label">Estado *</label>
                                    <select class="form-select" name="status" required>
                                        <?php
                                        $statuses = ['Disponible', 'Vendida', 'Reservada', 'En Negociación'];
                                        foreach ($statuses as $status) {
                                            $selected = ($property['status'] ?? 'Disponible') === $status ? 'selected' : '';
                                            echo "<option value=\"$status\" $selected>$status</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Destacada -->
                                <div class="col-md-4">
                                    <label class="form-label d-block">Destacada</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="featured" 
                                               <?php echo ($property['featured'] ?? false) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Mostrar en página principal</label>
                                    </div>
                                </div>

                                <!-- Imagen principal -->
                                <div class="col-md-6">
                                    <label class="form-label">Imagen Principal *</label>
                                    <input type="file" class="form-control" name="image_main" id="image_main" accept="image/jpeg,image/png,image/webp">
                                    <div id="main-preview" class="preview-container">
                                        <?php if (!empty($property['image_main'])): ?>
                                            <div class="preview-item" id="main-img-container">
                                                <img src="<?php echo escape_output($property['image_main']); ?>">
                                                <button type="button" class="edit-existing-btn" title="Editar" onclick="editExistingImage('<?php echo $property['image_main']; ?>', 'main-img-container', 'main')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="remove-btn" onclick="removeExistingImage('<?php echo $property['image_main']; ?>', 'main-img-container')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">Formatos: JPG, PNG, WEBP. Máx: 5MB.</small>
                                </div>

                                <!-- Galería de imágenes -->
                                <div class="col-md-6">
                                    <label class="form-label">Galería de Imágenes (múltiples)</label>
                                    <input type="file" class="form-control" name="image_gallery[]" id="image_gallery"
                                           accept="image/jpeg,image/png,image/webp" multiple>
                                    <div id="gallery-preview" class="preview-container">
                                        <?php if (!empty($property['image_gallery'])): 
                                            $gallery = is_array($property['image_gallery']) 
                                                ? $property['image_gallery'] 
                                                : pg_array_to_php_array($property['image_gallery']);
                                            foreach ($gallery as $index => $img_url): 
                                                $img_id = "gal-img-" . $index;
                                            ?>
                                                <div class="preview-item" id="<?php echo $img_id; ?>">
                                                    <img src="<?php echo escape_output($img_url); ?>">
                                                    <button type="button" class="edit-existing-btn" title="Editar" onclick="editExistingImage('<?php echo $img_url; ?>', '<?php echo $img_id; ?>', 'gallery')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="remove-btn" onclick="removeExistingImage('<?php echo $img_url; ?>', '<?php echo $img_id; ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        JPG, PNG, WEBP permitidos.
                                    </small>
                                </div>

                                <!-- Características -->
                                <div class="col-md-6">
                                    <label class="form-label">Características (una por línea)</label>
                                    <textarea class="form-control" name="features[]" id="features_input" rows="5" 
                                              placeholder="Ej: Terraza amplia&#10;Jardín privado&#10;Garaje para 3 vehículos"><?php 
                                              if (!empty($property['features'])) {
                                                  $features = is_array($property['features']) ? $property['features'] : pg_array_to_php_array($property['features']);
                                                  echo implode("\n", $features);
                                              }
                                              ?></textarea>
                                    <small class="text-muted">Escribe una característica por línea</small>
                                </div>

                                <!-- Amenidades -->
                                <div class="col-md-6">
                                    <label class="form-label">Amenidades (una por línea)</label>
                                    <textarea class="form-control" name="amenities[]" id="amenities_input" rows="5" 
                                              placeholder="Ej: Piscina privada&#10;Área BBQ&#10;Aire acondicionado central"><?php 
                                              if (!empty($property['amenities'])) {
                                                  $amenities = is_array($property['amenities']) ? $property['amenities'] : pg_array_to_php_array($property['amenities']);
                                                  echo implode("\n", $amenities);
                                              }
                                              ?></textarea>
                                    <small class="text-muted">Escribe una amenidad por línea</small>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>
                                        <?php echo $property_id ? 'Actualizar Propiedad' : 'Crear Propiedad'; ?>
                                    </button>
                                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <!-- Contenedor para imágenes eliminadas -->
                                    <div id="deleted-images-container"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="mapModalLabel"><i class="fas fa-map-marker-alt me-2"></i>Seleccionar Ubicación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="map-selector"></div>
                    <div class="p-3 bg-light border-top">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Arrastra el marcador rojo a la ubicación exacta de la propiedad.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="map-current-pos">
                                    <i class="fas fa-crosshairs me-2"></i>Mi posición
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-location" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>Confirmar Ubicación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Recortar Imagen -->
    <div class="modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="cropperModalLabel"><i class="fas fa-crop-alt me-2"></i>Edición Profesional de Imagen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="cropper-container-wrapper">
                        <img id="cropper-image" src="">
                    </div>
                    
                    <div class="filter-controls">
                        <div class="row g-2">
                            <div class="col-md-6 filter-group">
                                <label>Brillo</label>
                                <input type="range" class="form-range" id="brightness" min="0" max="200" value="100">
                            </div>
                            <div class="col-md-6 filter-group">
                                <label>Contraste</label>
                                <input type="range" class="form-range" id="contrast" min="0" max="200" value="100">
                            </div>
                            <div class="col-12 filter-group">
                                <label>Filtros</label>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-dark filter-btn active" data-filter="none">Original</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark filter-btn" data-filter="grayscale(100%)">B/N</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark filter-btn" data-filter="sepia(100%)">Sepia</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark filter-btn" data-filter="saturate(200%)">Vívido</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark filter-btn" data-filter="hue-rotate(90deg)">Azulado</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-2 bg-dark text-white border-top d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="cropper.rotate(-90)" title="Rotar Izquierda">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="cropper.rotate(90)" title="Rotar Derecha">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="cropper.setAspectRatio(1.5)">3:2</button>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="cropper.setAspectRatio(1)">1:1</button>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="cropper.setAspectRatio(NaN)">Libre</button>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="cropper.reset()" title="Resetear">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="me-auto small text-muted">
                        <span id="current-img-size">Tamaño: Calculando...</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-crop">
                        <i class="fas fa-save me-2"></i>Guardar y Optimizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/admin-utils.js"></script>
    <script>
        function previewImages(input, containerId) {
            const container = document.getElementById(containerId);
            // Si es galería, mantenemos las existentes pero añadimos las nuevas para previsualizar
            // Si es imagen principal, reemplazamos
            if (containerId === 'main-preview') {
                container.innerHTML = '';
            }

            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `<img src="${e.target.result}">`;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        document.getElementById('image_main').addEventListener('change', function() {
            previewImages(this, 'main-preview');
        });

        document.getElementById('image_gallery').addEventListener('change', function() {
            // Para la galería, mostramos las nuevas junto a las que ya estaban (visualización)
            // Nota: Al subir el formulario se enviarán los archivos seleccionados
            previewImages(this, 'gallery-preview');
        });

        function removeExistingImage(url, containerId) {
            if (confirm('¿Estás seguro de que quieres eliminar esta imagen? Se borrará permanentemente.')) {
                // Ocultar el elemento visualmente
                document.getElementById(containerId).style.display = 'none';
                
                // Agregar a la lista de eliminados para el backend
                const container = document.getElementById('deleted-images-container');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'removed_images[]';
                input.value = url;
                container.appendChild(input);
            }
        }
    </script>
</body>
</html>
