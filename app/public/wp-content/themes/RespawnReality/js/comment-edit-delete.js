jQuery(document).ready(function($) {
    var editModal = $('#edit-comment-modal');
    var deleteModal = $('#delete-comment-modal');
    var commentId;

    // Edit comment
    $(document).on('click', '.edit-comment', function(e) {
        e.preventDefault();
        commentId = $(this).data('comment-id');
        var commentContent = $('#comment-' + commentId + ' .comment-content').text().trim();
        $('#edit-comment-text').val(commentContent);
        editModal.show();
    });

    // Save edited comment
    $('#save-comment').on('click', function() {
        var newContent = $('#edit-comment-text').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'edit_comment',
                comment_ID: commentId,
                comment_content: newContent,
                _wpnonce: $('#comment_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    // Reload the comment to reflect the changes
                    location.reload();
                } else {
                    alert('Došlo je do pogreške prilikom uređivanja komentara.');
                }
                editModal.hide();
            }
        });
    });

    // Cancel edit
    $('.close').on('click', function() {
        editModal.hide();
        deleteModal.hide();
    });

    // Delete comment
    $(document).on('click', '.delete-comment', function(e) {
        e.preventDefault();
        commentId = $(this).data('comment-id');
        deleteModal.show();
    });

    // Confirm delete comment
    $('#confirm-delete-comment').on('click', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_comment',
                comment_ID: commentId,
                _wpnonce: $('#comment_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#comment-' + commentId).remove();
                    location.reload();
                } else {
                    alert('Došlo je do pogreške prilikom brisanja komentara.');
                }
                deleteModal.hide();
            }
        });
    });

    // Cancel delete comment
    $('#cancel-delete-comment').on('click', function() {
        deleteModal.hide();
    });

    // Close modal when clicking outside of it
    $(window).on('click', function(event) {
        if (event.target == editModal[0]) {
            editModal.hide();
        }
        if (event.target == deleteModal[0]) {
            deleteModal.hide();
        }
    });
    var stars = document.querySelectorAll('.rating-stars .star');
    var ratingValue = document.getElementById('rating-value');

    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            var value = this.getAttribute('data-value');
            ratingValue.value = value;
            stars.forEach(function(s) {
                s.classList.remove('active');
            });
            for (var i = 0; i < value; i++) {
                stars[i].classList.add('active');
            }
        });
    });
});
