/**
 * Will remove all search fields that are not used from the URL to keep URL clean.
 */
function submitForm() {

    // Get all the form text inputs
    const form = document.getElementById("submit-form");
    const inputs = form.querySelectorAll("input");

    // if the input was not used, disable it
    for(let i=0; i< inputs.length; i++){
        if(inputs[i].value === null || inputs[i].value.trim() === ''){
            inputs[i].disabled = true;
        }
    }

    document.getElementById("submit-form").submit();
}
