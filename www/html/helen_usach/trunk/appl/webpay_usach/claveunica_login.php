<?php

require_once(
    dirname(__FILE__)
    . '/../../../../commonlib_seven/trunk/php/auto_load.php'
);

require_once(
    dirname(__FILE__)
    . '/class_claveunica_client.php'
);

class w_claveunica_login extends w_base
{
    public function __construct()
    {
        base::__construct();
    }

    public function iniciar()
    {
        $state = bin2hex(random_bytes(32));

        session::set('CLAVEUNICA_STATE', $state);
        session::set('CLAVEUNICA_STATE_CREATED_AT', time());

        $client = new ClaveUnicaClient();

        header('Cache-Control: no-store');
        header('Pragma: no-cache');
        header('Location: ' . $client->getAuthorizationUrl($state));
        exit;
    }
}


$variables = array(
    'CLAVEUNICA_CLIENT_ID',
    'CLAVEUNICA_CLIENT_SECRET',
    'CLAVEUNICA_REDIRECT_URI'
);

foreach ($variables as $nombre) {
    $enServidor = isset($_SERVER[$nombre])
        && is_string($_SERVER[$nombre])
        && trim($_SERVER[$nombre]) !== '';

    $valorEntorno = getenv($nombre);
    $enEntorno = is_string($valorEntorno)
        && trim($valorEntorno) !== '';

    error_log(
        '[Diagnostico ClaveUnica] ' . $nombre
        . ' | SERVER: ' . ($enServidor ? 'presente' : 'ausente')
        . ' | getenv: ' . ($enEntorno ? 'presente' : 'ausente')
    );
}

try {
    $window = new w_claveunica_login();
    $window->iniciar();
} catch (Throwable $exception) {
    error_log(
        'Error al iniciar ClaveUnica: '
        . $exception->getMessage()
    );

    http_response_code(500);
    echo 'No fue posible iniciar la autenticacion con ClaveUnica.';
}