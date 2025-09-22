document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const postId = this.dataset.postid;
            const commentInput = this.querySelector('.comment-input');
            const commentList = this.closest('.comments-section').querySelector('.comment-list');
            const commentsTitle = this.closest('.comments-section').querySelector('.comments-title');
            const commentStat = this.closest('.post-card').querySelector('.stat i.fa-comment').parentElement;

            let formData = new FormData();
            formData.append("post_id", postId);
            formData.append("comment", commentInput.value);

            try {
                const response = await fetch("/space/actions/comment.php", {
                    method: "POST",
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const newComment = document.createElement("div");
                    newComment.className = "comment";
                    newComment.innerHTML = `<strong>${data.username}:</strong> ${data.comment}`;
                    commentList.prepend(newComment);

                    commentInput.value = "";

                    let newCount = parseInt(commentsTitle.textContent.match(/\d+/)[0]) + 1;
                    commentsTitle.textContent = `Comments (${newCount})`;
                    commentStat.innerHTML = `<i class="far fa-comment"></i> ${newCount}`;
                } else {
                    alert("Failed to add comment");
                }
            } catch (err) {
                console.error("Comment error:", err);
            }
        });
    });
});


document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', async function () {
        const icon = this.querySelector('i');
        const countSpan = this.querySelector('span');
        const postId = this.dataset.postid; 

        const formData = new FormData();
        formData.append("post_id", postId);

        try {
            const response = await fetch("/space/actions/like.php", {
                method: "POST",
                body: formData
            });

            const text = await response.text(); 
            console.log("Raw response:", text); 

            const data = JSON.parse(text);

            if (data.success) {
                countSpan.textContent = data.count;

                if (data.liked) {
                    this.classList.add('liked');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else {
                    this.classList.remove('liked');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
            } else {
                console.error("Like failed:", data.message, data.error);
            }
        } catch (err) {
            console.error("Like failed (JS):", err);
        }
    });
});
