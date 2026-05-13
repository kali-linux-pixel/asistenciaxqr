<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
    .bandeja-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .bandeja-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .search-box {
        width: 280px;
    }
    .inbox-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .inbox-card {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
    }
    .inbox-card:hover {
        transform: translateY(-2px);
        border-color: #0B0B93;
        box-shadow: 0 10px 25px rgba(11,11,147,0.06);
    }
    .inbox-card.unread {
        border-color: rgba(11,11,147,0.3);
        background: linear-gradient(to right, rgba(11,11,147,0.01), #ffffff);
        box-shadow: 0 4px 15px rgba(11,11,147,0.04);
    }
    .inbox-card.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        bottom: 20%;
        width: 4px;
        background: #0B0B93;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    .inbox-user-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        min-width: 0; /* For text truncation */
    }
    .inbox-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #0B0B93, #1e3a8a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .inbox-text {
        min-width: 0;
        flex: 1;
    }
    .user-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .user-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1a2332;
    }
    .user-aula {
        font-size: 0.7rem;
        background: #e8edf8;
        color: #0B0B93;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 99px;
    }
    .last-msg {
        font-size: 0.82rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .inbox-card.unread .last-msg {
        font-weight: 700;
        color: #1e293b;
    }
    .inbox-status {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
        margin-left: 15px;
        flex-shrink: 0;
    }
    .msg-time {
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .unread-badge {
        background: #ef4444;
        color: #fff;
        font-weight: 900;
        font-size: 0.7rem;
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        box-shadow: 0 3px 6px rgba(239,68,68,0.3);
        animation: pulse 2s infinite;
    }
</style>

<div class="bandeja-container">
    <div class="bandeja-title-row">
        <div>
            <div style="font-size:0.8rem; color:#9ca3af; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                Comunicación Escolar
            </div>
            <h2 style="font-weight:800; color:#0B0B93; font-size:1.5rem; margin:0;">Buzón de Mensajes</h2>
        </div>
        <div class="input-wrap search-box">
            <i class="input-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInbox" class="form-control" placeholder="Filtrar profesor...">
        </div>
    </div>

    <?php flash('msg_bandeja'); ?>

    <div class="inbox-list" id="inboxList">
        <?php if(empty($data['profesores'])): ?>
            <div class="card" style="text-align:center; padding:4rem 2rem;">
                <i class="fa-solid fa-users-rectangle" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px;"></i>
                <h3 style="font-weight:700; color:#1e293b; margin-bottom:5px;">No hay profesores registrados</h3>
                <p style="font-size:0.85rem; color:#64748b;">Cuando registres docentes en el panel, aparecerán sus buzones automáticos aquí.</p>
            </div>
        <?php else: ?>
            <?php foreach($data['profesores'] as $p): 
                $hasUnread = ($p['no_leidos'] > 0);
            ?>
                <a href="<?php echo URLROOT; ?>/mensajes/chat/<?php echo $p['id']; ?>" 
                   class="inbox-card <?php echo $hasUnread ? 'unread' : ''; ?>"
                   data-name="<?php echo strtolower($p['nombre']); ?>">
                    
                    <div class="inbox-user-info">
                        <div class="inbox-avatar">
                            <?php echo mb_strtoupper(mb_substr($p['nombre'], 0, 2)); ?>
                        </div>
                        <div class="inbox-text">
                            <div class="user-header">
                                <span class="user-name"><?php echo htmlspecialchars($p['nombre']); ?></span>
                                <?php if($p['grado']): ?>
                                    <span class="user-aula"><?php echo formatAula($p['grado'], $p['seccion']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="last-msg">
                                <?php if($p['ultimo_msg']): ?>
                                    <?php echo htmlspecialchars($p['ultimo_msg']); ?>
                                <?php else: ?>
                                    <span style="font-style:italic; color:#94a3b8;">Haz clic para iniciar conversación</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="inbox-status">
                        <?php if($p['fecha_ultimo']): ?>
                            <span class="msg-time"><?php echo date('d M', strtotime($p['fecha_ultimo'])); ?></span>
                        <?php endif; ?>
                        
                        <?php if($hasUnread): ?>
                            <span class="unread-badge"><?php echo $p['no_leidos']; ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInbox');
        const inboxList = document.getElementById('inboxList');
        
        if(searchInput && inboxList) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const cards = inboxList.getElementsByClassName('inbox-card');
                
                Array.from(cards).forEach(card => {
                    const name = card.getAttribute('data-name');
                    if(name.includes(filter)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
