-- ============================================
-- IBRON INMOBILIARIA - DATOS INICIALES
-- ============================================
-- Este archivo contiene datos de ejemplo para pruebas
-- Incluye usuario admin y propiedades de muestra
-- ============================================

-- ============================================
-- 1. USUARIO ADMINISTRADOR
-- ============================================
-- Usuario: admin
-- Contraseña: 123
-- IMPORTANTE: El hash debe generarse con bcrypt o password_hash en PHP
-- Para Supabase, necesitarás hashear la contraseña antes de insertar
-- Hash bcrypt de "123": $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (username, password_hash, email, full_name, role, is_active)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'ibroninmobiliaria@gmail.com',
    'Norvi Rosario',
    'admin',
    true
)
ON CONFLICT (username) DO NOTHING;

-- ============================================
-- 2. PROPIEDADES DE EJEMPLO
-- ============================================

-- Obtener el ID del usuario admin para las referencias
DO $$
DECLARE
    admin_id UUID;
BEGIN
    SELECT id INTO admin_id FROM users WHERE username = 'admin' LIMIT 1;

    -- Propiedad 1: Casa de Lujo en Punta Cana
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Casa de Lujo en Punta Cana',
        'Espectacular casa de lujo ubicada en una de las zonas más exclusivas de Punta Cana. Con vista al mar, acabados de primera calidad y amplios espacios para disfrutar del clima tropical. Perfecta para familias que buscan el máximo confort y privacidad.',
        'Casa',
        18500000.00,
        5,
        4,
        450.00,
        'Punta Cana',
        'Cap Cana, Punta Cana, La Altagracia',
        'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800',
        ARRAY['Terraza amplia', 'Jardín privado', 'Garaje para 3 vehículos', 'Sistema de seguridad', 'Cocina equipada'],
        ARRAY['Piscina privada', 'Área BBQ', 'Walk-in closet', 'Aire acondicionado central', 'Vista al mar'],
        'Disponible',
        true,
        admin_id
    );

    -- Propiedad 2: Apartamento Moderno en Santo Domingo
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Apartamento Moderno en Naco',
        'Hermoso apartamento ubicado en el corazón de Naco, una de las zonas más codiciadas de Santo Domingo. Cuenta con acabados modernos, amplios espacios y excelente iluminación natural. Ideal para profesionales o parejas jóvenes.',
        'Apartamento',
        7800000.00,
        3,
        2,
        165.00,
        'Santo Domingo',
        'Naco, Distrito Nacional',
        'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800',
        ARRAY['Balcón amplio', 'Cocina moderna', 'Área de lavandería', '1 parqueo techado', 'Pisos de porcelanato'],
        ARRAY['Gimnasio', 'Elevador', 'Portón eléctrico', 'Área social', 'Seguridad 24/7'],
        'Disponible',
        true,
        admin_id
    );

    -- Propiedad 3: Villa Exclusiva en Casa de Campo
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Villa Exclusiva en Casa de Campo',
        'Impresionante villa de lujo en el prestigioso resort Casa de Campo. Diseño arquitectónico excepcional con materiales de primera calidad importados. Acceso a campo de golf, marina y playas privadas.',
        'Villa',
        45000000.00,
        6,
        5,
        650.00,
        'La Romana',
        'Casa de Campo, La Romana',
        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800',
        ARRAY['Vista panorámica', 'Habitación principal con terraza', 'Cava de vinos', 'Cocina gourmet', 'Sistema domótica'],
        ARRAY['Piscina infinity', 'Jacuzzi', 'Gazebo', 'Cancha de tenis', 'Casa de huéspedes', 'Gym privado'],
        'Disponible',
        true,
        admin_id
    );

    -- Propiedad 4: Solar en Bávaro
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Solar Residencial en Bávaro',
        'Excelente terreno ubicado en zona de alto desarrollo en Bávaro. Perfecto para construcción de villa o proyecto residencial. Todos los servicios disponibles en la zona.',
        'Solar',
        3200000.00,
        0,
        0,
        800.00,
        'Bávaro',
        'Bávaro, Punta Cana',
        'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800',
        ARRAY['Esquina', 'Acceso pavimentado', 'Servicios básicos disponibles', 'Zona residencial'],
        ARRAY['Electricidad', 'Agua potable', 'Internet', 'Seguridad de la zona'],
        'Disponible',
        false,
        admin_id
    );

    -- Propiedad 5: Penthouse en La Esperilla
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Penthouse de Lujo en La Esperilla',
        'Espectacular penthouse de dos niveles con vistas panorámicas de la ciudad. Acabados de lujo, amplias terrazas y amenidades de primera clase. Lo máximo en vida urbana sofisticada.',
        'Penthouse',
        15600000.00,
        4,
        3,
        320.00,
        'Santo Domingo',
        'La Esperilla, Distrito Nacional',
        'https://images.unsplash.com/photo-1567496898669-ee935f5f647a?w=800',
        ARRAY['Doble altura', 'Terraza privada', 'Jacuzzi en terraza', 'Smart home', 'Cocina italiana'],
        ARRAY['Piscina en rooftop', 'BBQ área', '3 parqueos', 'Cuarto de servicio', 'Lobby de lujo'],
        'Disponible',
        true,
        admin_id
    );

    -- Propiedad 6: Apartamento (VENDIDA) - Ejemplo
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Apartamento en Bella Vista',
        'Acogedor apartamento en excelente ubicación. Recientemente renovado con acabados modernos.',
        'Apartamento',
        5200000.00,
        2,
        2,
        110.00,
        'Santo Domingo',
        'Bella Vista, Distrito Nacional',
        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800',
        ARRAY['Balcón', 'Cocina equipada', 'Closets empotrados', 'Aire acondicionado'],
        ARRAY['Ascensor', 'Portería', '1 parqueo', 'Planta eléctrica'],
        'Vendida',
        false,
        admin_id
    );

    -- Propiedad 7: Local Comercial
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Local Comercial en Autopista San Isidro',
        'Local comercial en ubicación estratégica sobre la Autopista San Isidro. Alto flujo vehicular y peatonal. Ideal para negocio de retail, restaurante o servicios.',
        'Local Comercial',
        8500000.00,
        0,
        2,
        250.00,
        'Santo Domingo Este',
        'Autopista San Isidro, Santo Domingo Este',
        'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800',
        ARRAY['Frente a avenida principal', 'Amplio estacionamiento', 'Oficinas en segundo nivel', 'Baños públicos'],
        ARRAY['Estacionamiento para 15 vehículos', 'Generador eléctrico', 'Sistema de seguridad', 'Cisterna'],
        'Disponible',
        true,
        admin_id
    );

    -- Propiedad 8: Casa en Santiago
    INSERT INTO properties (
        title,
        description,
        type,
        price,
        bedrooms,
        bathrooms,
        area,
        location,
        address,
        image_main,
        features,
        amenities,
        status,
        featured,
        created_by
    ) VALUES (
        'Casa Residencial en Santiago',
        'Hermosa casa familiar en exclusiva urbanización de Santiago. Perfecta distribución de espacios, amplio patio trasero y excelente ubicación cerca de colegios y supermercados.',
        'Casa',
        9200000.00,
        4,
        3,
        280.00,
        'Santiago',
        'Cerros de Gurabo, Santiago',
        'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800',
        ARRAY['Patio amplio', 'Marquesina doble', 'Cocina moderna', 'Habitación de servicio', 'Terraza techada'],
        ARRAY['Área de BBQ', 'Piscina', 'Gazebo', 'Portón eléctrico', 'Cisterna grande'],
        'Disponible',
        false,
        admin_id
    );

END $$;

-- ============================================
-- 3. MENSAJE DE CONTACTO DE EJEMPLO
-- ============================================

INSERT INTO contact_messages (name, email, phone, message, status)
VALUES (
    'María Pérez',
    'maria.perez@example.com',
    '809-555-1234',
    'Hola, estoy interesada en conocer más detalles sobre las propiedades disponibles en Punta Cana. ¿Podrían contactarme?',
    'new'
);

-- ============================================
-- FIN DE DATOS INICIALES
-- ============================================
