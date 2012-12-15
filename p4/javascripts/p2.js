
  // show mini profile for user
  function show_user_profile(user_id) {
    $.ajax({
      type: "get",
      url: "/users/mini_profile/"+user_id, 
      success: function(resp) {
        $(resp).dialog({
          modal: true,
          resizable: false,
          width: 400,
          dialogClass: "miniprofile",
          close: function() {
            $("#mymodal").remove();
          }
        });
      }
    });
  }

  // display page-specific help
  function show_help() {
    $("#context_help").dialog({
        modal: true,
        dialogClass: "miniprofile",
        width: 600
    });
  }

  // csrf_token set in template
  $(document).ready(function() {
    $("body").bind("ajaxSend", function(elm, xhr, s){
      xhr.setRequestHeader('X-CSRF-Token', csrf_token);
    });
  });

