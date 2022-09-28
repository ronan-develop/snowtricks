const iframe = document.querySelector("body > div.hero > header > div > span.video > iframe");

if (iframe) {
    iframe.removeAttribute('width');
    iframe.removeAttribute('height');
}