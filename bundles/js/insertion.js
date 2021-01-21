function insert() {
    [].forEach.call(document.getElementsByClassName("script-insertion"), function (t) {
        let e = document.createElement("script");
        e.src = t.getAttribute("data-src"), document.body.appendChild(e)
    });
    [].forEach.call(document.getElementsByClassName("style-insertion"), function (t) {
        t.setAttribute('rel', 'stylesheet')
    });
    [].forEach.call(document.getElementsByTagName("html"), function (t) {
        t.classList.add('ready');
    });
}
window.addEventListener ? window.addEventListener("load", insert) : window.attachEvent ? window.attachEvent("onload", insert) : window.onload = insert;
