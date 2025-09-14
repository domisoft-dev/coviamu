<?php
require_once 'utils.php';

$baseAdmin = "http://localhost/coviamu/public/endpointCooperativas.php";
$GLOBALS['newUserId'] = null;

// Login admin
echo "=== Test Login Admin ===\n";
$res = request($baseAdmin, ['nombre'=>'ian','contrasena'=>'222']);
check($res, "Login Admin");

$adminCookies = $res['cookies'];

// Obtener usuarios
echo "=== Test Obtener Usuarios ===\n";
$resUsuarios = request($baseAdmin, [], 'POST', $adminCookies);
if (isset($resUsuarios['json']) && is_array($resUsuarios['json'])) {
    echo "✅ Obtener Usuarios PASÓ\n";
    // Buscar ID del usuario 'ian'
    foreach ($resUsuarios['json'] as $u) {
        if ((isset($u['nombre']) && $u['nombre'] === 'ian') || (isset($u['name']) && $u['name'] === 'ian')) {
            $GLOBALS['newUserId'] = $u['id'];
            echo "✅ Encontrado ID de usuario: {$GLOBALS['newUserId']}\n";
            break;
        }
    }
    if (!$GLOBALS['newUserId']) {
        echo "⚠️ No se pudo encontrar el usuario 'ian' en la lista\n";
        print_r($resUsuarios['json']);
    }
} else {
    echo "⚠️ Respuesta de usuarios inválida:\n";
    print_r($resUsuarios['json']);
}

// Aprobar usuario
if ($GLOBALS['newUserId']) {
    echo "=== Test Aprobar Usuario ID={$GLOBALS['newUserId']} ===\n";
    $res = request($baseAdmin, ['id'=>$GLOBALS['newUserId'],'estado'=>'aprobado'], 'POST', $adminCookies);

    if (isset($res['json']['success']) && $res['json']['success'] === true) {
        echo "✅ Aprobar Usuario ID={$GLOBALS['newUserId']} PASÓ\n";
    } else if (isset($res['json']['error']) && $res['json']['error'] === 'Error al actualizar estado') {
        echo "⚠️ Usuario ID={$GLOBALS['newUserId']} ya estaba aprobado\n";
    } else {
        echo "❌ Aprobar Usuario ID={$GLOBALS['newUserId']} FALLÓ\n";
        echo "   Respuesta: " . json_encode($res['json']) . "\n";
    }
} else {
    echo "⚠️ No se pudo aprobar el usuario porque no se encontró ID\n";
}