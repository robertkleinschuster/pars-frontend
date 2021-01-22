var i = function () {
    [].forEach.call(document.getElementsByClassName("script-insertion"), function (t) {
        let e = document.createElement("script");
        e.src = t.getAttribute("data-src"), document.body.appendChild(e)
    });
    [].forEach.call(document.getElementsByClassName("style-insertion"), function (t) {
        t.setAttribute('rel', 'stylesheet')
    });
    [].forEach.call(document.getElementsByTagName("html"), function (t) {
        t.classList.add('ready');
        setTimeout(function () {
            t.classList.add('idle');
        }, 200);
    });
}
window.addEventListener ? window.addEventListener("load", i) : window.attachEvent ? window.attachEvent("onload", i) : window.onload = i;
