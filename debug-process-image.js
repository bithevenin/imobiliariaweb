// Simplificación temporal para debugging
function processCroppedImage(base64, blob) {
    console.log('processCroppedImage NUEVA VERSION', { target: currentCropTarget, base64Length: base64.length });

    const sizeStr = formatFileSize(blob.size);
    const uniqueId = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

    // Buscar el formulario
    const form = document.getElementById('property-form');
    console.log('Form element:', form);

    if (!form) {
        alert('ERROR: No se encontró el formulario. Por favor recarga la página.');
        return;
    }

    // Crear input hidden
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = currentCropTarget === 'main' ? 'cropped_main' : 'cropped_gallery[]';
    hiddenInput.value = base64;
    hiddenInput.id = 'input-' + uniqueId;

    // Agregar al DOM
    try {
        form.appendChild(hiddenInput);
        console.log('✅ Hidden input agregado exitosamente!', {
            name: hiddenInput.name,
            id: hiddenInput.id,
            valueLength: hiddenInput.value.length,
            formChildrenCount: form.children.length
        });

        // Verificar que realmente se agregó
        const check = document.getElementById(hiddenInput.id);
        console.log('Verificación: Input existe en DOM?', check !== null);

    } catch (error) {
        console.error('Error al agregar input:', error);
        alert('ERROR al agregar imagen: ' + error.message);
        return;
    }

    // Código del preview visual (opcional, solo para UI)
    const container = document.getElementById(currentCropTarget === 'main' ? 'main-preview' : 'gallery-preview');
    if (container) {
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

        if (currentCropTarget === 'main') {
            container.innerHTML = '';
        }
        container.appendChild(previewItem);
    }
}
