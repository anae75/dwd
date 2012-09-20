  function rgb2hex(rgb) {
      rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
      function hex(x) {
          return ("0" + parseInt(x).toString(16)).slice(-2);
      }
      return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
  }

  function hexToRgb(hex) {
      var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      return result ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16)
      } : null;
  }

//
//
//------------------------------
// Led
/*
    <div id="led1" class=led onclick="">
      <canvas id="led1_canvas" height="50px" width="50px"> </canvas>
      <div class="label">1</div>
    </div>
*/

function led(diagram, id, top, left)
{
  this.diagram = diagram;
  this.dom_id = diagram.dom_id + "_led" + id;

  var dpos = diagram.obj.offset();
  top += dpos.top;
  left += dpos.left;

  this.obj = $('<div class="led"/>').attr({
      id: this.dom_id,
      style: "position: absolute;" +
              "top: "+ top +"px;" +
              "left: " + left +"px;",
      led_id: id
  }).appendTo("#" + diagram.dom_id);
  //alert(this.obj[0].id);

  var options ={ 
    containment: "parent", 
    drag: function() {
      diagram.draw();
    }
  };
  this.obj.draggable(options);

  this.canvas = $('<canvas />').attr({
    height: "50px",
    width: "50px"
  }).appendTo(this.obj)[0];

  // draw the circle
  var context = this.canvas.getContext("2d");
  context.fillStyle = "red";
  context.strokeStyle = "#555555";
  context.beginPath();
  //draw arc: arc(x, y, radius, startAngle, endAngle, anticlockwise)
  context.arc(25, 25, 20, Math.PI*2, 0, true);
  context.closePath();
  context.fill();
  context.stroke();

  this.change_color =
  function(color)
  {
    var context = this.canvas.getContext("2d");
    context.fillStyle = color;
    context.fill();
  };

}


//------------------------------
// Diagram
// has_many leds

function diagram(dom_id, frameset_dom_id)
{
  this.dom_id = dom_id;
  this.obj = $("#"+dom_id);
  this.leds = new Array();
  this.frames = new Array();
  this.frames_target = $("#"+frameset_dom_id);

  this.canvas = this.obj.find("canvas")[0]; 

  this.clear = 
  function clear()
  {
    var context = this.canvas.getContext("2d");
    context.save();
    context.setTransform(1, 0, 0, 1, 0, 0);
    context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    context.restore();
  };

  this.draw =
  function draw() 
  {
    this.clear();
    // redraw LED wiring
    var context = this.canvas.getContext("2d");
    var canvas_pos = this.obj.find("canvas").offset();
    for(i = 1; i < this.leds.length; i++) {

      var pos = this.leds[i-1].obj.offset();
      var x1 = pos.left + this.leds[i-1].obj.width()/2 - canvas_pos.left;
      var y1 = pos.top + this.leds[i-1].obj.height()/2 - canvas_pos.top;

      var pos = this.leds[i].obj.offset();
      var x2 = pos.left + this.leds[i].obj.width()/2 - canvas_pos.left;
      var y2 = pos.top + this.leds[i].obj.height()/2 - canvas_pos.top;

      context.beginPath();
      context.moveTo(x1,y1);
      context.lineTo(x2,y2);
      context.stroke();

    }
  };

  this.clear_frames =
  function ()
  {
    this.frames_target.find("li.frame").remove();
    this.frames = new Array();
    this.append_frame();
  };

  this.append_frame =
  function(append_after = null)
  {
    this.frames.push(new frame(this, this.frames_target, this.frames.length+1, append_after));
    this.current_frame = this.frames.length - 1;
    this.frames[this.current_frame].display();
    this.update_selected(); 
  };

  this.duplicate_frame =
  function()
  {
    var colors = this.frame().colors.slice(0);
    this.append_frame(this.frame().obj);
    this.frame().colors = colors;
    this.frame().display();
  };

  this.initialize = 
  function(n_leds)
  {
    var top = 0, left = 0;
    var width = this.obj.width();

    // remove existing leds
    this.obj.find(".led").remove()
    this.leds = new Array();
    
    // add leds
    for(i = 0; i < n_leds; i++) {
      this.leds.push(new led(this, i, top, left));
      if(left+55 >= width) {
        left = 0;
        top += 55;
      } else {
        left += 55;
      }
    }

    // make the leds droppable
    $("div.led").droppable ({ 
        drop: function(event, ui) {
          //change_color($(this), ui.draggable[0].style.backgroundColor);
          //change_color($(this), ui.draggable.css("background-color"));
          $diagram.frame().change_color($(this).attr("led_id"), ui.draggable.css("background-color"));
        }
      });

    this.clear_frames();
    this.draw();
  }

  this.frame =
  function()
  {
    return this.frames[this.current_frame]; 
  }

  this.move_to_frame =
  function(id)
  {
    for(i = 0; i< this.frames.length; i++) {
      if(this.frames[i].id == id) {
        break;
      }
    }
    this.current_frame = i;
    this.update_selected();
  };

  this.update_selected =
  function()
  {
    $("#" + this.frames_target.attr("id") + " li.frame div").removeClass("selected");
    this.frames[this.current_frame].obj.find("div").addClass("selected");
    this.frames[this.current_frame].display();
  }

  // return frames in user order
  this.ordered_frames =
  function ()
  {
    return this.frames;
    //$("div.frame").map(function() { return $(this).attr("frame_id"); } );
  }

  this.export =
  function()
  {
    var formatted_frames = new Array();
    var formatted_colors = new Array();

    var frames = this.ordered_frames();
    for(i = 0; i < frames.length; i ++) {
      var colors = frames[i].colors;
      for(j = 0; j < colors.length; j ++) {
        formatted_colors[j] = format_color(colors[j]);
      } 
      formatted_frames[i] = "[ " + formatted_colors.join(",") + " ]";
    } 
    var output = "[ " + formatted_frames.join(" ,") + " ]";
    return output;
  };

} // end diagram

