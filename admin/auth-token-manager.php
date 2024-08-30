<?php

if(isset($_POST['email']) && isset($_POST['password'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $token    = verifyToken($email, $password);

    if($token){
      echo 'Token salvo com sucesso!';
    }else{
      echo 'Token não encontrado!';
    }
    // Para consultar o token basta usar o seguinte código: $token = get_option('universa_auth_token');
}