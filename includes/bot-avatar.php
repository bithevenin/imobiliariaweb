<?php
/**
 * Asistente Virtual Animado - Ibron Inmobiliaria
 * Bot 3D que saluda y pestañea (SVG + CSS)
 */
?>

<div class="bot-container" id="ai-bot">
    <div class="bot-wrapper">
        <svg class="bot-svg" width="100" height="120" viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Sombra en el suelo (opcional para efecto levitación) -->
            <ellipse class="bot-shadow" cx="50" cy="115" rx="20" ry="5" fill="black" fill-opacity="0.2" />
            
            <!-- Cuerpo Principal -->
            <rect x="25" y="45" width="50" height="45" rx="15" fill="#2a2a2a" stroke="#d4a745" stroke-width="2"/>
            <rect x="35" y="55" width="20" height="15" rx="2" fill="#d4a745" fill-opacity="0.1" stroke="#d4a745" stroke-opacity="0.3"/>
            
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
            
            <!-- Cabeza -->
            <g class="bot-head">
                <rect x="30" y="15" width="40" height="35" rx="12" fill="#2a2a2a" stroke="#d4a745" stroke-width="2"/>
                <!-- Pantalla/Cara -->
                <rect x="35" y="20" width="30" height="20" rx="6" fill="#1a1a1a"/>
                
                <!-- Ojos -->
                <g class="bot-eyes">
                    <circle class="bot-eye eye-left" cx="43" cy="30" r="3" fill="#d4a745">
                        <animate class="blink-anim" attributeName="ry" values="3;0;3" dur="4s" repeatCount="indefinite" begin="0s" />
                    </circle>
                    <circle class="bot-eye eye-right" cx="57" cy="30" r="3" fill="#d4a745">
                        <animate class="blink-anim" attributeName="ry" values="3;0;3" dur="4s" repeatCount="indefinite" begin="0s" />
                    </circle>
                </g>
                
                <!-- Antena -->
                <line x1="50" y1="15" x2="50" y2="5" stroke="#d4a745" stroke-width="2"/>
                <circle class="bot-antenna-light" cx="50" cy="5" r="2.5" fill="#d4a745"/>
            </g>
        </svg>
        
        <!-- Tooltip de Saludo -->
        <div class="bot-tooltip">¡Hola! ¿Cómo puedo ayudarte hoy?</div>
    </div>
</div>

<style>
:root {
    --bot-gold: #d4a745;
    --bot-dark: #2a2a2a;
}

.bot-container {
    position: fixed;
    bottom: 30px;
    left: 30px;
    z-index: 9999;
    cursor: pointer;
    transition: all 0.3s ease;
}

.bot-container:hover {
    transform: scale(1.1);
}

.bot-wrapper {
    position: relative;
    filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
}

/* Animación: Levitación */
.bot-svg {
    animation: bot-float 3s ease-in-out infinite;
}

@keyframes bot-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Animación: Sombra Dinámica */
.bot-shadow {
    animation: shadow-scale 3s ease-in-out infinite;
    transform-origin: center;
}

@keyframes shadow-scale {
    0%, 100% { transform: scale(1); opacity: 0.2; }
    50% { transform: scale(0.8); opacity: 0.1; }
}

/* Animación: Saludo del Brazo Derecho */
.bot-arm-right {
    transform-origin: 79px 55px;
    animation: bot-wave 5s ease-in-out infinite;
}

@keyframes bot-wave {
    0%, 80%, 100% { transform: rotate(0deg); }
    85%, 95% { transform: rotate(-40deg); }
    90% { transform: rotate(-10deg); }
}

/* Animación: Luz de Antena */
.bot-antenna-light {
    animation: antenna-glow 2s ease-in-out infinite;
}

@keyframes antenna-glow {
    0%, 100% { fill: var(--bot-gold); filter: blur(0px); }
    50% { fill: #fff; filter: blur(2px); }
}

/* Ojos (Pestañeo mediante SVG Animate y CSS fallback) */
.bot-eye {
    transform-origin: center;
}

/* Tooltip */
.bot-tooltip {
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    color: #333;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.bot-container:hover .bot-tooltip {
    opacity: 1;
    visibility: visible;
    top: -50px;
}

/* Responsive */
@media (max-width: 768px) {
    .bot-container {
        bottom: 20px;
        left: 20px;
    }
    .bot-svg {
        width: 70px;
        height: 85px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bot = document.getElementById('ai-bot');
    
    // Función para manejar clics (preparación para el chat futuro)
    bot.addEventListener('click', function() {
        alert('Asistente Virtual: "Pronto estaré listo para conversar contigo usando Inteligencia Artificial. ¡Estamos trabajando en ello!"');
    });

    // Pestañeo aleatorio adicional si se requiere por JS para más realismo
    const blinkAnims = document.querySelectorAll('.blink-anim');
    setInterval(() => {
        const randomDelay = Math.random() * 5000 + 2000;
        blinkAnims.forEach(anim => {
            // anim.beginElement(); // Si se quisiera disparar manualmente
        });
    }, 4000);
});
</script>
