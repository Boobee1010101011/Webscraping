import React from 'react';
import { createRoot } from 'react-dom/client';
import {
    AlertTriangle,
    ArrowRight,
    ArrowUpRight,
    CalendarDays,
    FileText,
    Minus,
    Plus,
    Search,
    Moon,
    Sun,
    Users,
    X,
} from 'lucide-react';

const iconMap = { alert: AlertTriangle, calendar: CalendarDays, file: FileText, users: Users };

function mountIcon(container, Icon, props = {}) {
    if (!container) return;
    createRoot(container).render(React.createElement(Icon, {
        'aria-hidden': true,
        strokeWidth: 1.8,
        ...props,
    }));
}

function normalizeIcons() {
    document.title = 'PPI Competitor Monitoring';

    const storedTheme = localStorage.getItem('theme');
    const dark = storedTheme ? storedTheme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.classList.toggle('dark', dark);

    const brandName = document.querySelector('.brand-name');
    if (brandName) brandName.textContent = 'PPI Competitor Monitoring';

    mountIcon(document.querySelector('.brand-mark'), ArrowUpRight, { strokeWidth: 2.1 });
    mountIcon(document.querySelector('.icon-button'), Search);

    const themeToggle = document.querySelector('.theme-toggle');
    const themeIcon = document.querySelector('.theme-icon');
    const themeRoot = themeIcon ? createRoot(themeIcon) : null;
    const updateThemeToggle = () => {
        const isDark = document.documentElement.classList.contains('dark');
        themeToggle?.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        themeRoot?.render(React.createElement(isDark ? Sun : Moon, { 'aria-hidden': true, size: 17, strokeWidth: 1.8 }));
    };
    updateThemeToggle();
    themeToggle?.addEventListener('click', () => {
        const isDark = !document.documentElement.classList.contains('dark');
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeToggle();
    });

    const scanButton = document.querySelector('.scan-button');
    if (scanButton) {
        scanButton.querySelector('svg')?.remove();
        const slot = document.createElement('span');
        scanButton.prepend(slot);
        mountIcon(slot, Plus);
    }

    document.querySelectorAll('.metric-icon').forEach((slot, index) => {
        mountIcon(slot, iconMap[['file', 'alert', 'users', 'calendar'][index] ?? 'file']);
    });

    document.querySelectorAll('.section-link, .action-button').forEach((button) => {
        button.querySelector('svg')?.remove();
        const slot = document.createElement('span');
        button.append(slot);
        mountIcon(slot, ArrowRight, { size: 15 });
    });

    mountIcon(document.querySelector('.detail-close'), X);
    mountIcon(document.querySelector('#lightbox-close'), X);
    mountIcon(document.querySelector('#zoom-out'), Minus);
    mountIcon(document.querySelector('#zoom-in'), Plus);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', normalizeIcons, { once: true });
} else {
    normalizeIcons();
}
