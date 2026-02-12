$file = "c:\Users\brayam Thevenin\Desktop\Inmobiliaria\assets\js\admin-utils.js"
$content = Get-Content $file -Raw

# Definir la nueva función
$newFunction = @'
    function processCroppedImage(base64, blob) {
        console.log('✨ processCroppedImage called', { 
            target: currentCropTarget, 
            base64Length: base64.length,
            blobSize: blob.size 
        });
        
        // Encontrar el formulario con múltiples métodos
        let form = document.getElementById('property-form');
        if (!form) {
            console.warn('Form not found by ID, trying querySelector...');
            form = document.querySelector('form#property-form') || document.querySelector('form');
        }
        
        if (!form) {
            console.error('❌ No se encontró el formulario!');
            alert('ERROR: No se puede guardar la imagen. El formulario no existe. Por favor recarga la página.');
            return;
        }
        
        console.log('Form found:', form.id || 'no-id');
        
        const container = document.getElementById(currentCropTarget === 'main' ? 'main-preview' : 'gallery-preview');
        const sizeStr = formatFileSize(blob.size);
        const uniqueId = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        
        // Primero eliminar inputs antiguos si es main
        if (currentCropTarget === 'main') {
            const oldMain = document.querySelectorAll('input[name="cropped_main"]');
            console.log('Removing old main inputs:', oldMain.length);
            oldMain.forEach(el => el.remove());
            if (container) container.innerHTML = '';
        }
        
        // Crear y agregar el input hidden
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = currentCropTarget === 'main' ? 'cropped_main' : 'cropped_gallery[]';
        hiddenInput.value = base64;
        hiddenInput.id = 'input-' + uniqueId;
        
        try {
            form.appendChild(hiddenInput);
            console.log('✅ Hidden input added successfully!', {
                name: hiddenInput.name,
                id: hiddenInput.id,
                valueLength: hiddenInput.value.length,
                formChildren: form.children.length
            });
            
            // Verificar que se agregó
            const verification = document.getElementById(hiddenInput.id);
            if (!verification) {
                console.error('❌ WARNING: Input was added but cannot be found in DOM!');
            }
        } catch (error) {
            console.error('❌ Error adding input to form:', error);
            alert('ERROR al agregar imagen: ' + error.message);
            return;
        }
        
        // Preview visual (opcional, solo para UI)
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
            container.appendChild(previewItem);
            console.log('Preview added to container');
        }
        
        // Manejar eliminación de imagen existente si aplica
        if (pendingDeletionUrl && pendingDeletionContainer) {
            const delContainer = document.getElementById('deleted-images-container');
            if (delContainer) {
                const delInput = document.createElement('input');
                delInput.type = 'hidden';
                delInput.name = 'removed_images[]';
                delInput.value = pendingDeletionUrl;
                delContainer.appendChild(delInput);
                
                const oldEl = document.getElementById(pendingDeletionContainer);
                if (oldEl) oldEl.remove();
                
                console.log('Marked old image for deletion:', pendingDeletionUrl);
            }
            
            pendingDeletionUrl = null;
            pendingDeletionContainer = null;
        }
    }
'@

# Regex pattern para encontrar la función completa
$pattern = '(?s)    function processCroppedImage\(base64, blob\) \{.*?^\s{4}\}'

if ($content -match $pattern) {
    $content = $content -replace $pattern, $newFunction
    Set-Content -Path $file -Value $content -Encoding UTF8
    Write-Host "✅ Función reemplazada exitosamente!" -ForegroundColor Green
} else {
    Write-Host "❌ No se pudo encontrar la función" -ForegroundColor Red
}
'@
