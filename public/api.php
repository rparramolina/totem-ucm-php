<?php
/**
 * API Router
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Autenticacion.php';

header('Content-Type: application/json');

session_start();

$method = $_SERVER['REQUEST_METHOD'];

// Handle query parameter based routing
if ($method === 'GET') {
    // Route: GET ?hero-slides
    if (isset($_GET['hero-slides'])) {
        $sede = $_GET['sede'] ?? 'talca';
        $db = Database::getInstance();
        $slides = $db->fetchAll("SELECT * FROM hero_slides WHERE sede = ? ORDER BY order_index ASC, id ASC", [$sede]);
        echo json_encode($slides);
        exit;
    }

    // Route: GET ?main-slides
    if (isset($_GET['main-slides'])) {
        $sede = $_GET['sede'] ?? 'talca';
        $db = Database::getInstance();
        $slides = $db->fetchAll("SELECT * FROM main_slides WHERE sede = ? AND is_visible = true ORDER BY order_index ASC, id ASC", [$sede]);
        echo json_encode($slides);
        exit;
    }

    // Route: GET ?settings
    if (isset($_GET['settings'])) {
        $sede = $_GET['sede'] ?? 'talca';
        $db = Database::getInstance();
        $settings = $db->fetchOne("SELECT * FROM global_settings WHERE sede = ? LIMIT 1", [$sede]);
        echo json_encode($settings ?: []);
        exit;
    }

    // Route: GET ?sedes
    if (isset($_GET['sedes'])) {
        $db = Database::getInstance();
        $sedes = $db->fetchAll("SELECT * FROM sedes WHERE is_active = true ORDER BY name ASC");
        echo json_encode($sedes);
        exit;
    }

    // Route: GET ?users (protected)
    if (isset($_GET['users'])) {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        $db = Database::getInstance();
        $users = $db->fetchAll("SELECT id, username, email, role FROM users ORDER BY id ASC");
        echo json_encode($users);
        exit;
    }
}

// Handle POST requests (Create)
if ($method === 'POST') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    // Route: POST ?hero-slides
    if (isset($_GET['hero-slides'])) {
        $sede = $input['sede'] ?? 'talca';
        $db = Database::getInstance();
        $id = $db->insert(
            "INSERT INTO hero_slides (sede, image_url, subtitle, title, order_index, link_url, start_date, end_date) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $sede,
                $input['image_url'] ?? '',
                $input['subtitle'] ?? '',
                $input['title'] ?? '',
                $input['order_index'] ?? 0,
                $input['link_url'] ?? '',
                $input['start_date'] ?? '',
                $input['end_date'] ?? ''
            ]
        );
        $response = array_merge(['id' => $id, 'sede' => $sede], $input);
        echo json_encode($response);
        exit;
    }

    // Route: POST ?main-slides
    if (isset($_GET['main-slides'])) {
        $sede = $input['sede'] ?? 'talca';
        $db = Database::getInstance();
        $id = $db->insert(
            "INSERT INTO main_slides (sede, image_url, alt_text, is_visible, order_index, title, subtitle) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $sede,
                $input['image_url'] ?? '',
                $input['alt_text'] ?? '',
                $input['is_visible'] ?? true,
                $input['order_index'] ?? 0,
                $input['title'] ?? '',
                $input['subtitle'] ?? ''
            ]
        );
        $response = array_merge(['id' => $id, 'sede' => $sede], $input);
        echo json_encode($response);
        exit;
    }

    // Route: POST ?users (SuperAdministrador only)
    if (isset($_GET['users'])) {
        if ($_SESSION['user']['role'] !== 'SuperAdministrador') {
            http_response_code(403);
            echo json_encode(['error' => 'Permiso insuficiente']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['username']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos']);
            exit;
        }
        $hash = password_hash($input['password'], PASSWORD_BCRYPT);
        $db = Database::getInstance();
        try {
            $id = $db->insert(
                "INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, ?)",
                [$input['username'], $hash, $input['email'] ?? '', $input['role'] ?? 'Administrador']
            );
            echo json_encode(['id' => $id, 'username' => $input['username'], 'email' => $input['email'] ?? '', 'role' => $input['role'] ?? 'Administrador']);
        } catch (PDOException $e) {
            if ($e->getCode() == '23505') {
                http_response_code(400);
                echo json_encode(['error' => 'El nombre de usuario ya existe']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error interno']);
            }
        }
        exit;
    }

    // Route: POST ?upload (file upload)
    if (isset($_GET['upload'])) {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }
        
        // Check for upload errors
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'PHP extension stopped the file upload',
            ];
            $errorMsg = $uploadErrors[$_FILES['file']['error']] ?? 'Unknown upload error';
            http_response_code(400);
            echo json_encode(['error' => $errorMsg, 'code' => $_FILES['file']['error']]);
            exit;
        }
        
        // Validate file type
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed)) {
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de archivo no permitido. Use: jpg, jpeg, png, gif, webp']);
            exit;
        }
        
        // Validate file size (max 5MB)
        if ($_FILES['file']['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'Archivo demasiado grande. Máximo 5MB']);
            exit;
        }
        
        // Validate tmp file
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid uploaded file (possible attack)']);
            exit;
        }
        
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create upload directory', 'dir' => $uploadDir]);
                exit;
            }
        }
        
        // Check if upload directory is writable
        if (!is_writable($uploadDir)) {
            http_response_code(500);
            echo json_encode(['error' => 'Upload directory not writable', 'dir' => $uploadDir, 'perms' => substr(sprintf('%o', fileperms($uploadDir)), -4)]);
            exit;
        }
        
        // Generate unique filename
        $filename = uniqid('img_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            echo json_encode(['url' => '/uploads/' . $filename]);
        } else {
            $lastError = error_get_last();
            http_response_code(500);
            echo json_encode([
                'error' => 'Upload failed',
                'details' => $lastError['message'] ?? 'Unknown error',
                'tmp_name' => $_FILES['file']['tmp_name'],
                'destination' => $destination,
                'tmp_exists' => file_exists($_FILES['file']['tmp_name']),
                'destination_writable' => is_writable(dirname($destination))
            ]);
        }
        exit;
    }
}

// Handle PUT requests (Update)
if ($method === 'PUT') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $db = Database::getInstance();

    // Route: PUT ?settings
    if (isset($_GET['settings'])) {
        $sede = $_GET['sede'] ?? 'talca';
        $db->update(
            "UPDATE global_settings SET 
                logo_url = ?, header_title = ?, timezone = ?, 
                footer_title = ?, footer_subtitle = ?, footer_image_url = ?, footer_qr_url = ?, 
                header_subtitle = ?, clock_sync_mode = ?
             WHERE sede = ?",
            [
                $input['logo_url'] ?? '', $input['header_title'] ?? '', $input['timezone'] ?? 'America/Santiago',
                $input['footer_title'] ?? '', $input['footer_subtitle'] ?? '', $input['footer_image_url'] ?? '', $input['footer_qr_url'] ?? '',
                $input['header_subtitle'] ?? '', $input['clock_sync_mode'] ?? 'auto',
                $sede
            ]
        );
        echo json_encode($input);
        exit;
    }

    // Route: PUT ?hero-slides&id=123
    if (isset($_GET['hero-slides']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db->update(
            "UPDATE hero_slides SET image_url = ?, title = ?, subtitle = ?, order_index = ?, link_url = ?, start_date = ?, end_date = ? WHERE id = ?",
            [
                $input['image_url'] ?? '', $input['title'] ?? '', $input['subtitle'] ?? '',
                $input['order_index'] ?? 0, $input['link_url'] ?? '', $input['start_date'] ?? '', $input['end_date'] ?? '',
                $id
            ]
        );
        echo json_encode(array_merge(['id' => $id], $input));
        exit;
    }

    // Route: PUT ?main-slides&id=123
    if (isset($_GET['main-slides']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db->update(
            "UPDATE main_slides SET image_url = ?, alt_text = ?, order_index = ?, title = ?, subtitle = ?, is_visible = ? WHERE id = ?",
            [
                $input['image_url'] ?? '', $input['alt_text'] ?? '', $input['order_index'] ?? 0,
                $input['title'] ?? '', $input['subtitle'] ?? '', $input['is_visible'] ?? true,
                $id
            ]
        );
        echo json_encode(array_merge(['id' => $id], $input));
        exit;
    }

    // Route: PUT ?users&id=123
    if (isset($_GET['users']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $input = json_decode(file_get_contents('php://input'), true);
        $db->update(
            "UPDATE users SET username = ?, email = ? WHERE id = ?",
            [$input['username'] ?? '', $input['email'] ?? '', $id]
        );
        if (isset($input['newPassword']) && !empty($input['newPassword'])) {
            $hash = password_hash($input['newPassword'], PASSWORD_BCRYPT);
            $db->update("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $id]);
        }
        echo json_encode(['success' => true, 'message' => 'Perfil actualizado']);
        exit;
    }
}

// Handle DELETE requests
if ($method === 'DELETE') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $db = Database::getInstance();

    // Route: DELETE ?hero-slides&id=123
    if (isset($_GET['hero-slides']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db->delete("DELETE FROM hero_slides WHERE id = ?", [$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    // Route: DELETE ?main-slides&id=123
    if (isset($_GET['main-slides']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db->delete("DELETE FROM main_slides WHERE id = ?", [$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    // Route: DELETE ?users&id=123
    if (isset($_GET['users']) && isset($_GET['id'])) {
        if ($_SESSION['user']['id'] == $_GET['id']) {
            http_response_code(400);
            echo json_encode(['error' => 'No puedes eliminar tu propio usuario']);
            exit;
        }
        $id = intval($_GET['id']);
        $db->delete("DELETE FROM users WHERE id = ?", [$id]);
        echo json_encode(['success' => true]);
        exit;
    }
}

// Default: 404
http_response_code(404);
echo json_encode(['error' => 'Not found']);
