<div class=centering>
  <div class=form_content style="width: 425px">
    <h2>Post Something!</h2>
    <form id=create_post_form method='POST' action='/posts/p_create'>
            <?= Helper::csrf_token() ?>
            <textarea form=create_post_form rows="4" cols="50" name="text" maxlength=160 ></textarea>
            <br><br>
            <button type='submit'>Submit</button>
    </form>
  </div>
</div>

<div id=context_help class=help>
  <span class="title">This is where you can create a new post</span>
  <dl>
  <dt> Let your followers know what you're up to </dt>
  <dd> Enter some short text and click "Submit" to tell the world. </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>

<script>
  $(document).ready(function() {
    $("#create_post_form").validate({
      rules: {
        text: {
          required: true,
          maxlength: 150
        }
      }
    });
  });
</script>
