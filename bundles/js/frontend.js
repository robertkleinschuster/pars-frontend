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

const debounce = (fn) => {
    let frame;
    return (...params) => {
        if (frame) {
            cancelAnimationFrame(frame);
        }
        frame = requestAnimationFrame(() => {
            fn(...params);
        });

    }
};
const storeScroll = () => {
    document.documentElement.dataset.scroll = window.scrollY;
    if ( window.scrollY > 300) {
        document.documentElement.classList.add('scroll-300')
    }
    if ( window.scrollY < 300) {
        document.documentElement.classList.remove('scroll-300')
    }
}
document.addEventListener('scroll', debounce(storeScroll), { passive: true });
storeScroll();
