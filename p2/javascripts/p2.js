  // move a user from one stream to another
  function move_to_stream(user, new_stream)
  {
    // ajax call to change the stream
    user_id = user.attr("data-userid");
    new_stream_id = new_stream.attr("data-streamid");
    $.ajax({
      type: "get",
      url: "/streams/move/" + user_id + "/" + new_stream_id, 
      success: function(resp) {
        // move the html element
        list = new_stream.find("ul");
        user.appendTo(list);
      },
      error: function() { 
        alert("An error occurred while trying to move this user to a different stream."); 
      }
    });
  }

  // follow a user
  function follow(obj, user_id) {
    $.ajax({
      type: "get",
      url: "/users/follow/" + user_id, 
      success: function(resp) {
        // update the page
        $("#button_follow_"+user_id)[0].disabled = "disabled";
        $("#label_follow_"+user_id).html("You are now following this user.");
        $(".following_"+user_id).show();
        alert("You are now following this user.");
      },
      error: function() { 
        alert("An error occurred while trying to follow this user."); 
      }
    });
  }

  // unfollow a user
  function unfollow(obj, user_id) {
    if(!confirm("Are you sure you want to unfollow this user?")) {
      return;
    }
    $.ajax({
      type: "get",
      url: "/users/unfollow/" + user_id, 
      success: function(resp) {
        // update the page
        $("#button_unfollow_"+user_id)[0].disabled = "disabled";
        $("#label_unfollow_"+user_id).html("You are no longer following this user.");
        $(".following_"+user_id).hide();
        $(".user_"+user_id).remove();
      },
      error: function() { 
        alert("An error occurred while trying to unfollow this user."); 
      }
    });
  }

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

