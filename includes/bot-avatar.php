<?php
/**
 * Asistente Virtual Animado "Norvis" - Ibron Inmobiliaria
 * Bot 3D con personalidad, gestos aleatorios y sistema de chat inteligente.
 */
?>

<!-- Contenedor del Bot -->
<div class="bot-container" id="ai-bot">
    <div class="bot-wrapper" id="bot-avatar-trigger">
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
            
            <!-- Cabeza -->
            <g class="bot-head-group">
                <g class="bot-head">
                    <rect x="30" y="15" width="40" height="35" rx="12" fill="#2a2a2a" stroke="#d4a745" stroke-width="2"/>
                    <rect x="35" y="20" width="30" height="20" rx="6" fill="#1a1a1a"/>
                    <g class="bot-eyes">
                        <g class="eye-group eye-left">
                            <circle cx="43" cy="30" r="3.5" fill="#d4a745" />
                            <rect class="eyelid" x="39" y="26" width="8" height="8" fill="#1a1a1a" transform="scale(1, 0)" transform-origin="43px 26px" />
                        </g>
                        <g class="eye-group eye-right">
                            <circle cx="57" cy="30" r="3.5" fill="#d4a745" />
                            <rect class="eyelid" x="53" y="26" width="8" height="8" fill="#1a1a1a" transform="scale(1, 0)" transform-origin="57px 26px" />
                        </g>
                    </g>
                    <line x1="50" y1="15" x2="50" y2="5" stroke="#d4a745" stroke-width="2"/>
                    <circle class="bot-antenna-light" cx="50" cy="5" r="2.8" fill="#d4a745"/>
                </g>
            </g>
        </svg>
        
        <div class="bot-tooltip" id="bot-bubble">¡Hola! ¿En qué puedo ayudarte?</div>
    </div>

    <!-- Interfaz de Chat Premium -->
    <div class="bot-chat-window" id="bot-chat">
        <div class="chat-header">
            <div class="header-info">
                <span class="status-dot"></span>
                <strong>Norvis - Asistente Ibron</strong>
            </div>
            <button class="chat-close" id="close-chat">&times;</button>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <div class="message bot-msg">
                ¡Hola! Me llamo Norvis 🤖. Estoy aquí para ayudarte a encontrar la casa de tus sueños. ¿Qué buscas hoy?
            </div>
        </div>

        <div class="chat-suggestions" id="chat-suggestions">
            <button class="suggest-btn" data-query="Quiero una vivienda">Quiero una vivienda</button>
            <button class="suggest-btn" data-query="Hablar con representante">Representante</button>
            <button class="suggest-btn" data-query="Redes Sociales">Redes Sociales</button>
        </div>

        <div class="chat-input-area">
            <input type="text" id="chat-input" placeholder="Escribe aquí...">
            <button id="send-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
:root {
    --norvis-gold: #d4a745;
    --norvis-gold-light: #ffeb3b;
    --norvis-dark: #1a1a1a;
    --norvis-bg: rgba(255, 255, 255, 0.98);
    --norvis-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

.bot-container {
    position: fixed;
    bottom: 30px;
    left: 30px;
    z-index: 10000;
    font-family: 'Montserrat', sans-serif;
}

.bot-wrapper {
    cursor: pointer;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    filter: drop-shadow(0 8px 15px rgba(0,0,0,0.2));
}

.bot-wrapper:hover { transform: scale(1.1); }

/* --- ANIMACIONES NORVIS --- */
.bot-svg { animation: bot-float 4s ease-in-out infinite; }
@keyframes bot-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

.eyelid { animation: blink-cycle 5s infinite; }
@keyframes blink-cycle { 0%, 94%, 98%, 100% { transform: scale(1, 0); } 96% { transform: scale(1, 1); } }

.bot-arm-right { transform-origin: 79px 55px; animation: bot-wave 8s ease-in-out infinite; }
@keyframes bot-wave { 0%, 90%, 100% { transform: rotate(0deg); } 93%, 97% { transform: rotate(-35deg); } 95% { transform: rotate(-10deg); } }

.bot-antenna-light { animation: antenna-glow 2s ease-in-out infinite; }
@keyframes antenna-glow { 0%, 100% { fill: var(--norvis-gold); } 50% { fill: var(--norvis-gold-light); } }

/* Gestos JS */
.gesture-jump { animation: bot-jump 0.5s ease-out; }
@keyframes bot-jump { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-25px); } }
.gesture-spin { animation: bot-spin 0.6s ease-in-out; }
@keyframes bot-spin { from { transform: rotateY(0); } to { transform: rotateY(360deg); } }
.head-tilt-left { transform: rotate(-8deg); transition: 0.5s; }
.head-tilt-right { transform: rotate(8deg); transition: 0.5s; }
.head-look-up { transform: translateY(-2px) scale(1.02); transition: 0.5s; }

