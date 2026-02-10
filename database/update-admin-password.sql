-- ============================================
-- ACTUALIZAR CONTRASEÑA DEL ADMIN (NUEVO HASH)
-- ============================================

-- Este es un hash recién generado para la contraseña "123"
UPDATE users 
SET password_hash = '$2y$10$iX7AQcL7I1EAMwrFFqS5qeGTOofSB9ljZyR2DCTk6tZxpQICyM1G'
WHERE username = 'admin';

-- Verificar
SELECT username, email, role, 
       substring(password_hash, 1, 20) || '...' as hash_preview
FROM users 
WHERE username = 'admin';
