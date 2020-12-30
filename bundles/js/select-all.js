[].forEach.call(document.getElementsByClassName('select-all'), function (el) {
    el.addEventListener('click', function () {
        let that = this;
        [].forEach.call(document.getElementsByName(this.getAttribute('data-name')), function (el, i) {
            el.checked = that.checked;
        });
    });
});
