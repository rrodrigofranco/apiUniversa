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


function verifyToken($email, $password){
  $email = $_POST['email'];
  $password = $_POST['password'];

  $url = "https://universa-api.universaeducacional.com.br/centec/v1/auth/login";
  
  $data = [
      'email' => $email,
      'password' => $password
  ];

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));

  $response = curl_exec($ch);

  if (curl_errno($ch)) {
      echo 'cURL error: ' . curl_error($ch);
  } else {
    $response_data = json_decode($response, true);
    if (isset($response_data["token"])) {
        $token = $response_data["token"];
        update_option('universa_email', $email);
        update_option('universa_password', $password);
        update_option('universa_auth_token', $token);
    }

  }
    curl_close($ch);

    return true;
}

function do_this_in_a_time() {
  $email = get_option('universa_email');
  $password = get_option('universa_password');
  verifyToken($email, $password);
}

function schedule_token_verification() {
  if (!wp_next_scheduled('envento_verificar_token')) {
      wp_schedule_single_event(time() + 86400, 'envento_verificar_token'); // 86400 seconds = 24 hours
  }
}

add_action('wp', 'schedule_token_verification');
add_action('envento_verificar_token', 'do_this_in_a_time');

?>