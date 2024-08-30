
<?php
  include(API_UNIVERSA_DIRECTORY_PATH . 'admin/auth-token-manager.php');
  $email = get_option('universa_email');
  $password = get_option('universa_password');
  
?>
<div id = "login">
<h1 id = "h1-login" >Login API Universa</h1>
<form id="login-form" method="POST" >
  <div class="row-login">
    <label for="email">Email</label>
    <input type="email" name="email" autocomplete="off" placeholder="email@exemplo.com" value="<?php echo isset($email) ? esc_attr($email) : ''; ?>">
  </div>
  <div class="row-login">
    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Entre com a senha" value="<?php echo isset($password) ? esc_attr($password) : ''; ?>">
  </div>
  <button id= "login-submit" type="submit">Login</button>
  
</form>
<?php echo $success ?>
</div>