// capture nav
let nav = document.querySelector("body > nav");
let aside = document.querySelector("body > aside > nav");

window.onscroll = () => {

    stayStuck();
}

function stayStuck() {

    if (window.scrollY >= 0.01) {
        nav.classList.add("sticky");
        aside.classList.add("sticky");
        aside.style = 'background-color:#fff;';
    } else {
        nav.classList.remove("sticky");
        aside.classList.add("sticky");
    }
}