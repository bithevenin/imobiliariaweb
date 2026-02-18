-- ============================================
-- GUÍA RÁPIDA DE USO - SUPABASE
-- ============================================

# Configuración de Supabase para Ibron Inmobiliaria

## Paso 1: Crear el proyecto en Supabase
1. Ve a https://supabase.com
2. Crea una cuenta o inicia sesión
3. Crea un nuevo proyecto
4. Guarda las credenciales:
   - Project URL
   - API Key (anon/public)
   - API Key (service_role) - solo para backend

## Paso 2: Ejecutar el Schema SQL
1. En Supabase Dashboard, ve a "SQL Editor"
2. Copia y pega el contenido del archivo `supabase_schema.sql`
3. Haz clic en "Run" para crear las tablas

## Paso 3: Insertar Datos Iniciales
1. En "SQL Editor", copia y pega el contenido de `initial_data.sql`
2. Haz clic en "Run" para insertar el usuario admin y propiedades de ejemplo

## Paso 4: Verificar la instalación
1. Ve a "Table Editor" en Supabase
2. Verifica que las tablas se crearon:
   - users
   - properties
   - contact_messages
   - user_sessions

## Paso 5: Configurar en el proyecto PHP
1. Abre `config/supabase.php` (lo vamos a crear)
2. Agrega tus credenciales:

```php
define('SUPABASE_URL', 'https://tu-proyecto.supabase.co');
define('SUPABASE_ANON_KEY', 'tu-anon-key-aqui');
define('SUPABASE_SERVICE_KEY', 'tu-service-role-key-aqui');
```

## Credenciales de Admin
- Usuario: `admin`
- Contraseña: `123`

## Características de Seguridad Implementadas

### Row Level Security (RLS)
- ✅ Propiedades: Público puede ver, solo autenticados pueden modificar
- ✅ Mensajes: Público puede crear, solo admin puede leer
- ✅ Usuarios: Solo pueden ver su propia información

### Índices para Performance
- ✅ Índices en campos de búsqueda frecuente
- ✅ Full-text search en español para propiedades
- ✅ Índices compuestos para filtros complejos

### Triggers Automáticos
- ✅ Actualización automática de `updated_at`
- ✅ Timestamp de creación automático

### Funciones Helper
- `increment_property_views()` - Incrementar vistas de propiedad
- `get_dashboard_stats()` - Obtener estadísticas del dashboard

## Próximos Pasos
1. Copiar las credenciales de Supabase
2. Configurarlas en `config/supabase.php`
3. Probar la conexión desde PHP
4. Implementar las funciones CRUD

## Notas Importantes
- La contraseña "123" es temporal para desarrollo
- Cambiar en producción a una contraseña segura
- El hash bcrypt en initial_data.sql funciona con PHP password_verify()
- Asegúrate de habilitar CORS en Supabase si haces llamadas desde el frontend
