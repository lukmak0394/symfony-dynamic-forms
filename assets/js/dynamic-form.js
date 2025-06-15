$(document).ready(() => {
    const templateSelect = $('#template-select');
    const fieldWrapper = $('#form-fields');
    const entryValues = window.entryFieldValues || [];

    const renderField = (field, existingValues, container) => {
        const safeValues = Array.isArray(existingValues) ? existingValues : [];
        const valueObj = safeValues.find(v => v.templateField.id === field.id);
        const value = valueObj ? valueObj.value : '';

        const wrapper = $('<div>').css('margin-bottom', '1rem');
        const label = $('<label>').text(field.displayName);
        wrapper.append(label);

        let $input;
        if (field.type === 'select') {
            $input = $('<select>');
            try {
                (field.params || []).forEach(opt => {
                    const $option = $('<option>').val(opt).text(opt);
                    if (opt === value) $option.prop('selected', true);
                    $input.append($option);
                });
            } catch (e) {
                console.error('Error parsing select options:', e);
            }
        } else {
            const type = field.type === 'date' ? 'date'
                      : field.type === 'datetime' ? 'datetime-local'
                      : 'text';
            $input = $('<input>').attr({ type, value });
        }

        $input.attr('name', `field_${field.id}`);
        if (field.required) $input.prop('required', true);

        wrapper.append('<br>').append($input);
        container.append(wrapper);
    };

    const loadFields = (templateId) => {
        $.getJSON(`/${window.locale}/api/templates/${templateId}/fields`)
            .done(fields => {
                fieldWrapper.empty();
                fields.forEach(field => renderField(field, entryValues, fieldWrapper));
            })
            .fail(err => {
                console.error('Błąd podczas ładowania pól szablonu:', err);
            });
    };

    templateSelect.on('change', (e) => {
        loadFields(e.target.value);
    });
});
