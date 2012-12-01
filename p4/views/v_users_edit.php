<div class=centering>
  <div class=form_content style="width: 300px">

    <form id=users_edit_form method='POST' action='/users/p_edit'>
        <?= Helper::csrf_hidden_field() ?>

        First Name<br>
        <input type='text' name='first_name' value="<?=htmlspecialchars($user->first_name)?>">
        <br><br>
        
        Last Name<br>
        <input type='text' name='last_name' value="<?=htmlspecialchars($user->last_name)?>">
        <br><br>

        Email<br>
        <input type='text' name='email' value="<?=htmlspecialchars($user->email)?>">
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

<div id=context_help class=help>
  <span class="title">This is where you can change your own settings information</span>
  <dl>
  <dt> Need to change your name, email or password? </dt>
  <dd> Fill in the new information and click "Submit" to apply your changes. </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
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
