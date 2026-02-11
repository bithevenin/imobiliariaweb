<?php
/**
 * Formulario de Propiedades - Ibron Inmobiliaria
 * Crear y editar propiedades
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/supabase.php';
require_auth();

$page_title = 'Nueva Propiedad';
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

        // [LÓGICA DE IMÁGENES PRESERVADA]
        if (!empty($_POST['cropped_main'])) {
            $base64_img = $_POST['cropped_main'];
            $file_data = decode_base64_image($base64_img);
            if ($file_data) {
                $temp_file = tempnam(sys_get_temp_dir(), 'prop_main_');
                file_put_contents($temp_file, $file_data['data']);
                $file_array = ['name' => 'main_opt.jpg', 'type' => $file_data['type'], 'tmp_name' => $temp_file, 'error' => UPLOAD_ERR_OK, 'size' => strlen($file_data['data'])];
                $upload_result = upload_property_image($file_array, $prop_id, 'main');
                if ($upload_result) $data['image_main'] = $upload_result;
                unlink($temp_file);
            }
        }
        
        if (!empty($_POST['cropped_gallery']) && is_array($_POST['cropped_gallery'])) {
            $gallery_urls = !empty($property['image_gallery']) ? (is_array($property['image_gallery']) ? $property['image_gallery'] : pg_array_to_php_array($property['image_gallery'])) : [];
            foreach ($_POST['cropped_gallery'] as $index => $base64_img) {
                $file_data = decode_base64_image($base64_img);
                if ($file_data) {
                    $temp_file = tempnam(sys_get_temp_dir(), 'prop_gal_');
                    file_put_contents($temp_file, $file_data['data']);
                    $file_array = ['name' => 'gal_opt_' . $index . '.jpg', 'type' => $file_data['type'], 'tmp_name' => $temp_file, 'error' => UPLOAD_ERR_OK, 'size' => strlen($file_data['data'])];
                    $upload_result = upload_property_image($file_array, $prop_id, 'gallery');
                    if ($upload_result) $gallery_urls[] = $upload_result;
                    unlink($temp_file);
                }
            }
            if (!empty($gallery_urls)) $data['image_gallery'] = php_array_to_pg_array($gallery_urls);
        }

        if (isset($_POST['removed_images']) && is_array($_POST['removed_images'])) {
            foreach ($_POST['removed_images'] as $img_url) {
                $path_parts = explode('/public/property-images/', $img_url);
                if (count($path_parts) > 1) {
                    supabase_storage_delete('property-images', $path_parts[1]);
                    if ($img_url === ($property['image_main'] ?? '')) $data['image_main'] = null;
                    if (!empty($property['image_gallery'])) {
                        $current_gallery = is_array($property['image_gallery']) ? $property['image_gallery'] : pg_array_to_php_array($property['image_gallery']);
                        $index = array_search($img_url, $current_gallery);
                        if ($index !== false) {
                            unset($current_gallery[$index]);
                            $data['image_gallery'] = php_array_to_pg_array(array_values($current_gallery));
                        }
                    }
                }
            }
        }

        if (empty($error)) {
            if ($property_id) {
                $result = supabase_update('properties', $property_id, $data);
                if ($result) $success = 'Propiedad actualizada exitosamente';
                else $error = 'Error al actualizar la propiedad';
            } else {
                $result = supabase_insert('properties', $data);
                if ($result) {
                    header('Location: ' . SITE_URL . '/admin/properties-manage.php?success=1');
                    exit;
                } else $error = 'Error al crear la propiedad';
            }
        }
    }
}

// Contador de mensajes para el sidebar
$all_messages = supabase_get('contact_messages', []);
$unread_count = $all_messages ? count(array_filter($all_messages, fn($m) => ($m['status'] ?? '') === 'new')) : 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <style>
        :root { --sidebar-width: 250px; }
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: linear-gradient(180deg, #2a2a2a 0%, #3d3d3d 100%); position: fixed; left: 0; top: 0; z-index: 1000; transition: all 0.3s ease; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: block; padding: 12px 25px; color: rgba(255, 255, 255, 0.8); text-decoration: none; border-left: 3px solid transparent; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(212, 167, 69, 0.1); border-left-color: var(--color-gold); color: white; }
        .main-content { margin-left: var(--sidebar-width); transition: all 0.3s ease; width: calc(100% - var(--sidebar-width)); }
        .mobile-header { display: none; background: white; padding: 10px 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 999; }
        @media (max-width: 768px) {
            .sidebar { left: calc(-1 * var(--sidebar-width)); }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; width: 100%; }
            .mobile-header { display: flex; justify-content: space-between; align-items: center; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 998; }
            .sidebar-overlay.active { display: block; }
        }
        .preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .preview-item { position: relative; width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: #eee; }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .preview-item .remove-btn { position: absolute; top: 2px; right: 2px; background: rgba(220, 53, 69, 0.8); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; display: flex; align-items: center; justify-content: center; }
        .preview-item .edit-existing-btn { position: absolute; top: 2px; left: 2px; background: rgba(0, 123, 255, 0.8); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; display: flex; align-items: center; justify-content: center; }
        #map-selector { height: 400px; width: 100%; border-radius: 8px; }
        .cropper-container-wrapper { max-height: 500px; background: #f8f9fa; }
    </style>
</head>

<body class="bg-light">

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid p-0">
        <div class="mobile-header">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="height: 30px;">
            <button class="btn btn-dark" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>

        <div class="d-flex">
            <nav class="sidebar" id="sidebar">
                <div class="sidebar-header text-center">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" style="max-width: 120px;" class="mb-2">
                    <p class="text-white-50 small mb-0">Admin Panel</p>
                </div>
                <div class="sidebar-menu">
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php"><i class="fas fa-building me-2"></i>Propiedades</a>
                    <a href="<?php echo SITE_URL; ?>/admin/property-form.php" class="active"><i class="fas fa-plus-circle me-2"></i>Nueva Propiedad</a>
                    <a href="<?php echo SITE_URL; ?>/admin/messages.php">
                        <i class="fas fa-envelope me-2"></i>Mensajes
                        <?php if ($unread_count > 0): ?><span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span><?php endif; ?>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Ver Sitio Web</a>
                    <hr class="mx-3 bg-white-50">
                    <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a>
                </div>
            </nav>

            <main class="main-content p-3 p-md-4">
                <div class="admin-header d-none d-md-block mb-4 bg-white p-3 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 fw-bold h4"><?php echo $page_title; ?></h2>
                            <p class="text-muted mb-0 small"><?php echo $property_id ? 'Actualiza los datos de la propiedad' : 'Completa los campos para publicar'; ?></p>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/admin/properties-manage.php" class="btn btn-outline-dark btn-sm px-3">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>

                <?php if ($error): ?><div class="alert alert-danger shadow-sm border-0 small mb-3"><i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success shadow-sm border-0 small mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-md-4">
                        <form id="property-form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-9"><label class="form-label small fw-bold">Título *</label><input type="text" class="form-control form-control-sm" name="title" value="<?php echo $property['title'] ?? ''; ?>" required></div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Tipo *</label>
                                    <select class="form-select form-select-sm" name="type" required>
                                        <option value="">Seleccionar...</option>
                                        <?php $types=['Casa','Apartamento','Villa','Solar','Oficina','Local','Penthouse','Terreno']; foreach($types as $t){ $s=($property['type']??'')===$t?'selected':''; echo "<option value='$t' $s>$t</option>"; } ?>
                                    </select>
                                </div>
                                <div class="col-12"><label class="form-label small fw-bold">Descripción *</label><textarea class="form-control form-control-sm" name="description" rows="4" required><?php echo $property['description'] ?? ''; ?></textarea></div>
                                <div class="col-6 col-md-3"><label class="form-label small fw-bold">Precio (RD$) *</label><input type="text" class="form-control form-control-sm" name="price" id="price" value="<?php echo $property['price'] ?? ''; ?>" required></div>
                                <div class="col-6 col-md-3"><label class="form-label small fw-bold">Habitaciones</label><input type="number" class="form-control form-control-sm" name="bedrooms" value="<?php echo $property['bedrooms'] ?? 0; ?>"></div>
                                <div class="col-6 col-md-3"><label class="form-label small fw-bold">Baños</label><input type="number" class="form-control form-control-sm" name="bathrooms" value="<?php echo $property['bathrooms'] ?? 0; ?>"></div>
                                <div class="col-6 col-md-3"><label class="form-label small fw-bold">Área (m²)</label><input type="number" class="form-control form-control-sm" name="area" value="<?php echo $property['area'] ?? ''; ?>"></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Ubicación *</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="location" id="location" value="<?php echo $property['location'] ?? ''; ?>" required>
                                        <button class="btn btn-outline-dark" type="button" data-bs-toggle="modal" data-bs-target="#mapModal"><i class="fas fa-map-marker-alt"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="form-label small fw-bold">Dirección</label><input type="text" class="form-control form-control-sm" name="address" value="<?php echo $property['address'] ?? ''; ?>"></div>

                                <div class="col-6 col-md-4">
                                    <label class="form-label small fw-bold">Estado</label>
                                    <select class="form-select form-select-sm" name="status">
                                        <?php $stats=['Disponible','Vendida','Reservada']; foreach($stats as $st){ $s=($property['status']??'')===$st?'selected':''; echo "<option value='$st' $s>$st</option>"; } ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label small fw-bold d-block">Destacada</label>
                                    <div class="form-check form-switch mt-1"><input class="form-check-input" type="checkbox" name="featured" <?php echo ($property['featured']??0)?'checked':''; ?>></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Imagen Principal</label>
                                    <input type="file" class="form-control form-control-sm" name="image_main" id="image_main" accept="image/*">
                                    <div id="main-preview" class="preview-container">
                                        <?php if (!empty($property['image_main'])): ?><div class="preview-item" id="main-c"><img src="<?php echo $property['image_main']; ?>"><button type="button" class="remove-btn" onclick="removeExistingImage('<?php echo $property['image_main']; ?>', 'main-c')"><i class="fas fa-trash"></i></button></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Galería</label>
                                    <input type="file" class="form-control form-control-sm" name="image_gallery[]" id="image_gallery" accept="image/*" multiple>
                                    <div id="gallery-preview" class="preview-container">
                                        <?php if(!empty($property['image_gallery'])): $gal=is_array($property['image_gallery'])?$property['image_gallery']:pg_array_to_php_array($property['image_gallery']); foreach($gal as $i=>$u): ?>
                                            <div class="preview-item" id="gal-<?php echo $i; ?>"><img src="<?php echo $u; ?>"><button type="button" class="remove-btn" onclick="removeExistingImage('<?php echo $u; ?>', 'gal-<?php echo $i; ?>')"><i class="fas fa-trash"></i></button></div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <hr>
                                    <button type="submit" class="btn btn-primary px-5">Guardar Propiedad</button>
                                </div>
                                <div id="deleted-images-container"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modales (Mapa, Cropper) preservados -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg border-0 shadow"><div class="modal-content border-0">
            <div class="modal-header bg-dark text-white border-0"><h6 class="modal-title h6">Elegir Ubicación</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0"><div id="map-selector"></div></div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-primary btn-sm px-4" id="confirm-location" data-bs-dismiss="modal">Confirmar</button></div>
        </div></div>
    </div>
    
    <div class="modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg border-0 shadow"><div class="modal-content border-0">
            <div class="modal-header bg-dark text-white border-0"><h6 class="modal-title">Editar Imagen</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0"><div class="cropper-container-wrapper"><img id="cropper-image" src=""></div></div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-primary btn-sm px-4" id="save-crop">Aplicar y Optimizar</button></div>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/admin-utils.js"></script>
    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarToggle) {
            sidebarToggle.onclick = () => { sidebar.classList.toggle('active'); sidebarOverlay.classList.toggle('active'); }
        }
        if (sidebarOverlay) {
            sidebarOverlay.onclick = () => { sidebar.classList.remove('active'); sidebarOverlay.classList.remove('active'); }
        }

        // Logic for previews
        function removeExistingImage(url, id) {
            if (confirm('Eliminar esta imagen?')) {
                document.getElementById(id).style.display = 'none';
                const cont = document.getElementById('deleted-images-container');
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'removed_images[]'; inp.value = url;
                cont.appendChild(inp);
            }
        }
    </script>
</body>
</html>
