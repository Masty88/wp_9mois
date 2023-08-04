document.addEventListener('DOMContentLoaded', () => {

    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Add a click event on each of them
    $navbarBurgers.forEach( el => {
        el.addEventListener('click', () => {

            // Get the target from the "data-target" attribute
            const target = el.dataset.target;
            const $target = document.getElementById(target);

            // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
            el.classList.toggle('is-active');
            $target.classList.toggle('is-active');

        });
    });

});

const burgerButton = document.querySelector('.navbar-burger');
const menuMobile = document.querySelector('.menu-mobile');

burgerButton.addEventListener('click', function() {
    console.log("click")

    if(menuMobile.classList.contains('is-animated')){
        menuMobile.classList.remove('is-animated');
        console.log('Removed is-active class');
    }else{
        menuMobile.classList.add('is-animated');
        console.log('Added is-active class');
    }
});

