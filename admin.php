<?php
// admin.php
session_start();
require 'config.php';

// Manejo de Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Regenerar ID de sesión para prevenir Session Fixation
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error_login = "Credenciales incorrectas.";
    }
}

// Cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Si no está logueado, mostrar formulario de login
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login Administrador</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Acceso VIP</h2>
            <?php if(isset($error_login)) echo "<p class='text-red-500 text-sm mb-4'>$error_login</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Usuario" required class="w-full border p-2 mb-4 rounded">
                <input type="password" name="password" placeholder="Contraseña" required class="w-full border p-2 mb-6 rounded">
                <button type="submit" name="login" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">Ingresar</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// DASHBOARD LOGIC (Usuario autenticado)

// Generar token para un invitado específico
if (isset($_GET['generar_token'])) {
    $id = (int)$_GET['generar_token'];
    $token = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("UPDATE invitados SET token = ? WHERE id = ?");
    $stmt->execute([$token, $id]);
    header("Location: admin.php?msg=token_generado");
    exit;
}

// Regenerar todos los tokens
if (isset($_GET['regenerar_tokens'])) {
    $stmt = $pdo->query("SELECT id FROM invitados");
    $invitados = $stmt->fetchAll();
    $stmtUpdate = $pdo->prepare("UPDATE invitados SET token = ? WHERE id = ?");
    foreach ($invitados as $inv) {
        $token = bin2hex(random_bytes(16));
        $stmtUpdate->execute([$token, $inv['id']]);
    }
    header("Location: admin.php?msg=tokens_regenerados");
    exit;
}

