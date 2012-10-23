<div class=centering>
  <div class=form_content style="width: 300px">

    <form method='POST' action='/users/p_signup'>

        First Name<br>
        <input type='text' name='first_name'> <?= @$errors["first_name"] ?>
        <br><br>
        
        Last Name<br>
        <input type='text' name='last_name'> <?= @$errors["last_name"] ?>
        <br><br>

        Email<br>
        <input type='text' name='email'> <?= @$errors["email"] ?>
        <br><br>
        
        Password<br>
        <input type='password' name='password'> <?= @$errors["password"] ?>
        <br><br>
        
        <button type='submit'>Submit</button>

    </form>
  </div>
</div>

