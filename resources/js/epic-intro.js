class EpicIntro {
    constructor() {
        this.overlay = null;
        this.lines = [
            '> INICIANDO GAMEVAULT...',
            '> CARGANDO ARCHIVOS...',
            '> ACCESO CONCEDIDO'
        ];
        this.currentLine = 0;
        this.charIndex = 0;
        this.typingSpeed = 50;
        this.lineDelay = 400;
        this.hasShown = sessionStorage.getItem('epicIntroShown');
    }

    init() {
        if (this.hasShown || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const fondo = document.body.getAttribute('data-fondo');
        if (fondo !== 'principal') {
            return;
        }

        this.createOverlay();
        this.startSequence();
    }

    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: 'Share Tech Mono', monospace;
            color: #00ff41;
            text-shadow: 0 0 10px #00ff41;
        `;

        const terminal = document.createElement('div');
        terminal.style.cssText = `
            font-size: 1.2rem;
            line-height: 2;
            white-space: pre;
        `;
        terminal.id = 'epic-terminal';

        const progressBar = document.createElement('div');
        progressBar.style.cssText = `
            width: 300px;
            height: 4px;
            background: #0b3d0b;
            margin-top: 2rem;
            border-radius: 2px;
            overflow: hidden;
        `;

        const progressFill = document.createElement('div');
        progressFill.style.cssText = `
            width: 0%;
            height: 100%;
            background: #00ff41;
            box-shadow: 0 0 10px #00ff41;
            transition: width 0.3s ease;
        `;
        progressFill.id = 'epic-progress';

        progressBar.appendChild(progressFill);
        this.overlay.appendChild(terminal);
        this.overlay.appendChild(progressBar);
        document.body.appendChild(this.overlay);
        document.body.style.overflow = 'hidden';
    }

    startSequence() {
        this.typeLine();
        this.animateProgress();

        const skipHandler = () => this.skip();
        document.addEventListener('click', skipHandler, { once: true });
        document.addEventListener('keydown', skipHandler, { once: true });
    }

    typeLine() {
        if (this.currentLine >= this.lines.length) {
            setTimeout(() => this.flashAndReveal(), 500);
            return;
        }

        const terminal = document.getElementById('epic-terminal');
        const line = this.lines[this.currentLine];

        if (this.charIndex < line.length) {
            terminal.textContent += line[this.charIndex];
            this.charIndex++;
            setTimeout(() => this.typeLine(), this.typingSpeed);
        } else {
            terminal.textContent += '\n';
            this.currentLine++;
            this.charIndex = 0;
            setTimeout(() => this.typeLine(), this.lineDelay);
        }
    }

    animateProgress() {
        const progressFill = document.getElementById('epic-progress');
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 5;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
            }
            progressFill.style.width = progress + '%';
        }, 100);
    }

    flashAndReveal() {
        this.overlay.style.transition = 'opacity 0.5s ease';
        this.overlay.style.opacity = '0';
        
        setTimeout(() => {
            this.overlay.remove();
            document.body.style.overflow = '';
            sessionStorage.setItem('epicIntroShown', 'true');
            
            // Notificar a matrix.js que la entrada épica terminó
            if (typeof window.setEpicIntroFinished === 'function') {
                window.setEpicIntroFinished();
            }
        }, 500);
    }

    skip() {
        if (this.overlay) {
            this.flashAndReveal();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const epicIntro = new EpicIntro();
    epicIntro.init();
});
