/**
 * Utilidades para el Panel Administrativo
 * Ibron Inmobiliaria
 */

// Variables globales para el cropper
let cropper;
let currentCropTarget; // 'main' o 'gallery'
let currentBrightness = 100;
let currentContrast = 100;
let currentFilter = 'none';

document.addEventListener('DOMContentLoaded', function () {
    // 1. CapitalizaciÃ³n automÃ¡tica
    const capitalizeFields = [
        'title',
        'description',
        'address',
        'location',
        'features_input',
        'amenities_input'
    ];

    capitalizeFields.forEach(id => {
        const el = document.getElementById(id) || document.getElementsByName(id)[0];
        if (el) {
            el.addEventListener('input', function () {
                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }
            });
        }
    });

    // DEBUG: Interceptar submit del formulario para verificar inputs
    const propertyForm = document.getElementById('property-form');
    if (propertyForm) {
        propertyForm.addEventListener('submit', function (e) {
            const croppedMain = document.querySelectorAll('input[name="cropped_main"]');
            const croppedGallery = document.querySelectorAll('input[name="cropped_gallery[]"]');

            croppedMain.forEach((input, i) => {
            });

            if (croppedMain.length === 0) {
            }
        });
    }

    // 2. Formato de precio dinÃ¡mico ($1,000)
    const priceInput = document.getElementById('price');
    if (priceInput) {
        const formatCurrency = (value) => {
            const cleanValue = value.replace(/[^\d]/g, '');
            if (!cleanValue) return '';
            let integerPart = cleanValue;
            if (integerPart) {
                integerPart = parseInt(integerPart).toLocaleString('en-US');
            }

            // Detectar moneda si existe el selector
            const currencySelector = document.getElementById('currency');
            const currencyPrefix = (currencySelector && currencySelector.value === 'USD') ? 'US$ ' : 'RD$ ';

            return currencyPrefix + integerPart;
        };

        // Escuchar cambios en el selector de moneda para actualizar el formato del precio
        const currencySelector = document.getElementById('currency');
        if (currencySelector) {
            currencySelector.addEventListener('change', function () {
                if (priceInput.value) {
                    priceInput.value = formatCurrency(priceInput.value);
                }
            });
        }

        priceInput.addEventListener('keydown', function (e) {
            if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode === 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode === 86 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode === 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        priceInput.addEventListener('input', function (e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = this.value;
            const formattedValue = formatCurrency(this.value);
            this.value = formattedValue;
            if (formattedValue.length > oldValue.length) {
                const diff = formattedValue.length - oldValue.length;
                this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
            } else {
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        if (priceInput.value) {
            priceInput.value = formatCurrency(priceInput.value);
        }
    }

    // 3. IntegraciÃ³n Mapa Interactivo (Leaflet)
    let map, marker;
    let selectedLat = 18.4861;
    let selectedLng = -69.9312;
    let selectedAddress = "";

    const mapModal = document.getElementById('mapModal');
    if (mapModal) {
        mapModal.addEventListener('shown.bs.modal', function () {
            if (!map) {
                map = L.map('map-selector').setView([selectedLat, selectedLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Â© OpenStreetMap contributors'
                }).addTo(map);
                marker = L.marker([selectedLat, selectedLng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    updateCoords(pos.lat, pos.lng);
                });
                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    updateCoords(e.latlng.lat, e.latlng.lng);
                });
            } else {
                map.invalidateSize();
            }
        });
    }

    function updateCoords(lat, lng) {
        selectedLat = lat;
        selectedLng = lng;
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(res => res.json())
            .then(data => {
                if (data.display_name) {
                    selectedAddress = data.display_name;
                    marker.bindPopup(selectedAddress).openPopup();
                }
            }).catch(err => console.log('Geocoding error:', err));
    }

    const confirmBtn = document.getElementById('confirm-location');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            const locationInput = document.getElementById('location');
            const addressInput = document.getElementById('address');
            if (locationInput) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${selectedLat}&lon=${selectedLng}&zoom=14`)
                    .then(res => res.json())
                    .then(data => {
                        const city = data.address.city || data.address.town || data.address.suburb || data.address.village;
                        locationInput.value = city ? city + (data.address.state ? ", " + data.address.state : "") : `${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`;
                    });
            }
            if (addressInput) addressInput.value = selectedAddress;
        });
    }

    const mapCurrentBtn = document.getElementById('map-current-pos');
    if (mapCurrentBtn) {
        mapCurrentBtn.addEventListener('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 16);
                    updateCoords(lat, lng);
                });
            }
        });
    }

    // 4. Modern Image Editor (Cropper.js) & Compression
    const imageMain = document.getElementById('image_main');
    const imageGallery = document.getElementById('image_gallery');
    const cropperModalEl = document.getElementById('cropperModal');

    if (!cropperModalEl) {
        return;
    }

    const cropperModal = new bootstrap.Modal(cropperModalEl);
    const cropperImage = document.getElementById('cropper-image');
    const saveCropBtn = document.getElementById('save-crop');

    if (!saveCropBtn) {
        return;
    }

    // Controles de Filtros
    const brightnessInput = document.getElementById('brightness');
    const contrastInput = document.getElementById('contrast');
    const filterBtns = document.querySelectorAll('.filter-btn');

    if (imageMain) {
        imageMain.addEventListener('change', function (e) {
            handleImagePick(e, 'main');
        });
    }

    if (imageGallery) {
        imageGallery.addEventListener('change', function (e) {
            handleImagePick(e, 'gallery');
        });
    }

    function handleImagePick(e, target) {
        const file = e.target.files[0];
        if (!file) return;

        currentCropTarget = target;
        const reader = new FileReader();
        reader.onload = (event) => {
            cropperImage.src = event.target.result;
            cropperModal.show();
        };
        reader.readAsDataURL(file);
    }

    cropperModalEl.addEventListener('shown.bs.modal', function () {
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropperImage, {
            aspectRatio: 1.5,
            viewMode: 1,
            autoCropArea: 1,
            ready: function () {
                applyFiltersToPreview();
                updateSizeInfo();
            }
        });
    });

    // Aplicar filtros a la vista previa del cropper
    function applyFiltersToPreview() {
        const filterStr = `brightness(${currentBrightness}%) contrast(${currentContrast}%) ${currentFilter === 'none' ? '' : currentFilter}`;
        cropperImage.style.filter = filterStr;

        // Usar variable CSS para propagar a los elementos internos de cropper
        document.documentElement.style.setProperty('--cropper-filter', filterStr);
    }

    function updateSizeInfo() {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({ width: 1200 });
        canvas.toBlob((blob) => {
            const sizeText = formatFileSize(blob.size);
            const sizeElement = document.getElementById('current-img-size');
            if (sizeElement) {
                sizeElement.textContent = `TamaÃ±o: ${sizeText}`;
            }
        }, 'image/jpeg', 0.7);
    }

    if (brightnessInput) {
        brightnessInput.addEventListener('input', (e) => {
            currentBrightness = e.target.value;
            applyFiltersToPreview();
            updateSizeInfo();
        });
    }

    if (contrastInput) {
        contrastInput.addEventListener('input', (e) => {
            currentContrast = e.target.value;
            applyFiltersToPreview();
            updateSizeInfo();
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            applyFiltersToPreview();
            updateSizeInfo();
        });
    });

    saveCropBtn.addEventListener('click', function () {
        if (!cropper) {
            return;
        }

        // Crear canvas final con filtros aplicados
        const croppedCanvas = cropper.getCroppedCanvas({
            width: 1200,
            height: 800,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Aplicar filtros permanentes via Canvas
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = croppedCanvas.width;
        finalCanvas.height = croppedCanvas.height;
        const ctx = finalCanvas.getContext('2d');

        const filterStr = `brightness(${currentBrightness}%) contrast(${currentContrast}%) ${currentFilter === 'none' ? '' : currentFilter}`;
        ctx.filter = filterStr;
        ctx.drawImage(croppedCanvas, 0, 0);

        finalCanvas.toBlob((blob) => {
            const reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function () {
                const base64data = reader.result;
                processCroppedImage(base64data, blob);
            };
        }, 'image/jpeg', 0.7);

        cropperModal.hide();
    });

    function processCroppedImage(base64, blob) {
        const container = document.getElementById(currentCropTarget === 'main' ? 'main-preview' : 'gallery-preview');
        const sizeStr = formatFileSize(blob.size);
        const uniqueId = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

        // CRÃTICO: Primero eliminar inputs antiguos, ANTES de crear el nuevo
        if (currentCropTarget === 'main') {
            const oldMain = document.querySelectorAll('input[name="cropped_main"]');
            oldMain.forEach(el => el.remove());
            if (container) container.innerHTML = '';
        }

        // Crear input hidden
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = currentCropTarget === 'main' ? 'cropped_main' : 'cropped_gallery[]';
        hiddenInput.value = base64;
        hiddenInput.id = 'input-' + uniqueId;
        const form = document.getElementById('property-form') || document.querySelector('form');
        if (form) {
            form.appendChild(hiddenInput);
        } else {
            alert('Error: No se puede guardar la imagen. Formulario no encontrado.');
        }

        // Preview con badge de tamaÃ±o
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.id = 'preview-' + uniqueId;
        previewItem.innerHTML = `
            <img src="${base64}">
            <span class="badge bg-success position-absolute top-0 end-0 m-1" style="font-size: 8px">Optimizado</span>
            <span class="image-size-badge">${sizeStr}</span>
            <button type="button" class="remove-btn" onclick="document.getElementById('preview-${uniqueId}').remove(); document.getElementById('input-${uniqueId}').remove();">
                <i class="fas fa-trash"></i>
            </button>
        `;

        // Si venÃ­amos de editar una imagen existente, marcarla para borrar
        if (pendingDeletionUrl && pendingDeletionContainer) {
            const delContainer = document.getElementById('deleted-images-container');
            const delInput = document.createElement('input');
            delInput.type = 'hidden';
            delInput.name = 'removed_images[]';
            delInput.value = pendingDeletionUrl;
            delContainer.appendChild(delInput);

            const oldEl = document.getElementById(pendingDeletionContainer);
            if (oldEl) oldEl.remove();

            // Limpiar variables pendientes
            pendingDeletionUrl = null;
            pendingDeletionContainer = null;
        }

        container.appendChild(previewItem);
    }
});

let pendingDeletionUrl = null;
let pendingDeletionContainer = null;

/**
 * Cargar imagen existente para ediciÃ³n
 */
function editExistingImage(url, containerId, target) {
    const cropperModalEl = document.getElementById('cropperModal');
    const cropperModal = new bootstrap.Modal(cropperModalEl);
    const cropperImage = document.getElementById('cropper-image');

    currentCropTarget = target;
    pendingDeletionUrl = url;
    pendingDeletionContainer = containerId;

    cropperImage.src = ''; // Limpiar previo

    fetch(url)
        .then(res => res.blob())
        .then(blob => {
            const reader = new FileReader();
            reader.onload = (e) => {
                cropperImage.src = e.target.result;
                cropperModal.show();
            };
            reader.readAsDataURL(blob);
        })
        .catch(err => {
            alert('Error al cargar la imagen para ediciÃ³n. Puede ser un problema de CORS.');
        });
}

// Modificar saveCropBtn listener para ejecutar la eliminaciÃ³n pendiente
// Buscamos el listener previo o lo sobreescribimos. En mi admin-utils.js ya estÃ¡ definido.
// Lo actualizarÃ© en el bloque processCroppedImage para que use pendingDeletionUrl.


function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeExistingImage(url, containerId) {
    if (confirm('Â¿EstÃ¡s seguro de que quieres eliminar esta imagen? Se borrarÃ¡ permanentemente.')) {
        document.getElementById(containerId).style.display = 'none';
        const container = document.getElementById('deleted-images-container');
        if (container) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'removed_images[]';
            input.value = url;
            container.appendChild(input);
        }
    }
}

