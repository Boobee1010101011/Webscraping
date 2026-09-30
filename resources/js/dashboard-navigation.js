const dashboardRoutes = new Set(['/', '/tiktok-monitoring', '/bank-monitoring', '/generated-content']);

function isDashboardUrl(url) {
    return url.origin === window.location.origin && dashboardRoutes.has(url.pathname);
}

function updateActiveNavigation(pathname) {
    document.querySelectorAll('.nav a').forEach((link) => {
        const linkUrl = new URL(link.href, window.location.origin);
        link.classList.toggle('active', linkUrl.pathname === pathname);
    });
}

async function loadDashboard(url, { replace = false } = {}) {
    const currentContent = document.querySelector('[data-dashboard-content]') || document.querySelector('main');
    if (!currentContent) {
        window.location.href = url.href;
        return;
    }

    currentContent.classList.add('is-loading');

    try {
        const response = await fetch(url.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' },
        });

        if (!response.ok) throw new Error(`Dashboard request failed: ${response.status}`);

        const html = await response.text();
        const parsedDocument = new DOMParser().parseFromString(html, 'text/html');
        const nextContent = parsedDocument.querySelector('[data-dashboard-content]') || parsedDocument.querySelector('main');

        if (!nextContent) {
            window.location.href = url.href;
            return;
        }

        const replaceContent = () => {
            currentContent.replaceWith(nextContent);
            document.title = parsedDocument.title || document.title;
            updateActiveNavigation(url.pathname);
            window.dispatchEvent(new CustomEvent('dashboard:updated'));
        };

        if (document.startViewTransition) await document.startViewTransition(replaceContent).finished;
        else replaceContent();

        if (replace) window.history.replaceState({}, '', url.href);
        else window.history.pushState({}, '', url.href);
    } catch (error) {
        console.error(error);
        window.location.href = url.href;
    } finally {
        (document.querySelector('[data-dashboard-content]') || document.querySelector('main'))?.classList.remove('is-loading');
    }
}

document.addEventListener('click', (event) => {
    const reportButton = event.target.closest('.view-report');
    if (reportButton && !document.getElementById('detail-panel')) {
        const sourceUrl = reportButton.dataset.sourceUrl;
        if (sourceUrl) window.open(sourceUrl, '_blank', 'noopener,noreferrer');
        return;
    }

    const link = event.target.closest('a');
    if (!link || event.defaultPrevented || link.target === '_blank' || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    const url = new URL(link.href, window.location.origin);
    if (!isDashboardUrl(url) || url.hash) return;

    event.preventDefault();
    if (url.href !== window.location.href) loadDashboard(url);
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form.filters');
    if (!form || form.method.toLowerCase() !== 'get') return;

    const url = new URL(form.action, window.location.origin);
    if (!isDashboardUrl(url)) return;

    event.preventDefault();
    const formUrl = new URL(url.href);
    new FormData(form).forEach((value, key) => {
        if (value !== '') formUrl.searchParams.set(key, value);
        else formUrl.searchParams.delete(key);
    });
    loadDashboard(formUrl);
});

window.addEventListener('popstate', () => loadDashboard(new URL(window.location.href), { replace: true }));
