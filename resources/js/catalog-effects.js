class CatalogEffects {
    constructor() {
        this.initCardObserver();
        this.initTiltEffect();
        this.initGlitchOnEntry();
    }

    initCardObserver() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.catalog-card').forEach(card => {
                card.classList.add('visible');
            });
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.catalog-card').forEach(card => {
            observer.observe(card);
        });
    }

    initTiltEffect() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        document.querySelectorAll('.tilt-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
            });
        });
    }

    initGlitchOnEntry() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const glitchTitle = document.querySelector('.glitch-title');
        if (glitchTitle) {
            glitchTitle.style.animation = 'none';
            setTimeout(() => {
                glitchTitle.style.animation = 'glitchTitle 4s ease-in-out infinite';
            }, 100);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.catalog-card') || document.querySelector('.glitch-title')) {
        new CatalogEffects();
    }
});
