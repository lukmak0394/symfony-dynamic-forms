$(document).ready(() => {
    const templateSelect = $('#template-select');
    const form = $('#dynamic-fields form[name="dynamic_entry_data"]');
    const entryValues = window.entryFieldValues || [];

    const prepareSelectField = (value, params) => {
        input = $('<select>');
        if(params && Array.isArray(params)) {
            params.forEach(opt => {
                const option = $('<option>').val(opt).text(opt);
                (opt === value) ? option.prop('selected', true) : option.prop('selected', false);
                input.append(option);
            });
        }
        return input;
    }

    const prepareInputField = (field, value) => {
        if (field.type === 'date') {
            return $('<input>').attr({ type: 'date', value });
        }
        if (field.type === 'datetime') {
            return $('<input>').attr({ type: 'datetime-local', value });
        }
        return $('<input>').attr({ type: 'text', value });
    };


    const renderField = (field, existingValues, container) => {

        const safeValues = Array.isArray(existingValues) ? existingValues : [];
        const valueObj = safeValues.find(v => v.templateField.id === field.id);
        const value = valueObj ? valueObj.value : '';

        const wrapper = $('<div>');
        const label = $('<label>').text(field.displayName);
        wrapper.append(label);

        let input;

        switch (field.type) {
            case 'text':
                input = prepareInputField(field, value);
                break;
            case 'select':
                input = prepareSelectField(value, field.params);
                break;
            case 'date':
            case 'datetime':
                input = prepareInputField(field, value);
                break;
            case 'cost':
                input = prepareInputField(field, value);
                break;
            case 'number':
                input = prepareInputField(field, value);
                break;
            case 'email':
                input = prepareInputField(value);
                break;
            case 'url':
                input = prepareInputField(value);
                break;
            default:
                console.warn(`Nieobsługiwany typ pola: ${field.type}`);
                return;
        }
       
        input.attr('name', `dynamic_entry_data[field_${field.id}]`);
        input.prop('required', field.required);

        wrapper.append(input);
        container.find('button[type="submit"]').before(wrapper); 
    };

    const loadFields = (templateId) => {
        $.getJSON(`/${window.locale}/api/templates/${templateId}/fields`)
            .done(fields => {
                form.children('div').not(':has(button[type="submit"])').remove();
                fields.forEach(field => renderField(field, entryValues, form));
            })
            .fail(err => {
                console.error('Błąd podczas ładowania pól szablonu:', err);
            });
    };

    templateSelect.on('change', (e) => {
        loadFields(e.target.value);
    });
});
