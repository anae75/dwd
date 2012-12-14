
function reset_canvas(id)
{
  var canvas = $("#"+id)[0];
  var context = canvas.getContext("2d");
  context.save();
  context.setTransform(1, 0, 0, 1, 0, 0);
  context.clearRect(0, 0, canvas.width, canvas.height);
  context.restore();
}



function clear_prompts()
{
  $("#dialog_form").hide();
  $("#dialog_prompt").hide();
  $("#dialog_text").val("");
  $("#drawing_prompt").hide();
  reset_canvas("drawing_canvas");
}

function close_dialog_prompt()
{
  clear_prompts();
  next_shot();
}

function add_dialog_prompt(shot)
{
  var canvas_pos = $(".canvas_holder").position()
  var prompt = $("#dialog_prompt");
  var char_id = shot["prompt_dialog"];
  var left = canvas_pos.left + shot["images"][char_id]["posx"] + shot["images"][char_id]["image"].width*2/3;
  var top = canvas_pos.top + shot["images"][char_id]["posy"]; 
  $("#dialog_form #shot_id")[0].value = shot["shot_id"];
  $("#dialog_form #character_id")[0].value = char_id;
  prompt.css("display", "block");
  prompt.css("position", "absolute");
  prompt.css("left", left+"px");
  prompt.css("top", top+"px");
}

function add_drawing_prompt(shot)
{
  var canvas_pos = $(".canvas_holder").position();
  var prompt = $("#drawing_prompt");
  var char_id = shot["prompt_drawing"];
  var left = canvas_pos.left + shot["images"][char_id]["posx"] - $("#drawing_prompt").width()/2; 
  var top = canvas_pos.top + shot["images"][char_id]["posy"]; 
  $("#dialog_form #shot_id")[0].value = shot["shot_id"];
  $("#dialog_form #character_id")[0].value = char_id;
  prompt.css("display", "block");
  prompt.css("position", "absolute");
  prompt.css("left", left+"px");
  prompt.css("top", top+"px");
  drawing.enable(); // XXX global
}

function add_dialog_form(shot, char_id)
{
  var canvas_pos = $(".canvas_holder").position();
  var dialog_form = $("#dialog_form");
  var char_id = shot["prompt_drawing"];
  if(!char_id) { 
    char_id = shot["prompt_dialog"]; 
  }
  var left = canvas_pos.left + shot["images"][char_id]["posx"] + shot["images"][char_id]["image"].width - 75; 
  var top = canvas_pos.top + shot["images"][char_id]["posy"] + shot["images"][char_id]["image"].height - 100 ; 
  dialog_form.css("position", "absolute");
  dialog_form.css("left", left+"px");
  dialog_form.css("top", top+"px");
  dialog_form.show();
}

// make-believe semaphore
function simple_sem(n, c)
{
   var nwaiting = n;
   var callback = c;
   this.p = function() {
     n -= 1;
     if(n <= 0) {
       callback();
     }
   }
   this.v = function() {
     n += 1;
   }
}

function show(shot, callback)
{
  // load all images first, then display the shot
  var images = shot["images"]
  if(images) {
    var nimages = Object.keys(images).length;
    var sem = new simple_sem(nimages, function(){show_internal(shot, callback);});
    for(key in images) {
      var image = new Image();
      images[key]["image"] = image;
      image.onload = function() {
        sem.p();
      }
      image.src = images[key]["image_url"];
    }
  } else {
    show_internal(shot, callback);
  }
}

