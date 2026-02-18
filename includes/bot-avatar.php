<?php
/**
 * Asistente Virtual Animado "Norvis" - Ibron Inmobiliaria
 * Bot 3D con personalidad, gestos aleatorios y saludo inicial.
 */
?>

<div class="bot-container" id="ai-bot">
    <div class="bot-wrapper">
        <svg class="bot-svg" width="100" height="120" viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Sombra en el suelo -->
            <ellipse class="bot-shadow" cx="50" cy="115" rx="20" ry="5" fill="black" fill-opacity="0.2" />
            
            <!-- Cuerpo Principal -->
            <rect x="25" y="45" width="50" height="45" rx="15" fill="#2a2a2a" stroke="#d4a745" stroke-width="2"/>
            
            <!-- Detalle Pecho -->
            <rect x="40" y="55" width="20" height="15" rx="2" fill="#d4a745" fill-opacity="0.1" stroke="#d4a745" stroke-opacity="0.3"/>
            
            <!-- Brazo Derecho (Saluda) -->
            <g class="bot-arm-right">
                <rect x="75" y="55" width="8" height="25" rx="4" fill="#2a2a2a" stroke="#d4a745" stroke-width="1.5"/>
                <circle cx="79" cy="80" r="4" fill="#d4a745"/>
            </g>
            
            <!-- Brazo Izquierdo -->
            <g class="bot-arm-left">
                <rect x="17" y="55" width="8" height="25" rx="4" fill="#2a2a2a" stroke="#d4a745" stroke-width="1.5"/>
                <circle cx="21" cy="80" r="4" fill="#d4a745"/>
            </g>
            
            <!-- Cabeza (Contenedor para rotación) -->
            <g class="bot-head-group">
                <g class="bot-head">
                    <rect x="30" y="15" width="40" height="35" rx="12" fill="#2a2a2a" stroke="#d4a745" stroke-width="2"/>
                    <!-- Pantalla/Cara -->
                    <rect x="35" y="20" width="30" height="20" rx="6" fill="#1a1a1a"/>
                    
                    <!-- Ojos -->
                    <g class="bot-eyes">
                        <!-- Ojo Izquierdo -->
                        <g class="eye-group eye-left">
                            <circle cx="43" cy="30" r="3.5" fill="#d4a745" />
                            <rect class="eyelid" x="39" y="26" width="8" height="8" fill="#1a1a1a" transform="scale(1, 0)" transform-origin="43px 26px" />
                        </g>
                        <!-- Ojo Derecho -->
                        <g class="eye-group eye-right">
                            <circle cx="57" cy="30" r="3.5" fill="#d4a745" />
                            <rect class="eyelid" x="53" y="26" width="8" height="8" fill="#1a1a1a" transform="scale(1, 0)" transform-origin="57px 26px" />
                        </g>
                    </g>
                    
                    <!-- Antena -->
                    <line x1="50" y1="15" x2="50" y2="5" stroke="#d4a745" stroke-width="2"/>
                    <circle class="bot-antenna-light" cx="50" cy="5" r="2.8" fill="#d4a745"/>
                </g>
            </g>
        </svg>
        
        <!-- Tooltip de Norvis -->
        <div class="bot-tooltip" id="bot-message">¡Hola! ¿Cómo puedo ayudarte hoy?</div>
    </div>
</div>

<style>
:root {
    --bot-gold: #d4a745;
    --bot-gold-light: #ffeb3b;
    --bot-dark: #2a2a2a;
}

.bot-container {
    position: fixed;
    bottom: 30px;
    left: 30px;
    z-index: 9999;
    cursor: pointer;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.bot-container:hover {
    transform: scale(1.15) rotate(2deg);
}

.bot-wrapper {
    position: relative;
    filter: drop-shadow(0 12px 20px rgba(0,0,0,0.4));
}

/* --- ANIMACIONES BASE --- */

/* Levitación */
.bot-svg {
    animation: bot-float 3.5s ease-in-out infinite;
}

@keyframes bot-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

/* Sombra Dinámica */
.bot-shadow {
    animation: shadow-scale 3.5s ease-in-out infinite;
    transform-origin: center;
}

@keyframes shadow-scale {
    0%, 100% { transform: scale(1); opacity: 0.25; }
    50% { transform: scale(0.75); opacity: 0.12; }
}

/* --- CABEZA Y GESTOS --- */

.bot-head-group {
    transform-origin: 50px 45px;
    transition: transform 0.5s ease-in-out;
}

/* Movimiento de cabeza aleatorio (Clase añadida por JS) */
.head-tilt-left { transform: rotate(-10deg); }
.head-tilt-right { transform: rotate(10deg); }
.head-look-up { transform: translateY(-3px) scale(1.02); }

/* --- OJOS Y PESTAÑEO --- */

.eyelid {
    animation: blink-cycle 6s infinite;
}

@keyframes blink-cycle {
    0%, 94%, 98%, 100% { transform: scale(1, 0); }
    96% { transform: scale(1, 1); } /* El cierre rápido */
}

/* --- SALUDO Y BRAZOS --- */

.bot-arm-right {
    transform-origin: 79px 55px;
    animation: bot-wave 6s ease-in-out infinite;
}

@keyframes bot-wave {
    0%, 85%, 100% { transform: rotate(0deg); }
    88%, 96% { transform: rotate(-45deg); }
    92% { transform: rotate(-15deg); }
}

/* --- ANTENA --- */
.bot-antenna-light {
    animation: antenna-glow 1.5s ease-in-out infinite;
}

@keyframes antenna-glow {
    0%, 100% { fill: var(--bot-gold); filter: drop-shadow(0 0 2px var(--bot-gold)); }
    50% { fill: var(--bot-gold-light); filter: drop-shadow(0 0 6px var(--bot-gold-light)); }
}

/* --- TOOLTIP / DIÁLOGO --- */

.bot-tooltip {
    position: absolute;
    top: -45px;
    left: 20px; /* Alineado un poco a la derecha del bot */
    background: white;
    color: #222;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 700;
    white-space: normal; /* Permitir que el texto salte de línea si es largo */
    max-width: 200px;    /* Limitar el ancho para que no se pierda */
    width: max-content;
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border-bottom: 3px solid var(--bot-gold);
}

/* Triángulo del tooltip ajustado a la izquierda */
.bot-tooltip::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 20px;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid white;
}

