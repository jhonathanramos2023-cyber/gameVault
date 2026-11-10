class MatrixRain {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.canvas.setAttribute('aria-hidden', 'true');
        this.canvas.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none;';
        document.body.prepend(this.canvas);
        this.ctx = this.canvas.getContext('2d');
        this.columns = [];
        this.fontSize = 14;
        this.running = false;
        this.animationId = null;
        this.lastTime = 0;
        this.fpsInterval = 1000 / 30;
    }

    init() {
        this.resize();
        window.addEventListener('resize', () => this.resize());
        this.setupColumns();
        this.start();
    }

    resize() {
        this.canvas.width = window.innerWidth * Math.min(window.devicePixelRatio, 1.5);
        this.canvas.height = window.innerHeight * Math.min(window.devicePixelRatio, 1.5);
        this.ctx.scale(Math.min(window.devicePixelRatio, 1.5), Math.min(window.devicePixelRatio, 1.5));
        this.setupColumns();
    }

    setupColumns() {
        const columnCount = Math.floor(window.innerWidth / this.fontSize);
        this.columns = Array(columnCount).fill(1);
    }

    getIntensity() {
        const body = document.body;
        const fondo = body.getAttribute('data-fondo');

        if (fondo === 'login') return 1;
        if (fondo === 'principal') return 0.5;
        if (fondo === 'panel' || fondo === 'colaboradores') return 0.3;
        return 0.2;
    }

    getColor() {
        const body = document.body;
        const fondo = body.getAttribute('data-fondo');

        if (fondo === 'panel' || fondo === 'colaboradores') {
            return `rgba(255, 42, 42, ${this.getIntensity() * 0.3})`;
        }
        return `rgba(0, 255, 65, ${this.getIntensity() * 0.5})`;
    }

    draw(timestamp) {
        if (!this.running) return;

        this.animationId = requestAnimationFrame((t) => this.draw(t));

        const elapsed = timestamp - this.lastTime;
        if (elapsed < this.fpsInterval) return;

        this.lastTime = timestamp - (elapsed % this.fpsInterval);

        this.ctx.fillStyle = 'rgba(2, 10, 2, 0.05)';
        this.ctx.fillRect(0, 0, window.innerWidth, window.innerHeight);

        this.ctx.fillStyle = this.getColor();
        this.ctx.font = `${this.fontSize}px 'Share Tech Mono', monospace`;

        for (let i = 0; i < this.columns.length; i++) {
            const char = Math.random() > 0.5 ? '1' : '0';
            const x = i * this.fontSize;
            const y = this.columns[i] * this.fontSize;

            this.ctx.fillText(char, x, y);

            if (y > window.innerHeight && Math.random() > 0.975) {
                this.columns[i] = 0;
            }

            this.columns[i]++;
        }
    }

    start() {
        if (this.running) return;
        this.running = true;
        this.lastTime = performance.now();
        this.draw(performance.now());
    }

    stop() {
        this.running = false;
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
    }
}

let matrixRain = null;

document.addEventListener('DOMContentLoaded', () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    matrixRain = new MatrixRain();
    matrixRain.init();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            matrixRain.stop();
        } else {
            matrixRain.start();
        }
    });
});
