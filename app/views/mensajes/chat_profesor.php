<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Estilos Especializados para el Chat Corporativo -->
<style>
    .chat-layout {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 180px);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .chat-header {
        padding: 1.2rem 1.5rem;
        background: linear-gradient(135deg, #0B0B93, #1e3a8a);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .chat-header .avatar {
        width: 45px;
        height: 45px;
        background: #FBBF24;
        color: #0B0B93;
        font-weight: 800;
        font-size: 1.2rem;
    }
    .chat-messages {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .msg-row {
        display: flex;
        width: 100%;
    }
    .msg-row.sent {
        justify-content: flex-end;
    }
    .msg-row.received {
        justify-content: flex-start;
    }
    .msg-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 0.88rem;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
    }
    .msg-row.sent .msg-bubble {
        background: #0B0B93;
        color: #fff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 3px 10px rgba(11,11,147,0.2);
    }
    .msg-row.received .msg-bubble {
        background: #fff;
        color: #1e293b;
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .msg-meta {
        font-size: 0.68rem;
        margin-top: 5px;
        display: block;
        text-align: right;
        opacity: 0.7;
    }
    .msg-row.sent .msg-meta {
        color: rgba(255,255,255,0.8);
    }
    .msg-row.received .msg-meta {
        color: #64748b;
    }
    .chat-input-area {
        padding: 1rem 1.5rem;
        background: #fff;
        border-top: 1px solid #e2e8f0;
    }
    .chat-form {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .chat-form input {
        flex: 1;
        border: 2px solid #e2e8f0;
        border-radius: 99px;
        padding: 12px 20px;
        outline: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .chat-form input:focus {
        border-color: #0B0B93;
        box-shadow: 0 0 0 3px rgba(11,11,147,0.1);
    }
    .btn-send {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #0B0B93;
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(11,11,147,0.2);
    }
    .btn-send:hover {
        transform: scale(1.05);
        background: #1e3a8a;
    }
</style>

<div class="chat-layout">
    <!-- Cabecera del Chat -->
    <div class="chat-header">
        <div class="avatar" style="display:flex; align-items:center; justify-content:center; border-radius:50%;">
            DI
        </div>
        <div>
            <div style="font-weight:700; font-size:1.05rem;"><?php echo htmlspecialchars($data['destinatario']['nombre']); ?></div>
            <div style="font-size:0.75rem; opacity:0.8; display:flex; align-items:center; gap:5px;">
                <span style="width:8px; height:8px; background:#22c55e; border-radius:50%; display:inline-block;"></span> 
                Director Principal — Canal Directo
            </div>
        </div>
    </div>

    <!-- Cuerpo de Mensajes -->
    <div class="chat-messages" id="chatMessages">
        <?php if(empty($data['conversacion'])): ?>
            <div style="text-align:center; margin:auto; padding:2rem; color:#64748b;">
                <i class="fa-solid fa-comments" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px;"></i>
                <h3 style="font-weight:700; color:#1e293b; margin-bottom:5px;">Inicia una conversación</h3>
                <p style="font-size:0.85rem; max-width:300px; margin:auto;">Escríbele tus dudas pedagógicas o consultas administrativas directamente al Director.</p>
            </div>
        <?php else: ?>
            <?php foreach($data['conversacion'] as $m): 
                $esMio = ($m['remitente_id'] == $_SESSION['user_id']);
            ?>
                <div class="msg-row <?php echo $esMio ? 'sent' : 'received'; ?>">
                    <div class="msg-bubble">
                        <?php echo nl2br(htmlspecialchars($m['contenido'])); ?>
                        <span class="msg-meta">
                            <?php echo date('g:i A', strtotime($m['fecha_envio'])); ?>
                            <?php if($esMio): ?>
                                <i class="fa-solid fa-check-double" style="margin-left:3px; font-size:0.65rem; color:<?php echo $m['leido'] ? '#FBBF24' : 'rgba(255,255,255,0.5)'; ?>"></i>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Input de Mensaje -->
    <div class="chat-input-area">
        <form action="<?php echo URLROOT; ?>/mensajes/enviar" method="POST" class="chat-form">
            <input type="hidden" name="destinatario_id" value="<?php echo $data['destinatario']['id']; ?>">
            <input type="text" name="contenido" placeholder="Escribe tu consulta al Director aquí..." required autocomplete="off" id="msgInput">
            <button type="submit" class="btn-send">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Desplazar al final del chat automáticamente
        const chat = document.getElementById('chatMessages');
        chat.scrollTop = chat.scrollHeight;
        
        // Enfocar el input
        document.getElementById('msgInput').focus();
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
