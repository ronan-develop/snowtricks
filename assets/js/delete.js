const axios = require('axios').default;

document.querySelectorAll('a#delete-button').forEach((link) => {
    link.addEventListener('click', launchModal);
});

/**
 * open modal and set attributes
 * @param {event} e click
 */
function launchModal(e) {
    e.preventDefault();
    // handle attributes
    const target = this.parentNode.parentNode.parentNode;
    const modal = document.getElementById("modal-trick");
    modal.removeAttribute("style");
    modal.setAttribute('aria-modal', 'true');
    modal.removeAttribute('aria-hidden');
    let link = this.dataset.delete;
    let cancel = document.querySelector("#modal-trick > div > button.secondary.outline");
    let close = document.querySelector("#modal-trick > div > span");
    let confirm = document.querySelector("#modal-trick > div > button:nth-child(5)");

    close.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal("trick");
        cancel.removeEventListener("click", closeModal, true);
        return;
    });

    cancel.addEventListener('click', (e) => {
        console.log('cancel');
        e.preventDefault();
        closeModal("trick");
        cancel.removeEventListener("click", closeModal, true);
        return;
    });

    confirm.addEventListener('click', (e) => {
        e.preventDefault();
        deleteTrick(link, target);
    });
}

/**
 * close modal
 */
function closeModal(target) {
    const modal = document.getElementById(`modal-${target}`);
    console.log(modal);
    modal.setAttribute("style", "display: none;");
    modal.setAttribute('aria-hidden', 'true');
    modal.removeAttribute('aria-modal');
}

/**
 * async route to delete trick
 * @param {string} url data-delete
 */
function deleteTrick(url, trick) {
    trick.remove();
    closeModal("trick");
    success('✔ Figure supprimée');

    // axios.post(url).then((response) => {
    //     console.log(response);
    // });
}

function success(message) {
    const modal = document.querySelector("#modal-success");
    let close = modal.querySelector("span.js-close-modal");
    let confirm = document.querySelector("#modal-success > div > button");

    console.log(modal);
    console.log(message);

    modal.removeAttribute("style");
    modal.setAttribute('aria-modal', 'true');
    modal.removeAttribute('aria-hidden');

    close.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal("success");
        return;
    });

    confirm.addEventListener('click', (e)=>{
        e.preventDefault();
        closeModal("success");
        return;
    });
}