<style>
@import url('https://fonts.googleapis.com/css?family=Open+Sans&display=swap');

#login {
  font-family: 'Open Sans', sans-serif;
  background: #f9faff;
  color: #3a3c47;
  line-height: 1.6;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 0;
  padding: 0;
}

#h1-login {
  margin-top: 48px;
}

#login-form {
  background: #fff;
  max-width: 360px;
  width: 100%;
  padding: 58px 44px;
  border: 1px solid ##e1e2f0;
  border-radius: 4px;
  box-shadow: 0 0 5px 0 rgba(42, 45, 48, 0.12);
  transition: all 0.3s ease;
}

.row-login {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

.row-login label {
  font-size: 13px;
  color: #8086a9;
}

.row-login input {
  flex: 1;
  padding: 13px;
  border: 1px solid #d6d8e6;
  border-radius: 4px;
  font-size: 16px;
  transition: all 0.2s ease-out;
}

.row-login input:focus {
  outline: none;
  box-shadow: inset 2px 2px 5px 0 rgba(42, 45, 48, 0.12);
}

.row input::placeholder {
  color: #C8CDDF;
}

#login-submit {
  width: 100%;
  padding: 12px;
  font-size: 18px;
  background: #15C39A;
  color: #fff;
  border: none;
  border-radius: 100px;
  cursor: pointer;
  font-family: 'Open Sans', sans-serif;
  margin-top: 15px;
  transition: background 0.2s ease-out;
}

#login-submit:hover {
  background: #55D3AC;
}

@media(max-width: 458px) {
  
  #login {
    margin: 0 18px;
  }
  
  #login-form {
    background: #f9faff;
    border: none;
    box-shadow: none;
    padding: 20px 0;
  }

}
</style>

<div id = "login">
<h1 id = "h1-login" >Login API Universa</h1>
<form id = "login-form" method = "POST">
  <div class="row-login">
    <label for="email">Email</label>
    <input type="email"  name="email" autocomplete="off" placeholder="email@exemplo.com">
  </div>
  <div class="row-login">
    <label for="password">Password</label>
    <input type="password" name="password">
  </div>
  <button id= "login-submit" type="submit">Login</button>
</form>
</div>

<?php
if(isset($_POST['email']) && isset($_POST['password'])) {
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
          global $wpdb;
          $table_name = $wpdb->prefix . 'auth_tokens';
          update_option('universa_auth_token', $token);
          echo 'Token salvo com sucesso!';
      } else {
          echo 'Token não encontrado!';
      }

    }
    curl_close($ch);
    // Para consultar o token basta usar o seguinte código: $token = get_option('site_auth_token');
}
?>