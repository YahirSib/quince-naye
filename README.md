# Deploy en Vercel - Invitación Quinceañera

## Requisitos previos

1. **Cuenta en Vercel** → https://vercel.com (plan gratuito)
2. **Cuenta en Aiven** → https://aiven.io (plan Free: MySQL con 1GB storage)
3. **Git instalado**
4. **Node.js instalado** (para la CLI de Vercel: `npm i -g vercel`)

---

## Paso 1: Crear base de datos en Aiven (gratis)

1. Ir a https://aiven.io/signup y crear cuenta (puedes usar Google/GitHub)
2. En el dashboard, click **"Create service"**
3. Seleccionar **"MySQL"**
4. En **Service plan** seleccionar **"Hobby"** (gratis)
5. Seleccionar una región cercana (ej: `Amazon Web Services - US East (Virginia)`)
6. Nombre del servicio: `party-db` (o lo que quieras)
7. Click **"Create service"**
8. Esperar ~2 minutos a que se cree
9. Click en el servicio → copiar los datos de conexión:
   - **Host** (algo como `party-db-yahir-name.aws-0.us-east-1.xxx.aivencloud.com`)
   - **Port** (normalmente `12964`)
   - **User** (algo como `avnadmin`)
   - **Password** (la que aparece o la que configuraste)

> **Ventaja:** Aiven NO requiere SSL obligatorio, así que funciona perfecto con Vercel.

---

## Paso 2: Crear las tablas en Aiven

1. Ir a la pestaña **"Service overview"** → **"MySQL"** → click en el link de conexión
2. O usar phpMyAdmin que viene incluido (click en **"phpMyAdmin"** en el dashboard)
3. Crear la base de datos:

```sql
CREATE DATABASE quince_rapunzel;
USE quince_rapunzel;
```

4. Crear las tablas:

```sql
CREATE TABLE invitados (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) DEFAULT NULL,
  cantidad INT(1) DEFAULT NULL,
  token VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE asistentes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  invitado_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) NOT NULL,
  asistira ENUM('si','no') NOT NULL,
  acompanantes INT(11) DEFAULT 0,
  mensaje TEXT DEFAULT NULL,
  fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (id),
  UNIQUE KEY unique_invitado (invitado_id)
);

CREATE TABLE admin_users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (username)
);
```

---

## Paso 3: Generar hash de contraseña

Ejecutar en https://onlinephp.io o en tu PHP local:

```php
<?php
echo password_hash('tu_contraseña_a_elegir', PASSWORD_DEFAULT);
?>
```

Copiar el hash generado (empieza con `$2y$10$...`).

---

## Paso 4: Insertar datos iniciales

En phpMyAdmin de Aiven o en la consola SQL:

```sql
USE quince_rapunzel;

-- Insertar usuario admin (reemplaza EL_HASH con el que generaste)
INSERT INTO admin_users (username, password_hash) VALUES
('admin', 'EL_HASH_GENERADO');

-- Insertar tus invitados (ejemplo)
INSERT INTO invitados (nombre, cantidad) VALUES
('Juan Pérez', 3),
('María López', 2),
('Carlos García', 5),
('Ana Martínez', 1);

-- Generar tokens únicos
UPDATE invitados SET token = CONCAT(
  SUBSTR(MD5(RAND()), 1, 8),
  SUBSTR(MD5(CONCAT(id, RAND())), 1, 8),
  SUBSTR(MD5(CONCAT(nombre, RAND())), 1, 8)
) WHERE token IS NULL;
```

---

## Paso 5: Configurar variables de entorno en Vercel

En Vercel → tu proyecto → **Settings → Environment Variables**:

| Variable | Valor |
|----------|-------|
| `DB_HOST` | `party-db-yahir-name.aws-0.us-east-1.xxx.aivencloud.com` |
| `DB_PORT` | `12964` |
| `DB_NAME` | `quince_rapunzel` |
| `DB_USER` | `avnadmin` |
| `DB_PASS` | `tu_password_de_aiven` |

> **NO agregar** `DB_SSL` (Aiven no lo requiere).

Luego ir a **Deployments** → click en los tres puntos del deployment más reciente → **Redeploy**.

---

## Paso 6: Verificar

1. Abrir `https://tu-proyecto.vercel.app`
2. Debería mostrar la invitación sin errores
3. Abrir `https://tu-proyecto.vercel.app/admin.php`
4. Login con `admin` / `tu_contraseña`

---

## URLs importantes

| URL | Descripción |
|-----|-------------|
| `https://tu-proyecto.vercel.app/` | Invitación general |
| `https://tu-proyecto.vercel.app/?token=ABC123DEF456` | Invitación personalizada |
| `https://tu-proyecto.vercel.app/admin.php` | Panel de administración |

---

## Troubleshooting

### Error "Cannot connect to MySQL using SSL"
- Aiven NO requiere SSL, verificar que NO tengas `DB_SSL=true` en Vercel
- Si estaba, eliminarla y redeployar

### Error 500 / No conecta a la BD
- Verificar que las variables de entorno en Vercel estén bien
- Verificar que el **Host** incluya el puerto completo
- Revisar logs: Vercel → Dashboard → Logs

### Error "Access denied"
- Verificar user/password en Aiven (Service overview → Connection information)
- El usuario por defecto es `avnadmin`

### Imágenes no cargan
- Verificar nombres de archivos (sin tildes ni espacios)
- Las imágenes deben estar en la raíz del repositorio (no en api/)

### Música no carga
- Verificar tamaño del MP3 (máx recomendado 4MB para Vercel)
