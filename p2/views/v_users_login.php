
<div class=centering>
  <div class=form_content style="width: 300px">
    <form id=users_login_form method='POST' action='/users/p_login'>

        Email*<br>
        <input type='text' name='email'>
        
        <br><br>
        
        Password*<br>
        <input type='password' name='password'>

        <br><br>
        
        <button type='submit'>Submit</button>
    </form>
  </div>
</div>

<script>
  $(document).ready(function() {
    $("#users_login_form").validate({
      rules: {
        email: "required email",
        password: "required"
      }
    });
  });
</script>
