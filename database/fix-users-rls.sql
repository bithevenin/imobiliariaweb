-- ============================================
-- FIX RLS POLICIES FOR USERS TABLE
-- Permitir a usuarios anónimos leer users para login
-- ============================================

-- El problema: El login no puede buscar usuarios porque las políticas
-- RLS en la tabla 'users' bloquean SELECT para rol 'anon'

-- ELIMINAR política restrictiva actual
DROP POLICY IF EXISTS "Users can view their own data" ON users;

-- CREAR nueva política que permita a ANON leer usuarios para login
CREATE POLICY "Allow anon to read users for login"
ON users FOR SELECT
TO anon
USING (true);

-- Mantener la política de UPDATE solo para usuarios autenticados
-- (esta probablemente ya existe, pero si no, descomenta):
-- CREATE POLICY "Only authenticated users can update their own data"
-- ON users FOR UPDATE
-- TO authenticated
-- USING (auth.uid() = id);

-- Verificar las políticas
SELECT 
    schemaname, 
    tablename, 
    policyname, 
    roles,
    qual
FROM pg_policies 
WHERE tablename = 'users';
