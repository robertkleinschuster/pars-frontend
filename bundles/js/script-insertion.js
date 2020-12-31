function insert() {
    [].forEach.call(document.getElementsByClassName("script-insertion"), function (t) {
        let e = document.createElement("script");
        e.src = t.getAttribute("data-src"), document.body.appendChild(e)
    })
}

window.addEventListener ? window.addEventListener("load", insert) : window.attachEvent ? window.attachEvent("onload", insert) : window.onload = insert;
