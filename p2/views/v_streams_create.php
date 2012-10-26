<div class=centering>
  <div class=form_content style="width: 425px">
    <h2>Create a New Stream</h2>
    <form id=create_stream_form method='POST' action='/streams/p_create'>
      <?= Helper::csrf_token() ?>
      <label>Name:*</label>
      <input type"text" name="name" cols=50>
      <br><br>
      <label>description:</label>
      <textarea form=create_stream_form rows="4" cols="50" name="description" maxlength=210 > </textarea>
      <br><br>
      <button type='submit'>Submit</button>
    </form>
  </div>
</div>

<div id=context_help class=help>
  <span class="title">This is where you create a new stream</span>
  <dl>
  <dd> Enter a short name for your new stream.  For example "Family" or "Cooking Club". </dd>
  <dd> The description is optional. </dd>
  <dd> Then click "Submit" to manage your new stream. </dd>
  </dl>
</div>

<script>
  $(document).ready(function() {
    $("#create_stream_form").validate({
      rules: {
        name: {
          required: true,
          maxlength: 50
        },
        description: {
          maxlength: 200
        }
      }
    });
  });
</script>
