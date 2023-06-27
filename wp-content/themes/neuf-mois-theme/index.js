document.addEventListener('DOMContentLoaded', () => {
    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Add a click event on each of them
    $navbarBurgers.forEach(el => {
        el.addEventListener('click', () => {

            // Get the target from the "data-target" attribute
            const target = el.dataset.target;
            const $target = document.getElementById(target);

            // Toggle the "is-active" class on "navbar-burger"
            el.classList.toggle('is-active');

            // Check if "navbar-menu" is active
            if ($target.classList.contains('is-active')) {
                // If it's active, we want to animate it out
                $target.style.animationName = 'slideOut';
                $target.addEventListener('animationend', () => {
                    // Once the animation is complete, remove the "is-active" class
                    $target.classList.remove('is-active');
                }, { once: true });  // The event listener will be removed after one use
            } else {
                // If it's not active, we want to animate it in
                $target.style.animationName = 'slideIn';
                $target.classList.add('is-active');
            }
        });
    });
});
