function onDragStart(event){
  event.dataTransfer.setData("Text", event.target.id);
  
  var insertboxes = document.querySelectorAll(".insertbox");
  setTimeout(function() {
    insertboxes.forEach(element => {
      element.style.display = "block";
    });
  }, 10);
}

function onDragEnd(event){  
  var insertboxes = document.querySelectorAll(".insertbox");  
  setTimeout(function() {
    insertboxes.forEach(element => {
      element.style.display = "none";
    });
  }, 10);
}

function insert(pos, target){
  if (pos == "before"){
    var tempbox = target.cloneNode();
    target.parentNode.insertBefore(tempbox, target);
  } else {
    var tempbox = target.cloneNode();
    target.parentNode.insertBefore(tempbox, target.nextSibling);
  }
}

function onDragOver(event){
  event.preventDefault();
}

function onDrop(event){
  event.preventDefault();
  var target = event.target;
  var parent = target.parentNode;

  if (target.preventiousElementSibling == null){
    insert("before", target);
  } else if (target.preventiousElementSibling.tagName == "LI"){
    insert("before", target);
  }
  if (target.nextElementSibling == null){
    insert("after", target);
  } else if (target.nextElementSibling.tagName == "LI"){
    insert("after", target);
  }
  
  var dragged_id = event.dataTransfer.getData("Text");
  var dragged_el = document.getElementById(dragged_id);
  target.outerHTML = dragged_el.outerHTML;
  dragged_el.remove();
  
  for (var i = 0; i < parent.children.length; i++) {
    element = parent.children[i];
    if (element.preventiousElementSibling != null){
      if (element.preventiousElementSibling.innerHTML == element.innerHTML){
        element.preventiousElementSibling.remove();
      }
    }
  }
}

function ShowModal(){  
  document.getElementById("register").style.display = "block";
}

function CloseModal(){  
  document.getElementById("register").style.display = "none";
}

function Logout(){
  var cookies = document.cookie.split(";");
  for (var i=0; i<cookies.length; i++) {
    var name = cookies[i].trim().split("=")[0];
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/;";
  }
  window.location = window.location.href;
}

function displayStats(){
  var dict = {};
  tables = document.getElementsByClassName("full_table");
  for (var table = 0; table < tables.length; table++){
    for (var row = 1; row < tables[table].rows.length; row++){
      var question = tables[table].rows[row].cells[0].innerText;
      var user = tables[table].rows[row].cells[2].innerText;
      var answer = tables[table].rows[row].cells[3].innerText;
      if (!(question in dict)){
        dict[question] = {};
      }
      if (answer.includes(",")){
        var split_answer = answer.split(",");
        split_answer.forEach(subanswer => {  
          var name = subanswer.trim();          
          if (!(name in dict[question])){
            dict[question][name] = 1;
          } else {
            dict[question][name]++;
          }
        });
      } else if (answer.includes(";")){
        var split_answer = answer.split(";");
        for (var subanswer = 0; subanswer < split_answer.length; subanswer++){
          var name = split_answer[subanswer].trim();
          if (!(name in dict[question])){
            dict[question][name] = split_answer.length-subanswer;
          } else {
            dict[question][name] += split_answer.length-subanswer;
          }
        }
      } else {
        if (!(answer in dict[question])){
          dict[question][answer] = 1;
        } else {
          dict[question][answer]++;
        }
      }
    }
  }
  
  function drawBar(ctx, upperLeftCornerX, upperLeftCornerY, width, height, color){
    ctx.save();
    ctx.fillStyle = color;
    ctx.fillRect(upperLeftCornerX, upperLeftCornerY, width, height);
    ctx.restore();
  }
    
  var graph_h = 15;
  for (var question in dict){
    for (var answer in dict[question]){
      graph_h += 20;
    }
    graph_h += 20;
  }
  
  var graphCanvas = document.getElementById("graphCanvas");
  graphCanvas.width = 600;
  graphCanvas.height = graph_h;      
  var ctx = graphCanvas.getContext("2d");
  var iter = 0;
  for (var question in dict){
    ctx.font = "bold 10pt Courier";
    ctx.fillText(question, 0, 15+(iter*20));
    ctx.font = "10pt Courier";
    iter++;
    for (var answer in dict[question]){
      var value = dict[question][answer];
      ctx.fillText(answer+" ("+value.toString()+")", 0, 15+(iter*20));
      drawBar(ctx, 10, 20+(iter*20), value*20, 5, 'blue');
      iter++;
    }
  }
}