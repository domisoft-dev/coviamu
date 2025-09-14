<?php
require_once 'utils.php';

$baseUser = "http://localhost/coviamu/public/endpointUsers.php";

// Intentamos registrar el usuario
echo "=== Test Registro Usuario ===\n";
$res = request($baseUser, ['name'=>'ian','email'=>'ian@test.com','contrasena'=>'1234']);

if (isset($res['json']['success']) && $res['json']['success'] === true) {
    // Registro exitoso
    echo "✅ Registro Usuario PASÓ\n";
    echo "   Respuesta: " . json_encode($res['json']) . "\n";
    $GLOBALS['newUserId'] = $res['json']['id'] ?? null; // Si no devuelve ID, queda null
} 
else if (isset($res['json']['error']) && $res['json']['error'] === "Usuario ya existe") {
    // Usuario ya existe
    echo "⚠️ Usuario ya existe, buscando ID...\n";
    $GLOBALS['newUserId'] = null;
} 
else {
    // Cualquier otro error
    echo "❌ Registro Usuario FALLÓ inesperadamente\n";
    $GLOBALS['newUserId'] = null;
}
?>