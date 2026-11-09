<?php
use Andaluh\Rules\BaseRule;

// Cabecera ligera y directa
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$texto = isset($data['texto']) ? trim($data['texto']) : '';
$sibilantRealization = $data['sibilant_realization'] ?? 'distincion';

if ($texto === '') {
    echo json_encode(['ok' => true, 'resultado' => '']);
    exit;
}

// Carga directa del autoloader o clase sin comprobaciones pesadas
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/lib/Andaluh/AndaluPao.php';
}

try {
    $pao = class_exists('Andaluh\\AndaluPao') ? new \Andaluh\AndaluPao() : new AndaluPao();

    switch ($sibilantRealization) {
        case 'seismo':
            $resultado = $pao->transliterate($texto, BaseRule::VAF_S, BaseRule::VAF_S);
            break;
        case 'ceismo':
            $resultado = $pao->transliterate($texto, BaseRule::VAF_Z, BaseRule::VAF_Z);
            break;
        case 'cedilla':
            $resultado = $pao->transliterate($texto, BaseRule::VAF_Ç, BaseRule::VAF_Ç);
            break;
        case 'zeta_barra':
            $resultado = $pao->transliterate($texto, BaseRule::VAF_Ƶ, BaseRule::VAF_Ƶ);
            break;
        default:
            $resultado = $pao->transliterate($texto);
            break;
    }

    echo json_encode([
        'ok' => true,
        'resultado' => $resultado
    ]);
} catch (\Throwable $t) {
    echo json_encode([
        'ok' => false,
        'error' => $t->getMessage()
    ]);
}

/*
use Andaluh\Rules\BaseRule;

// Configuración para evitar caídas de FastCGI / 502
ini_set('memory_limit', '256M');
set_time_limit(30);

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$texto = isset($data['texto']) ? trim($data['texto']) : '';
$sibilantRealization = $data['sibilant_realization'] ?? '';

if (!is_string($sibilantRealization)) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'sibilant_realization debe ser un string.'
    ]);
    exit;
}

if ($texto === '') {
    echo json_encode(['ok' => true, 'resultado' => '']);
    exit;
}

// Cargar mediante autoloader de Composer si existe en el zip subido
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Autoloader nativo para cargar todas las clases de Andaluh dinámicamente
    spl_autoload_register(function ($clase) {
        $prefijo = 'Andaluh\\';
        $baseDir = __DIR__ . '/lib/Andaluh/';

        if (strncmp($prefijo, $clase, strlen($prefijo)) !== 0) {
            return;
        }

        $claseRelativa = substr($clase, strlen($prefijo));
        $ruta = $baseDir . $claseRelativa . '.php';
        if (file_exists($ruta)) {
            require_once $ruta;
        }
    });

    require_once __DIR__ . '/lib/Andaluh/AndaluPao.php';
}

try {
    $pao = class_exists('Andaluh\\AndaluPao') ? new \Andaluh\AndaluPao() : new AndaluPao();

    if ($sibilantRealization == "seismo") {
        $resultado = $pao->transliterate($texto,BaseRule::VAF_S,BaseRule::VAF_S);
    }
    else if ($sibilantRealization == "ceismo") {
        $resultado = $pao->transliterate($texto,BaseRule::VAF_Z,BaseRule::VAF_Z);
    }
    else if ($sibilantRealization == "cedilla") {
        $resultado = $pao->transliterate($texto,BaseRule::VAF_Ç,BaseRule::VAF_Ç);
    }
    else if ($sibilantRealization == "zeta_barra") {
        $resultado = $pao->transliterate($texto,BaseRule::VAF_Ƶ,BaseRule::VAF_Ƶ);
    }
    else { // 'distincion'
        $resultado = $pao->transliterate($texto);
    }

    echo json_encode([
        'ok' => true,
        'resultado' => $resultado
    ]);
} catch (\Throwable $t) {
    echo json_encode([
        'ok' => false,
        'error' => $t->getMessage() . ' (' . basename($t->getFile()) . ':' . $t->getLine() . ')'
    ]);
}
*/
?>