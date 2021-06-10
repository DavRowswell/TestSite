/**
 * Will set some values dynamically used for search and submit the form.
 * Will also remove any unused fields from the get request.
 */
function submitForm() {

    // Set two values dynamically used by the program to search and sort
    document.getElementById("type").value = document.getElementById("or").checked ? 'or' : 'and';
    if (document.getElementById("imageCheck")) {
        // need to check if it exists since it is not always used
        document.getElementById("hasImage").value = document.getElementById("imageCheck").checked ? '*' : '';

        // Disable the imageCheck field if its not checked
        document.getElementById("imageCheck").disabled = !document.getElementById("imageCheck").checked;
    }

    // Get all the form text inputs
    const form = document.getElementById("submit-form");
    const inputs = form.querySelectorAll("input[type=text]");

    // if the input was not used, disable it
    for(let i=0; i< inputs.length; i++){
        if(inputs[i].value.length === 0){
            inputs[i].disabled = true;
        }
    }

    document.getElementById("submit-form").submit();
}

/**
 * Submits the form for the all_ package.
 */
function All_SubmitForm() {
    document.getElementById("submit-form").submit();
}
