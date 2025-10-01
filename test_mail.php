<?php
// Mostrar errores (solo para pruebas en local)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Lista de rutas probables donde puede estar vendor/autoload.php
$autoloadCandidates = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/wordpress-6.8.1/wordpress/vendor/autoload.php',
    __DIR__ . '/wordpress-6.8.1/wordpress/wp-content/plugins/itc_tienda/includes/vendor/autoload.php',
    __DIR__ . '/wordpress/wp-content/plugins/itc_tienda/includes/vendor/autoload.php',
    __DIR__ . '/wp-content/plugins/itc_tienda/includes/vendor/autoload.php',
    __DIR__ . '/wordpress/wp-content/plugins/itc_tienda/includes/vendor/autoload.php'
];

$autoload = null;
foreach ($autoloadCandidates as $p) {
    if (file_exists($p)) {
        $autoload = $p;
        break;
    }
}

if (!$autoload) {
    echo "<h2>No se encontró vendor/autoload.php</h2>";
    echo "<p>Buscados (relativos a " . __DIR__ . "):</p><ul>";
    foreach ($autoloadCandidates as $p) {
        echo "<li>" . htmlspecialchars($p) . (file_exists($p) ? " ✅" : " ❌") . "</li>";
    }
    echo "</ul>";
    echo "<p>Si no existe, ejecuta <code>composer install</code> en la carpeta que contiene el composer.json o ajusta la ruta arriba.</p>";
    exit;
}

// incluir autoload encontrado
require $autoload;

// namespaced classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h3>Usando autoload: " . htmlspecialchars($autoload) . "</h3>";

/*
 * CONFIG - ajustá aquí si querés probar con otros datos
 * (estos son los que pasaste: cambialos si querés)
 */
$smtpHost = 'dtc034.ferozo.com';
$smtpUser = 'ecommerce@itc-web.com.ar';
$smtpPass = '79BOF@79lN';
$smtpPort = 465;
$smtpSecure = PHPMailer::ENCRYPTION_SMTPS; // para puerto 465
$fromEmail = 'ecommerce@itc-web.com.ar';  // debe ser del mismo dominio idealmente
$fromName  = 'ITC Test';
$toEmail   = 'liffdomotic@gmail.com';      // <- cambiá por la cuenta donde querés recibir (ej: tu Gmail)
$toName    = 'Destinatario Test';

$mail = new PHPMailer(true);

try {
    // Ver debug SMTP en pantalla (0 = off, 2 = client+server)
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // verás la conversación SMTP en la página
    $mail->Debugoutput = function($str, $level) {
        echo nl2br(htmlspecialchars($str)) . "<br>";
    };

    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port       = $smtpPort;

    // Opciones para entornos locales / certificados autofirmados
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    // From / To
    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($toEmail, $toName);
    $mail->addReplyTo($smtpUser, 'Reply');

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Prueba SMTP desde test_mail.php';
    $mail->Body    = '<p>Prueba de envío: ' . date('c') . '</p><p>Si este mensaje no llega, pegá la salida debug que aparece arriba y te digo qué significa.</p>';

    // Enviar
    if ($mail->send()) {
        echo "<h3>Mensaje enviado (PHPMailer->send() devolvió true)</h3>";
    } else {
        echo "<h3>PHPMailer->send() devolvió false</h3>";
        echo "Error: " . htmlspecialchars($mail->ErrorInfo);
    }
} catch (Exception $e) {
    echo "<h3>Excepción capturada</h3>";
    echo "Mensaje: " . htmlspecialchars($mail->ErrorInfo);
    echo "<br>Exception: " . htmlspecialchars($e->getMessage());
}