function format_color(color) 
{
  var c = hexToRgb(color);
  $foo = color;
  return "[" + c.r + ", " + c.g + ", " + c.b + "]";
}

//------------------------------
// Frame
// belongs_to diagram
// has_many colors

  function frame(diagram, target, id, append_after)
  {
    this.diagram = diagram;
    this.target = target;
    this.id = id;

    this.initialize =
    function(color) {
      this.colors = new Array();
      for(i = 0; i < this.diagram.leds.length; i++) {
        this.colors[i] = color;  
      }
    };
    this.initialize("#ffffff"); // initialize to white

    this.update_color = 
    function (i, color)
    {
      this.colors[i] = color;
    };

    this.display =
    function() 
    {
      for(i = 0; i < this.diagram.leds.length; i++) {
        this.diagram.leds[i].change_color(this.colors[i]); 
      }
    };
    
    this.change_color =
    function (i, color)
    {
      $foo = color;
      this.colors[i] = rgb2hex(color);
      this.diagram.leds[i].change_color(this.colors[i]); 
    };

    // create a dom element in the target 
    var li = $('<li class="frame"/>');
    var lastframe = target.find("div.frame").last().parent();
    if(append_after) {
      lastframe = append_after;
    }
    if(lastframe.length > 0) {
      lastframe.after(li);
    } else {
      li.prependTo(target.find("ul"));
    }
    var div = $('<div class="frame">' + id + '</frame>').appendTo(li);
    div.attr({ frame_id: id });
    this.obj = li;

    // click
    var f = this;
    //div.click(function() { f.display(); } );
    div.click(function() { f.diagram.move_to_frame(f.id); } );

  } // end frame

//------------------------------
// Palette

  function add_palette_pot(target, i, color)
  {

    var li = $('<li />').appendTo(target);

    var pot = $('<div class="pot"/>').attr({
        id: "pot"+i,
        style: "background-color:"+color+";",
        title: "Drag to Color an LED."
    }).appendTo(li);

    var picker = $('<span class="pot"/>').attr({
        id: "pot"+i+"_picker"
    }).appendTo(pot);

    picker.jPicker(
      {
        window:
        {
          expandable: true
        },
        images: {
          clientPath: '../jPicker/images/'
        },
        color:
        {
          active: new $.jPicker.Color({ hex: color })
        }
      },
      // commit callback
      function(color, context)
        {
          var all = color.val('all');
          $("#" + this.parentNode.id).css(
            {
              backgroundColor: all && '#' + all.hex || 'transparent'
            }); // prevent IE from throwing exception if hex is empty
        }
      );

      // make palette pots draggable
      pot.draggable( {
        revert: false,
        helper: function() { return $("<span class='color_drag'>Drop me</span>")[0]; }
      } );

  }