// Obtener asistentes con JOIN a invitados
$stmt = $pdo->query("
    SELECT a.*, i.nombre AS nombre_invitado, i.cantidad AS max_personas, i.token AS token_invitado
    FROM asistentes a
    LEFT JOIN invitados i ON a.invitado_id = i.id
    ORDER BY a.fecha_registro DESC
");
$asistentes = $stmt->fetchAll();

// Obtener lista completa de invitados
$stmt = $pdo->query("SELECT * FROM invitados ORDER BY id");
$listaInvitados = $stmt->fetchAll();

// Preparar datos para gráficos
$total_si = 0;
$total_no = 0;
$total_acompanantes = 0;
$invitadosConfirmaron = 0;
$invitadosSinConfirmar = 0;

foreach ($asistentes as $row) {
    if ($row['asistira'] == 'si') {
        $total_si++;
        $total_acompanantes += $row['acompanantes'];
    } else {
        $total_no++;
    }
}
$total_personas_esperadas = $total_si + $total_acompanantes;

// Contar invitados que ya confirmaron vs los que no
$invitadosConfirmaron = $total_si + $total_no;
$invitadosSinConfirmar = count($listaInvitados) - $invitadosConfirmaron;
if ($invitadosSinConfirmar < 0) $invitadosSinConfirmar = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Recepción 15 Años</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <nav class="bg-purple-800 text-white p-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-bold">Admin Panel - 15 Años</h1>
        <a href="admin.php?logout=1" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm font-semibold">Cerrar Sesión</a>
    </nav>

    <div class="container mx-auto p-6">
        <?php if (isset($_GET['msg'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                <?php
                switch($_GET['msg']) {
                    case 'token_generado': echo 'Token generado correctamente.'; break;
                    case 'tokens_regenerados': echo 'Todos los tokens han sido regenerados.'; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Tarjetas de Métricas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-6 rounded shadow border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Confirmados (Sí)</h3>
                <p class="text-3xl font-bold"><?= $total_si ?></p>
            </div>
            <div class="bg-white p-6 rounded shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Acompañantes</h3>
                <p class="text-3xl font-bold"><?= $total_acompanantes ?></p>
            </div>
            <div class="bg-white p-6 rounded shadow border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Total Personas</h3>
                <p class="text-3xl font-bold text-purple-600"><?= $total_personas_esperadas ?></p>
            </div>
            <div class="bg-white p-6 rounded shadow border-l-4 border-red-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Cancelaron</h3>
                <p class="text-3xl font-bold"><?= $total_no ?></p>
            </div>
            <div class="bg-white p-6 rounded shadow border-l-4 border-yellow-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Sin Responder</h3>
                <p class="text-3xl font-bold text-yellow-600"><?= $invitadosSinConfirmar ?></p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-bold mb-4 text-center">Proporción de Asistencia</h2>
                <div class="w-full max-w-xs mx-auto">
                    <canvas id="chartAsistencia"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-bold mb-4 text-center">Estado de Invitados</h2>
                <div class="w-full max-w-xs mx-auto">
                    <canvas id="chartInvitados"></canvas>
                </div>
            </div>
        </div>

        <!-- Sección de Links de Invitación -->
        <div class="bg-white rounded shadow overflow-hidden mb-8">
            <div class="bg-purple-800 text-white p-4 flex justify-between items-center">
                <h2 class="font-bold text-lg">Links de Invitación Personalizados</h2>
                <div class="flex gap-2">
                    <button onclick="copiarTodosLinks()" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded text-sm font-semibold">
                        Copiar Todos
                    </button>
                    <a href="admin.php?regenerar_tokens=1" onclick="return confirm('¿Estás seguro? Esto invalidará todos los links anteriores.')" class="bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded text-sm font-semibold">
                        Regenerar Todos
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800 uppercase text-sm leading-normal">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4 text-center">Personas</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4">Link de Invitación</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php
                        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
                        $baseUrl = rtrim($baseUrl, '/') . '/index.php';
                        ?>
                        <?php foreach ($listaInvitados as $inv): ?>
                        <?php
                            $link = $baseUrl . '?token=' . $inv['token'];
                            $confirmado = false;
                            foreach ($asistentes as $a) {
                                if ($a['invitado_id'] == $inv['id']) {
                                    $confirmado = true;
                                    break;
                                }
                            }
                        ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100 <?= $confirmado ? 'bg-green-50' : '' ?>">
                            <td class="py-3 px-4"><?= $inv['id'] ?></td>
                            <td class="py-3 px-4 font-bold"><?= htmlspecialchars($inv['nombre']) ?></td>
                            <td class="py-3 px-4 text-center"><?= $inv['cantidad'] ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($confirmado): ?>
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-bold">Confirmado</span>
                                <?php else: ?>
                                    <span class="bg-yellow-200 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <input type="text" value="<?= htmlspecialchars($link) ?>" readonly class="flex-1 bg-gray-100 border border-gray-300 rounded px-2 py-1 text-xs font-mono" id="link-<?= $inv['id'] ?>">
                                    <button onclick="copiarLink(<?= $inv['id'] ?>)" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs" title="Copiar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <a href="admin.php?generar_token=<?= $inv['id'] ?>" onclick="return confirm('¿Regenerar el link de <?= htmlspecialchars(addslashes($inv['nombre'])) ?>?')" class="text-yellow-600 hover:text-yellow-800 text-xs font-bold" title="Regenerar token">
                                    Regenerar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabla de Registros de Asistencia -->
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="bg-purple-800 text-white p-4">
                <h2 class="font-bold text-lg">Registro de Confirmaciones</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800 uppercase text-sm leading-normal">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Invitado</th>
                            <th class="py-3 px-4">Nombre Confirmó</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-center">Acompañantes</th>
                            <th class="py-3 px-4">Mensaje</th>
                            <th class="py-3 px-4">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php foreach ($asistentes as $a): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-4"><?= htmlspecialchars($a['id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($a['nombre_invitado'] ?? 'N/A') ?></td>
                            <td class="py-3 px-4 font-bold"><?= htmlspecialchars($a['nombre']) ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php if($a['asistira'] == 'si'): ?>
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs">Confirmado</span>
                                <?php else: ?>
                                    <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs">No asiste</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($a['acompanantes']) ?></td>
                            <td class="py-3 px-4 italic truncate max-w-xs" title="<?= htmlspecialchars($a['mensaje']) ?>">
                                <?= htmlspecialchars($a['mensaje']) ?>
                            </td>
                            <td class="py-3 px-4"><?= date('d/m/Y H:i', strtotime($a['fecha_registro'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('chartAsistencia').getContext('2d');
        const chartAsistencia = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Confirmados', 'No Asisten'],
                datasets: [{
                    data: [<?= $total_si ?>, <?= $total_no ?>],
                    backgroundColor: ['#10B981', '#EF4444'],
                    hoverOffset: 4
                }]
            },
            options: { responsive: true }
        });

        const ctx2 = document.getElementById('chartInvitados').getContext('2d');
        const chartInvitados = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Confirmaron', 'Pendientes'],
                datasets: [{
                    data: [<?= $invitadosConfirmaron ?>, <?= $invitadosSinConfirmar ?>],
                    backgroundColor: ['#8B5CF6', '#FCD34D'],
                    hoverOffset: 4
                }]
            },
            options: { responsive: true }
        });

        function copiarLink(id) {
            const input = document.getElementById('link-' + id);
            input.select();
            navigator.clipboard.writeText(input.value).then(() => {
                alert('Link copiado al portapapeles');
            }).catch(() => {
                document.execCommand('copy');
                alert('Link copiado');
            });
        }

        function copiarTodosLinks() {
            const inputs = document.querySelectorAll('input[id^="link-"]');
            let todosLinks = [];
            inputs.forEach(input => {
                todosLinks.push(input.value);
            });

            if (todosLinks.length === 0) {
                alert('No hay links para copiar');
                return;
            }

            const texto = todosLinks.join('\n');
            navigator.clipboard.writeText(texto).then(() => {
                alert(todosLinks.length + ' links copiados al portapapeles');
            }).catch(() => {
                const textarea = document.createElement('textarea');
                textarea.value = texto;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert(todosLinks.length + ' links copiados al portapapeles');
            });
        }
    </script>
</body>
</html>