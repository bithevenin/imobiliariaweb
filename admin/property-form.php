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
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        // Recoger datos del formulario
        $data = [
            'title' => sanitize_input($_POST['title']),
            'description' => sanitize_input($_POST['description']),
            'type' => sanitize_input($_POST['type']),
            'price' => (float)$_POST['price'],
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
        
        // Subir imagen principal si se proporcionó
        if (isset($_FILES['image_main']) && $_FILES['image_main']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_property_image($_FILES['image_main'], $prop_id, 'main');
            if ($upload_result) {
                $data['image_main'] = $upload_result;
            } else {
                $error = 'Error al subir la imagen principal';
            }
        }

        // Subir imágenes de galería si se proporcionaron
        $gallery_urls = [];
        if (isset($_FILES['image_gallery']) && !empty($_FILES['image_gallery']['name'][0])) {
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
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                            <div class="row g-3">
                                <!-- Título -->
                                <div class="col-md-8">
                                    <label class="form-label">Título *</label>
                                    <input type="text" class="form-control" name="title" 
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
                                    <textarea class="form-control" name="description" rows="4" required><?php echo escape_output($property['description'] ?? ''); ?></textarea>
                                </div>

                                <!-- Precio -->
                                <div class="col-md-4">
                                    <label class="form-label">Precio (RD$) *</label>
                                    <input type="number" class="form-control" name="price" step="0.01" 
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
                                    <input type="text" class="form-control" name="location" 
                                           value="<?php echo escape_output($property['location'] ?? ''); ?>" required>
                                </div>

                                <!-- Dirección -->
                                <div class="col-md-6">
                                    <label class="form-label">Dirección Completa</label>
                                    <input type="text" class="form-control" name="address" 
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
                                            <div class="preview-item">
                                                <img src="<?php echo escape_output($property['image_main']); ?>">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">Esta imagen se mostrará como portada</small>
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
                                            foreach ($gallery as $img_url): ?>
                                                <div class="preview-item">
                                                    <img src="<?php echo escape_output($img_url); ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Puedes seleccionar múltiples imágenes (Ctrl/Cmd + clic)
                                    </small>
                                </div>

                                <!-- Características -->
                                <div class="col-md-6">
                                    <label class="form-label">Características (una por línea)</label>
                                    <textarea class="form-control" name="features[]" rows="5" 
                                              placeholder="Ej: Terraza amplia&#10;Jardín privado&#10;Garaje para 3 vehículos"></textarea>
                                    <small class="text-muted">Escribe una característica por línea</small>
                                </div>

                                <!-- Amenidades -->
                                <div class="col-md-6">
                                    <label class="form-label">Amenidades (una por línea)</label>
                                    <textarea class="form-control" name="amenities[]" rows="5" 
                                              placeholder="Ej: Piscina privada&#10;Área BBQ&#10;Aire acondicionado central"></textarea>
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
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>
</html>
