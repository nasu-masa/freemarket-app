document.querySelector('.p-item-detail__like-button').addEventListener('click', function () {
    const itemId = this.dataset.id;
    const isLiked = this.dataset.liked === '1';
    const token = document.querySelector('meta[name="csrf-token"]').content;

    const url = `/item/${itemId}/like`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
        .then(res => {
            if (res.status === 401) {
                window.location.href = '/login';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;

            //いいね数を更新
            document.querySelector('.p-item-detail__count').textContent = data.likeCount;

            //ボタンの状態更新
            if (data.isLiked) {
                this.classList.add('is-liked');
                this.dataset.liked = '1';
            } else {
                this.classList.remove('is-liked');
                this.dataset.liked = '0';
            }
        });
});