<?php
/**
 * Main Totem View - Renders /talca or /curico
 */

$sede = 'talca';
$path = $_SERVER['REQUEST_URI'] ?? '/';

if (strpos($path, '/curico') !== false) {
    $sede = 'curico';
}

// Get settings from API
$settings = [
    'logo_url' => 'https://images.griddo.ucm.cl/logo-ucm-fdc7556e-fa16-4e78-a558-69dfbde3d0d2',
    'header_title' => 'Campus San Miguel',
    'timezone' => 'America/Santiago',
    'footer_title' => '¿Buscas tu sala?',
    'footer_subtitle' => 'Escanea para descargar el mapa.',
    'footer_image_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=2086&auto=format&fit=crop',
    'footer_qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://ucm.cl'
];

// Try to get from database
try {
    $dbHost = getenv('DB_HOST') ?: 'db';
    $dbName = getenv('DB_NAME') ?: 'totem_ucm';
    $dbUser = getenv('DB_USER') ?: 'user';
    $dbPass = getenv('DB_PASSWORD') ?: 'password';
    $dsn = "pgsql:host={$dbHost};dbname={$dbName}";
    $conn = new PDO($dsn, $dbUser, $dbPass);
    $stmt = $conn->prepare("SELECT * FROM global_settings WHERE sede = ? LIMIT 1");
    $stmt->execute([$sede]);
    $dbSettings = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($dbSettings) {
        foreach ($dbSettings as $key => $value) {
            if (!empty($value)) {
                $settings[$key] = $value;
            }
        }
    }
} catch (Exception $e) {
    // Use defaults
}

$now = new DateTime('now', new DateTimeZone($settings['timezone']));
$timeStr = $now->format('H:i');

// Fecha en español manual
$dayNamesEs = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$monthNamesEs = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$dayName = $dayNamesEs[$now->format('w')];
$day = $now->format('j');
$monthName = $monthNamesEs[$now->format('n') - 1];
$dateStr = $dayName . ', ' . $day . ' de ' . $monthName;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Totem UCM - <?= ucfirst($sede) ?></title>
    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --ucm-blue: #003366;
            --ucm-gold: #FDB913;
            --ucm-gray: #F0F2F5;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--ucm-gray);
            color: #333;
        }

        .text-4k-huge {
            font-size: 3vh;
            line-height: 1;
        }

        .text-4k-title {
            font-size: 2.2vh;
            line-height: 1.1;
        }

        .text-4k-subtitle {
            font-size: 1.6vh;
        }

        .text-4k-body {
            font-size: 1vh;
        }

        .text-4k-small {
            font-size: 0.7vh;
        }
    </style>
</head>

