// Gráfico de barras do painel, desenhado direto no canvas. Uma série só
// (receita por dia), então uma lib de gráfico seria mais peso que ajuda.

const money = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
const PAD = { top: 16, right: 12, bottom: 28, left: 72 };
const GRID_LINES = 4;

// Teto "redondo" para o eixo: 1, 2 ou 5 vezes a potência de 10 mais próxima
function niceCeil(value) {
    const pow = 10 ** Math.floor(Math.log10(value));
    const unit = value / pow;
    const step = unit <= 1 ? 1 : unit <= 2 ? 2 : unit <= 5 ? 5 : 10;
    return step * pow;
}

function cssVar(el, name) {
    return getComputedStyle(el).getPropertyValue(name).trim();
}

export function draw(canvas) {
    const series = JSON.parse(canvas.dataset.series);
    if (!series.length) return;

    const dpr = window.devicePixelRatio || 1;
    const width = canvas.clientWidth;
    const height = canvas.clientHeight;
    canvas.width = width * dpr;
    canvas.height = height * dpr;

    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    ctx.clearRect(0, 0, width, height);

    const colors = {
        bar: cssVar(canvas, '--accent'),
        line: cssVar(canvas, '--line'),
        text: cssVar(canvas, '--text-muted'),
    };

    const plotW = width - PAD.left - PAD.right;
    const plotH = height - PAD.top - PAD.bottom;
    const top = niceCeil(Math.max(100, ...series.map((d) => d.revenueCents)));

    ctx.font = '12px system-ui, sans-serif';
    ctx.textBaseline = 'middle';

    for (let i = 0; i <= GRID_LINES; i++) {
        const y = PAD.top + plotH * (1 - i / GRID_LINES);
        ctx.strokeStyle = colors.line;
        ctx.beginPath();
        ctx.moveTo(PAD.left, y);
        ctx.lineTo(width - PAD.right, y);
        ctx.stroke();

        ctx.fillStyle = colors.text;
        ctx.textAlign = 'right';
        ctx.fillText(money.format((top * i) / GRID_LINES / 100), PAD.left - 8, y);
    }

    const slot = plotW / series.length;
    const barW = Math.max(2, slot * 0.62);
    const labelEvery = Math.ceil(series.length / 10);

    series.forEach((day, i) => {
        const h = (plotH * day.revenueCents) / top;
        const x = PAD.left + i * slot + (slot - barW) / 2;
        ctx.fillStyle = colors.bar;
        ctx.fillRect(x, PAD.top + plotH - h, barW, h);

        if (i % labelEvery === 0) {
            ctx.fillStyle = colors.text;
            ctx.textAlign = 'center';
            ctx.fillText(day.date.slice(8), x + barW / 2, height - PAD.bottom / 2);
        }
    });
}

export function drawAll() {
    document.querySelectorAll('canvas.sales-chart').forEach(draw);
}
