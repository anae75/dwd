<div class=centering>
  <div class=form_content style="width: 300px">

    <form id=users_edit_form method='POST' action='/users/p_edit'>

        First Name<br>
        <input type='text' name='first_name' value='<?=$user->first_name?>'>
        <br><br>
        
        Last Name<br>
        <input type='text' name='last_name' value='<?=$user->last_name?>'>
        <br><br>

        Email<br>
        <input type='text' name='email' value='<?=$user->email?>'>
        <br><br>

        New Password<br>
        <input type='password' id=password name='password'>
        <br><br>

        Confirm New Password<br>
        <input type='password' name='password_confirm'>
        <br><br>

        <button type='submit'>Submit</button>

    </form>
  </div>
</div>

<script>
  $(document).ready(function() {
    $("#users_edit_form").validate({
      rules: {
        first_name: "required",
        last_name:  "required",
        email:      "required email",
        password:   { 
                    minlength: 8 
                    },
        password_confirm: { 
                    required: "#password:filled",
                    equalTo: "#password"
                    }
      },
      messages: {
        password_confirm: {
                    equalTo: "Enter the same password twice.",
                    required: "Enter the same password twice."
                    }
      }
    });
  });
</script>
