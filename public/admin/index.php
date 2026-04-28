<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /admin/login.php');
    exit;
}
$user = $_SESSION['user'];
$sede = $_GET['sede'] ?? 'talca';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Totem UCM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    <header class="bg-[#003366] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="https://images.griddo.ucm.cl/logo-ucm-fdc7556e-fa16-4e78-a558-69dfbde3d0d2" alt="Logo" class="h-10 bg-white px-3 py-1 rounded">
                <h1 class="text-xl font-bold">Administración Totem</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm opacity-80"><?= htmlspecialchars($user['username']) ?></span>
                <a href="/admin/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm transition">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex gap-2">
            <a href="?sede=talca" class="px-6 py-2 rounded-t-lg <?= $sede === 'talca' ? 'bg-white shadow text-[#003366] font-semibold' : 'bg-gray-200 text-gray-600' ?>">Talca</a>
            <a href="?sede=curico" class="px-6 py-2 rounded-t-lg <?= $sede === 'curico' ? 'bg-white shadow text-[#003366] font-semibold' : 'bg-gray-200 text-gray-600' ?>">Curicó</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-8">
        <div class="bg-white rounded-b-2xl rounded-tr-2xl shadow-lg p-6">
            <div class="flex gap-8 border-b mb-6">
                <button onclick="showTab('hero')" id="tab-hero" class="tab-active pb-3 px-2">Banner Superior</button>
                <button onclick="showTab('main')" id="tab-main" class="text-gray-500 hover:text-[#003366] pb-3 px-2">Carrusel Central</button>
                <button onclick="showTab('settings')" id="tab-settings" class="text-gray-500 hover:text-[#003366] pb-3 px-2">Configuración</button>
                <?php if ($user['role'] === 'SuperAdministrador'): ?>
                <button onclick="showTab('users')" id="tab-users" class="text-gray-500 hover:text-[#003366] pb-3 px-2">Usuarios</button>
                <?php endif; ?>
            </div>

            <div id="content-hero" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Slides del Banner Superior</h2>
                    <button onclick="showHeroModal()" class="bg-[#003366] text-white px-4 py-2 rounded-lg hover:bg-[#004488] transition">+ Agregar Slide</button>
                </div>
                <div id="hero-slides-list" class="space-y-4"><p class="text-gray-400">Cargando...</p></div>
            </div>

            <div id="content-main" class="tab-content" style="display:none">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Slides del Carrusel Central</h2>
                    <button onclick="showMainModal()" class="bg-[#003366] text-white px-4 py-2 rounded-lg hover:bg-[#004488] transition">+ Agregar Slide</button>
                </div>
                <div id="main-slides-list" class="space-y-4"><p class="text-gray-400">Cargando...</p></div>
            </div>

            <div id="content-settings" class="tab-content" style="display:none">
                <h2 class="text-lg font-semibold mb-4">Configuración General</h2>
                <form id="settings-form" class="space-y-4 max-w-2xl">
                    <div>
                        <label class="block text-sm font-medium mb-1">Título del Header</label>
                        <input type="text" id="settings-header_title" name="header_title" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Logo</label>
                        <div id="settings-logo-preview" class="mb-2"></div>
                        <input type="file" id="settings-logo_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                        <input type="hidden" id="settings-logo_url" name="logo_url">
                        <p class="text-xs text-gray-500 mt-1">O ingresa URL: <input type="text" id="settings-logo_url_text" class="mt-1 w-full px-2 py-1 border rounded"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">URL Imagen Footer</label>
                        <div id="settings-footer_image-preview" class="mb-2"></div>
                        <input type="file" id="settings-footer_image_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                        <input type="hidden" id="settings-footer_image_url" name="footer_image_url">
                        <p class="text-xs text-gray-500 mt-1">O ingresa URL: <input type="text" id="settings-footer_image_url_text" class="mt-1 w-full px-2 py-1 border rounded"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">URL QR Footer</label>
                        <div id="settings-footer_qr-preview" class="mb-2"></div>
                        <input type="file" id="settings-footer_qr_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                        <input type="hidden" id="settings-footer_qr_url" name="footer_qr_url">
                        <p class="text-xs text-gray-500 mt-1">O ingresa URL: <input type="text" id="settings-footer_qr_url_text" class="mt-1 w-full px-2 py-1 border rounded"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Título Footer</label>
                        <input type="text" id="settings-footer_title" name="footer_title" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Subtítulo Footer</label>
                        <input type="text" id="settings-footer_subtitle" name="footer_subtitle" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <button type="submit" class="bg-[#FDB913] text-[#003366] px-6 py-2 rounded-lg font-semibold hover:bg-yellow-400 transition">Guardar</button>
                </form>
            </div>

            <?php if ($user['role'] === 'SuperAdministrador'): ?>
            <div id="content-users" class="tab-content" style="display:none">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Gestión de Usuarios</h2>
                    <button onclick="showUserModal()" class="bg-[#003366] text-white px-4 py-2 rounded-lg hover:bg-[#004488] transition">+ Agregar Usuario</button>
                </div>
                <div id="users-list" class="space-y-4"><p class="text-gray-400">Cargando...</p></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hero Modal -->
    <div id="hero-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <h3 class="text-xl font-bold mb-4" id="hero-modal-title">Agregar Slide Banner</h3>
            <form id="hero-form" class="space-y-4">
                <input type="hidden" id="hero-id" value="">
                <div id="hero-preview" class="mb-2 hidden"><img src="" class="w-full h-32 object-cover rounded"></div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subir Imagen</label>
                    <input type="file" id="hero-image_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">O ingresa URL</label>
                    <input type="text" id="hero-image_url_text" class="w-full px-4 py-2 border rounded-lg" placeholder="https://ejemplo.com/imagen.jpg">
                    <p class="text-xs text-gray-500 mt-1">Dejar en blanco si se subió un archivo</p>
                </div>
                <input type="hidden" id="hero-image_url" name="image_url">
                <div>
                    <label class="block text-sm font-medium mb-1">Título</label>
                    <input type="text" id="hero-title" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtítulo</label>
                    <input type="text" id="hero-subtitle" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Orden</label>
                    <input type="number" id="hero-order_index" class="w-full px-4 py-2 border rounded-lg" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL Link</label>
                    <input type="text" id="hero-link_url" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-[#003366] text-white py-2 rounded-lg hover:bg-[#004488] transition">Guardar</button>
                    <button type="button" onclick="closeHeroModal()" class="flex-1 bg-gray-300 py-2 rounded-lg hover:bg-gray-400 transition">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Modal -->
    <div id="main-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <h3 class="text-xl font-bold mb-4" id="main-modal-title">Agregar Slide Central</h3>
            <form id="main-form" class="space-y-4">
                <input type="hidden" id="main-id" value="">
                <div id="main-preview" class="mb-2 hidden"><img src="" class="w-full h-32 object-cover rounded"></div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subir Imagen</label>
                    <input type="file" id="main-image_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">O ingresa URL</label>
                    <input type="text" id="main-image_url_text" class="mt-1 w-full px-2 py-1 border rounded" placeholder="https://ejemplo.com/imagen.jpg">
                    <input type="hidden" id="main-image_url" name="image_url">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Texto Alternativo</label>
                    <input type="text" id="main-alt_text" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Título</label>
                    <input type="text" id="main-title" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtítulo</label>
                    <input type="text" id="main-subtitle" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Orden</label>
                    <input type="number" id="main-order_index" class="w-full px-4 py-2 border rounded-lg" value="0">
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-[#003366] text-white py-2 rounded-lg hover:bg-[#004488] transition">Guardar</button>
                    <button type="button" onclick="closeMainModal()" class="flex-1 bg-gray-300 py-2 rounded-lg hover:bg-gray-400 transition">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Modal -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4">
            <h3 class="text-xl font-bold mb-4" id="user-modal-title">Agregar Usuario</h3>
            <form id="user-form" class="space-y-4">
                <input type="hidden" id="user-id" value="">
                <div>
                    <label class="block text-sm font-medium mb-1">Usuario</label>
                    <input type="text" id="user-username" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="user-email" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Contraseña</label>
                    <input type="password" id="user-password" class="w-full px-4 py-2 border rounded-lg" placeholder="Nueva contraseña">
                    <p class="text-xs text-gray-500 mt-1">Dejar en blanco para mantener actual</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Rol</label>
                    <select id="user-role" class="w-full px-4 py-2 border rounded-lg">
                        <option value="Administrador">Administrador</option>
                        <option value="SuperAdministrador">SuperAdministrador</option>
                    </select>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-[#003366] text-white py-2 rounded-lg hover:bg-[#004488] transition">Guardar</button>
                    <button type="button" onclick="closeUserModal()" class="flex-1 bg-gray-300 py-2 rounded-lg hover:bg-gray-400 transition">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var sede = '<?= $sede ?>';
        var heroSlides = [];
        var mainSlides = [];
        var settings = {};
        var users = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadHeroSlides();
            loadMainSlides();
            loadSettings();
            <?php if ($user['role'] === 'SuperAdministrador'): ?>
            loadUsers();
            <?php endif; ?>
        });

        function showTab(tab) {
            var tabs = document.querySelectorAll('.tab-content');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].style.display = 'none';
            }
            var tabBtns = document.querySelectorAll('[id^="tab-"]');
            for (var j = 0; j < tabBtns.length; j++) {
                tabBtns[j].className = 'text-gray-500 hover:text-[#003366] pb-3 px-2';
            }
            document.getElementById('content-' + tab).style.display = 'block';
            document.getElementById('tab-' + tab).className = 'tab-active pb-3 px-2';
        }

        function setupFilePreview(fileInputId, previewDivId) {
            var fileInput = document.getElementById(fileInputId);
            var previewDiv = document.getElementById(previewDivId);
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        previewDiv.classList.remove('hidden');
                        previewDiv.querySelector('img').src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        setupFilePreview('hero-image_file', 'hero-preview');
        setupFilePreview('main-image_file', 'main-preview');
        setupFilePreview('settings-logo_file', 'settings-logo-preview');
        setupFilePreview('settings-footer_image_file', 'settings-footer_image-preview');
        setupFilePreview('settings-footer_qr_file', 'settings-footer_qr-preview');

        function loadHeroSlides() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/api.php?hero-slides&sede=' + sede);
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    heroSlides = JSON.parse(xhr.responseText);
                    renderHeroSlides();
                }
            };
            xhr.send();
        }

        function renderHeroSlides() {
            var list = document.getElementById('hero-slides-list');
            if (!heroSlides.length) {
                list.innerHTML = '<p class="text-gray-400">No hay slides. Agrega uno.</p>';
                return;
            }
            var html = '';
            for (var i = 0; i < heroSlides.length; i++) {
                var slide = heroSlides[i];
                html += '<div class="border rounded-lg p-4 flex gap-4 items-center">' +
                    '<img src="' + slide.image_url + '" class="w-32 h-20 object-cover rounded">' +
                    '<div class="flex-1"><h3 class="font-semibold">' + (slide.title || '') + '</h3>' +
                    '<p class="text-sm text-gray-500">' + (slide.subtitle || '') + '</p></div>' +
                    '<div class="flex gap-2">' +
                    '<button onclick="editHeroSlide(' + slide.id + ')" class="text-blue-600 hover:underline">Editar</button>' +
                    '<button onclick="deleteHeroSlide(' + slide.id + ')" class="text-red-600 hover:underline">Eliminar</button>' +
                    '</div></div>';
            }
            list.innerHTML = html;
        }

        function showHeroModal(id) {
            document.getElementById('hero-modal').classList.remove('hidden');
            document.getElementById('hero-modal').classList.add('flex');
            if (id) {
                for (var i = 0; i < heroSlides.length; i++) {
                    if (heroSlides[i].id === id) {
                        var slide = heroSlides[i];
                        break;
                    }
                }
                document.getElementById('hero-modal-title').textContent = 'Editar Slide Banner';
                document.getElementById('hero-id').value = slide.id;
                document.getElementById('hero-image_url').value = slide.image_url;
                document.getElementById('hero-title').value = slide.title || '';
                document.getElementById('hero-subtitle').value = slide.subtitle || '';
                document.getElementById('hero-order_index').value = slide.order_index || 0;
                document.getElementById('hero-link_url').value = slide.link_url || '';
            } else {
                document.getElementById('hero-modal-title').textContent = 'Agregar Slide Banner';
                document.getElementById('hero-form').reset();
                document.getElementById('hero-id').value = '';
            }
        }

        function closeHeroModal() {
            document.getElementById('hero-modal').classList.add('hidden');
            document.getElementById('hero-modal').classList.remove('flex');
        }

        document.getElementById('hero-form').addEventListener('submit', function(e) {
            e.preventDefault();
            var imageUrl = document.getElementById('hero-image_url_text').value || document.getElementById('hero-image_url').value;
            var heroFileInput = document.getElementById('hero-image_file');
            
            if (heroFileInput.files.length > 0) {
                var formData = new FormData();
                formData.append('file', heroFileInput.files[0]);
                var uploadXhr = new XMLHttpRequest();
                uploadXhr.open('POST', '/api.php?upload');
                uploadXhr.withCredentials = true;
                uploadXhr.onload = function() {
                    if (uploadXhr.status === 200) {
                        var uploadData = JSON.parse(uploadXhr.responseText);
                        if (uploadData.url) {
                            saveHeroSlide(uploadData.url);
                        } else {
                            alert('Error al subir imagen: ' + (uploadData.error || 'Unknown'));
                        }
                    }
                };
                uploadXhr.send(formData);
            } else {
                saveHeroSlide(imageUrl);
            }
        });

        function saveHeroSlide(imageUrl) {
            var id = document.getElementById('hero-id').value;
            var data = {
                image_url: imageUrl,
                title: document.getElementById('hero-title').value,
                subtitle: document.getElementById('hero-subtitle').value,
                order_index: parseInt(document.getElementById('hero-order_index').value),
                link_url: document.getElementById('hero-link_url').value,
                sede: sede
            };
            
            var xhr = new XMLHttpRequest();
            if (id) {
                xhr.open('PUT', '/api.php?hero-slides&id=' + id);
            } else {
                xhr.open('POST', '/api.php?hero-slides');
            }
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    closeHeroModal();
                    loadHeroSlides();
                } else {
                    alert('Error al guardar');
                }
            };
            xhr.send(JSON.stringify(data));
        }

        function editHeroSlide(id) { showHeroModal(id); }
        function deleteHeroSlide(id) {
            if (confirm('¿Eliminar slide?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE', '/api.php?hero-slides&id=' + id);
                xhr.withCredentials = true;
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        loadHeroSlides();
                    }
                };
                xhr.send();
            }
        }

        function loadMainSlides() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/api.php?main-slides&sede=' + sede);
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    mainSlides = JSON.parse(xhr.responseText);
                    renderMainSlides();
                }
            };
            xhr.send();
        }

        function renderMainSlides() {
            var list = document.getElementById('main-slides-list');
            if (!mainSlides.length) {
                list.innerHTML = '<p class="text-gray-400">No hay slides. Agrega uno.</p>';
                return;
            }
            var html = '';
            for (var i = 0; i < mainSlides.length; i++) {
                var slide = mainSlides[i];
                html += '<div class="border rounded-lg p-4 flex gap-4 items-center">' +
                    '<img src="' + slide.image_url + '" class="w-32 h-20 object-cover rounded">' +
                    '<div class="flex-1"><p class="text-sm text-gray-500">' + (slide.alt_text || '') + '</p></div>' +
                    '<div class="flex gap-2">' +
                    '<button onclick="editMainSlide(' + slide.id + ')" class="text-blue-600 hover:underline">Editar</button>' +
                    '<button onclick="deleteMainSlide(' + slide.id + ')" class="text-red-600 hover:underline">Eliminar</button>' +
                    '</div></div>';
            }
            list.innerHTML = html;
        }

        function showMainModal(id) {
            document.getElementById('main-modal').classList.remove('hidden');
            document.getElementById('main-modal').classList.add('flex');
            if (id) {
                for (var i = 0; i < mainSlides.length; i++) {
                    if (mainSlides[i].id === id) {
                        var slide = mainSlides[i];
                        break;
                    }
                }
                document.getElementById('main-modal-title').textContent = 'Editar Slide Central';
                document.getElementById('main-id').value = slide.id;
                document.getElementById('main-image_url').value = slide.image_url;
                document.getElementById('main-alt_text').value = slide.alt_text || '';
                document.getElementById('main-title').value = slide.title || '';
                document.getElementById('main-subtitle').value = slide.subtitle || '';
                document.getElementById('main-order_index').value = slide.order_index || 0;
            } else {
                document.getElementById('main-modal-title').textContent = 'Agregar Slide Central';
                document.getElementById('main-form').reset();
                document.getElementById('main-id').value = '';
            }
        }

        function closeMainModal() {
            document.getElementById('main-modal').classList.add('hidden');
            document.getElementById('main-modal').classList.remove('flex');
        }

        document.getElementById('main-form').addEventListener('submit', function(e) {
            e.preventDefault();
            var imageUrl = document.getElementById('main-image_url_text').value || document.getElementById('main-image_url').value;
            var mainFileInput = document.getElementById('main-image_file');
            
            if (mainFileInput.files.length > 0) {
                var formData = new FormData();
                formData.append('file', mainFileInput.files[0]);
                var uploadXhr = new XMLHttpRequest();
                uploadXhr.open('POST', '/api.php?upload');
                uploadXhr.withCredentials = true;
                uploadXhr.onload = function() {
                    if (uploadXhr.status === 200) {
                        var uploadData = JSON.parse(uploadXhr.responseText);
                        if (uploadData.url) {
                            saveMainSlide(uploadData.url);
                        } else {
                            alert('Error al subir imagen: ' + (uploadData.error || 'Unknown'));
                        }
                    }
                };
                uploadXhr.send(formData);
            } else {
                saveMainSlide(imageUrl);
            }
        });

        function saveMainSlide(imageUrl) {
            var id = document.getElementById('main-id').value;
            var data = {
                image_url: imageUrl,
                alt_text: document.getElementById('main-alt_text').value,
                title: document.getElementById('main-title').value,
                subtitle: document.getElementById('main-subtitle').value,
                order_index: parseInt(document.getElementById('main-order_index').value),
                sede: sede
            };
            
            var xhr = new XMLHttpRequest();
            if (id) {
                xhr.open('PUT', '/api.php?main-slides&id=' + id);
            } else {
                xhr.open('POST', '/api.php?main-slides');
            }
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    closeMainModal();
                    loadMainSlides();
                } else {
                    alert('Error al guardar');
                }
            };
            xhr.send(JSON.stringify(data));
        }

        function editMainSlide(id) { showMainModal(id); }
        function deleteMainSlide(id) {
            if (confirm('¿Eliminar slide?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE', '/api.php?main-slides&id=' + id);
                xhr.withCredentials = true;
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        loadMainSlides();
                    }
                };
                xhr.send();
            }
        }

        function loadSettings() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/api.php?settings&sede=' + sede);
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    settings = JSON.parse(xhr.responseText);
                    document.getElementById('settings-header_title').value = settings.header_title || '';
                    document.getElementById('settings-logo_url').value = settings.logo_url || '';
                    document.getElementById('settings-footer_title').value = settings.footer_title || '';
                    document.getElementById('settings-footer_subtitle').value = settings.footer_subtitle || '';
                    document.getElementById('settings-footer_image_url').value = settings.footer_image_url || '';
                    document.getElementById('settings-footer_qr_url').value = settings.footer_qr_url || '';
                }
            };
            xhr.send();
        }

        document.getElementById('settings-form').addEventListener('submit', function(e) {
            e.preventDefault();
            var fileFields = [
                { input: 'settings-logo_file', hidden: 'settings-logo_url' },
                { input: 'settings-footer_image_file', hidden: 'settings-footer_image_url' },
                { input: 'settings-footer_qr_file', hidden: 'settings-footer_qr_url' }
            ];
            
            uploadSettingsFiles(0, fileFields);
        });

        function uploadSettingsFiles(index, fields) {
            if (index >= fields.length) {
                saveSettings();
                return;
            }
            var field = fields[index];
            var fileInput = document.getElementById(field.input);
            if (fileInput.files.length > 0) {
                var formData = new FormData();
                formData.append('file', fileInput.files[0]);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/api.php?upload');
                xhr.withCredentials = true;
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        if (data.url) {
                            document.getElementById(field.hidden).value = data.url;
                            uploadSettingsFiles(index + 1, fields);
                        } else {
                            alert('Error al subir: ' + (data.error || 'Unknown'));
                        }
                    }
                };
                xhr.send(formData);
            } else {
                uploadSettingsFiles(index + 1, fields);
            }
        }

        function saveSettings() {
            var data = {
                header_title: document.getElementById('settings-header_title').value,
                logo_url: document.getElementById('settings-logo_url').value,
                footer_title: document.getElementById('settings-footer_title').value,
                footer_subtitle: document.getElementById('settings-footer_subtitle').value,
                footer_image_url: document.getElementById('settings-footer_image_url').value,
                footer_qr_url: document.getElementById('settings-footer_qr_url').value,
                sede: sede
            };
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', '/api.php?settings&sede=' + sede);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Configuración guardada');
                    loadSettings();
                }
            };
            xhr.send(JSON.stringify(data));
        }

        function loadUsers() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/api.php?users');
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    users = JSON.parse(xhr.responseText);
                    renderUsers();
                }
            };
            xhr.send();
        }

        function renderUsers() {
            var list = document.getElementById('users-list');
            if (!users.length) {
                list.innerHTML = '<p class="text-gray-400">No hay usuarios.</p>';
                return;
            }
            var html = '';
            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                html += '<div class="border rounded-lg p-4 flex gap-4 items-center">' +
                    '<div class="flex-1"><h3 class="font-semibold">' + user.username + '</h3>' +
                    '<p class="text-sm text-gray-500">' + (user.email || '') + ' - ' + user.role + '</p></div>' +
                    '<div class="flex gap-2">' +
                    '<button onclick="editUser(' + user.id + ')" class="text-blue-600 hover:underline">Editar</button>' +
                    '<button onclick="deleteUser(' + user.id + ')" class="text-red-600 hover:underline">Eliminar</button>' +
                    '</div></div>';
            }
            list.innerHTML = html;
        }

        function showUserModal(id) {
            document.getElementById('user-modal').classList.remove('hidden');
            document.getElementById('user-modal').classList.add('flex');
            if (id) {
                for (var i = 0; i < users.length; i++) {
                    if (users[i].id === id) {
                        var user = users[i];
                        break;
                    }
                }
                document.getElementById('user-modal-title').textContent = 'Editar Usuario';
                document.getElementById('user-id').value = user.id;
                document.getElementById('user-username').value = user.username;
                document.getElementById('user-email').value = user.email || '';
                document.getElementById('user-role').value = user.role;
                document.getElementById('user-password').placeholder = 'Nueva contraseña (opcional)';
            } else {
                document.getElementById('user-modal-title').textContent = 'Agregar Usuario';
                document.getElementById('user-form').reset();
                document.getElementById('user-id').value = '';
                document.getElementById('user-password').placeholder = 'Contraseña';
            }
        }

        function closeUserModal() {
            document.getElementById('user-modal').classList.add('hidden');
            document.getElementById('user-modal').classList.remove('flex');
        }

        document.getElementById('user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            var id = document.getElementById('user-id').value;
            var data = {
                username: document.getElementById('user-username').value,
                email: document.getElementById('user-email').value,
                role: document.getElementById('user-role').value
            };
            var password = document.getElementById('user-password').value;
            if (password) data.newPassword = password;
            
            var xhr = new XMLHttpRequest();
            if (id) {
                xhr.open('PUT', '/api.php?users&id=' + id);
            } else {
                data.password = password || 'admin123';
                xhr.open('POST', '/api.php?users');
            }
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    closeUserModal();
                    loadUsers();
                } else {
                    alert('Error al guardar');
                }
            };
            xhr.send(JSON.stringify(data));
        });

        function editUser(id) { showUserModal(id); }
        function deleteUser(id) {
            if (confirm('¿Eliminar usuario?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE', '/api.php?users&id=' + id);
                xhr.withCredentials = true;
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        loadUsers();
                    }
                };
                xhr.send();
            }
        }
    </script>
</body>
</html>
