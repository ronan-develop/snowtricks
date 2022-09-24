// const axios = require('axios').default;

document.querySelectorAll('a#delete-button').forEach((link)=>{
    link.addEventListener('click', launchModal);
});
window.addEventListener('load',() => {
    document.querySelector("#modal").style = 'display: none;';

});

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