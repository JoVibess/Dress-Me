import { CountUp } from 'countup.js';
import ApexCharts from 'apexcharts';

const themeStorageKey = 'dressme_middle_theme';

function setupThemeToggle() {
    const root = document.querySelector('[data-middle-theme]');
    const toggle = document.querySelector('[data-theme-toggle]');
    const themeOptions = document.querySelectorAll('[data-theme-option]');

    if (!root || !toggle || themeOptions.length === 0) {
        return;
    }

    const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const initialTheme = localStorage.getItem(themeStorageKey) || preferredTheme;

    const applyTheme = (theme) => {
        root.setAttribute('data-bs-theme', theme);
        toggle.setAttribute('data-theme-state', theme);
        themeOptions.forEach((option) => {
            option.setAttribute('aria-pressed', option.dataset.themeOption === theme ? 'true' : 'false');
        });
        localStorage.setItem(themeStorageKey, theme);
    };

    applyTheme(initialTheme);

    if (toggle.dataset.themeToggleBound === 'true') {
        return;
    }

    toggle.dataset.themeToggleBound = 'true';
    themeOptions.forEach((option) => {
        option.addEventListener('click', () => {
            applyTheme(option.dataset.themeOption);
        });
    });
}

function setupCountup() {
    document.querySelectorAll('[data-countup]').forEach((element) => {
        if (element.dataset.countupBound === 'true') {
            return;
        }

        element.dataset.countupBound = 'true';
        const target = Number.parseFloat(element.textContent.trim().replace(/\s/g, ''));

        if (Number.isNaN(target)) {
            return;
        }

        let options = {};
        const rawOptions = element.getAttribute('data-countup');

        if (rawOptions) {
            try {
                options = JSON.parse(rawOptions);
            } catch {
                options = {};
            }
        }

        const countUp = new CountUp(element, target, options);

        if (!countUp.error) {
            countUp.start();
        }
    });
}

function setupCreditUsageChart() {
    const element = document.querySelector('[data-credit-usage-chart]');

    if (!element || element.dataset.chartBound === 'true') {
        return;
    }

    element.dataset.chartBound = 'true';

    let chartData;

    try {
        chartData = JSON.parse(element.dataset.creditUsageChart);
    } catch {
        return;
    }

    const chart = new ApexCharts(element, {
        chart: {
            type: 'line',
            fontFamily: 'inherit',
            height: 280,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: true,
            },
        },
        series: chartData.series,
        labels: chartData.labels,
        colors: [
            'color-mix(in srgb, transparent, var(--tblr-primary) 100%)',
        ],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            width: 3,
            lineCap: 'round',
            curve: 'smooth',
        },
        grid: {
            padding: {
                top: -12,
                right: 0,
                left: -4,
                bottom: -4,
            },
            strokeDashArray: 4,
        },
        xaxis: {
            type: 'datetime',
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false,
            },
        },
        yaxis: {
            min: 0,
            max: 100,
            labels: {
                padding: 4,
                formatter: (value) => `${Math.round(value)}%`,
            },
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: (value) => `${value}% used`,
            },
        },
        legend: {
            show: true,
            position: 'bottom',
            offsetY: 12,
            markers: {
                width: 10,
                height: 10,
                radius: 100,
            },
            itemMargin: {
                horizontal: 8,
                vertical: 8,
            },
        },
    });

    chart.render();
}

function initMiddleDashboard() {
    setupThemeToggle();
    setupCountup();
    setupCreditUsageChart();
}

document.addEventListener('DOMContentLoaded', initMiddleDashboard);
document.addEventListener('turbo:load', initMiddleDashboard);
