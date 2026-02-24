    document.getElementById('priceInput').addEventListener('input', function(e) {
        // 数字以外を除去
        let value = e.target.value.replace(/[^0-9]/g, '');

        // カンマ付与
        e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    });