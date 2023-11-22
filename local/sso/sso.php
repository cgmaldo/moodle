<?php

require (__DIR__.'\..\..\config.php');
require ('constantes.php');
require ('lib.php');
require ('src/JWT.php');
require ('src/Key.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

global $DB, $USER;

try {
    $token  = required_param('token', PARAM_TEXT);
} catch (\Throwable $th) { 
    $error = $th->getMessage();
    paginaCustom('Acceso a la plataforma de formación', 'No es posible acceder a la plataforma:', $error, 'danger');
}

try {
    // JWT::$leeway = 60; // Atraso respecto entidad firmadora y entidad decodificadora
    $decoded = JWT::decode($token, new Key(SEED_ENCRYPT, ALGORITHM));
    if ($decoded->exp<=time()) { // Si he expirado el token
        paginaCustom('Acceso a la plataforma de formación', 'No es posible acceder a la plataforma:', 'Token caducado', 'danger');
    } 
    if (!isset($decoded->usr) || empty($decoded->usr)) {
        paginaCustom('Acceso a la plataforma de formación', 'No es posible acceder a la plataforma:', 'Token mal formado', 'danger');
    } else {
        $user = $DB->get_record('user', array('username'=>$decoded->usr, 'deleted' => 0, 'suspended' => 0));
        if (empty($user)) {
            paginaCustom('Acceso a la plataforma de formación', 'No es posible acceder a la plataforma:', 'Estado del usuario no válido', 'danger');
        } else {
            $USER = get_admin();
            $USER = $user;
            // Entramos a moodle 
	        redirect(new moodle_url('/'));
        }
    }
} catch (\Throwable $th) { // Si se produce un error
    $error = $th->getMessage();
    paginaCustom('Acceso a la plataforma de formación', 'No es posible acceder a la plataforma:', 'Token mal formado', 'danger');
}
