-- Create global_settings table
CREATE TABLE IF NOT EXISTS global_settings (
    id SERIAL PRIMARY KEY,
    sede VARCHAR(50),
    logo_url TEXT,
    header_title VARCHAR(255),
    timezone VARCHAR(100) DEFAULT 'America/Santiago',
    footer_title VARCHAR(255),
    footer_subtitle VARCHAR(255),
    footer_image_url TEXT,
    footer_qr_url TEXT,
    header_subtitle VARCHAR(255),
    clock_sync_mode VARCHAR(50) DEFAULT 'auto'
);

-- Create hero_slides table
CREATE TABLE IF NOT EXISTS hero_slides (
    id SERIAL PRIMARY KEY,
    sede VARCHAR(50) NOT NULL,
    image_url TEXT NOT NULL,
    subtitle VARCHAR(255),
    title VARCHAR(255),
    order_index INTEGER DEFAULT 0,
    link_url TEXT DEFAULT '',
    start_date TEXT DEFAULT '',
    end_date TEXT DEFAULT ''
);

-- Create main_slides table
CREATE TABLE IF NOT EXISTS main_slides (
    id SERIAL PRIMARY KEY,
    sede VARCHAR(50) NOT NULL,
    image_url TEXT NOT NULL,
    alt_text VARCHAR(255),
    is_visible BOOLEAN DEFAULT true,
    order_index INTEGER DEFAULT 0,
    title TEXT DEFAULT '',
    subtitle TEXT DEFAULT ''
);

-- Create sedes table
CREATE TABLE IF NOT EXISTS sedes (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT true
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    email VARCHAR(255),
    role VARCHAR(50) DEFAULT 'Administrador',
    reset_token TEXT,
    reset_token_expiry BIGINT
);

-- Insert default settings for Talca
INSERT INTO global_settings (sede, logo_url, header_title, timezone, footer_title, footer_subtitle, footer_image_url, footer_qr_url)
VALUES (
    'talca',
    'https://images.griddo.ucm.cl/logo-ucm-fdc7556e-fa16-4e78-a558-69dfbde3d0d2',
    'Campus San Miguel',
    'America/Santiago',
    '¿Buscas tu sala?',
    'Escanea para descargar el mapa.',
    'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=2086&auto=format&fit=crop',
    'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://ucm.cl'
) ON CONFLICT DO NOTHING;

-- Insert default settings for Curico
INSERT INTO global_settings (sede, logo_url, header_title, timezone, footer_title, footer_subtitle, footer_image_url, footer_qr_url)
VALUES (
    'curico',
    'https://images.griddo.ucm.cl/logo-ucm-fdc7556e-fa16-4e78-a558-69dfbde3d0d2',
    'Campus Curico',
    'America/Santiago',
    '¿Buscas tu sala?',
    'Escanea para descargar el mapa.',
    'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=2086&auto=format&fit=crop',
    'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://ucm.cl'
) ON CONFLICT DO NOTHING;

-- Insert sample hero slides for Talca
INSERT INTO hero_slides (sede, image_url, subtitle, title, order_index)
VALUES
('talca', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070&auto=format&fit=crop', 'BIENVENIDOS', 'Universidad Católica del Maule', 1),
('talca', 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?q=80&w=2070&auto=format&fit=crop', 'CONOCE', 'Nuestras Carreras', 2)
ON CONFLICT DO NOTHING;

-- Insert sample hero slides for Curico
INSERT INTO hero_slides (sede, image_url, subtitle, title, order_index)
VALUES
('curico', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070&auto=format&fit=crop', 'BIENVENIDOS', 'Campus Curicó', 1),
('curico', 'https://images.unsplash.com/photo-1523580494863-6f3031224c9f4?q=80&w=2070&auto=format&fit=crop', 'EXPLORA', 'Instalaciones', 2)
ON CONFLICT DO NOTHING;

-- Insert sample main slides for Talca
INSERT INTO main_slides (sede, image_url, alt_text, is_visible, order_index)
VALUES
('talca', 'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=2070&auto=format&fit=crop', 'Campus UCM', true, 1),
('talca', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070&auto=format&fit=crop', 'Vida Universitaria', true, 2)
ON CONFLICT DO NOTHING;

-- Insert sample main slides for Curico
INSERT INTO main_slides (sede, image_url, alt_text, is_visible, order_index)
VALUES
('curico', 'https://images.unsplash.com/photo-1523580494863-6f3031224c9f4?q=80&w=2070&auto=format&fit=crop', 'Campus Curicó', true, 1),
('curico', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070&auto=format&fit=crop', 'Aulas', true, 2)
ON CONFLICT DO NOTHING;

-- Insert sedes
INSERT INTO sedes (name, is_active) VALUES
('talca', true),
('curico', true)
ON CONFLICT DO NOTHING;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password_hash, email, role)
VALUES ('admin', '$2y$10$M/p1Dr9Vwaw88Bzu3XmO..HC4RolXWqRJmYVzm4p9TDbXsYwdf84u', 'admin@ucm.cl', 'SuperAdministrador')
ON CONFLICT DO NOTHING;
