<?php

if(isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $token = verifyToken($email, $password);

    if($token){
      echo 'Token salvo com sucesso!';
    }else{
      echo 'Token não encontrado!';
    }
    // Para consultar o token basta usar o seguinte código: $token = get_option('universa_auth_token');
}

function do_this_in_a_time() {
  $email = get_option('universa_email');
  $password = get_option('universa_password');
  verifyToken($email, $password);
}

function schedule_token_verification() {
  if (!wp_next_scheduled('envento_verificar_token')) {
      wp_schedule_single_event(time() + 60, 'envento_verificar_token'); // 3600 seconds = 1 hour
  }
}

add_action('wp', 'schedule_token_verification');
add_action('envento_verificar_token', 'do_this_in_a_time');
  

?>