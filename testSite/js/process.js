

function Process() {
    var vals = document.getElementById("submit-form").children;
    //console.log(document.getElementById("imageCheck"));
    if (document.getElementById("imageCheck")) {
        if (document.getElementById("imageCheck").checked)
        document.getElementById("hasImage").value = (document.getElementById("imageCheck").checked) ? '*' : '';
    }
    if (document.getElementById("or").checked)
        document.getElementById("type").value = 'or';
    else 
        document.getElementById("type").value = 'and';
    document.getElementById("submit-form").submit();
}

function allProcess() {
    document.getElementById("submit-form").submit();
}

function clearURL() {
    var vals = document.getElementById("submit-form");
    var inputs = vals.querySelectorAll("input[type=text]");
    for(var i=0;i< inputs.length;i++){
        if(inputs[i].value.length == 0){
            inputs[i].disabled = true;
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

window.onpageshow = function(event){
    var vals = document.getElementById("submit-form");
    var inputs = vals.querySelectorAll("input[type=text]");
    for(var i=0;i< inputs.length;i++){
        if(inputs[i].value.length == 0){
            inputs[i].disabled = false;
        }
    }
}


