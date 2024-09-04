<?php
$success = "";
$token   = null;

if(isset($_POST['email']) && isset($_POST['password'])) {
    $client = ApiUniversa();
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $data = [
        'email' => $email,
        'password' => $password
    ];

    $data_token = $client->createData('/v1/auth/login', $data);

    if (isset($data_token["token"])) {
        $token = $data_token["token"];
        update_option('universa_email', $email);
        update_option('universa_password', $password);
        update_option('universa_auth_token', $token);
    }

    $success = $token ? '<div class="alert-success">Token salvo com sucesso!</div>' : '<div class="alert-danger">Token não encontrado!</div>';
    // Para consultar o token basta usar o seguinte código: $token = get_option('universa_auth_token');
}