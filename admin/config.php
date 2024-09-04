
<?php
  include API_UNIVERSA_DIRECTORY_PATH . 'admin/auth-token-manager.php';
  $email       = get_option('universa_email');
  $password    = get_option('universa_password');
  $token       = get_option('universa_auth_token');
  $token_timer = get_option('universa_token_timer') != '' ? get_option('universa_token_timer') : null;
  $sync_timer  = get_option('universa_sync_timer')  != '' ? get_option('universa_sync_timer') : null;
  
?>
<div id = "login">
<h1 id = "h1-login" >Configurações</h1>
<form id="login-form" method="POST" >
  <div class="row-login">
    <label for="email">Email</label>
    <input type="email" name="email" autocomplete="off" placeholder="email@exemplo.com" value="<?php echo isset($email) ? esc_attr($email) : ''; ?>">
  </div>
  <div class="row-login">
    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Entre com a senha" value="<?php echo isset($password) ? esc_attr($password) : ''; ?>">
  </div>
  <div class="row-login">
    <label for="token">Token</label>
    <input type="token" name="token" autocomplete="off" value="<?php echo isset($token) ? esc_attr($token) : ''; ?>" disabled>
  </div>
  <div class="row-login">
    <label for="token_timer">Tempo de Atualização do Token (Minutos)</label>
    <input type="token_timer" name="token_timer" autocomplete="off" value="<?php echo isset($token_timer) ? esc_attr($token_timer) : '60'; ?>">
  </div>
  <div class="row-login">
    <label for="sync_timer">Tempo de Sicronização (horas)</label>
    <input type="sync_timer" name="sync_timer" autocomplete="off" value="<?php echo isset($sync_timer) ? esc_attr($sync_timer) : '24'; ?>">
  </div>
  <button id= "login-submit" type="submit">Salvar</button>
  
</form>
<?php echo $success ?>
</div>