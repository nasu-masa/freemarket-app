document.addEventListener('DOMContentLoaded', () => {
    const ui = document.getElementById('address-ui');
    if (!ui) return;

    ['address', 'building'].forEach(key => {
        document.getElementById(`${key}-hidden`).value = ui.dataset[key]?.trim() || '';
    });
});
