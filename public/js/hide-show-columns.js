/**
 * Adds an event listener to all hide/show radio inputs.
 * When the radio value is changed, the corresponding table column
 * is hidden or shown.
 *
 * Kudos to https://www.javascripttutorial.net/javascript-dom/javascript-change-event/
 */

let table = document.getElementById('table');
let headers = table.getElementsByTagName('th');
let rows = table.getElementsByTagName('tr');

let hideShowDiv = document.getElementById('tableColumnFilterDiv');
let radios = hideShowDiv.getElementsByTagName('input');

for (let radio of radios) {
    radio.addEventListener('change', function (e) {
       let target = e.target;
       let id = target.id;

       let thId = target.name.substring(4);

       let header = headers.namedItem(thId);
       let rowIndex = header.cellIndex;

        for (let row of rows) {
            row.cells[rowIndex].hidden = !id.includes('show');
        }

        if (id.includes('show')) {
            header.hidden = false;
        } else if (id.includes('hide')) {
            header.hidden = true;
        }
    });
}