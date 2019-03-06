
function Process() {
    var vals = document.getElementById("submit-form").children;
    console.log(vals[3]);
    document.getElementById("submit-form").submit();
}

function clearURL() {
    var vals = document.getElementById("submit-form");
    var inputs = vals.querySelectorAll("input[type=text]");
    for(var i=0;i< inputs.length;i++){
        if(inputs[i].value.length == 0){
            inputs[i].parentNode.removeChild(inputs[i]);
        }
    }
}

document.onkeypress = keyPress;

function keyPress(e){
  var x = e || window.event;
  var key = (x.keyCode || x.which);
  if(key == 13 || key == 3){
   Process(clearURL());
  }
}

window.onload = pageReload;

function pageReload(){
    
    //location.reload(true);
}


