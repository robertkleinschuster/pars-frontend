[].forEach.call(document.getElementsByClassName('dynamic-field-add'), function (el) {
    el.addEventListener('click', function () {
        let template = document.getElementById('dynamic-field-template').innerHTML;
        template = template.replaceAll('{label}', this.getAttribute('data-dynamic-field-label'));
        template = template.replaceAll('{name}', this.getAttribute('data-dynamic-field-name'));
        let element = document.createRange().createContextualFragment(template);
        document.getElementById(this.getAttribute('data-dynamic-field-container')).appendChild(element);
    });
});

/**
 * String.prototype.replaceAll() polyfill
 * https://gomakethings.com/how-to-replace-a-section-of-a-string-with-another-one-with-vanilla-js/
 * @author Chris Ferdinandi
 * @license MIT
 */
if (!String.prototype.replaceAll) {
    String.prototype.replaceAll = function (str, newStr) {
        if (Object.prototype.toString.call(str).toLowerCase() === '[object regexp]') {
            return this.replace(str, newStr);
        }
        return this.replace(new RegExp(str, 'g'), newStr);
    };
}
