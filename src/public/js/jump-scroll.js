document.querySelector('.p-item-detail__comment-scroll-button')
        .addEventListener('click', () => {
            document.getElementById('commentList')
                .scrollIntoView({
                    behavior: 'smooth'
                })
        });