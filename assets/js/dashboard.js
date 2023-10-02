const createNewSessionButton = document.getElementById('createbtn');

function showForm(event) {
    event.preventDefault();
    const sessionForm = document.getElementById('sessionForm');
    sessionForm.style.display = 'block';
}
createNewSessionButton.addEventListener('click', showForm);