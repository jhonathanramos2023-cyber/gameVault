import 'bootstrap';
import './matrix.js';
import './epic-intro.js';
import './catalog-effects.js';
import './login-effects.js';
import './panel-effects.js';

document.addEventListener('DOMContentLoaded', () => {
    const selector = document.querySelector('[data-image-preview]');
    const preview = document.querySelector('[data-preview-grid]');
    if (!selector || !preview) return;

    selector.addEventListener('change', () => {
        preview.replaceChildren();
        const files = Array.from(selector.files ?? []);

        files.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'col-6 col-md-4 col-xl-3';
            const image = document.createElement('img');
            image.className = 'preview-image';
            image.alt = `Vista previa ${index + 1}`;
            image.src = URL.createObjectURL(file);
            const label = document.createElement('label');
            label.className = 'form-label small mt-2';
            label.textContent = `Texto alternativo ${index + 1}`;
            const input = document.createElement('input');
            input.className = 'form-control form-control-sm';
            input.name = `textos_alternativos[${index}]`;
            input.maxLength = 255;
            input.placeholder = 'Describe la imagen';
            card.append(image, label, input);
            preview.append(card);
        });
    });
});
