$('a[href^="#"]').on('click',function(e) {
    e.preventDefault();
    document.querySelector(this.hash).scrollIntoView({
        behavior: 'smooth'
    });
});

$('.nav-link').on('click',function(e) {
    document.querySelector(this.hash).scrollIntoView({
        behavior: 'smooth'
    });
});

// The debounce function receives our function as a parameter
const debounce = (fn) => {

    // This holds the requestAnimationFrame reference, so we can cancel it if we wish
    let frame;

    // The debounce function returns a new function that can receive a variable number of arguments
    return (...params) => {

        // If the frame variable has been defined, clear it now, and queue for next frame
        if (frame) {
            cancelAnimationFrame(frame);
        }

        // Queue our function call for the next frame
        frame = requestAnimationFrame(() => {

            // Call our function and pass any params we received
            fn(...params);
        });

    }
};


// Reads out the scroll position and stores it in the data attribute
// so we can use it in our stylesheets
const storeScroll = () => {
    document.documentElement.dataset.scroll = window.scrollY;
    if ( window.scrollY > 300) {
        document.documentElement.classList.add('scroll-300')
    }
    if ( window.scrollY < 300) {
        document.documentElement.classList.remove('scroll-300')
    }
}

// Listen for new scroll events, here we debounce our `storeScroll` function
document.addEventListener('scroll', debounce(storeScroll), { passive: true });

// Update scroll position for first time
//storeScroll();
