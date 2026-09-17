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
