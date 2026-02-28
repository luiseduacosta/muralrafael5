<!-- templates/element/input_div.php -->
<?php /* requires previous Form->control($name) */ 

echo '<input class="showCode" name="code_toggle_' . $name . '" type="checkbox"/> <label for="code_toggle_' . $name . '">Mostrar código HTML</label>';
echo '<div class="inputDiv" name="code_toggle_' . $name . '" contenteditable>' . $content . '</div>'; 
?>

<script>
(function() {
  /* form div editable content */
  <?= 'const name = "' . $name . '";' ?>
  const inputDiv = document.querySelector('form div[name=code_toggle_'+name+']');
  const inputControl = document.querySelector('form textarea[name='+name+'], form input[name='+name+']');
  const updateControl = (evt) => { inputControl.value = evt.target.innerHTML };
  const updateDiv = (evt) => { inputDiv.innerHTML = evt.target.value };
  inputDiv.addEventListener('input', updateControl);
  inputControl.addEventListener('input', updateDiv);

  /* show html code */
  const showCode = document.querySelector('form .showCode[name=code_toggle_'+name+']');
  const toggleCode = (evt) => { inputControl.classList.toggle('hidden', !evt.target.checked ) };
  showCode.addEventListener('change', toggleCode);

})();
</script>
