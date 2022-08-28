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