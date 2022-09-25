// const axios = require('axios').default;

document.querySelectorAll('a#delete-button').forEach((link)=>{
    link.addEventListener('click', launchModal);
});

function launchModal(e){
    e.preventDefault();
    // handle attributes
    const modal = document.getElementById("modal-trick");
    modal.removeAttribute("style");
    modal.setAttribute('aria-modal', 'true');
    modal.removeAttribute('aria-hidden');
    let link = this.dataset.delete;
    let cancel = document.querySelector("#modal-trick > div > button.secondary.outline");
    let confirm = document.querySelector("#modal-trick > div > button:nth-child(5)");

    cancel.addEventListener('click', (e)=>{
        console.log('cancel');
        e.preventDefault();
        closeModal();
        cancel.removeEventListener("click", closeModal, true);
        return;
    });

    confirm.addEventListener('click', (e)=>{
        e.preventDefault();

    })
}

function closeModal() {
    const modal = document.getElementById("modal-trick");
    modal.setAttribute("style", "display: none;");
    modal.setAttribute('aria-hidden', 'true');
    modal.removeAttribute('aria-modal');
}

function deleteTrick(link) {
    console.log(this);
}

// function cancel() {
//     console.log('cancel');
//     return;
// }

// function deleteTrick() {
//     console.log('yep');
//     const url = this.href;
//     this.parentNode.parentNode.parentNode.remove();
//     this.remove();
    
//     axios.post(url).then((response)=>{
//         console.log(response);
//     });
// }