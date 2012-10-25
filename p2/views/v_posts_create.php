<div class=centering>
  <div class=form_content style="width: 425px">
    <h2>Post Something!</h2>
    <form id=create_post_form method='POST' action='/posts/p_create'>
            <textarea form=create_post_form rows="4" cols="50" name="text" maxlength=160 > </textarea>
            <br><br>
            <button type='submit'>Submit</button>
    </form>
  </div>
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
