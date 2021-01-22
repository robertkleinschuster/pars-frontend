document.querySelector('a[href^="#"]').addEventListener('click',function(e) {
    e.preventDefault();
    document.querySelector(this.hash).scrollIntoView({
        behavior: 'smooth'
    });
});
document.querySelector('.nav-link').addEventListener('click',function(e) {
    document.querySelector(this.hash).scrollIntoView({
        behavior: 'smooth'
    });
});
