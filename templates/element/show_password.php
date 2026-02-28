<!-- templates/element/show_password.php -->
<?php /* requires previous <input type="password" id="password"> */ ?>
<div class="checkbox show-password">
  <input type="checkbox" id="show-password"> <label for="show-password" id="show-password-label">Mostrar senha</label>
</div>

<script>
  addEventListener('load', event => {
    const show = 'Mostrar senha';
    const hide = 'Ocultar senha';
    const passwordInput = document.getElementById('password');
    const togglePasswordCheckbox = document.getElementById('show-password');
    const togglePasswordLabel = document.getElementById('show-password-label');
    const togglePassword = () => {
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        togglePasswordLabel.textContent = hide;
        togglePasswordCheckbox.setAttribute('aria-label', hide);
      } else {
        passwordInput.type = 'password';
        togglePasswordLabel.textContent = show;
        togglePasswordCheckbox.setAttribute('aria-label', show);
      }
    };
    togglePasswordCheckbox.addEventListener('click', togglePassword);
  });
</script>