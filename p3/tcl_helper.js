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

function diagram(dom_id)
{
  this.dom_id = dom_id;
  this.obj = $("#"+dom_id);
  this.leds = new Array();
  this.frames = new Array();

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
    this.frames = new Array();
    this.frames.push(new frame(this));
    this.current_frame = 0;
    this.frames[this.current_frame].display();
  };

  this.append_frame =
  function()
  {
    this.frames.push(new frame(this));
    this.current_frame = this.frames.length - 1;
    this.frames[this.current_frame].display();
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
      if(left+50 > width) {
        left = 0;
        top += 50;
      } else {
        left += 50;
      }
    }
    this.clear_frames();
    this.draw();
  }

  this.frame =
  function()
  {
    return this.frames[this.current_frame]; 
  }

} // end diagram


//------------------------------
// Frame
// belongs_to diagram
// has_many colors

  function frame(diagram)
  {
    this.diagram = diagram;
    this.colors = new Array();
    for(i = 0; i < diagram.leds.length; i++) {
      this.colors[i] = "#ffffff";  // initialize to white
    }
    
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
      this.colors[i] = color;
      this.diagram.leds[i].change_color(this.colors[i]); 
    };
  }

//------------------------------
// Palette

  function add_palette_pot(target, i, color)
  {

    var pot = $('<div class="pot"/>').attr({
        id: "pot"+i,
        style: "background-color:"+color+";",
        title: "Drag to Color an LED."
    }).appendTo(target);

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

