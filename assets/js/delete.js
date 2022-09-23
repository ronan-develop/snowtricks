const deleteButtons = document.querySelectorAll('#delete-button');

for (let i = 0; i < deleteButtons.length; i++) {
    deleteButtons[i].addEventListener('click', displayConfirm);
}

function displayConfirm(e){
    e.preventDefault();
    let url = Routing.generate('app_delete_trick');
    console.log(url);
}