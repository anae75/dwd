
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

<div id=context_help class=help>
  <span class="title">This is where you log in</span>
  <dl>
  <dd>Enter your email and password to log in and see your streams.</dd>
  <dt>Don't have an account?</dt>
  <dd>Click "Sign Up" on the top menu bar to create an account now.</dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
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
