
  function follow(user_id) {
    $.ajax({
      type: "get",
      url: "/users/follow/" + user_id, 
      success: function(resp) {
        alert("You are now following.");
      },
      error: function() { 
        alert("An error occurred while trying to follow this user."); 
      }
    });
  }

  function unfollow(user_id) {
    $.ajax({
      type: "get",
      url: "/users/unfollow/" + user_id, 
      success: function(resp) {
        alert("You are no longer following.");
      },
      error: function() { 
        alert("An error occurred while trying to unfollow this user."); 
      }
    });
  }