<body>
    <div class="flex flex-col w-full h-full bg-[#F0F2F5]">
        <!-- Header (7%) -->
        <header
            class="h-[7%] bg-[#003366] text-white px-[5vw] flex justify-between items-center shrink-0 shadow-xl relative z-50">
            <div class="bg-white px-[2vw] py-[0.8vh] rounded-xl shadow-inner h-[75%] flex items-center">
                <img src="<?= $settings['logo_url'] ?>" alt="Logo UCM" class="h-full w-auto object-contain"
                    style="display: block;">
            </div>
            <div class="flex items-center gap-[3vw]">
                <div class="text-right border-r border-white/20 pr-[3vw] h-[60%] flex flex-col justify-center">
                    <div class="text-[#FDB913] font-bold text-4k-body uppercase tracking-wider" id="date-display">
                        <?= $dateStr ?></div>
                    <div class="text-white/80 text-4k-small font-light"><?= $settings['header_title'] ?></div>
                </div>
                <div class="flex items-center gap-[1vw]">
                    <span class="text-[#FDB913] text-4k-subtitle">🕐</span>
                    <span class="text-4k-huge font-light tabular-nums leading-none" id="clock"><?= $timeStr ?></span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 w-full px-[5vw] py-[2.5vh] flex flex-col gap-[2.5vh] overflow-hidden relative z-10">
            <!-- Hero Slider (20%) -->
            <section class="h-[20%] w-full rounded-[2rem] overflow-hidden shadow-lg z-10 shrink-0 bg-white mb-[5vh]">
                <div id="hero-track">
                    <!-- Slides loaded via JS -->
                </div>
            </section>

            <!-- Main Carousel (remaining height) -->
            <section
                class="flex-1 w-full bg-gray-50 rounded-[2rem] overflow-hidden shadow-[0_10px_40px_rgba(0,51,102,0.08)] relative border z-10"
                id="main-carousel">
                <!-- Slides loaded via JS -->
            </section>
        </main>

        <!-- Footer (12%) -->
        <footer
            class="h-[12%] bg-white px-[5vw] flex items-center justify-between shadow-[0_-5px_30px_rgba(0,0,0,0.05)] z-50 border-t border-gray-100 shrink-0">
            <div class="w-1/2 pr-[2vw] flex flex-col justify-center">
                <div class="flex items-center gap-[1vw] mb-[0.5vh]">
                    <div class="bg-[#003366] p-[0.8vh] rounded-full">
                        <span class="text-white text-4k-subtitle">📍</span>
                    </div>
                    <h2 class="text-4k-title font-bold text-[#003366]"><?= $settings['footer_title'] ?></h2>
                </div>
                <p class="text-4k-subtitle text-gray-500 pl-[3.5vw] leading-tight"><?= $settings['footer_subtitle'] ?>
                </p>
            </div>
            <div class="w-1/2 flex gap-[2vw] h-full items-center justify-end py-[1.5vh]">
                <div
                    class="h-full aspect-video rounded-[1.2rem] overflow-hidden border border-gray-200 relative bg-gray-50 shadow-sm">
                    <img src="<?= $settings['footer_image_url'] ?>" class="w-full h-full object-cover"
                        style="display: block;">
                </div>
                <div
                    class="h-full aspect-square bg-white border border-gray-200 p-[0.5vh] rounded-[1.2rem] shadow-md flex items-center justify-center">
                    <img src="<?= $settings['footer_qr_url'] ?>" class="w-full h-full object-contain"
                        style="display: block;">
                </div>
            </div>
        </footer>
    </div>

    <script>
        const sede = '<?= $sede ?>';
        let currentHeroSlide = 0;
        let currentMainSlide = 0;
        let heroSlides = [];
        let mainSlides = [];

        // Fetch slides from API
        async function fetchData() {
            try {
                const heroRes = await fetch('/api.php?hero-slides&sede=' + sede);
                const mainRes = await fetch('/api.php?main-slides&sede=' + sede);
                const settingsRes = await fetch('/api.php?settings&sede=' + sede);

                heroSlides = await heroRes.json();
                mainSlides = await mainRes.json();

                renderHeroSlides();
                renderMainSlides();
            } catch (e) {
                console.error('Error fetching data:', e);
            }
        }

        function renderHeroSlides() {
            const track = document.getElementById('hero-track');
            if (!heroSlides.length) {
                track.innerHTML = '<div class="text-4k-body text-gray-400 p-8">Cargando slides...</div>';
                return;
            }

            track.innerHTML = heroSlides.map((slide, index) => {
                const bgColor = index % 3 === 2 ? 'bg-[#FDB913] text-[#003366]' : 'bg-[#003366] text-white';
                const borderColor = index % 3 === 2 ? '#003366' : '#003366';
                return '<div class="hero-slide flex" style="min-width: 100%; height: 100%; position: relative; display: flex;">' +
                    '<div style="width: 60%; height: 100%; position: relative; overflow: hidden;">' +
                    '<img src="' + slide.image_url + '" style="width: 100%; height: 100%; object-fit: cover; display: block;">' +
                    '<div style="position: absolute; inset: 0; background-color: rgba(0, 51, 102, 0.1);"></div>' +
                    '</div>' +
                    '<div class="' + bgColor + '" style="width: 40%; height: 100%; display: flex; flex-direction: column; justify-content: center; padding: 0 4vw; position: relative;">' +
                    '<div style="position: absolute; top: 50%; left: -1.8vh; transform: translateY(-50%); width: 0; height: 0; border-top: 1.5vh solid transparent; border-bottom: 1.5vh solid transparent; border-right: 2vh solid ' + borderColor + ';"></div>' +
                    '<h2 class="font-bold text-4k-body uppercase tracking-wider mb-[0.5vh] ' + (index % 3 === 2 ? 'text-white' : 'text-[#FDB913]') + '">' + (slide.subtitle || '') + '</h2>' +
                    '<h1 class="font-bold text-4k-title leading-tight">' + (slide.title || '') + '</h1>' +
                    '</div>' +
                    '</div>';
            }).join('');

            startHeroCarousel();
        }

        function renderMainSlides() {
            const carousel = document.getElementById('main-carousel');
            if (!mainSlides.length) {
                carousel.innerHTML = '<div class="text-4k-body text-gray-400 flex items-center justify-center w-full h-full">No hay piezas activas</div>';
                return;
            }

            carousel.innerHTML = mainSlides.map((slide, index) => {
                return '<div class="main-slide-item" style="position: absolute; inset: 0; overflow: hidden; opacity: ' + (index === 0 ? '1' : '0') + '; transition: opacity 1s ease-in-out; z-index: ' + (index === 0 ? '10' : '0') + ';">' +
                    '<img src="' + slide.image_url + '" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="' + (slide.alt_text || 'Imagen') + '">' +
                    '</div>';
            }).join('');

            startMainCarousel();
        }

        function startHeroCarousel() {
            if (heroSlides.length <= 1) return;
            setInterval(function () {
                currentHeroSlide = (currentHeroSlide + 1) % heroSlides.length;
                const track = document.getElementById('hero-track');
                track.style.transform = 'translateX(-' + (currentHeroSlide * 100) + '%)';
            }, 6000);
        }

        function startMainCarousel() {
            if (mainSlides.length <= 1) return;
            setInterval(function () {
                currentMainSlide = (currentMainSlide + 1) % mainSlides.length;
                document.querySelectorAll('.main-slide-item').forEach(function (el, i) {
                    el.style.opacity = i === currentMainSlide ? 1 : 0;
                    el.style.zIndex = i === currentMainSlide ? 10 : 0;
                });
            }, 5000);
        }

        // Clock
        const monthNamesEs = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        const dayNamesEs = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];

        function formatDateSpanish(date) {
            const dayName = dayNamesEs[date.getDay()];
            const day = date.getDate();
            const month = monthNamesEs[date.getMonth()];
            return dayName + ', ' + day + ' de ' + month;
        }

        function updateClock() {
            fetch('https://worldtimeapi.org/api/timezone/America/Santiago')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    const now = new Date(data.datetime);
                    document.getElementById('clock').textContent = now.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit', hour12: false });
                    document.getElementById('date-display').textContent = formatDateSpanish(now);
                })
                .catch(function () {
                    const now = new Date();
                    document.getElementById('clock').textContent = now.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit', hour12: false });
                    document.getElementById('date-display').textContent = formatDateSpanish(now);
                });
        }

        // Init
        console.log('Page loaded, fetching data...');
        fetchData();
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>

</html>