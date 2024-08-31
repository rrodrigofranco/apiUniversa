<?php
$success = "";
if(isset($_POST['email']) && isset($_POST['password'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $token    = verifyToken($email, $password);

    $success = $token ? '<div class="alert-success">Token salvo com sucesso!</div>' : '<div class="alert-danger">Token não encontrado!</div>';
    // Para consultar o token basta usar o seguinte código: $token = get_option('universa_auth_token');
}