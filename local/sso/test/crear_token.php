<?php

require (__DIR__.'\..\constantes.php');
require (__DIR__.'\..\..\..\config.php');
require ('../src/JWT.php');
require ('../src/Key.php');

use Firebase\JWT\JWT;
// use Firebase\JWT\Key;

if (isset($_POST['crear'])) {
    $payload = array();
    $payload['usr']=$_POST['usr'];
    $payload['exp'] = time()+MAX_TIME_TOKEN;
    $jwt = JWT::encode($payload, SEED_ENCRYPT, ALGORITHM);
    echo 'JWT:<br>';
    echo $jwt.'<br>';
    echo "<a href='http://localhost/moodle/local/sso/sso.php?token={$jwt}' target='_blank'>Entrar usando el token generado </a>";
} else {
    echo <<<EOT
        <form action="crear_token.php" method="post">
            <label for="nombre"> Nombre Usuario </label>
            <input type="text" id="usr" name="usr" require max="25" />
            <input type="submit" name="crear" value="creartoken" />
        </form>
    EOT;
}