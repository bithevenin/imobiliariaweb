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
    // COPY TO CLIPBOARD
    // ============================================
    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(button => {
        button.addEventListener('click', function () {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                // Mostrar feedback visual
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-2"></i>Copiado!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    });

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
