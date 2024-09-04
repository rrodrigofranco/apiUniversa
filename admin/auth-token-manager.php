<?php
$success = "";
$token   = null;

if(isset($_POST['email']) && isset($_POST['password'])) {
    $client      = ApiUniversa();
    $email       = $_POST['email'];
    $password    = $_POST['password'];
    $token_timer = $_POST['token_timer'];
    $sync_timer  = $_POST['sync_timer'];

    $data = [
        'email' => $email,
        'password' => $password
    ];

    $data_token = $client->createData('/v1/auth/login', $data);

    if (isset($data_token["token"])) {
        $token = $data_token["token"];
        update_option('universa_email',       $email);
        update_option('universa_password',    $password);
        update_option('universa_auth_token',  $token);
        update_option('universa_token_timer', $token_timer);
        update_option('universa_sync_timer',  $sync_timer);
        
    }

    $success = $token ? '<div class="alert-success">Configuração feita com sucesso!</div>' : '<div class="alert-danger">Problemas na configuração!</div>';
    // Para consultar o token basta usar o seguinte código: $token = get_option('universa_auth_token');
}