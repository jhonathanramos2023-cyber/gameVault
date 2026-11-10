class PanelEffects {
    constructor() {
        this.initCounterAnimation();
    }

    initCounterAnimation() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const stats = document.querySelectorAll('.gv-stat strong');
        stats.forEach(stat => {
            const target = parseInt(stat.textContent.replace(/,/g, ''));
            if (isNaN(target)) return;

            let current = 0;
            const duration = 2000;
            const increment = target / (duration / 16);

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    stat.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    stat.textContent = target.toLocaleString();
                }
            };

            updateCounter();
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.gv-stat')) {
        new PanelEffects();
    }
});
