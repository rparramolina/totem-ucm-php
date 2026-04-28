<?php
/**
 * Admin Login Page
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: /admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/Autenticacion.php';

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = Database::getInstance();
    $user = $db->fetchOne("SELECT * FROM users WHERE username = ?", [$username]);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        header('Location: /admin/');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Totem UCM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <img src="https://images.griddo.ucm.cl/logo-ucm-fdc7556e-fa16-4e78-a558-69dfbde3d0d2" alt="Logo UCM" class="h-16 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-[#003366]">Administración Totem</h1>
                <p class="text-gray-500 text-sm mt-2">Ingresa tus credenciales para continuar</p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#003366] focus:border-[#003366] outline-none transition">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#003366] focus:border-[#003366] outline-none transition">
                </div>

                <button type="submit"
                    class="w-full bg-[#003366] text-white py-3 rounded-lg font-semibold hover:bg-[#004488] transition duration-200">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="/talca" class="text-sm text-gray-500 hover:text-[#003366] transition">← Volver al Totem</a>
            </div>
        </div>
    </div>
</body>
</html>
