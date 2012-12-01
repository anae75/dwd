<dl>
  <dd>
  <a href="/test/echo_input/foobar">simple link to echo_input</a>
  </dd>

  <dd>
  <button onclick='$.ajax({
        type: "get",
        url: "/test/echo_input/blah", 
        success: function(resp) {
          alert(resp);
        }
      });'>ajax call to echo_input</a>
  </dd>

  <dd>
  <button onclick="call_echo_input('10');">ajax via function</button>
  </dd>

</dl>

<?= Time::time_ago(Time::now() -60) ?><br>
<?= Time::display(Time::now() -60, null, "America/New_York") ?><br>

<?= View::instance('v_test_fragment', Array("local" => "some value")) ?>

<script>
  function call_echo_input(inputdata) {
    $.ajax({
      type: "get",
      url: "/test/echo_input/" + inputdata, 
      success: function(resp) {
        alert(resp);
      },
      error: function(){ alert("error!"); }
    });
  }
</script>
