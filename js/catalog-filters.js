(function () {
    function init() {
        var catalog = document.querySelector('.catalog');
        if (!catalog) return;
        var items = catalog.querySelector('.catalog__items');
        var actions = catalog.querySelector('.catalog__actions');
        if (!items || !actions) return;

        function markVisible() {
            items.querySelectorAll('[data-fls-watcher]').forEach(function (el) {
                el.classList.add('--watcher-view');
            });
        }

        function load(url, push) {
            catalog.classList.add('is-loading');
            fetch(url, { headers: { 'X-Requested-With': 'fetch' } })
                .then(function (res) {
                    if (!res.ok) throw new Error('Bad response');
                    return res.text();
                })
                .then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var newItems = doc.querySelector('.catalog__items');
                    var newActions = doc.querySelector('.catalog__actions');
                    if (newItems) items.innerHTML = newItems.innerHTML;
                    if (newActions) actions.innerHTML = newActions.innerHTML;
                    if (push) history.pushState({ catalogUrl: url }, '', url);
                    markVisible();
                    if (typeof window.customMiniSelect === 'function') window.customMiniSelect();
                })
                .catch(function () {
                    window.location.href = url;
                })
                .finally(function () {
                    catalog.classList.remove('is-loading');
                });
        }

        actions.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter-url]');
            if (!btn) return;
            load(btn.getAttribute('data-filter-url'), true);
        });

        window.addEventListener('popstate', function () {
            load(location.href, false);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
