    document.addEventListener('DOMContentLoaded', function() {

        const radios = document.querySelectorAll('.c-select__radio');
        const selectLabel = document.querySelector('.c-select__label');
        const checkbox = document.getElementById('payment_method');
        const summary = document.getElementById('summary-payment');

        var oldPayment = document.getElementById('old-payment').value;

        // old がある場合は UI を復元
        if (oldPayment) {
            var target = document.querySelector('input[name="payment_method"][value="' + oldPayment + '"]');

            if (target) {
                target.checked = true;

                var selectedLabel = document.querySelector('label[for="' + target.id + '"]').textContent;
                selectLabel.textContent = selectedLabel;
                checkbox.checked = false;
            }
        }

        // ラジオ選択時の処理
        radios.forEach(radio => {
            const handler = () => {
                const selectedLabel = document.querySelector(`label[for="${radio.id}"]`).textContent;
                selectLabel.textContent = selectedLabel;
                summary.textContent = selectedLabel;
                checkbox.checked = false;
            };

            radio.addEventListener('click', handler);
            radio.addEventListener('change', handler);
        });
    });