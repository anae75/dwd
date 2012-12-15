<div class=centering>
  <div class=form_content style="width: 300px">

    <form id=users_signup_form method='POST' action='/users/p_signup'>

        First Name*<br>
        <input type='text' name='first_name'>
        <br><br>
        
        Last Name*<br>
        <input type='text' name='last_name'>
        <br><br>

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
  <span class="title">This is where you sign up</span>
  <dl>
  <dt>Welcome!</dt>
  <dd>Enter the required information and click "Submit" to get started.</dd>
  <dt>Already have an account?</dt>
  <dd>Click "Log In" on the top menu bar to continue your adventure.</dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>

<script>
  $(document).ready(function() {
    $("#users_signup_form").validate({
      rules: {
        first_name: "required",
        last_name:  "required",
        email:      "required email",
        password:   { required: true,
                    minlength: 8 }
      }
    });
  });
</script>
