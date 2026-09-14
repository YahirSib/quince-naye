<?php
// rsvp_handler.php - Endpoint para AJAX
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$token = isset($input['token']) ? trim($input['token']) : '';
$nombre = isset($input['nombre']) ? htmlspecialchars(trim($input['nombre']), ENT_QUOTES, 'UTF-8') : '';
$asistira = isset($input['asistira']) && in_array($input['asistira'], ['si', 'no']) ? $input['asistira'] : '';
$acompanantes = isset($input['acompanantes']) ? filter_var($input['acompanantes'], FILTER_SANITIZE_NUMBER_INT) : 0;
$mensaje = isset($input['mensaje']) ? htmlspecialchars(trim($input['mensaje']), ENT_QUOTES, 'UTF-8') : '';

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Token no proporcionado']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, nombre, cantidad FROM invitados WHERE token = ?");
$stmt->execute([$token]);
$invitado = $stmt->fetch();

if (!$invitado) {
    echo json_encode(['success' => false, 'message' => 'Invitación no válida']);
    exit;
}

$stmtCheck = $pdo->prepare("SELECT id FROM asistentes WHERE invitado_id = ?");
$stmtCheck->execute([$invitado['id']]);
if ($stmtCheck->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Ya has confirmado tu asistencia anteriormente']);
    exit;
}

if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
    exit;
}

if (empty($asistira)) {
    echo json_encode(['success' => false, 'message' => 'Selecciona si asistirás']);
    exit;
}

if ($asistira === 'si') {
    $maxPermitidos = $invitado['cantidad'];
    if ($acompanantes < 0) {
        $acompanantes = 0;
    }
    if ($acompanantes > $maxPermitidos - 1) {
        $acompanantes = $maxPermitidos - 1;
    }
} else {
    $acompanantes = 0;
}

$stmt = $pdo->prepare("INSERT INTO asistentes (invitado_id, nombre, asistira, acompanantes, mensaje) VALUES (?, ?, ?, ?, ?)");
if ($stmt->execute([$invitado['id'], $nombre, $asistira, $acompanantes, $mensaje])) {
    echo json_encode([
        'success' => true,
        'message' => '¡Gracias por confirmar tu asistencia!',
        'nombre' => $nombre,
        'asistira' => $asistira,
        'acompanantes' => $acompanantes
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al procesar tu confirmación. Intenta de nuevo.']);
}
