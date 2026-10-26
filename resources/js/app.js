import { Offcanvas, Toast } from 'bootstrap';
import { drawAll } from './sales-chart';

// Bootstrap só troca de tema por data-bs-theme; aqui ele segue o sistema ou o data-theme da página
function applyTheme() {
    const forced = document.documentElement.dataset.theme;
    const dark = forced ? forced === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.dataset.bsTheme = dark ? 'dark' : 'light';
}

function showToasts() {
    document.querySelectorAll('.toast[data-autoshow]').forEach((el) => {
        Toast.getOrCreateInstance(el, { delay: 6000 }).show();
    });
}

function bindConfirms() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });
}

function bindSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) Offcanvas.getOrCreateInstance(sidebar);
}

applyTheme();
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    applyTheme();
    drawAll();
});

document.addEventListener('DOMContentLoaded', () => {
    bindSidebar();
    showToasts();
    bindConfirms();
    drawAll();
});

window.addEventListener('resize', drawAll);