function show_internal(shot, callback) 
{
  reset_canvas("hero_canvas");
  clear_prompts();
  $(".cleanup").remove();

  var canvas = $("#hero_canvas")[0];
  var context = canvas.getContext("2d");

  var images = shot["images"];
  for(key in images) {
    var image = images[key]["image"];
    var id = images[key]["relative_to"];
    if( id ) {
      owner = images[id]; 
      images[key]["posx"] = owner["posx"] - image.width/2;
      images[key]["posy"] = owner["posy"];
    }
    image.myleft = images[key]["posx"];
    image.mytop = images[key]["posy"];
    context.drawImage(image, image.myleft, image.mytop);
  }

  $("#caption").text(shot["caption"]);

  if(shot["text"]) {
    show_text(shot["text"]);
  }

  var dialogs = shot["dialogs"];
  if(dialogs) {
    for(key in dialogs) {
      show_dialog(images[key], dialogs[key]);
    }
  }

  var wait_for_prompt = false;
  if(shot["prompt_dialog"]) {
    wait_for_prompt = true;
    add_dialog_prompt(shot);
  }
  if(shot["prompt_drawing"]) {
    wait_for_prompt = true;
    add_drawing_prompt(shot);
  }
  if(wait_for_prompt) {
    add_dialog_form(shot);
  }
  if(callback && !wait_for_prompt) {
    setTimeout(callback, 2000);
  }
}

var cur_shot;
function next_shot()
{
  if( cur_shot < shots.length ) {
    show(shots[cur_shot], next_shot);  
    cur_shot += 1;
  } else {
    // the final shot has completed
    // display the end menu
    $("#scene_end_menu").dialog({
      modal: true,
      resizable: false,
      dialogClass: "miniprofile",
      width: 400
    });
  }
}

function play_shots(shots) 
{
  cur_shot = 0;
  next_shot();
}

// display dialog text
function show_dialog(image, text)
{
  dialog = $("<div class='dialog bubble cleanup'>"+text+"</div>").appendTo($("body"));
  var canvas_pos = $(".canvas_holder").position();
  var left = canvas_pos.left + image["posx"] + image["image"].width*2/3;
  var top = canvas_pos.top + image["posy"];
  dialog.css("display", "block");
  dialog.css("position", "absolute");
  dialog.css("left", left+"px");
  dialog.css("top", top+"px");
}

// display dialog text
function show_text(text)
{
  dialog = $("<div class='textblock cleanup'><h1>"+text+"</h1></div>").appendTo($("body"));
  var canvas_pos = $(".canvas_holder").position();
  var left = canvas_pos.left;
  var top = canvas_pos.top;
  dialog.css("display", "block");
  dialog.css("position", "absolute");
  dialog.css("left", left+"px");
  dialog.css("top", top+"px");
}

//------------------------------------------------------------
// drawing canvas
//------------------------------------------------------------

function drawing_canvas(canvas_id) 
{
  var painting = false;
  var x, y;
  var canvas = $("#"+canvas_id);
  var context = canvas[0].getContext("2d");
  context.strokeStyle = "black";
  context.lineWidth = 4;
  var drawing_enabled = false;
  var has_content = false;

  this.enable =
  function enable() 
  {
    drawing_enabled = true;
  };

  this.disable =
  function disable()
  {
    drawing_enabled = false;
  };

  this.setup_drawing = 
  function ()
  {
    canvas.mousedown(function(e) {
      if(drawing_enabled) {
        painting = true;
        x = e.pageX - canvas.offsetParent().position().left; 
        y = e.pageY - canvas.offsetParent().position().top ; 
      }
    });
    canvas.mouseup(function(e) {
      if(drawing_enabled) {
        painting = false;
        has_content = true;
      }
    });
    canvas.mousemove(function(e) {
      if(drawing_enabled && painting) {
        var newx = e.pageX - canvas.offsetParent().position().left;
        var newy = e.pageY - canvas.offsetParent().position().top ;
        context.beginPath();
        context.moveTo(x,y);
        context.lineTo(newx, newy);
        context.closePath();
        context.stroke();
        x = newx;
        y = newy;
      }
    });
  };

  this.reset_canvas = 
  function()
  {
    foo = canvas;
    context.save();
    context.setTransform(1, 0, 0, 1, 0, 0);
    context.clearRect(0, 0, canvas.width(), canvas.height());
    context.restore();
    has_content = false; 
  }

  this.is_empty = 
  function()
  {
    return !has_content;
  }

  this.draw_image =
  function(image, left=0, top=0)
  {
    context.drawImage(image, left, top);
  }

  this.setup_drawing();

} // end drawing_canvas
