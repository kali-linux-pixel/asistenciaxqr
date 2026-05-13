<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; display:flex; align-items:center; gap:10px;">
        <span class="title-icon" style="background:var(--amarillo); color:#000; border-radius:8px; padding:6px 10px;"><i class="fa-solid fa-star"></i></span>
        Registro de Calificaciones Oficiales
    </h3>
</div>

<?php flash('nota_message'); ?>

<!-- ALERTA PARA EL PROFESOR SI ESTÁN VACÍAS -->
<?php if(!$data['isDirector'] && $data['faltanNotas']): ?>
<div style="background: #fdecea; border: 2px solid #c0392b; border-radius:12px; padding: 1.2rem; display:flex; align-items:center; gap: 15px; margin-bottom:1.5rem; animation: pulse 2s infinite;">
    <div style="width:50px; height:50px; background:#c0392b; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <div>
        <h4 style="color:#c0392b; margin:0 0 2px 0; font-weight:800;">🔔 ATENCIÓN PROFESOR</h4>
        <p style="margin:0; color:#782117; font-size:0.85rem; font-weight:600;">No ha subido calificaciones aún para este periodo. **Debe completar los registros ahora.**</p>
    </div>
</div>
<?php endif; ?>

<!-- Barra de Selección/Filtros -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(to right, #f8fafc, #ffffff);">
    <div class="card-body" style="padding: 1.2rem;">
        <form action="<?php echo URLROOT; ?>/notas" method="GET" style="display:flex; flex-wrap:wrap; gap:15px; align-items:flex-end;">
            
            <?php if($data['isDirector']): ?>
            <!-- Director elige -->
            <div style="flex: 1; min-width:200px;">
                <label style="color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Consultar Aula</label>
                <select name="grado" class="form-control" required style="background:#fff;">
                    <option value="" disabled <?php echo empty($data['selectedGrado']) ? 'selected' : ''; ?>>Seleccionar...</option>
                    <?php foreach($data['grades'] as $g): ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($data['selectedGrado'] == $g['id']) ? 'selected' : ''; ?>>
                            <?php echo formatAula($g['grado'], $g['seccion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
                <!-- Profesor ya tiene su aula asignada en sesión -->
                <input type="hidden" name="grado" value="<?php echo $data['selectedGrado']; ?>">
                <div style="flex: 1; min-width:150px;">
                    <label style="color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Mi Aula Asignada</label>
                    <div style="padding: 9px 12px; background:#e8edf8; color:var(--azul); font-weight:800; border-radius:8px; border:1.5px solid var(--azul-claro);">
                        <i class="fa-solid fa-chalkboard-user" style="margin-right:4px;"></i> 
                        <?php echo !empty($data['teacherGradeName']) ? $data['teacherGradeName'] : 'Sin Aula'; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div style="flex: 1; min-width:200px;">
                <label style="color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Materia</label>
                <select name="curso" class="form-control" required style="background:#fff;">
                    <option value="" disabled <?php echo empty($data['selectedCurso']) ? 'selected' : ''; ?>>Seleccionar Materia...</option>
                    <?php foreach($data['cursos'] as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($data['selectedCurso'] == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1; min-width:150px;">
                <label style="color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Periodo</label>
                <select name="periodo" class="form-control" style="background:#fff;">
                    <option value="1er Bimestre" <?php echo $data['periodo'] == '1er Bimestre' ? 'selected' : ''; ?>>1º Bimestre</option>
                    <option value="2do Bimestre" <?php echo $data['periodo'] == '2do Bimestre' ? 'selected' : ''; ?>>2º Bimestre</option>
                    <option value="3er Bimestre" <?php echo $data['periodo'] == '3er Bimestre' ? 'selected' : ''; ?>>3º Bimestre</option>
                    <option value="4to Bimestre" <?php echo $data['periodo'] == '4to Bimestre' ? 'selected' : ''; ?>>4º Bimestre</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="height:41px;">
                    <i class="fa-solid fa-arrows-rotate"></i> Cargar Planilla
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Planilla de Datos -->
<?php if($data['selectedCurso'] && $data['selectedGrado']): ?>
    <div class="card">
        <div class="card-header" style="background:#fff; padding:1.2rem; display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap;">
            <div>
                <span style="display:block; font-size:0.75rem; color:<?php echo $data['isDirector'] ? 'var(--azul)' : 'var(--verde)'; ?>; font-weight:800; text-transform:uppercase; letter-spacing:1px;">
                    <?php echo $data['isDirector'] ? 'Modo Lectura: Visualizando' : 'Modo Edición: Calificando'; ?>
                </span>
                <h4 style="margin:0 0 4px 0; color:#1e293b; font-size:1.1rem; font-weight:700;">
                    <?php 
                        $curName = ''; foreach($data['cursos'] as $c) if($c['id']==$data['selectedCurso']) $curName=$c['nombre'];
                        echo htmlspecialchars($curName); 
                    ?>
                    <span style="color:#94a3b8; font-weight:400; margin-left:8px;">| <?php echo $data['periodo']; ?></span>
                </h4>
                
                <!-- MOSTRAR DOCENTE ENCARGADO SI ES DIRECTOR -->
                <?php if($data['isDirector']): ?>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600; display:flex; align-items:center; gap:5px;">
                        <i class="fa-solid fa-user-tie" style="color:var(--azul);"></i>
                        Docente Encargado: <span style="color:#334155; font-weight:800;"><?php echo htmlspecialchars($data['profesorEncargado']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- ACCIONES EXCLUSIVAS DIRECTOR -->
            <?php if($data['isDirector']): ?>
                <div style="display:flex; gap:10px;">
                    <a href="<?php echo URLROOT; ?>/notas/exportar?grado=<?php echo $data['selectedGrado']; ?>&curso=<?php echo $data['selectedCurso']; ?>&periodo=<?php echo urlencode($data['periodo']); ?>" 
                       class="btn btn-accent btn-sm" style="background:#1d6f42; color:#fff; border:none; box-shadow:0 4px 10px rgba(29, 111, 66, 0.2);">
                        <i class="fa-regular fa-file-excel"></i> Exportar a Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-print"></i> Imprimir Reporte
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <form action="<?php echo URLROOT; ?>/notas/guardar" method="POST">
            <input type="hidden" name="curso_id" value="<?php echo $data['selectedCurso']; ?>">
            <input type="hidden" name="periodo" value="<?php echo $data['periodo']; ?>">
            <input type="hidden" name="grado_id" value="<?php echo $data['selectedGrado']; ?>">

            <div class="table-wrap">
                <table style="background:#fff;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="width:50px;">Nº</th>
                            <th>Apellidos y Nombres</th>
                            <th style="width:140px; text-align:center;">Calificación</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['alumnosNotas'])): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    <i class="fa-solid fa-user-slash" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                                    Sin alumnos registrados en este grupo.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; foreach($data['alumnosNotas'] as $alumno): ?>
                                <tr>
                                    <td style="color:#94a3b8; font-weight:600; font-size:0.8rem;"><?php echo $counter++; ?></td>
                                    <td style="font-weight:700; color:#334155;">
                                        <?php echo htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombres']); ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if($data['isDirector']): ?>
                                            <!-- MODO SOLO LECTURA PARA DIRECTOR -->
                                            <span style="font-size:1.2rem; font-weight:900; color:<?php echo empty($alumno['nota']) ? '#cbd5e1' : 'var(--azul)'; ?>;">
                                                <?php echo !empty($alumno['nota']) ? htmlspecialchars($alumno['nota']) : '--'; ?>
                                            </span>
                                        <?php else: ?>
                                            <!-- MODO EDICIÓN PARA PROFESORES -->
                                            <input type="text" name="notas[<?php echo $alumno['alumno_id']; ?>]" 
                                                   class="form-control" 
                                                   style="text-align:center; font-weight:800; font-size:1.1rem; color:var(--azul); background:#f8fafc; border-color:#cbd5e1; text-transform: uppercase;"
                                                   maxlength="5" placeholder="--"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   value="<?php echo htmlspecialchars($alumno['nota'] ?? ''); ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($data['isDirector']): ?>
                                            <span style="font-style:italic; color:#64748b; font-size:0.8rem;"><?php echo !empty($alumno['comentario']) ? htmlspecialchars($alumno['comentario']) : 'Sin observaciones'; ?></span>
                                        <?php else: ?>
                                            <input type="text" name="comentarios[<?php echo $alumno['alumno_id']; ?>]" 
                                                   class="form-control" placeholder="Agregar nota..." style="font-size:0.82rem;"
                                                   value="<?php echo htmlspecialchars($alumno['comentario'] ?? ''); ?>">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(!empty($data['alumnosNotas']) && !$data['isDirector']): ?>
                <!-- BOTÓN DE GUARDADO SOLO SE MUESTRA AL PROFESOR -->
                <div style="padding: 1.2rem; background: #f8fafc; border-top: 1px solid var(--border); text-align: right; border-radius: 0 0 16px 16px;">
                    <button type="submit" class="btn btn-success btn-lg" style="box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
                        <i class="fa-solid fa-upload"></i> Publicar / Actualizar Notas del Grupo
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
<?php else: ?>
    <!-- NUEVO TABLERO DE CONTROL Y CUMPLIMIENTO PARA EL DIRECTOR -->
    <?php if($data['isDirector']): ?>
        <div class="card" style="animation: fadeInUp 0.4s ease;">
            <div class="card-header" style="background:#fff; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px;">
                <span class="title-icon" style="background:var(--azul); color:#fff;"><i class="fa-solid fa-table-list"></i></span>
                <div>
                    <h4 style="margin:0; font-weight:800; color:#0f172a;">Monitoreo General de Calificaciones</h4>
                    <span style="font-size:0.75rem; color:#64748b;">Vista unificada de avance por docente — <?php echo $data['periodo']; ?></span>
                </div>
            </div>
            
            <div class="table-wrap">
                <table style="background:#fff;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th>Aula / Sección</th>
                            <th>Docente Responsable</th>
                            <th style="text-align:center;">Alumnos</th>
                            <th style="text-align:center;">Notas Registradas</th>
                            <th style="text-align:center;">Estado / Avance</th>
                            <th style="text-align:center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['reporteCumplimiento'] as $rep): 
                            // Calcular porcentaje o estado
                            $totalEsperadas = $rep['total_alumnos'] * 11; // Asumiendo las 11 materias oficiales
                            $notasActuales = $rep['total_notas'];
                            
                            $badgeClass = 'badge-danger';
                            $lbl = 'Pendiente';
                            if ($notasActuales > 0) {
                                $badgeClass = 'badge-warning';
                                $lbl = 'En Proceso';
                            }
                            // Simplificado: si subió aunque sea 1 nota por alumno ya se considera avanzado
                            if ($notasActuales >= $rep['total_alumnos'] && $rep['total_alumnos'] > 0) {
                                $badgeClass = 'badge-success';
                                $lbl = 'Subiendo / Completo';
                            }
                            
                            // Obtener primer curso para el link rápido
                            $primerCursoId = !empty($data['cursos']) ? $data['cursos'][0]['id'] : '';
                        ?>
                            <tr>
                                <td style="font-weight:800; color:var(--azul); font-size:0.95rem;">
                                    <i class="fa-solid fa-chalkboard" style="margin-right:5px; opacity:0.6;"></i> 
                                    <?php echo htmlspecialchars($rep['aula']); ?>
                                </td>
                                <td style="font-weight:700; color:#334155;">
                                    <?php echo !empty($rep['profesor_nombre']) ? htmlspecialchars($rep['profesor_nombre']) : '<span style="color:#94a3b8; font-weight:400; font-style:italic;">Sin profesor asignado</span>'; ?>
                                </td>
                                <td align="center" style="font-weight:700;"><?php echo $rep['total_alumnos']; ?></td>
                                <td align="center">
                                    <span style="font-weight:800; color:<?php echo $notasActuales > 0 ? 'var(--verde)' : '#64748b'; ?>;">
                                        <?php echo $notasActuales; ?>
                                    </span>
                                </td>
                                <td align="center">
                                    <?php if($rep['total_alumnos'] == 0): ?>
                                        <span class="badge badge-neutral">Sin Alumnos</span>
                                    <?php else: ?>
                                        <span class="badge <?php echo $badgeClass; ?>" style="font-weight:800;">
                                            <?php echo $lbl; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td align="center">
                                    <?php if($rep['total_alumnos'] > 0 && !empty($primerCursoId)): ?>
                                        <a href="<?php echo URLROOT; ?>/notas?grado=<?php echo $rep['grado_id']; ?>&curso=<?php echo $primerCursoId; ?>&periodo=<?php echo urlencode($data['periodo']); ?>" 
                                           class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px;">
                                            <i class="fa-solid fa-eye" style="color:var(--azul);"></i> Ver
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:0.75rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:1rem; background:#f8fafc; font-size:0.78rem; color:#64748b; border-radius:0 0 16px 16px; text-align:center;">
                💡 <span style="font-weight:700;">Consejo:</span> Use la barra superior para seleccionar un Aula y Curso específico si desea auditar o descargar el Excel detallado de un salón.
            </div>
        </div>
    <?php else: ?>
        <!-- Estado vacío Profesor -->
        <div style="text-align:center; padding: 4rem 2rem; background:#f8fafc; border-radius:16px; border: 2px dashed #e2e8f0;">
            <div style="width:80px; height:80px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; box-shadow:var(--sh-sm);">
                <i class="fa-solid fa-book-open" style="font-size:2.5rem; color:var(--amarillo);"></i>
            </div>
            <h3 style="color:#475569; font-weight:800;">Registro de Notas</h3>
            <p style="color:#64748b; font-size:0.9rem; max-width:400px; margin:0.5rem auto 0;">
                Seleccione la **Materia** y el **Periodo** escolar arriba para comenzar a calificar a sus alumnos.
            </p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require APPROOT . '/views/inc/footer.php'; ?>
