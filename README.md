# README - Ibron Inmobiliaria Website

## 📋 Descripción del Proyecto

Sitio web profesional para **Ibron Inmobiliaria, S.R.L.** desarrollado con PHP, HTML5, Bootstrap 5 y Supabase como base de datos. El sistema incluye un panel administrativo completo para gestionar propiedades sin necesidad de programadores.

## 🎨 Características Principales

### Frontend (Sitio Público)
- ✅ **Página de Inicio**: Hero section, propiedades destacadas, servicios, estadísticas y formulario de contacto
- ✅ **Página Acerca De**: Historia de la empresa, valores, perfil del director, misión y visión
- ✅ **Catálogo de Propiedades**: Sistema de filtros avanzado (búsqueda, tipo, precio, estado)
- ✅ **Diseño Responsive**: Optimizado para móviles, tablets y desktop
- ✅ **Botones Flotantes**: WhatsApp, Instagram, Facebook con enlaces directos
- ✅ **Colores de la Marca**: Negro (#000000), Dorado (#D4A745), Blanco (#FFFFFF)

### Backend (Panel Admin)
- ✅ **Sistema de Autenticación**: Login seguro con protección CSRF
- ✅ **Dashboard**: Estadísticas en tiempo real y accesos rápidos
- ✅ **Gestión de Propiedades**: CRUD completo (Crear, Leer, Actualizar, Eliminar)
- ✅ **Cambio de Estado**: Marcar propiedades como Disponible/Vendida/Reservada
- ✅ **Gestión de Mensajes**: Ver consultas de clientes potenciales

### Seguridad
- ✅ **CSRF Protection**: Tokens en todos los formularios
- ✅ **XSS Prevention**: Sanitización de inputs
- ✅ **SQL Injection**: Preparado para Supabase con Row Level Security (RLS)
- ✅ **Session Security**: Regeneración de IDs y headers seguros
- ✅ **Upload Security**: Restricción de tipos de archivo y ejecución PHP
- ✅ **Rate Limiting**: Prevención de spam en formularios

## 🗄️ Base de Datos - Supabase

### Configuración Inicial

1. **Crear proyecto en Supabase**:
   - Ve a [https://supabase.com](https://supabase.com)
   - Crea un nuevo proyecto
   - Guarda las credenciales (URL y API Keys)

2. **Ejecutar SQL**:
   ```sql
   -- 1. Ejecutar database/supabase_schema.sql
   -- Crea las tablas: users, properties, contact_messages, user_sessions
   
   -- 2. Ejecutar database/initial_data.sql
   -- Inserta usuario admin y propiedades de ejemplo
   ```

3. **Configurar credenciales**:
   - Edita `config/supabase.php`
   - Reemplaza `SUPABASE_URL`, `SUPABASE_ANON_KEY`, `SUPABASE_SERVICE_KEY`

### Tablas Principales

- **users**: Administradores del sistema
- **properties**: Catálogo de propiedades inmobiliarias
- **contact_messages**: Mensajes de formulario de contacto
- **user_sessions**: Control de sesiones activas

## 🔐 Credenciales de Admin

### Desarrollo (Cambiar en Producción)
- **Usuario**: `admin`
- **Contraseña**: `123`

### Acceso al Panel
```
http://localhost/Inmobiliaria/admin/login.php
```

## 📁 Estructura del Proyecto

```
Inmobiliaria/
├── admin/                      # Panel de administración
│   ├── login.php              # Autenticación
│   ├── dashboard.php          # Panel principal
│   ├── properties-manage.php  # Gestión de propiedades
│   ├── property-form.php      # Formulario CRUD
│   ├── messages.php           # Mensajes de contacto
│   └── logout.php             # Cerrar sesión
│
├── api/                       # Endpoints para AJAX (futuro)
│   ├── properties.php         # API REST propiedades
│   ├── contact.php            # Procesar formulario
│   └── auth.php               # Autenticación
│
├── assets/                    # Recursos estáticos
│   ├── css/
│   │   └── style.css          # Estilos personalizados
│   ├── js/
│   │   └── main.js            # JavaScript principal
│   └── images/                # Imágenes del sitio
│       └── logo.png           # Logo de Ibron
│
├── config/                    # Configuración
│   ├── settings.php           # Configuración global + seguridad
│   └── supabase.php           # Credenciales y helpers Supabase
│
├── database/                  # Scripts SQL
│   ├── supabase_schema.sql    # Schema completo
│   ├── initial_data.sql       # Datos de ejemplo
│   └── README.md              # Guía de configuración
│
├── includes/                  # Componentes reutilizables
│   ├── header.php             # Header global
│   ├── footer.php             # Footer global
│   └── social-buttons.php     # Botones flotantes
│
├── uploads/                   # Archivos subidos
│   ├── properties/            # Imágenes de propiedades
│   └── .htaccess              # Seguridad (no PHP)
│
├── index.php                  # Página de inicio
├── about.php                  # Acerca de
├── properties.php             # Catálogo de propiedades
├── property-detail.php        # Detalle de propiedad (futuro)
└── README.md                  # Este archivo
```

## 🚀 Instalación

### Requisitos
- PHP 7.4 o superior
- Servidor web (Apache/Nginx)
- Cuenta de Supabase (gratuita)
- Navegador moderno

### Pasos de Instalación

1. **Clonar o descargar el proyecto**:
   ```bash
   # Si usas Git
   git clone [URL_DEL_REPO]
   
   # O simplemente copia la carpeta Inmobiliaria a tu servidor
   ```

2. **Configurar base de datos**:
   - Sigue las instrucciones en `database/README.md`
   - Ejecuta los SQL en Supabase
   - Configura credenciales en `config/supabase.php`

3. **Configurar sitio**:
   - Edita `config/settings.php`
   - Actualiza `SITE_URL` con tu URL local o de producción
   - Configura enlaces de redes sociales (Instagram, Facebook)

4. **Permisos de archivos**:
   ```bash
   # Linux/Mac
   chmod 755 uploads/
   chmod 755 uploads/properties/
   ```

5. **Acceder al sitio**:
   ```
   http://localhost/Inmobiliaria/
   ```

## 📱 Redes Sociales

El sitio incluye botones flotantes y enlaces a:

- **WhatsApp**: +1 (829) 352-6103
- **Instagram**: @ibroninmobiliaria (actualizar en `config/settings.php`)
- **Facebook**: /ibroninmobiliaria (actualizar en `config/settings.php`)
- **Email**: ibroninmobiliaria@gmail.com

## 🛡️ Seguridad en Producción

### Checklist de Seguridad

- [ ] Cambiar contraseña de admin de "123" a una segura
- [ ] Activar HTTPS y configurar `session.cookie_secure = 1`
- [ ] Actualizar `SITE_URL` en `config/settings.php`
- [ ] Configurar Content Security Policy (CSP) headers
- [ ] Deshabilitar `display_errors` en producción
- [ ] Configurar logs de errores en ubicación segura
- [ ] Implementar backups automáticos de Supabase
- [ ] Revisar permisos de archivos y directorios
- [ ] Actualizar enlaces de redes sociales reales

## 📊 Estado del Proyecto

### ✅ Completado (Modo de Desarrollo)
- Frontend completo (3 páginas principales)
- Panel de administración básico
- Sistema de autenticación
- Seguridad implementada (CSRF, XSS, sanitización)
- Diseño responsive
- SQL schema para Supabase
- Documentación

### 🔄 Pendiente (Conectar a Supabase)
- Integración real con Supabase API
- CRUD de propiedades funcional
- Formulario de contacto funcional
- Upload de imágenes
- Gestión de mensajes
- Panel de estadísticas en tiempo real

### 🎯 Próximas Características (Futuro)
- Página de detalle individual de propiedad
- Galería de imágenes con lightbox
- Integración con Google Maps
- Sistema de favoritos
- Búsqueda avanzada con autocompletado
- Multi-idioma (Español/Inglés)
- Notificaciones por email
- Analytics dashboard

## 🔧 Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Framework CSS**: Bootstrap 5.3.2
- **Iconos**: Font Awesome 6.5.1
- **Tipografía**: Google Fonts (Montserrat, Open Sans)
- **Animaciones**: AOS (Animate On Scroll)
- **Backend**: PHP 7.4+
- **Base de Datos**: Supabase (PostgreSQL)
- **Seguridad**: CSRF tokens, sanitización, RLS

## 📞 Soporte

Para soporte o consultas sobre este proyecto:

- **Email**: ibroninmobiliaria@gmail.com
- **Teléfono**: (829) 352-6103
- **WhatsApp**: https://wa.me/18293526103

## 📄 Licencia

Este proyecto fue desarrollado para uso exclusivo de **Ibron Inmobiliaria, S.R.L.**

---

**Desarrollado con ❤️ para Ibron Inmobiliaria**  
*Tu Mejor Inversión*
