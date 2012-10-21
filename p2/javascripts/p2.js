
  function follow(obj, user_id) {
    $.ajax({
      type: "get",
      url: "/users/follow/" + user_id, 
      success: function(resp) {
        $("#button_follow_"+user_id)[0].disabled = "disabled";
        $("#label_follow_"+user_id).html("You are now following this user.");
        alert("You are now following this user.");
      },
      error: function() { 
        alert("An error occurred while trying to follow this user."); 
      }
    });
  }

  function unfollow(obj, user_id) {
    $.ajax({
      type: "get",
      url: "/users/unfollow/" + user_id, 
      success: function(resp) {
        $("#button_unfollow_"+user_id)[0].disabled = "disabled";
        $("#label_unfollow_"+user_id).html("You are no longer following this user.");
        alert("You are no longer following this user.");
      },
      error: function() { 
        alert("An error occurred while trying to unfollow this user."); 
      }
    });
  }
