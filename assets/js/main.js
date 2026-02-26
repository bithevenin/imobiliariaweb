/**
 * JavaScript Principal - Ibron Inmobiliaria
 * Funcionalidades interactivas del sitio
 */

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // NAVBAR SCROLL EFFECT
    // ============================================
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ============================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Ignorar si es solo "#" o enlaces de collapse/modal
            if (href === '#' || href.startsWith('#collapse') || href.startsWith('#modal')) {
                return;
            }

            e.preventDefault();
            const target = document.querySelector(href);

            if (target) {
                const offsetTop = target.offsetTop - 80; // Offset para navbar fijo
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ============================================
    // FORM VALIDATION
    // ============================================
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ============================================
    // CONTADOR ANIMADO PARA ESTADÍSTICAS
    // ============================================
    function animateCounter(element, target, duration = 2000) {
        let current = 0;
        const increment = target / (duration / 16); // 60 FPS
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target + '+';
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + '+';
            }
        }, 16);
    }

    // Activar contador cuando la sección es visible
    const stats = document.querySelectorAll('.stat-item h2');
    if (stats.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    const text = entry.target.textContent;
                    const number = parseInt(text.replace('+', ''));
                    entry.target.textContent = '0+';
                    animateCounter(entry.target, number);
                }
            });
        }, { threshold: 0.5 });

        stats.forEach(stat => observer.observe(stat));
    }

    // ============================================
    // IMAGE LAZY LOADING FALLBACK
    // ============================================
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src || img.src;
        });
    } else {
        // Fallback para navegadores que no soportan lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
        document.body.appendChild(script);
    }

    // ============================================
    // TOOLTIP & POPOVER INITIALIZATION (Bootstrap)
    // ============================================
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // ============================================
    // AUTO-HIDE ALERTS
    // ============================================
    const autoHideAlerts = document.querySelectorAll('.alert[data-autohide]');
    autoHideAlerts.forEach(alert => {
        const delay = parseInt(alert.dataset.autohide) || 5000;
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, delay);
    });

    // ============================================
    // PROPERTY SEARCH WITH DEBOUNCE
    // ============================================
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Aquí iría la lógica de búsqueda AJAX cuando conectemos a Supabase
                console.log('Buscando:', this.value);
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    }

    // ============================================
    // PRICE FORMATTER
    // ============================================
    function formatPrice(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('es-DO');
            input.value = 'RD$ ' + value;
        }
    }

    const priceInputs = document.querySelectorAll('input[data-price]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function () {
            formatPrice(this);
        });
    });

    // ============================================
    // PHONE NUMBER FORMATTER (Dominican format)
    // ============================================
    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.substring(0, 10);
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
        }
        input.value = value;
    }

    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function () {
            formatPhoneNumber(this);
        });
    });

    // ============================================
    // IMAGE PREVIEW FOR FILE UPLOADS
    // ============================================
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById(input.id + '_preview');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // ============================================
    // CONFIRM DELETE MODAL
    // ============================================
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            const message = this.dataset.confirmDelete || '¿Estás seguro de que deseas eliminar este elemento?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ============================================
    // PROPERTY SHARING LOGIC
    // ============================================
    document.addEventListener('click', function (e) {
        const shareBtn = e.target.closest('.share-btn');
        if (!shareBtn) return;

        e.preventDefault();
        const id = shareBtn.dataset.id;
        const title = shareBtn.dataset.title;
        const url = shareBtn.dataset.url;
        
        // Convertir URL relativa a absoluta usando la ubicación actual como base
        const absoluteUrl = new URL(url, window.location.href).href;

        // Detectar si es móvil para decidir si usar navigator.share
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        if (navigator.share && isMobile) {
            navigator.share({
                title: title,
                text: `Mira esta propiedad: ${title}`,
                url: absoluteUrl,
            }).catch((error) => {
                console.log('Error sharing with native API', error);
                showShareModal(title, absoluteUrl);
            });
        } else {
            // Fallback para Desktop o si falla navigator.share
            showShareModal(title, absoluteUrl);
        }
    });

    function showShareModal(title, url) {
        // Crear el modal de compartir si no existe
        let shareModalEl = document.getElementById('shareModal');
        if (!shareModalEl) {
            shareModalEl = document.createElement('div');
            shareModalEl.id = 'shareModal';
            shareModalEl.className = 'modal fade';
            shareModalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Compartir Propiedad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-muted mb-4 small">${title}</p>
                            <div class="d-flex justify-content-around mb-4">
                                <a href="https://wa.me/?text=${encodeURIComponent('Mira esta propiedad: ' + title + ' ' + url)}" target="_blank" class="share-option text-center text-decoration-none">
                                    <div class="share-icon bg-success-subtle text-success mb-2">
                                        <i class="fab fa-whatsapp fa-2x"></i>
                                    </div>
                                    <span class="small text-dark">WhatsApp</span>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" target="_blank" class="share-option text-center text-decoration-none">
                                    <div class="share-icon bg-primary-subtle text-primary mb-2">
                                        <i class="fab fa-facebook-f fa-2x"></i>
                                    </div>
                                    <span class="small text-dark">Facebook</span>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent('Mira esta propiedad: ' + title)}&url=${encodeURIComponent(url)}" target="_blank" class="share-option text-center text-decoration-none">
                                    <div class="share-icon bg-info-subtle text-info mb-2">
                                        <i class="fab fa-twitter fa-2x"></i>
                                    </div>
                                    <span class="small text-dark">Twitter</span>
                                </a>
                                <div class="share-option text-center cursor-pointer" id="copyShareLink">
                                    <div class="share-icon bg-secondary-subtle text-secondary mb-2">
                                        <i class="fas fa-link fa-2x"></i>
                                    </div>
                                    <span class="small text-dark">Copiar</span>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" value="${url}" readonly id="shareUrlInput">
                                <button class="btn btn-outline-gold" type="button" id="copyShareBtn">Copiar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(shareModalEl);

            // Estilos dinámicos para el modal de compartir
            const style = document.createElement('style');
            style.innerHTML = `
                .share-icon {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto;
                    transition: transform 0.2s;
                }
                .share-option:hover .share-icon {
                    transform: scale(1.1);
                }
                .cursor-pointer { cursor: pointer; }
            `;
            document.head.appendChild(style);
        } else {
            // Actualizar contenido para la propiedad específica
            shareModalEl.querySelector('.modal-body p').textContent = title;
            shareModalEl.querySelector('#shareUrlInput').value = url;
            shareModalEl.querySelector('a[href^="https://wa.me/"]').href = `https://wa.me/?text=${encodeURIComponent('Mira esta propiedad: ' + title + ' ' + url)}`;
            shareModalEl.querySelector('a[href^="https://www.facebook.com/"]').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            shareModalEl.querySelector('a[href^="https://twitter.com/"]').href = `https://twitter.com/intent/tweet?text=${encodeURIComponent('Mira esta propiedad: ' + title)}&url=${encodeURIComponent(url)}`;
        }

        const modal = new bootstrap.Modal(shareModalEl);
        
        // Agregar eventos de copia
        const copyFn = () => {
            const copyInput = shareModalEl.querySelector('#shareUrlInput');
            copyInput.select();
            navigator.clipboard.writeText(url).then(() => {
                showToast('¡Enlace copiado al portapapeles!', 'success');
                modal.hide();
            });
        };

        shareModalEl.querySelector('#copyShareLink').onclick = copyFn;
        shareModalEl.querySelector('#copyShareBtn').onclick = copyFn;

        modal.show();
    }

});

// ============================================
// UTILIDADES GLOBALES
// ============================================

// Mostrar notificación toast
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    document.getElementById('toastContainer').appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', function () {
        toastEl.remove();
    });
}

// Loading overlay
function showLoading() {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
    overlay.style.zIndex = '9999';
    overlay.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Validar formato de email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validar teléfono dominicano
function isValidDominicanPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    return /^(\+?1)?[8][0-2,4][9]\d{7}$/.test(cleaned);
}

console.log('Ibron Inmobiliaria - Sistema cargado correctamente');
