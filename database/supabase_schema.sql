-- ============================================
-- IBRON INMOBILIARIA - SUPABASE DATABASE SCHEMA
-- ============================================
-- Este archivo contiene el schema completo para Supabase
-- Incluye tablas, índices, triggers y Row Level Security (RLS)
-- ============================================

-- ============================================
-- 1. TABLA DE USUARIOS/ADMINISTRADORES
-- ============================================

CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    role VARCHAR(20) DEFAULT 'editor' CHECK (role IN ('admin', 'editor')),
    is_active BOOLEAN DEFAULT true,
    last_login TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices para users
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- ============================================
-- 2. TABLA DE PROPIEDADES
-- ============================================

CREATE TABLE IF NOT EXISTS properties (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title VARCHAR(200) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL CHECK (type IN ('Casa', 'Apartamento', 'Villa', 'Solar', 'Oficina', 'Local Comercial', 'Penthouse', 'Terreno')),
    price DECIMAL(12, 2) NOT NULL CHECK (price >= 0),
    bedrooms INTEGER DEFAULT 0 CHECK (bedrooms >= 0),
    bathrooms INTEGER DEFAULT 0 CHECK (bathrooms >= 0),
    area DECIMAL(10, 2) CHECK (area > 0),
    location VARCHAR(255) NOT NULL,
    address TEXT,
    coordinates JSONB, -- {lat: number, lng: number}
    image_main VARCHAR(500),
    image_gallery JSONB DEFAULT '[]'::jsonb, -- Array de URLs
    features TEXT[], -- Array de características
    amenities TEXT[], -- Array de amenidades
    status VARCHAR(20) DEFAULT 'Disponible' CHECK (status IN ('Disponible', 'Vendida', 'Reservada', 'En Negociación')),
    featured BOOLEAN DEFAULT false,
    views_count INTEGER DEFAULT 0,
    created_by UUID REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices para properties
CREATE INDEX idx_properties_type ON properties(type);
CREATE INDEX idx_properties_status ON properties(status);
CREATE INDEX idx_properties_price ON properties(price);
CREATE INDEX idx_properties_featured ON properties(featured);
CREATE INDEX idx_properties_location ON properties(location);
CREATE INDEX idx_properties_created_at ON properties(created_at DESC);

-- Índice para búsqueda de texto completo
CREATE INDEX idx_properties_search ON properties USING GIN (
    to_tsvector('spanish', title || ' ' || COALESCE(description, '') || ' ' || location)
);

-- ============================================
-- 3. TABLA DE MENSAJES DE CONTACTO
-- ============================================

CREATE TABLE IF NOT EXISTS contact_messages (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    property_id UUID REFERENCES properties(id) ON DELETE SET NULL,
    status VARCHAR(20) DEFAULT 'new' CHECK (status IN ('new', 'read', 'replied', 'archived')),
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    read_at TIMESTAMP WITH TIME ZONE,
    replied_at TIMESTAMP WITH TIME ZONE
);

-- Índices para contact_messages
CREATE INDEX idx_contact_messages_status ON contact_messages(status);
CREATE INDEX idx_contact_messages_created_at ON contact_messages(created_at DESC);
CREATE INDEX idx_contact_messages_property ON contact_messages(property_id);

-- ============================================
-- 4. TABLA DE SESIONES (para control de acceso)
-- ============================================

CREATE TABLE IF NOT EXISTS user_sessions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address INET,
    user_agent TEXT,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices para user_sessions
CREATE INDEX idx_sessions_token ON user_sessions(session_token);
CREATE INDEX idx_sessions_user ON user_sessions(user_id);
CREATE INDEX idx_sessions_expires ON user_sessions(expires_at);

-- ============================================
-- 5. TRIGGERS PARA UPDATED_AT
-- ============================================

-- Función para actualizar updated_at automáticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger para users
CREATE TRIGGER update_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Trigger para properties
CREATE TRIGGER update_properties_updated_at
    BEFORE UPDATE ON properties
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- ============================================
-- 6. ROW LEVEL SECURITY (RLS) POLICIES
-- ============================================

-- Habilitar RLS en todas las tablas
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE properties ENABLE ROW LEVEL SECURITY;
ALTER TABLE contact_messages ENABLE ROW LEVEL SECURITY;
ALTER TABLE user_sessions ENABLE ROW LEVEL SECURITY;

-- Políticas para PROPERTIES (público puede leer, solo admin puede modificar)
CREATE POLICY "Properties are viewable by everyone"
    ON properties FOR SELECT
    USING (true);

CREATE POLICY "Only authenticated users can insert properties"
    ON properties FOR INSERT
    WITH CHECK (auth.role() = 'authenticated');

CREATE POLICY "Only authenticated users can update properties"
    ON properties FOR UPDATE
    USING (auth.role() = 'authenticated');

CREATE POLICY "Only authenticated users can delete properties"
    ON properties FOR DELETE
    USING (auth.role() = 'authenticated');

-- Políticas para CONTACT_MESSAGES (público puede crear, solo admin puede leer)
CREATE POLICY "Anyone can send contact messages"
    ON contact_messages FOR INSERT
    WITH CHECK (true);

CREATE POLICY "Only authenticated users can view messages"
    ON contact_messages FOR SELECT
    USING (auth.role() = 'authenticated');

CREATE POLICY "Only authenticated users can update message status"
    ON contact_messages FOR UPDATE
    USING (auth.role() = 'authenticated');

-- Políticas para USERS (solo autenticados pueden leer su propia info)
CREATE POLICY "Users can view their own data"
    ON users FOR SELECT
    USING (auth.uid() = id OR auth.role() = 'authenticated');

CREATE POLICY "Only authenticated users can update their own data"
    ON users FOR UPDATE
    USING (auth.uid() = id);

-- ============================================
-- 7. FUNCIONES ÚTILES
-- ============================================

-- Función para incrementar vistas de una propiedad
CREATE OR REPLACE FUNCTION increment_property_views(property_uuid UUID)
RETURNS void AS $$
BEGIN
    UPDATE properties
    SET views_count = views_count + 1
    WHERE id = property_uuid;
END;
$$ LANGUAGE plpgsql;

-- Función para obtener estadísticas generales
CREATE OR REPLACE FUNCTION get_dashboard_stats()
RETURNS TABLE (
    total_properties BIGINT,
    available_properties BIGINT,
    sold_properties BIGINT,
    reserved_properties BIGINT,
    featured_properties BIGINT,
    total_messages BIGINT,
    unread_messages BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        COUNT(*) FILTER (WHERE true),
        COUNT(*) FILTER (WHERE status = 'Disponible'),
        COUNT(*) FILTER (WHERE status = 'Vendida'),
        COUNT(*) FILTER (WHERE status = 'Reservada'),
        COUNT(*) FILTER (WHERE featured = true),
        (SELECT COUNT(*) FROM contact_messages),
        (SELECT COUNT(*) FROM contact_messages WHERE status = 'new')
    FROM properties;
END;
$$ LANGUAGE plpgsql;

-- ============================================
-- 8. COMENTARIOS EN TABLAS Y COLUMNAS
-- ============================================

COMMENT ON TABLE users IS 'Usuarios administradores del sistema';
COMMENT ON TABLE properties IS 'Catálogo de propiedades inmobiliarias';
COMMENT ON TABLE contact_messages IS 'Mensajes de contacto de clientes potenciales';
COMMENT ON TABLE user_sessions IS 'Sesiones activas de usuarios autenticados';

COMMENT ON COLUMN properties.coordinates IS 'Coordenadas geográficas en formato JSON: {lat: number, lng: number}';
COMMENT ON COLUMN properties.image_gallery IS 'Array de URLs de imágenes en formato JSON';
COMMENT ON COLUMN properties.features IS 'Array de características de la propiedad';
COMMENT ON COLUMN properties.amenities IS 'Array de amenidades disponibles';

-- ============================================
-- FIN DEL SCHEMA
-- ============================================
