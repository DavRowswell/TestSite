/**
 * Will set some values dynamically used for search and submit the form.
 * Will also remove any unused fields from the get request.
 */
function submitForm() {

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
 * Will submit the form after removing all the inputs.
 */
function submitEmptyForm() {
    const form = document.getElementById("submit-form");
    const inputs = form.querySelectorAll("input");

    // if the input was not used, disable it
    for(let i=0; i< inputs.length; i++){
        inputs[i].disabled = true;
    }

    document.getElementById('Database').disabled = false;
    document.getElementById("submit-form").submit();
}

/**
 * Submits the form for the all_ package.
 */
function All_SubmitForm() {
    document.getElementById("submit-form").submit();
}
