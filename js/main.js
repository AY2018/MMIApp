
// Array of month names in French
const frenchMonths = [
    "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
];

// Get the current date
const currentDate = new Date();

// Get the day and month
const day = currentDate.getDate();
const monthIndex = currentDate.getMonth();

// Format the date as "day month"
const formattedDate = day + ' ' + frenchMonths[monthIndex];

// Display the formatted date in the <h1> element
document.getElementById('currentDate').textContent = formattedDate;



/*************** Set all checkboxes in done to checked ***************/

// Get the container with class 'done'
// const container = document.querySelector('.done');

// // Get all the checkboxes within the container
// const checkboxes = container.querySelectorAll('input[type="checkbox"]');

// // Set all checkboxes within the container to be checked
// checkboxes.forEach((checkbox) => {
//     checkbox.checked = true;
// });




/*************** Changes when toggled button is clicked ***************/

// Get the toggle button and the elements with class 'done'
const toggleButton = document.getElementById('toggle-btn');
const doneElements = document.querySelectorAll('.done');
const notDoneElements = document.querySelectorAll('.notDone');

// Add a click event listener to the toggle button
toggleButton.addEventListener('click', () => {
    // Loop through the elements with class 'done'
    doneElements.forEach(element => {
        // Toggle the 'flex' and 'none' values for the 'display' property
        if (element.style.display === 'none' || element.style.display === '') {
            element.style.display = 'flex';
            element.style.flexDirection = 'row';
            notDoneElements.forEach(element => {
                element.style.display = 'none';
            });
        } else {
            element.style.display = 'none';
            notDoneElements.forEach(element => {
                element.style.display = 'flex';
            });
        }
    });
});


const addForm = document.getElementById("addForm");


function closeAdd() {
    addForm.style.display = "none";
}

function openAdd() {
    addForm.style.display = "flex";
}


const infoArticle = document.getElementById("infoDevoir");


function closeInfo() {
    infoArticle.style.display = "none";
}

function openInfo() {
    infoArticle.style.display = "flex";
}




const modifArticle = document.getElementById("modifForm");

function openModif() {
    infoArticle.style.display = "none";
    modifArticle.style.display = "flex";
}

function closeModif() {
    modifArticle.style.display = "none";
}


const deleteArticle = document.getElementById("articleDelete");

function openDlt() {
    deleteArticle.style.display = "flex";
}

function closeDlt() {
    deleteArticle.style.display = "none";
}
