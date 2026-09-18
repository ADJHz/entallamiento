const wizard = document.getElementById('uniforme-wizard');

if (wizard) {
    const direccionToggle = document.getElementById('direccion_select_toggle');
    const direccionLabel = document.getElementById('direccion_select_label');
    const direccionMenu = document.getElementById('direccion_select_menu');
    const direccionOptions = document.querySelectorAll('.direccion-dropdown-option');
    const direccionInput = document.getElementById('direccion_id_input');
    const colorFranjaInput = document.getElementById('color_franja_id_input');
    const grid = document.getElementById('uniforme-opciones-grid');
    const message = document.getElementById('uniforme-opciones-message');
    const submitButton = document.getElementById('uniforme-submit-button');
    const opcionesUrl = wizard.dataset.opcionesUrl;
    const selectionLocked = wizard.dataset.locked === '1';

    const updateSubmitState = () => {
        if (selectionLocked) {
            submitButton.disabled = true;
            return;
        }

        submitButton.disabled = !(direccionInput.value && colorFranjaInput.value);
    };

    const renderFallbackImage = (color) => `
        <div class="uniforme-opcion-visual flex h-48 w-full items-center justify-center rounded-xl bg-gradient-to-br from-slate-100 via-white to-slate-200 text-xs font-bold tracking-[0.12em] text-slate-500 uppercase">
            ${color}
        </div>
    `;

    const renderOpciones = (opciones) => {
            grid.innerHTML = '';

            if (!opciones.length) {
                grid.innerHTML = `
            <p class="col-span-full text-sm text-gray-500">
                No hay opciones disponibles para esta dirección.
            </p>
        `;
                return;
            }

            opciones.forEach((opcion, index) => {
                        const card = document.createElement('button');
                        card.type = 'button';
                        card.dataset.id = opcion.id;

                        card.className = `
            uniforme-opcion-card
            border border-gray-200
            bg-white
            p-4
            text-left
            rounded-lg
            shadow-sm
            hover:bg-gray-50
        `;

                        if (selectionLocked) {
                            card.classList.add('cursor-default');
                            card.disabled = true;
                        }

                        card.innerHTML = `
            <!-- TÍTULO DEL CARD -->
            <div class="mb-3 text-center">
                <h4 class="text-base font-bold text-gray-900">
                    ${opcion.descripcion ?? ''}
                </h4>
            </div>

            <!-- IMAGEN -->
            <div class="uniforme-opcion-visual">
                ${
                    opcion.imagen
                        ? `<img
                            src="${opcion.imagen}"
                            alt="${opcion.color} ${opcion.franja}"
                            class="uniforme-opcion-image mx-auto"
                            loading="${index < 2 ? 'eager' : 'lazy'}"
                            decoding="async"
                            fetchpriority="${index < 2 ? 'high' : 'low'}"
                           >`
                        : renderFallbackImage(opcion.color)
                }
            </div>

            <!-- INFORMACIÓN -->
            <div class="uniforme-opcion-meta mt-3 text-center">
                <p class="uniforme-opcion-color font-semibold text-gray-900">
                    ${opcion.color}
                </p>

                <p class="uniforme-opcion-franja text-sm font-bold text-gray-500">
                    Franja: ${opcion.franja}
                </p>
            </div>
        `;

        card.addEventListener('click', () => {
            if (selectionLocked) {
                return;
            }

            grid.querySelectorAll('.uniforme-opcion-card').forEach((el) => {
                el.classList.remove('is-selected');
            });

            card.classList.add('is-selected');
            colorFranjaInput.value = opcion.id;
            updateSubmitState();
        });

        grid.appendChild(card);

        if (String(opcion.id) === String(colorFranjaInput.value)) {
            card.click();
        }
    });
};

    const cargarOpciones = async(direccionId) => {
        message.textContent = '';

        if (!direccionId) {
            grid.innerHTML = '<p class="col-span-full text-sm text-gray-500">Selecciona una dirección para ver las opciones disponibles.</p>';
            colorFranjaInput.value = '';
            updateSubmitState();
            return;
        }

        grid.innerHTML = '<p class="col-span-full text-sm text-gray-500">Cargando opciones…</p>';

        try {
            const response = await fetch(`${opcionesUrl}?direccion_id=${direccionId}`, {
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'No se pudieron cargar las opciones.');
            }

            renderOpciones(data.options || []);
        } catch (error) {
            grid.innerHTML = '';
            message.textContent = error.message;
            colorFranjaInput.value = '';
        } finally {
            updateSubmitState();
        }
    };

    const seleccionarDireccion = (direccionId) => {
        if (selectionLocked) {
            return;
        }

        direccionInput.value = direccionId;
        colorFranjaInput.value = '';
        cargarOpciones(direccionId);
    };

    direccionOptions.forEach((option) => {
        option.addEventListener('click', () => {
            if (selectionLocked) {
                return;
            }

            const direccionId = option.dataset.value;
            const direccionNombre = option.dataset.label;

            if (direccionLabel) {
                direccionLabel.textContent = direccionNombre || 'Selecciona tu dirección';
            }

            if (direccionMenu) {
                direccionMenu.classList.add('hidden');
                direccionMenu.classList.remove('block');
            }

            if (direccionToggle) {
                direccionToggle.setAttribute('aria-expanded', 'false');
            }

            seleccionarDireccion(direccionId);
        });
    });

    updateSubmitState();

    if (direccionInput.value) {
        cargarOpciones(direccionInput.value);
    }
}
