// Apply the Filter to query string in the url.
function applyFilter(perPage) {
    window.location.href = getFilterQueryString(perPage);
}

// Asynchronous implmentation for migrate.
function migrate() {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", "migrate");
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("migrate=true");

    xmlhttp.onload = function (e) {
        // Check if the request was a success
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            console.log(xmlhttp.responseText);
            window.location.reload();
        }
    }
}

// Get the filter query string.
function getFilterQueryString(perPage) {
    let searchValue = document.getElementById('search').value;
    let courseValue = document.getElementById('course').value;
    let userValue = document.getElementById('user').value;
    let statusValue = document.getElementById('completionStatus').value;
    let queryString = '';

    if (searchValue != '') {
        queryString = queryString + '&search=' + searchValue;
    }

    if (courseValue != '') {
        queryString = queryString + '&course=' + courseValue;
    }

    if (userValue != '') {
        queryString = queryString + '&user=' + userValue;
    }

    if (statusValue != '') {
        queryString = queryString + '&status=' + statusValue;
    }

    return '?per_page=' + perPage + queryString;
}

// Listen to change event for perPage.
document.getElementById('perPage').addEventListener('change', function (event) {

    event.preventDefault();

    window.location.href = getFilterQueryString(this.value);
});