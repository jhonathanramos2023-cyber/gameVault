const FONDO_DURACION_MS = 5000;
const FONDO_DESVANECIMIENTO_MS = 1500;

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
        this.fondoVisible = true;
        this.fondoStartTime = null;
        this.epicIntroFinished = false;
        this.reducedMotion = false;
    }

    init() {
        this.resize();
        window.addEventListener('resize', () => this.resize());
        this.setupColumns();
        this.start();
        
        const fondo = document.querySelector('.fondo-seccion');
        if (fondo) {
            this.fondoStartTime = performance.now();
        }
    }

    setEpicIntroFinished() {
        this.epicIntroFinished = true;
        const fondo = document.querySelector('.fondo-seccion');
        if (fondo) {
            this.fondoStartTime = performance.now();
        }
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

        // Usar destination-out para borrar estelas sin pintar fondo opaco
        this.ctx.globalCompositeOperation = 'destination-out';
        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
        this.ctx.fillRect(0, 0, window.innerWidth, window.innerHeight);
        this.ctx.globalCompositeOperation = 'source-over';

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

        // Desvanecer imagen de fondo y capa oscura después de 5 segundos
        if (this.fondoStartTime && this.fondoVisible && !this.reducedMotion) {
            const fondoElapsed = timestamp - this.fondoStartTime;
            if (fondoElapsed >= FONDO_DURACION_MS) {
                const fadeElapsed = fondoElapsed - FONDO_DURACION_MS;
                const fadeProgress = Math.min(fadeElapsed / FONDO_DESVANECIMIENTO_MS, 1);
                
                const fondo = document.querySelector('.fondo-seccion');
                if (fondo) {
                    fondo.style.opacity = 1 - fadeProgress;
                    fondo.style.transition = 'opacity 0.1s linear';
                    // Desvanecer también la capa oscura (::after)
                    const baseOpacity = fondo.classList.contains('principal') || fondo.classList.contains('login') ? 0.35 : 0.75;
                    const overlayOpacity = baseOpacity * (1 - fadeProgress);
                    fondo.style.setProperty('--fondo-overlay-opacity', overlayOpacity);
                }
                
                if (fadeProgress >= 1) {
                    this.fondoVisible = false;
                    if (fondo) {
                        fondo.style.display = 'none';
                    }
                }
            }
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
        // Con prefers-reduced-motion, la imagen se queda estática sin lluvia ni desvanecimiento
        const fondo = document.querySelector('.fondo-seccion');
        if (fondo) {
            fondo.style.animation = 'none';
        }
        matrixRain = new MatrixRain();
        matrixRain.reducedMotion = true;
        matrixRain.init();

        // Exponer función para que epic-intro.js pueda notificar cuando termine
        window.setEpicIntroFinished = () => matrixRain.setEpicIntroFinished();

        return;
    }

    matrixRain = new MatrixRain();
    matrixRain.init();

    // Exponer función para que epic-intro.js pueda notificar cuando termine
    window.setEpicIntroFinished = () => matrixRain.setEpicIntroFinished();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            matrixRain.stop();
        } else {
            matrixRain.start();
        }
    });
});
