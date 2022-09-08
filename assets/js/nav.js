/**
 * Buttons
 */
 let lines = document.querySelector('nav svg');
 let cross = document.querySelector("body > aside > nav > ul > ul > a");
 
 /**
  * Events
  */
 lines.addEventListener('click', openMenu);
 cross.addEventListener('click', closeMenu);
 
 /**
  * open mobile nav menu
  * @param {Event} e click
  */
 function closeMenu(e) {

     e.preventDefault;
     document.querySelector('.side-content').style.width = '0px';
     cross.removeEventListener('click', closeMenu, true);
 }
 
 /**
  * close mobile nav menu
  * @param {Event} e click
  */
 function openMenu(e) {
    
     e.preventDefault;
     document.querySelector('.side-content').style.width = '250px';
 }