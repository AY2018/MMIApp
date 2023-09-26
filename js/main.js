
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

// Add a click event listener to the toggle button
toggleButton.addEventListener('click', () => {
    // Loop through the elements with class 'done'
    doneElements.forEach(element => {
        // Toggle the 'flex' and 'none' values for the 'display' property
        if (element.style.display === 'none' || element.style.display === '') {
            element.style.display = 'flex';
            element.style.flexDirection = 'row';
        } else {
            element.style.display = 'none';
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

    // Supprimez le cookie "id" en le définissant avec une date d'expiration passée
    // document.cookie = "id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    // location.reload();
}

function openInfo() {
    infoArticle.style.display = "flex";
    // var currentDate = new Date();
    // currentDate.setTime(currentDate.getTime() + (24 * 60 * 60 * 1000));
    // var expires = "expires=" + currentDate.toUTCString();
    // document.cookie = "id=" + devoirID + "; " + expires + "; path=/";
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
    console.log('lol');
}
