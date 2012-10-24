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