.bot-tooltip.visible, 
.bot-container:hover .bot-tooltip {
    opacity: 1;
    visibility: visible;
    top: -60px;
}

/* --- GESTOS CHISTOSOS (Clases JS) --- */

/* Salto de alegría */
.gesture-jump {
    animation: bot-jump 0.6s ease-out;
}

@keyframes bot-jump {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-30px) scale(1.05); }
}

/* Giro emocionante */
.gesture-spin {
    animation: bot-spin 0.8s ease-in-out;
}

@keyframes bot-spin {
    from { transform: rotateY(0deg); }
    to { transform: rotateY(360deg); }
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
    .bot-container { bottom: 20px; left: 15px; }
    .bot-svg { width: 80px; height: 100px; }
    .bot-tooltip { font-size: 12px; padding: 8px 14px; }
}
</style>

<script>
/**
 * Lógica de Personalidad de Norvis
 */
document.addEventListener('DOMContentLoaded', function() {
    const botContainer = document.getElementById('ai-bot');
    const botHead = document.querySelector('.bot-head-group');
    const botMessage = document.getElementById('bot-message');
    
    // 1. SALUDO INICIAL
    setTimeout(() => {
        botMessage.textContent = "¡Hola! Me llamo Norvis 🤖";
        botMessage.classList.add('visible');
        
        // Ejecutar un pequeño salto de alegría al presentarse
        botContainer.classList.add('gesture-jump');
        
        // Ocultar saludo después de 4 segundos y volver al normal
        setTimeout(() => {
            botMessage.classList.remove('visible');
            setTimeout(() => {
                botMessage.textContent = "¿En qué puedo ayudarte?";
            }, 500);
        }, 4000);
        
        // Limpiar clase de animación
        setTimeout(() => botContainer.classList.remove('gesture-jump'), 1000);
    }, 1500);

    // 2. COMPORTAMIENTO ALEATORIO (Personalidad)
    function performRandomGesture() {
        const rand = Math.random();
        
        // Limpiar gestos anteriores
        botContainer.classList.remove('gesture-jump', 'gesture-spin');
        botHead.classList.remove('head-tilt-left', 'head-tilt-right', 'head-look-up');

        if (rand < 0.15) {
            // Salto
            botContainer.classList.add('gesture-jump');
        } else if (rand < 0.30) {
            // Giro
            botContainer.classList.add('gesture-spin');
        } else if (rand < 0.50) {
            // Tildar cabeza izquierda
            botHead.classList.add('head-tilt-left');
        } else if (rand < 0.70) {
            // Tildar cabeza derecha
            botHead.classList.add('head-tilt-right');
        } else if (rand < 0.85) {
            // Mirar arriba
            botHead.classList.add('head-look-up');
        }
        
        // Programar el siguiente gesto (cada 4-8 segundos)
        const nextGesture = Math.random() * 4000 + 4000;
        setTimeout(performRandomGesture, nextGesture);
    }
    
    // Iniciar gestos
    setTimeout(performRandomGesture, 8000);

    // 3. INTERACCIÓN AL CLIC
    botContainer.addEventListener('click', function() {
        // Al hacer clic, Norvis se emociona
        botContainer.classList.add('gesture-jump');
        botMessage.textContent = "¡Estoy listo para ayudarte!";
        botMessage.classList.add('visible');
        
        setTimeout(() => {
            botContainer.classList.remove('gesture-jump');
            botMessage.classList.remove('visible');
        }, 2000);
        
        // En el futuro, abrir aquí la interfaz de chat con IA
        // showChatInterface(); 
    });
});
</script>
