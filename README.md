# Deploy en Vercel - Invitación Quinceañera

## Requisitos previos

1. **Cuenta en Vercel** → https://vercel.com (plan gratuito)
2. **Cuenta en TiDB Cloud** → https://tidbcloud.com (plan Starter gratuito)
3. **Git instalado**
4. **Node.js instalado** (para la CLI de Vercel: `npm i -g vercel`)

---

## Paso 1: Crear base de datos en TiDB Cloud (gratis)

1. Ir a https://tidbcloud.com/signup y crear cuenta (puedes usar Google/GitHub)
2. En el dashboard, click **"Create Cluster"**
3. Seleccionar **"Starter"** (tier gratuito: 5 GiB storage, 50M Request Units/mes)
4. Seleccionar una región (ej: `Oregon (us-west-2)`)
5. Esperar ~30 segundos a que se cree
6. Click en el cluster → ir a **"Connect"**
7. Copiar los datos de conexión:
   - **Host** (algo como `xxx.gateway.tidbcloud.com`)
   - **Port** (normalmente `4000`)
   - **User** (algo como `xxxx.root`)
   - **Password** (la que configuraste)

> **Importante:** TiDB Cloud requiere SSL. Ya viene habilitado por defecto.

---

## Paso 2: Crear las tablas en TiDB Cloud

1. Ir a la pestaña **"Chat2Query"** (consola SQL) en el dashboard de TiDB
2. Copiar y pegar el siguiente SQL:

```sql
-- Tabla de invitados
CREATE TABLE invitados (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) DEFAULT NULL,
  cantidad INT(1) DEFAULT NULL,
  token VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Tabla de asistentes
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

-- Tabla de admin
CREATE TABLE admin_users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (username)
);
```

3. Click **"Execute"** o presionar `Ctrl+Enter`

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

En la misma consola SQL de TiDB, ejecutar:

```sql
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

## Paso 5: Modificar config.php para TiDB Cloud

Reemplazar el contenido de `config.php` con:

```php
<?php
// config.php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'quince_rapunzel';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_SSL_CA       => true,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
```

> Nota: TiDB Cloud **no necesita** `DB_NAME` ya que todo va en una sola base. Puedes dejar el valor como `quince_rapunzel` o cualquier cosa, no afecta.

---

## Paso 6: Subir a GitHub

```bash
# 1. Dentro de la carpeta del proyecto
git init
git add .
git commit -m "Invitación quinceañera"

# 2. Crear repositorio privado en GitHub
git remote add origin https://github.com/TU_USUARIO/party-quince.git
git branch -M main
git push -u origin main
```

---

## Paso 7: Deploy en Vercel

1. Ir a https://vercel.com/dashboard
2. Click **"Add New Project"**
3. Seleccionar **"Import Git Repository"** → tu repositorio
4. **Framework Preset:** `Other`
5. **Root Directory:** `./` (dejar vacío)
6. Click **"Deploy"**

---

## Paso 8: Configurar variables de entorno en Vercel

En Vercel → tu proyecto → **Settings → Environment Variables**:

| Variable | Valor (ejemplo) |
|----------|-----------------|
| `DB_HOST` | `xxx.gateway.tidbcloud.com` |
| `DB_NAME` | `quince_rapunzel` |
| `DB_USER` | `xxxx.root` |
| `DB_PASS` | `tu_contraseña` |
| `DB_SSL` | `true` |

> **IMPORTANTE:** Agregar estas variables para **Production**, **Preview** y **Development**.

Luego ir a **Deployments** → click en los tres puntos del deployment más reciente → **Redeploy**.

---

## Paso 9: Verificar

1. Abrir `https://tu-proyecto.vercel.app`
2. Debería mostrar la invitación
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

### Error 500 / No conecta a la BD
- Verificar que las variables de entorno en Vercel estén bien
- Verificar que `PDO::MYSQL_ATTR_SSL_CA => true` esté en config.php
- Revisar logs: Vercel → Dashboard → Logs

### Error "Access denied"
- Verificar user/password en TiDB Cloud (Settings → Security → Database Users)
- Verificar que el IP no esté bloqueado (TiDB Cloud permite conexiones desde cualquier IP en Starter)

### Imágenes no cargan
- Verificar nombres de archivos (sin tildes ni espacios)
- Renombrar `quinceañera.jpeg` → `quinceanera.jpeg` si hay problemas

### Música no carga
- Verificar tamaño del MP3 (máx recomendado 4MB para Vercel)

### La página carga pero no ejecuta PHP
- Verificar que `vercel.json` y `composer.json` estén en la raíz del repositorio
- Verificar que el repositorio tenga los archivos: `index.php`, `admin.php`, `rsvp_handler.php`

---

## Alternativa: Aiven (MySQL gratis)

Si TiDB no funciona, usar Aiven:

1. Crear cuenta en https://aiven.io (plan Free: 1GB storage, 1GB RAM)
2. Crear servicio MySQL
3. Copiar credenciales de conexión
4. Configurar variables de entorno en Vercel igual que arriba
5. En config.php, reemplazar `PDO::MYSQL_ATTR_SSL_CA => true` con:
```php
PDO::MYSQL_ATTR_SSL_CA => '/path/to/ca-cert.pem',
```
