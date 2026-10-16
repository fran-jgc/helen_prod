<?php

require_once(
    dirname(__FILE__)
    . "/../../../../commonlib_seven/trunk/php/auto_load.php"
);

$metodo = session::get('METODO_AUTENTICACION');

$logoutRedirect = isset($_SERVER['CLAVEUNICA_LOGOUT_REDIRECT'])
    ? trim($_SERVER['CLAVEUNICA_LOGOUT_REDIRECT'])
    : '';

base::spd_lock_table_user();
session::destroy();

header('Cache-Control: no-store');
header('Pragma: no-cache');

if (
    $metodo === 'CLAVEUNICA'
    && $logoutRedirect !== ''
) {
    $logoutClaveUnica =
        'https://accounts.claveunica.gob.cl'
        . '/api/v1/accounts/app/logout/'
        . '?redirect='
        . rawurlencode($logoutRedirect);

    header('Location: ' . $logoutClaveUnica);
    exit;
}

header('Location: inicio_sesion.php');
exit;