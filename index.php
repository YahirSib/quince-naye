<?php
// index.php
require 'config.php';

$invitado = null;
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id, nombre, cantidad FROM invitados WHERE token = ?");
    $stmt->execute([$token]);
    $invitado = $stmt->fetch();

    if ($invitado) {
        $stmtCheck = $pdo->prepare("SELECT id FROM asistentes WHERE invitado_id = ?");
        $stmtCheck->execute([$invitado['id']]);
        if ($stmtCheck->fetch()) {
            $yaConfirmo = true;
        } else {
            $yaConfirmo = false;
        }
    }
}

$tokenValido = ($invitado && isset($yaConfirmo) && !$yaConfirmo);
$maxPersonas = $tokenValido ? $invitado['cantidad'] : 0;
$nombreInvitado = $tokenValido ? $invitado['nombre'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis 15 Años - Nayeli Azucena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Pinyon+Script&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: {
                            bg: '#faf5ff',
                            text: '#4a146b',
                            accent: '#c084fc',
                            light: '#e9d5ff',
                            gold: '#d97706',
                        }
                    },
                    fontFamily: {
                        'script': ['"Pinyon Script"', 'cursive'],
                        'serif': ['"Playfair Display"', 'serif'],
                        'sans': ['"Montserrat"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .bg-tangled {
            background: linear-gradient(180deg, #faf5ff 0%, #f3e8ff 40%, #e9d5ff 100%);
            position: relative;
        }
        
        /* Linternas animadas */
        .lantern {
            position: absolute;
            background: linear-gradient(180deg, #fcd34d 0%, #f59e0b 80%, #d97706 100%);
            box-shadow: 0 0 20px 5px rgba(245, 158, 11, 0.5);
            border-radius: 6px 6px 3px 3px;
            animation: floatup-sway 18s infinite ease-in-out;
            opacity: 0.85;
            z-index: 10;
        }
        .lantern::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 15%;
            width: 70%; height: 5px;
            background: #b45309;
            border-radius: 50%;
        }
        @keyframes floatup-sway {
            0% { transform: translateY(100vh) translateX(0px) scale(0.6) rotate(0deg); opacity: 0; }
            10% { opacity: 0.9; }
            25% { transform: translateY(60vh) translateX(25px) scale(0.7) rotate(5deg); }
            50% { transform: translateY(30vh) translateX(-20px) scale(0.8) rotate(-5deg); }
            75% { transform: translateY(0vh) translateX(15px) scale(0.9) rotate(3deg); opacity: 0.9; }
            100% { transform: translateY(-20vh) translateX(-10px) scale(1) rotate(-2deg); opacity: 0; }
        }

        /* Destellos mágicos (Polvo de hadas) */
        .sparkle {
            position: absolute;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.8);
            animation: twinkle 3s infinite ease-in-out;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        /* Flotación suave para las fotos de bebé y personajes */
        .animate-float {
            animation: float-element 6s ease-in-out infinite;
        }
        .animate-float-delay {
            animation: float-element 7s ease-in-out infinite 2s;
        }
        @keyframes float-element {
            0%, 100% { transform: translateY(0px) rotate(var(--rot, 0deg)); }
            50% { transform: translateY(-12px) rotate(calc(var(--rot, 0deg) + 2deg)); }
        }

        .fade-out {
            opacity: 0;
            pointer-events: none;
            transition: opacity 1.5s ease-out;
        }
    </style>
</head>
<body class="bg-[#f3e8ff] text-theme-text font-sans antialiased selection:bg-theme-accent selection:text-white overflow-hidden" id="body-container">

    <!-- PANTALLA DE INICIO (OVERLAY) -->
    <div id="intro-screen" class="fixed inset-0 bg-gradient-to-b from-[#faf5ff] to-[#e9d5ff] z-50 flex flex-col items-center justify-center p-6 text-center overflow-hidden">
        
        <!-- Destellos mágicos en el fondo -->
        <div class="sparkle" style="top: 10%; left: 20%; width: 4px; height: 4px; animation-delay: 0s;"></div>
        <div class="sparkle" style="top: 30%; left: 80%; width: 5px; height: 5px; animation-delay: 1s;"></div>
        <div class="sparkle" style="top: 70%; left: 15%; width: 3px; height: 3px; animation-delay: 2s;"></div>
        <div class="sparkle" style="top: 80%; left: 75%; width: 6px; height: 6px; animation-delay: 0.5s;"></div>

        <!-- ========================================== -->
        <!-- LADO IZQUIERDO: EUGENE + FOTO BEBÉ 1       -->
        <!-- ========================================== -->
        <img src="/EUGENE.png" alt="Eugene" class="absolute bottom-12 left-[-20px] w-36 md:w-48 animate-float pointer-events-none z-10 opacity-90">
        
        <div class="absolute top-20 left-4 md:left-12 w-28 h-32 md:w-36 md:h-40 p-2 bg-white rounded-sm shadow-xl animate-float-delay pointer-events-none z-10" style="--rot: -8deg; transform: rotate(-8deg);">
            <img src="/bebe1.png" alt="Foto Bebé 1" class="w-full h-full object-cover">
        </div>

        <!-- ========================================== -->
        <!-- LADO DERECHO: RAPUNZEL + FOTO BEBÉ 2       -->
        <!-- ========================================== -->
        <img src="/RAPUNZEL.png" alt="Rapunzel" class="absolute top-12 right-[-20px] w-40 md:w-52 animate-float-delay pointer-events-none z-10 opacity-90" style="--rot: 0deg;">
        
        <div class="absolute bottom-24 right-4 md:right-12 w-28 h-32 md:w-36 md:h-40 p-2 bg-white rounded-sm shadow-xl animate-float pointer-events-none z-10" style="--rot: 10deg; transform: rotate(10deg);">
            <img src="/bebe2.jpeg" alt="Foto Bebé 2" class="w-full h-full object-cover">
        </div>

        <!-- Linternas de fondo para el intro -->
        <div class="lantern" style="left: 25%; bottom: -10%; width: 22px; height: 35px; animation-duration: 12s; animation-delay: 1s;"></div>
        <div class="lantern" style="left: 70%; bottom: -20%; width: 28px; height: 42px; animation-duration: 15s; animation-delay: 3s;"></div>

        <!-- Contenedor del Botón Central -->
        <div class="relative z-20 bg-white/70 p-10 rounded-[3rem] backdrop-blur-md border border-white shadow-[0_0_40px_rgba(217,119,6,0.15)] max-w-xs md:max-w-sm mt-4">
            <h2 class="font-script text-6xl md:text-7xl text-theme-text mb-2 drop-shadow-sm">Nayeli Azucena</h2>
            <p class="font-serif uppercase tracking-[0.3em] text-theme-gold mb-6 font-bold">Mis 15 Años</p>

            <?php if ($tokenValido): ?>
            <!-- Saludo personalizado al invitado -->
            <div class="mb-6 border-t border-b border-theme-gold/40 py-5 space-y-3">
                <p class="font-serif text-xs tracking-widest uppercase text-theme-text/80">
                    Invita cordialmente a:
                </p>
                <p class="font-sans text-lg font-bold text-theme-text leading-tight">
                    <?= htmlspecialchars($nombreInvitado) ?>
                </p>
                <div class="flex items-center justify-center gap-2 text-theme-gold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <span class="font-sans text-xs font-bold uppercase tracking-widest">
                        <?= $maxPersonas ?> <?= $maxPersonas === 1 ? 'Invitado' : 'Invitados' ?>
                    </span>
                </div>
            </div>
            <?php elseif (!empty($token)): ?>
            <!-- Token inválido o ya utilizado -->
            <div class="mb-6 border-t border-b border-red-300/40 py-5">
                <?php if (isset($yaConfirmo) && $yaConfirmo): ?>
                    <p class="font-sans text-sm font-bold text-yellow-700 bg-yellow-50 rounded-xl py-3 px-4">
                        Ya has confirmado tu asistencia. ¡Gracias!
                    </p>
                <?php else: ?>
                    <p class="font-sans text-sm font-bold text-red-700 bg-red-50 rounded-xl py-3 px-4">
                        Invitación no válida o expirada.
                    </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (empty($token)): ?>
            <!-- Sin token: botón normal sin personalización -->
            <button onclick="startInvitation()" class="group relative overflow-hidden bg-gradient-to-r from-theme-text to-theme-accent text-white px-8 py-4 rounded-full font-bold uppercase tracking-widest text-xs md:text-sm shadow-lg transition-all transform hover:scale-105 flex items-center gap-3 mx-auto whitespace-nowrap mb-0 mt-4">
                <span class="absolute inset-0 w-full h-full bg-white opacity-0 group-hover:opacity-20 transition-opacity"></span>
                <svg class="w-5 h-5 relative z-10 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                <span class="relative z-10">Abrir Invitación</span>
            </button>
            <?php else: ?>
            <!-- Con token: botón de abrir invitación (mismo estilo que sin token) -->
            <button onclick="startInvitation()" class="group relative overflow-hidden bg-gradient-to-r from-theme-text to-theme-accent text-white px-8 py-4 rounded-full font-bold uppercase tracking-widest text-xs md:text-sm shadow-lg transition-all transform hover:scale-105 flex items-center gap-3 mx-auto whitespace-nowrap">
                <span class="absolute inset-0 w-full h-full bg-white opacity-0 group-hover:opacity-20 transition-opacity"></span>
                <svg class="w-5 h-5 relative z-10 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                <span class="relative z-10">Abrir Invitación</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- INVITACIÓN PRINCIPAL -->
    <div class="max-w-md mx-auto bg-tangled min-h-screen relative shadow-2xl border-x border-theme-light overflow-hidden">
        
        <!-- Audio Player -->
        <audio id="bg-music" loop preload="auto">
            <source src="/veo_en_ti_la_luz.mp3" type="audio/mpeg">
        </audio>

        <!-- Linternas animadas globales -->
        <div class="lantern" style="left: 15%; width: 18px; height: 28px; animation-duration: 20s; animation-delay: 0s;"></div>
        <div class="lantern" style="left: 85%; width: 25px; height: 38px; animation-duration: 17s; animation-delay: 4s;"></div>
        <div class="lantern" style="left: 45%; width: 15px; height: 22px; animation-duration: 22s; animation-delay: 7s;"></div>
        <div class="lantern" style="left: 35%; width: 22px; height: 32px; animation-duration: 19s; animation-delay: 2s;"></div>

        <!-- Sección 1: Portada Principal -->
        <div class="pt-16 pb-8 px-6 text-center relative z-10">
            <div class="mx-auto w-48 h-48 rounded-t-full border-4 border-theme-gold p-1 mb-6 relative shadow-[0_0_20px_rgba(217,119,6,0.3)] bg-white/70 backdrop-blur-sm overflow-hidden">
                <div class="w-full h-full rounded-t-full overflow-hidden bg-theme-light">
                    <!-- Se agrega 'object-top' para forzar la visualización de la parte superior (cara) de la imagen -->
                    <img src="/fotonayeli.jpeg" alt="Nayeli Azucena" class="w-full h-full object-cover object-top">
                </div>
            </div>

            <p class="text-[0.8rem] tracking-[0.2em] uppercase text-theme-text mb-4 leading-relaxed font-bold">
                Karen Azucena Sibrian Arriola<br>
                <span class="font-normal text-theme-text/80">te invita cordialmente a celebrar los</span>
            </p>
            
            <h1 class="font-serif text-2xl tracking-[0.3em] uppercase text-theme-gold mb-2 drop-shadow-sm">15 Años</h1>
            <p class="text-xs tracking-widest uppercase mb-4 text-theme-text/80">de su hija</p>
            
            <h2 class="font-script text-6xl text-theme-text mb-8 leading-none drop-shadow-sm">Nayeli Azucena</h2>

            <!-- Bloque de Fecha -->
            <div class="flex items-center justify-center gap-4 text-theme-text font-serif uppercase tracking-widest border-y border-theme-gold/30 py-4 mx-4">
                <div class="text-xs text-right leading-tight text-theme-text">
                    <span class="block text-[0.6rem] mb-1">Noviembre</span>
                    <span class="block font-bold">Sábado</span>
                </div>
                <div class="text-4xl font-normal text-theme-gold drop-shadow-sm">21</div>
                <div class="text-xs text-left leading-tight text-theme-text">
                    <span class="block text-[0.6rem] mb-1">2026</span>
                    <span class="block font-bold">A las 6PM</span>
                </div>
            </div>

            <!-- Reproductor de Música Interactivo -->
            <div class="flex items-center justify-center gap-6 mt-8 text-theme-text">
                <svg class="w-5 h-5 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                
                <button onclick="toggleMusic()" class="w-12 h-12 bg-gradient-to-tr from-theme-text to-theme-accent rounded-full flex items-center justify-center text-white shadow-lg hover:scale-110 transition-transform z-20">
                    <svg id="play-icon" class="w-5 h-5 ml-1 hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <svg id="pause-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                </button>
                
                <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
        </div>

        <!-- SECCIÓN: AGRADECIMIENTO A DIOS -->
        <div class="px-8 py-14 text-center relative border-t border-white/60 bg-white/40 backdrop-blur-md z-10 shadow-inner overflow-hidden">
            <p class="font-serif text-[0.8rem] leading-relaxed text-theme-text mb-6 italic relative z-10 px-4">
                "Hoy elevamos nuestro corazón a Dios para darle gracias por la vida de nuestra hija, por cada etapa que nos ha permitido acompañarla y por la bendición de verla llegar a sus 15 años. Ponemos su futuro en Sus manos, confiando en que Él iluminará siempre su camino y llenará su vida de amor, sueños y bendiciones."
            </p>
            
            <p class="font-script text-3xl text-theme-gold mb-2 relative z-10 leading-tight px-2">
                "Encomienda al Señor tu camino; confía en Él, y Él actuará."
            </p>
            <p class="font-sans text-xs tracking-widest text-theme-text font-bold relative z-10">
                Salmo 37:5
            </p>
        </div>

        <!-- Sección 2: Recepción y Ubicación -->
        <div class="px-6 py-10 text-center relative border-t border-white/60 bg-theme-light/20 backdrop-blur-sm z-10">
            <h3 class="font-script text-5xl text-theme-text mb-4">Recepción</h3>
            <p class="text-[0.75rem] tracking-[0.2em] uppercase font-bold mb-4 text-theme-gold">Salón de Eventos Los Laureles</p>
            
            <!-- Botones de Google Maps y Waze -->
            <div class="grid grid-cols-2 gap-3 mt-4">
                <!-- Google Maps -->
                <a href="https://maps.app.goo.gl/gk41jCQEtdbw8rnJ9" target="_blank" class="bg-white/80 hover:bg-white border border-theme-accent/30 rounded-2xl p-4 transition-all shadow-lg group flex flex-col items-center justify-center text-decoration-none">
                    <div class="w-10 h-10 bg-theme-gold text-white rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-theme-text block text-center leading-tight">Google<br>Maps</span>
                </a>

                <!-- Waze -->
                <a href="https://ul.waze.com/ul?venue_id=177471625.1774519643.3443843&overview=yes&utm_campaign=default&utm_source=waze_website&utm_medium=lm_share_location" target="_blank" class="bg-white/80 hover:bg-white border border-theme-accent/30 rounded-2xl p-4 transition-all shadow-lg group flex flex-col items-center justify-center text-decoration-none">
                    <div class="w-10 h-10 bg-[#33ccff] text-white rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-theme-text block text-center leading-tight">Ir con<br>Waze</span>
                </a>
            </div>
        </div>

        <!-- Sección 3: Cuenta Regresiva Real -->
        <div class="py-12 text-center bg-white/50 border-y border-white/60 z-10 relative backdrop-blur-sm overflow-hidden">
            <p class="font-script text-3xl text-theme-text mb-6 relative z-10">¡La magia está por comenzar!</p>
            <div class="flex justify-center gap-3 text-3xl font-serif text-theme-text relative z-10">
                <div class="flex flex-col bg-white p-3 rounded-xl border border-theme-light w-16 shadow-md">
                    <span id="dias" class="tracking-widest text-theme-gold">00</span>
                    <span class="text-[0.45rem] font-sans uppercase tracking-widest text-theme-text/70 mt-1 font-bold">Días</span>
                </div>
                <span class="text-theme-light mt-2">:</span>
                <div class="flex flex-col bg-white p-3 rounded-xl border border-theme-light w-16 shadow-md">
                    <span id="horas" class="tracking-widest text-theme-gold">00</span>
                    <span class="text-[0.45rem] font-sans uppercase tracking-widest text-theme-text/70 mt-1 font-bold">Hrs</span>
                </div>
                <span class="text-theme-light mt-2">:</span>
                <div class="flex flex-col bg-white p-3 rounded-xl border border-theme-light w-16 shadow-md">
                    <span id="mins" class="tracking-widest text-theme-gold">00</span>
                    <span class="text-[0.45rem] font-sans uppercase tracking-widest text-theme-text/70 mt-1 font-bold">Min</span>
                </div>
                <span class="text-theme-light mt-2">:</span>
                <div class="flex flex-col bg-white p-3 rounded-xl border border-theme-light w-16 shadow-md">
                    <span id="segs" class="tracking-widest text-theme-gold">00</span>
                    <span class="text-[0.45rem] font-sans uppercase tracking-widest text-theme-text/70 mt-1 font-bold">Seg</span>
                </div>
            </div>
        </div>

        <!-- Sección 4: Vestimenta y Regalos -->
        <div class="px-6 py-12 text-center bg-theme-light/20 backdrop-blur-md z-10 relative border-b border-theme-light">
            <h3 class="font-script text-5xl text-theme-text mb-4">Dress Code</h3>
            <p class="text-xs uppercase tracking-widest text-theme-gold mb-4 font-bold">Elegante y Formal</p>
            
            <div class="">
                <p class="text-[0.65rem] uppercase tracking-widest text-theme-text/90 mb-6 px-4 leading-relaxed font-medium">
                    Con el fin de que nuestra quinceañera brille en su día especial, les pedimos cordialmente evitar el uso de vestidos o prendas en color lila y cualquiera de sus tonalidades o derivados, ya que este color está reservado exclusivamente para ella. ¡Agradecemos su comprensión y estamos ansiosos por celebrar juntos!
                </p>
            </div>

            <h3 class="font-script text-5xl text-theme-text mb-4">Lluvia de Sobres</h3>
            <div class="w-16 h-16 mx-auto bg-theme-light rounded-full flex items-center justify-center mb-4 border-2 border-white shadow-md">
                <svg class="w-8 h-8 text-theme-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>
            <p class="text-[0.65rem] uppercase tracking-widest text-theme-text/90 mb-6 px-4 leading-relaxed font-medium">
                Esta noche será aún más especial con tu presencia. Si querés hacerme un presente, podrás dejar tu sobre en mi buzón de sueños.
            </p>
        </div>

        <!-- Sección 5: RSVP Formulario -->
        <div id="rsvp-section" class="bg-gradient-to-b from-white/60 to-[#d8b4fe]/60 px-8 py-12 text-center z-10 relative backdrop-blur-lg">
            <h3 class="font-script text-4xl text-theme-text mb-2 drop-shadow-sm">Confirmar Asistencia</h3>
            
            <p class="font-serif uppercase text-[0.65rem] tracking-widest mb-8 text-theme-text font-bold bg-white/70 py-2 rounded-full inline-block px-6 shadow-sm border border-white">
                Confirmar antes del <span class="text-theme-gold text-sm ml-1">15 de Octubre</span>
            </p>

            <?php if (!empty($token) && !$tokenValido): ?>
                <?php if (isset($yaConfirmo) && $yaConfirmo): ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-4 rounded-xl text-sm text-center font-bold tracking-wide">
                        Ya has confirmado tu asistencia. ¡Gracias!
                    </div>
                <?php else: ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-4 rounded-xl text-sm text-center font-bold tracking-wide">
                        Invitación no válida o expirada.
                    </div>
                <?php endif; ?>
            <?php elseif ($tokenValido): ?>
            <div class="bg-white/95 border border-white p-6 rounded-2xl shadow-2xl text-left relative overflow-hidden">
                <div id="rsvp-mensaje" class="hidden px-4 py-3 rounded-xl text-sm mb-6 text-center font-bold tracking-wide relative z-10"></div>

                <form id="rsvp-form" class="space-y-5 relative z-10">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-wider text-theme-text mb-2">Nombre y Apellido</label>
                        <input type="text" name="nombre" required value="<?= htmlspecialchars($nombreInvitado) ?>" placeholder="Ej. Juan Pérez" class="w-full bg-gray-50 border border-theme-light focus:border-theme-accent focus:ring-2 focus:ring-theme-accent/20 text-theme-text outline-none py-3 px-4 text-sm rounded-xl shadow-inner transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-wider text-theme-text mb-3">¿Asistirás a la fiesta?</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 text-xs text-theme-text bg-gray-50 p-4 rounded-xl shadow-sm cursor-pointer border border-transparent hover:border-theme-light transition-all">
                                <input type="radio" name="asistira" value="si" class="accent-theme-accent w-4 h-4" required>
                                ¡Sí, asistiré con gusto!
                            </label>
                            <label class="flex items-center gap-3 text-xs text-theme-text bg-gray-50 p-4 rounded-xl shadow-sm cursor-pointer border border-transparent hover:border-theme-light transition-all">
                                <input type="radio" name="asistira" value="no" class="accent-theme-accent w-4 h-4">
                                Lamentablemente no podré asistir
                            </label>
                        </div>
                    </div>

                    <?php if ($maxPersonas > 1): ?>
                    <div id="bloque-acompanantes" class="hidden">
                        <label class="block text-[0.65rem] font-bold uppercase tracking-wider text-theme-text mb-2">
                            Acompañantes <span class="text-theme-gold">(Máximo <?= $maxPersonas - 1 ?>)</span>
                        </label>
                        <select name="acompanantes" class="w-full bg-gray-50 border border-theme-light focus:border-theme-accent focus:ring-2 focus:ring-theme-accent/20 text-theme-text outline-none py-3 px-4 text-sm rounded-xl shadow-inner transition-all">
                            <?php for ($i = 0; $i < $maxPersonas; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'persona' : 'personas' ?></option>
                            <?php endfor; ?>
                        </select>
                        <p class="text-[0.55rem] text-theme-text/60 mt-1 italic">Incluyéndote, esta invitación es para <?= $maxPersonas ?> <?= $maxPersonas === 1 ? 'persona' : 'personas' ?> en total.</p>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="acompanantes" value="0">
                    <?php endif; ?>

                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-wider text-theme-text mb-2">Mensaje (opcional)</label>
                        <textarea name="mensaje" rows="3" placeholder="Escribe un mensaje para la quinceañera..." class="w-full bg-gray-50 border border-theme-light focus:border-theme-accent focus:ring-2 focus:ring-theme-accent/20 text-theme-text outline-none py-3 px-4 text-sm rounded-xl shadow-inner transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" id="btn-submit" class="w-full bg-theme-text hover:bg-theme-accent text-white text-xs font-bold uppercase tracking-widest py-4 rounded-xl mt-4 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Confirmar Asistencia
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="bg-white/60 backdrop-blur-md border border-white rounded-2xl py-6 px-4 shadow-sm">
                <p class="font-sans text-xs text-theme-text/70 tracking-wide">
                    Necesitás un enlace personalizado para confirmar tu asistencia.
                </p>
            </div>
            <?php endif; ?>
        </div>
        
    </div>

    <script>
        const audio = document.getElementById('bg-music');
        const playIcon = document.getElementById('play-icon');
        const pauseIcon = document.getElementById('pause-icon');

        function startInvitation() {
            const introScreen = document.getElementById('intro-screen');
            const bodyContainer = document.getElementById('body-container');
            
            introScreen.classList.add('fade-out');
            bodyContainer.classList.remove('overflow-hidden');
            
            audio.play().catch(error => {
                console.log("Audio requiere interacción en móviles", error);
            });
        }

        function toggleMusic() {
            if (audio.paused) {
                audio.play();
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            } else {
                audio.pause();
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }

        const targetDate = new Date("November 21, 2026 19:00:00").getTime();
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = targetDate - now;
            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("dias").innerHTML = days < 10 ? "0" + days : days;
                document.getElementById("horas").innerHTML = hours < 10 ? "0" + hours : hours;
                document.getElementById("mins").innerHTML = minutes < 10 ? "0" + minutes : minutes;
                document.getElementById("segs").innerHTML = seconds < 10 ? "0" + seconds : seconds;
            } else {
                clearInterval(countdown);
                document.getElementById("dias").innerHTML = "00";
                document.getElementById("horas").innerHTML = "00";
                document.getElementById("mins").innerHTML = "00";
                document.getElementById("segs").innerHTML = "00";
            }
        }, 1000);

        // RSVP AJAX
        const rsvpForm = document.getElementById('rsvp-form');
        if (rsvpForm) {
            const radioSi = rsvpForm.querySelector('input[name="asistira"][value="si"]');
            const radioNo = rsvpForm.querySelector('input[name="asistira"][value="no"]');
            const bloqueAcomp = document.getElementById('bloque-acompanantes');

            if (radioSi && radioNo && bloqueAcomp) {
                radioSi.addEventListener('change', function() {
                    bloqueAcomp.classList.remove('hidden');
                });
                radioNo.addEventListener('change', function() {
                    bloqueAcomp.classList.add('hidden');
                });
            }

            rsvpForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const btnSubmit = document.getElementById('btn-submit');
                const mensajeDiv = document.getElementById('rsvp-mensaje');
                const formData = new FormData(rsvpForm);
                const data = Object.fromEntries(formData.entries());

                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Enviando...';
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');

                fetch('rsvp_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    mensajeDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'border-green-400', 'bg-red-100', 'text-red-700', 'border-red-400', 'bg-yellow-100', 'text-yellow-700', 'border-yellow-400');

                    if (result.success) {
                        mensajeDiv.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-400');
                        mensajeDiv.textContent = result.message;
                        rsvpForm.reset();
                        rsvpForm.classList.add('hidden');

                        if (bloqueAcomp) bloqueAcomp.classList.add('hidden');
                    } else {
                        mensajeDiv.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');
                        mensajeDiv.textContent = result.message;
                    }

                    setTimeout(() => { mensajeDiv.classList.add('hidden'); }, 5000);
                })
                .catch(error => {
                    mensajeDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700', 'bg-yellow-100', 'text-yellow-700');
                    mensajeDiv.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');
                    mensajeDiv.textContent = 'Error de conexión. Intenta de nuevo.';
                    console.error('Error:', error);
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Confirmar Asistencia';
                    btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                });
            });
        }
    </script>
</body>
</html>