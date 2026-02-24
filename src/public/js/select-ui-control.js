    document.addEventListener('DOMContentLoaded', function() {

        const selects = document.querySelectorAll('.c-select');

        selects.forEach(select => {

            const radios = select.querySelectorAll('.c-select__radio');
            const selectLabel = select.querySelector('.c-select__label');
            const checkbox = select.querySelector('.c-select__checkbox');

            // old がある場合は UI を復元
            const oldValue = select.querySelector('.c-select__radio:checked');

            if (oldValue) {
                const label = select.querySelector(`label[for="${oldValue.id}"]`);

                if (label) {
                    selectLabel.textContent = label.textContent;
                    checkbox.checked = false;
                }
            }

            // ラジオ選択時の処理
            radios.forEach(radio => {
                const handler = () => {
                    const label = select.querySelector(`label[for="${radio.id}"]`);
                    selectLabel.textContent = label.textContent;
                    checkbox.checked = false;
                };

                radio.addEventListener('click', handler);
                radio.addEventListener('change', handler);
            });
        });
    });