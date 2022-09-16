const textArea = document.querySelector("#comment_form_content");
const submitComment = document.querySelector("#comment_form_Envoyer");

if (textArea) {
    textArea.addEventListener('input', activateButton);
}

/**
 * activate button on content length > 1
 */
function activateButton(e) {
    
    e.preventDefault();
    submitComment.removeAttribute('disabled');
}