/* Tooltip */
.bot-tooltip {
    position: absolute;
    top: -55px;
    left: 40px;
    background: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-bottom: 2px solid var(--norvis-gold);
    white-space: nowrap;
    opacity: 1;
    transition: 0.3s;
}

/* Ventana de Chat Premium Optimizado */
.bot-chat-window {
    position: absolute;
    bottom: 20px;
    left: 10px;
    width: 360px;
    height: 550px;
    background: var(--norvis-bg);
    backdrop-filter: blur(10px); /* Reducido para mejor performance */
    border-radius: 24px;
    box-shadow: var(--norvis-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0);
    transform-origin: bottom left;
    transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
    border: 1px solid rgba(212, 167, 69, 0.15);
    pointer-events: none;
}

.bot-chat-window.open {
    transform: scale(1);
    pointer-events: auto;
}

.chat-header {
    background: var(--norvis-dark);
    color: white;
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid var(--norvis-gold);
}

.header-info { display: flex; align-items: center; gap: 10px; }
.status-dot { width: 9px; height: 9px; background: #4caf50; border-radius: 50%; box-shadow: 0 0 5px #4caf50; }

.chat-close {
    background: rgba(255,255,255,0.08); border: none; color: white;
    width: 28px; height: 28px; border-radius: 50%; cursor: pointer; transition: 0.2s;
}
.chat-close:hover { background: var(--norvis-gold); color: black; }

.chat-messages {
    flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px;
    background: linear-gradient(to bottom, rgba(212, 167, 69, 0.03), white);
}

.message {
    max-width: 85%; padding: 12px 16px; border-radius: 18px; font-size: 14px; line-height: 1.5;
    animation: msg-in 0.3s ease-out;
}
@keyframes msg-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

.bot-msg { background: white; color: #444; align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid #eee; }
.user-msg { background: var(--norvis-gold); color: white; align-self: flex-end; border-bottom-right-radius: 4px; font-weight: 500; }

.chat-suggestions { padding: 12px 15px; display: flex; flex-wrap: wrap; gap: 6px; background: white; border-top: 1px solid #f5f5f5; }
.suggest-btn {
    background: #f9f9f9; border: 1px solid #eee; color: #666; padding: 6px 14px; border-radius: 40px;
    font-size: 11px; font-weight: 600; cursor: pointer; transition: 0.2s;
}
.suggest-btn:hover { border-color: var(--norvis-gold); color: var(--norvis-gold); transform: translateY(-1px); }

.chat-input-area { padding: 15px 20px; display: flex; gap: 10px; background: white; border-top: 1px solid #f5f5f5; }
#chat-input {
    flex: 1; border: 1px solid #eee; padding: 12px 18px; border-radius: 30px; outline: none;
    font-size: 14px; transition: 0.2s; background: #fafafa;
}
#chat-input:focus { border-color: var(--norvis-gold); background: white; }

#send-btn {
    background: var(--norvis-dark); color: var(--norvis-gold); border: none;
    width: 42px; height: 42px; border-radius: 50%; cursor: pointer; transition: 0.2s;
}
#send-btn:hover { background: var(--norvis-gold); color: white; transform: rotate(-5deg) scale(1.05); }

/* Cards */
.chat-property-card {
    background: white; border-radius: 18px; overflow: hidden; border: 1px solid #eee;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06); margin-bottom: 5px;
}
.chat-property-card img { width: 100%; height: 130px; object-fit: cover; }
.chat-property-info { padding: 15px; }
.chat-property-type { font-size: 10px; text-transform: uppercase; font-weight: 700; color: var(--norvis-gold); margin-bottom: 2px; }
.chat-property-info h6 { margin: 0 0 5px 0; font-size: 14px; font-weight: 700; color: #333; line-height: 1.3; }
.chat-property-location { font-size: 11px; color: #888; margin-bottom: 10px; }
.chat-property-location i { margin-right: 4px; }

.chat-property-stats { display: flex; gap: 12px; margin-bottom: 10px; padding: 8px 0; border-top: 1px solid #f5f5f5; border-bottom: 1px solid #f5f5f5; }
.p-stat { font-size: 11px; color: #666; font-weight: 600; display: flex; align-items: center; gap: 5px; }
.p-stat i { color: var(--norvis-gold); font-size: 12px; }

.chat-property-price { color: var(--norvis-dark); font-weight: 800; font-size: 16px; margin-bottom: 12px; }
.chat-property-links { display: flex; gap: 8px; }
.chat-property-links a {
    flex: 1; text-align: center; padding: 10px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 12px;
    transition: 0.2s;
}
.btn-details { background: #f8f8f8; color: #333; border: 1px solid #eee; }
.btn-details:hover { background: #eee; }
.btn-wa { background: #25d366; color: white; }
.btn-wa:hover { background: #1ebe57; transform: translateY(-1px); }

@media (max-width: 500px) {
    .bot-container { bottom: 15px; left: 15px; }
    .bot-tooltip { left: 45px; right: auto; }
    .bot-chat-window { 
        position: fixed;
        width: 100%;
        height: 85vh;
        left: 0; 
        bottom: 0;
        border-radius: 25px 25px 0 0;
        transform-origin: bottom center;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.2);
    }
    .chat-header { padding: 15px 20px; }
    .chat-messages { padding: 15px; gap: 10px; }
    .message { font-size: 13px; max-width: 90%; }
    .chat-input-area { padding: 12px 15px; padding-bottom: env(safe-area-inset-bottom, 25px); }
    #chat-input { font-size: 16px; padding: 10px 15px; }
    #send-btn { width: 40px; height: 40px; }
    .suggest-btn { padding: 6px 12px; font-size: 10px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('bot-avatar-trigger');
    const botContainer = document.getElementById('ai-bot');
    const botHead = document.querySelector('.bot-head-group');
    const chatWindow = document.getElementById('bot-chat');
    const closeChat = document.getElementById('close-chat');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const botBubble = document.getElementById('bot-bubble');

    // 1. GESTOS DE PERSONALIDAD
    function performRandomGesture() {
        if (chatWindow.classList.contains('open')) return;

        const rand = Math.random();
        botContainer.classList.remove('gesture-jump', 'gesture-spin');
        botHead.classList.remove('head-tilt-left', 'head-tilt-right', 'head-look-up');

        if (rand < 0.1) botContainer.classList.add('gesture-jump');
        else if (rand < 0.2) botContainer.classList.add('gesture-spin');
        else if (rand < 0.4) botHead.classList.add('head-tilt-left');
        else if (rand < 0.6) botHead.classList.add('head-tilt-right');
        
        setTimeout(performRandomGesture, Math.random() * 5000 + 5000);
    }
    setTimeout(performRandomGesture, 3000);

    // 2. ABRIR / CERRAR CHAT
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = chatWindow.classList.toggle('open');
        botBubble.style.opacity = isOpen ? '0' : '1';
        
        if (isOpen) {
            botContainer.classList.add('gesture-jump');
            setTimeout(() => botContainer.classList.remove('gesture-jump'), 600);
            chatInput.focus();
        }
    });

    closeChat.addEventListener('click', (e) => {
        e.stopPropagation();
        chatWindow.classList.remove('open');
        botBubble.style.opacity = '1';
    });

    // 3. MENSAJERÍA
    async function sendMessage(text) {
        if (!text.trim()) return;

        addMessage(text, 'user-msg');
        chatInput.value = '';

        const loader = addMessage('<span class="loading-dots">Un momento</span>', 'bot-msg');

        try {
            const apiUrl = '<?php echo SITE_URL; ?>/api/bot-search.php';
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ query: text })
            });

            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }

            const data = await response.json();
            
            loader.remove();

            if (data.success) {
                addMessage(data.message, 'bot-msg');

                // --- REDIRECCIÓN AUTOMÁTICA A WHATSAPP ---
                if (data.redirect_whatsapp && data.whatsapp_url) {
                    setTimeout(() => {
                        window.open(data.whatsapp_url, '_blank');
                    }, 2000); 
                }

                // --- NAVEGACIÓN INTERNA EN EL SITIO ---
                if (data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2500); // Un poco más de tiempo para que lean la confirmación
                }

                if (data.type === 'properties' && data.properties.length > 0) {
                    data.properties.forEach(p => renderProperty(p));
                }
                if (data.links) {
                    let linksHtml = '<div style="margin-top:10px; display:flex; flex-direction:column; gap:8px;">';
                    data.links.forEach(l => {
                        linksHtml += `<a href="${l.url}" target="_blank" style="background:white; border:1px solid #d4a745; color:#d4a745; padding:10px; border-radius:12px; text-decoration:none; font-size:12px; text-align:center; font-weight:600;"><i class="${l.icon} me-2"></i> ${l.label}</a>`;
                    });
                    linksHtml += '</div>';
                    addMessage(linksHtml, 'bot-msg');
                }
            } else {
                addMessage(data.message || 'Lo siento, hubo un error.', 'bot-msg');
            }
        } catch (error) {
            if (loader) loader.remove();
            addMessage('Error de conexión.', 'bot-msg');
        }
    }

    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = `message ${type}`;
        div.innerHTML = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return div;
    }

    function renderProperty(p) {
        const div = document.createElement('div');
        div.className = 'chat-property-card';
        // Formatear área si existe
        const areaLabel = p.area > 0 ? `<div class="p-stat"><i class="fas fa-ruler-combined"></i> ${p.area} m²</div>` : '';
        const bedLabel = p.bedrooms > 0 ? `<div class="p-stat"><i class="fas fa-bed"></i> ${p.bedrooms}</div>` : '';
        const bathLabel = p.bathrooms > 0 ? `<div class="p-stat"><i class="fas fa-bath"></i> ${p.bathrooms}</div>` : '';

        div.innerHTML = `
            <img src="${p.image}" alt="${p.title}">
            <div class="chat-property-info">
                <div class="chat-property-type">${p.type}</div>
                <h6>${p.title}</h6>
                <div class="chat-property-location"><i class="fas fa-map-marker-alt"></i> ${p.location}</div>
                
                <div class="chat-property-stats">
                    ${bedLabel}
                    ${bathLabel}
                    ${areaLabel}
                </div>

                <div class="chat-property-price">${p.price}</div>
                <div class="chat-property-links">
                    <a href="${p.url}" class="btn-details">Detalles</a>
                    <a href="${p.whatsapp}" target="_blank" class="btn-wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </div>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    sendBtn.addEventListener('click', () => sendMessage(chatInput.value));
    chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(chatInput.value); });

    document.getElementById('chat-suggestions').addEventListener('click', (e) => {
        if (e.target.classList.contains('suggest-btn')) {
            sendMessage(e.target.dataset.query);
        }
    });

    document.addEventListener('click', (e) => {
        if (!botContainer.contains(e.target) && chatWindow.classList.contains('open')) {
            chatWindow.classList.remove('open');
            botBubble.style.opacity = '1';
        }
    });
});
</script>
