const textArea = document.querySelector("#comment_form_content");
const submitComment = document.querySelector("#comment_form_Envoyer");

document.addEventListener('DOMContentLoaded', () => {

    if (textArea) {
        textArea.addEventListener('input', activateButton);

        new App();
    }
});


/**
 * activate button on content length > 1
 */
function activateButton(e) {

    e.preventDefault();
    submitComment.removeAttribute('disabled');
}
// class App {

//     constructor() {
//         this.handleComment();
//     }

//     handleComment() {
//         const form = document.querySelector('form.comment-form');
//         if (null === form) {
//             return;
//         }

//         form.addEventListener('submit', async(e)=>{
//             e.preventDefault();

//             const reponse = await fetch('/comment', {
//                 method: 'POST',
//                 body: new FormData(e.target)
//             });

//             if(!reponse.ok){
//                 return;
//             }

//             const json = await reponse.json();

//             if(json.code === 'COMMENT_ADDED_SUCCESSFULLY') {
//                 console.log(json);
//                 const commentContainer = document.querySelector('.comment-container');
//                 const count = document.querySelector("body > div:nth-child(4) > h3 > span");
//                 const message = document.getElementById('comment_form_content');
//                 count.innerText = json.number;
//                 if(json.number == 0){
//                     commentContainer.appendChild(json);
//                 } else {
//                     commentContainer.insertAdjacentHTML('beforeend', json);
//                 }
//                 commentContainer.lastElementChild.scrollIntoView();
//                 message = '';
//             }

//         });
//     }

// }
