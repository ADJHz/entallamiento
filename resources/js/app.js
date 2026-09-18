import { initFlowbite } from 'flowbite';

initFlowbite();

document.querySelectorAll('.identification-dropdown-option').forEach((option) => {
    option.addEventListener('click', () => {
        const value = option.dataset.value;
        const label = option.dataset.label;
        const input = document.querySelector('#identification_type');
        const selectedLabel = document.querySelector('#identification_type_label');
        const fieldLabel = document.querySelector('#identification_field_label_text');
        const menu = document.querySelector('#identification_type_menu');
        const toggle = document.querySelector('#identification_type_toggle');

        if (!value || !label || !input || !selectedLabel || !fieldLabel || !menu || !toggle) {
            return;
        }

        input.value = value;
        selectedLabel.textContent = label;
        fieldLabel.textContent = label;
        menu.classList.add('hidden');
        menu.classList.remove('block');
        toggle.setAttribute('aria-expanded', 'false');
    });
});

const elementLoginForm = document.querySelector('#element-login-form');

if (elementLoginForm) {
    const cspInput = elementLoginForm.querySelector('#email');
    const cuipInput = elementLoginForm.querySelector('#password');
    const lookupButton = elementLoginForm.querySelector('#element-lookup-button');
    const submitButton = elementLoginForm.querySelector('#element-submit-button');
    const nameInput = elementLoginForm.querySelector('#element_name');
    const message = elementLoginForm.querySelector('#element-lookup-message');

    const resetLookup = () => {
        nameInput.value = '';
        submitButton.disabled = true;
    };

    cspInput.addEventListener('input', resetLookup);
    cuipInput.addEventListener('input', resetLookup);

    lookupButton.addEventListener('click', async() => {
        const csp = cspInput.value.trim();
        const cuip = cuipInput.value.trim();

        message.textContent = '';
        resetLookup();

        if (!csp || !cuip) {
            message.textContent = 'Ingresa el CSP y el CUIP para buscar.';
            return;
        }

        lookupButton.disabled = true;
        lookupButton.textContent = '...';

        try {
            const response = await fetch(elementLoginForm.dataset.lookupUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': elementLoginForm.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ csp, cuip }),
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'No se encontró el elemento.');
            }

            nameInput.value = data.name;
            submitButton.disabled = false;
        } catch (error) {
            message.textContent = error.message;
        } finally {
            lookupButton.disabled = false;
            lookupButton.textContent = 'Buscar';
        }
    });
}