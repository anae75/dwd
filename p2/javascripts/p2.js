
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
    if(!confirm("Are you sure you want to unfollow this user?")) {
      return;
    }
    $.ajax({
      type: "get",
      url: "/users/unfollow/" + user_id, 
      success: function(resp) {
        $("#button_unfollow_"+user_id)[0].disabled = "disabled";
        $("#label_unfollow_"+user_id).html("You are no longer following this user.");
        $(".user_"+user_id).remove();
      },
      error: function() { 
        alert("An error occurred while trying to unfollow this user."); 
      }
    });
  }

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


