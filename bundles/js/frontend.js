var e = document.querySelectorAll('a[href*="#"]');
if (e.length) {
    e.forEach(function (e) {
        e.addEventListener('click', function (e) {
            if (this.hash === this.getAttribute('href') || this.href === window.location.href) {
                e.preventDefault();
            }
            var s = this.hash;
            var y = document.querySelector(s);
            if (y) {
                y.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}
var e = document.querySelectorAll('a');
if (e.length) {
    e.forEach(function (e) {
        e.addEventListener('mouseover', function (e) {
            injectLink(this);
        });
    });
}
var e = document.querySelectorAll('.nav-link');
if (e.length) {
    e.forEach(function (e) {
        injectLink(e);
    });
}

function injectLink(e)
{
    var href = e.getAttribute('href');
    if (href.startsWith('/') || href.startsWith('http')) {
        var link = href;
        if (!href.startsWith('http')) {
            link = '//' + window.location.host + href;
        }
        link = link.replace('http:', '');
        link = link.replace('https:', '');
        if (document.querySelectorAll('link[href="' + link + '"]').length === 0) {
            var hint = document.createElement("link");
            hint.rel = "prefetch";
            hint.as = "document";
            hint.href = link;
            document.head.appendChild(hint);
            var hint = document.createElement("link");
            hint.rel = "prerender";
            hint.as = "document";
            hint.href = link;
            document.head.appendChild(hint);
        }
    }
}
