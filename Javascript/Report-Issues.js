if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}

function toggleDetails(issueElement) {
    // Toggles the 'active' class on the clicked issue
    issueElement.classList.toggle('active');
}