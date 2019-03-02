
function Process() {
    let vals = document.getElementById("submit-form").children;
    console.log(vals[3]);
    document.getElementById("submit-form").submit();
}

function clearURL() {
    let vals = document.getElementById("submit-form");
    let inputs = vals.querySelectorAll("input[type=text]");
    for(var i=0;i< inputs.length;i++){
        if(inputs[i].value.length == 0){
            inputs[i].parentNode.removeChild(inputs[i]);
        }
    }
}

// $("body").keypress(function(e){
//  if (e.keyCode == 13){
    
//  }
// }

// document.getElementById("body").addEventListener("keyup", function(e){
//     if (e.keyCode == 13) {
//         event.preventDefault();
//         document.getElementById("submit-form").click();
       
//     }
// });


