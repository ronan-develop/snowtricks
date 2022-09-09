// capture arrows
let arrowDown = document.querySelector("body > div.container-fluid > div.header > i");
let arrowUp = document.querySelector(".fa-circle-arrow-up");

// capture elements for scrollToTricks
let h2 = document.querySelector("h2.h2");

let firstArticle = document.querySelector(
    "section >.container >article:nth-child(1)"
);

// scroll event
addEventListener("scroll", (e) => {
    
    e.preventDefault();
    scrollY < h2.offsetTop + h2.offsetHeight ? arrowUp.classList.add('hide') : arrowUp.classList.remove('hide');
    scrollY > document.querySelector('section').offsetTop ? arrowDown.classList.add('hide') : arrowDown.classList.remove('hide');
});

// event listener on arrows
arrowDown.addEventListener("click", scrollToTricks);
arrowUp.addEventListener("click", scrollUp);

// articles node
let articles = document.querySelectorAll("article");

// hide arrow-uip for less than 15 articles
if (articles.length < 15) {

    arrowUp.classList.add("hide");
}

/**
 * Click on arrow-down scroll to tricks-anchor
 * @param {Event} e click
 */
function scrollToTricks(e) {

    e.preventDefault();
    if (h2) {
        h2.scrollIntoView({
            behavior: "smooth",
            block: "center",
        });
    }
}

/**
 * Click on arrow-up scroll to article:nth-child(1)
 * @param {Event} e click
 */
function scrollUp(e) {

    e.preventDefault();
    if (firstArticle) {

        firstArticle.scrollIntoView({
            behavior: "smooth",
            block: "center",
        });
    }
}