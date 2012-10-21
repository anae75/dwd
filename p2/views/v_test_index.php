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
