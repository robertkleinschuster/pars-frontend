var db = function (fn) {
    let f;
    return function (...p) {
        if (f) {
            cancelAnimationFrame(f);
        }
        f = requestAnimationFrame(function () {
            fn(...p);
        });
    }
};
var ss = function () {
    var s = window.scrollY;
    var sp = Math.round(s / window.innerHeight * 100);
    if (s < 0) {
        s = 0;
    }
    var e = document.documentElement;
    var cl = document.documentElement.classList;
    e.dataset.scroll = s;
    e.dataset.scrollpercent = sp;
    var sq = 'scroll-quarter';
    var sh = 'scroll-half';
    var sc = 'scroll-complete';
    if (sp > 25) {
        cl.add(sq)
    }
    if (sp < 25) {
        cl.remove(sq)
    }
    if (sp > 50) {
        cl.add(sh)
    }
    if (sp < 50) {
        cl.remove(sh)
    }
    if (sp > 100) {
        cl.add(sc)
    }
    if (sp < 100) {
        cl.remove(sc)
    }
}
document.addEventListener('scroll', db(ss), {passive: true});
ss();
