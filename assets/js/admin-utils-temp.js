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
    // 1. Capitalización automática
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

    // 2. Formato de precio dinámico ($1,000)
    const priceInput = document.getElementById('price');
    if (priceInput) {
        const formatCurrency = (value) => {
            let cleanValue = value.replace(/[^\d.]/g, '');
            const parts = cleanValue.split('.');
            if (parts.length > 2) cleanValue = parts[0] + '.' + parts.slice(1).join('');
            if (!cleanValue) return '';
            let integerPart = parts[0];
            const decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
            if (integerPart) {
                integerPart = parseInt(integerPart).toLocaleString('en-US');
            }
            return 'RD$ ' + integerPart + decimalPart;
        };

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

    // 3. Integración Mapa Interactivo (Leaflet)
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
                    attribution: '© OpenStreetMap contributors'
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
        console.error('cropperModal element not found');
        return;
    }

    const cropperModal = new bootstrap.Modal(cropperModalEl);
    const cropperImage = document.getElementById('cropper-image');
    const saveCropBtn = document.getElementById('save-crop');

    if (!saveCropBtn) {
        console.error('save-crop button not found');
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
                sizeElement.textContent = `Tamaño: ${sizeText}`;
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
        console.log('Save crop button clicked');
        if (!cropper) {
            console.error('Cropper not initialized');
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
                console.log('Base64 data generated, length:', base64data.length);
                console.log('Calling processCroppedImage for target:', currentCropTarget);
                processCroppedImage(base64data, blob);
            };
        }, 'image/jpeg', 0.7);

        cropperModal.hide();
    });

