<?php
/**
 * Template za komentare i formu za komentare
 */

// Ako je stranica direktno pozvana bez WordPress okvira, prekini izvršenje
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ('1' === $comments_number) {
                printf(
                    __('Jedan komentar za "%s"', 'textdomain'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf(
                    _n(
                        '%1$s komentar za "%2$s"',
                        '%1$s komentara za "%2$s"',
                        $comments_number,
                        'textdomain'
                    ),
                    number_format_i18n($comments_number),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 50,
                'callback'   => 'custom_comments_callback'
            ]);
            ?>
        </ol>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    // Ako su komentari zatvoreni
    if (!comments_open() && get_comments_number()) :
        ?>
        <p class="no-comments"><?php _e('Komentari su zatvoreni.', 'textdomain'); ?></p>
    <?php endif; ?>

    <div class="comment-form-container">
        <?php
               comment_form([
            'title_reply'         => __('Dodajte svoj komentar', 'textdomain'),
            'title_reply_to'      => __('Odgovorite na %s', 'textdomain'),
            'cancel_reply_link'   => __('Otkaži odgovor', 'textdomain'),
            'label_submit'        => __('Objavi komentar', 'textdomain'),
            'comment_field'       => '
                <p class="comment-form-rating">
                    <label for="rating">' . __('Ocijeni igru:') . '</label>
                    <span class="rating-stars">
                        <span class="star" data-value="1">&#9733;</span>
                        <span class="star" data-value="2">&#9733;</span>
                        <span class="star" data-value="3">&#9733;</span>
                        <span class="star" data-value="4">&#9733;</span>
                        <span class="star" data-value="5">&#9733;</span>
                    </span>
                    <input type="hidden" name="rating" id="rating-value" value="5">
                </p>
                <p class="comment-form-comment">
                    <label for="comment">' . _x('Komentar', 'noun') . '</label>
                    <textarea id="comment" name="comment" cols="45" rows="8" required="required"></textarea>
                </p>',
            'fields' => apply_filters('comment_form_default_fields', [
                'author' => '
                    <p class="comment-form-author">
                        <label for="author">' . __('Ime') . '</label>
                        <input id="author" name="author" type="text" value="" size="30" required="required" />
                    </p>',
                'email' => '
                    <p class="comment-form-email">
                        <label for="email">' . __('Email') . '</label>
                        <input id="email" name="email" type="email" value="" size="30" required="required" />
                    </p>',
                'url' => '
                    <p class="comment-form-url">
                        <label for="url">' . __('Web stranica') . '</label>
                        <input id="url" name="url" type="url" value="" size="30" />
                    </p>',
                'cookies' => '
                    <p class="comment-form-cookies-consent">
                        <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />
                        <label for="wp-comment-cookies-consent">' . __('Spremi moje ime, email i web stranicu u ovom pregledniku za sljedeći put kada budem komentirao.') . '</label>
                    </p>',
                'nonce' => wp_nonce_field('comment_nonce_action', 'comment_nonce', true, false)
            ])
        ]);
        ?>
    </div>
</div>

<div id="edit-comment-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Uredi komentar</h2>
        <textarea id="edit-comment-text" rows="4" cols="50"></textarea>
        <button id="save-comment">Spremi</button>
    </div>
</div>

<div id="delete-comment-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Jeste li sigurni da želite obrisati komentar?</h2>
        <button id="confirm-delete-comment">Da</button>
        <button id="cancel-delete-comment">Ne</button>
    </div>
</div>

<style>
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0, 0, 0, 0.8); 
}

.modal-content {
    background-color: #1e1e1e;
    margin: 15% auto; 
    padding: 20px;
    border: 1px solid #3399ff;
    width: 80%; 
    max-width: 500px;
    border-radius: 10px;
    box-shadow: 0 0 10px #33ccff, 0 0 20px #00ffff;
    color: #ffffff;
    font-family: 'Orbitron', sans-serif;
    text-align: center;
}

.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #ffffff;
    text-decoration: none;
    cursor: pointer;
}

#edit-comment-text {
    width: 100%;
    padding: 10px;
    background-color: #333;
    border: 1px solid #3399ff;
    border-radius: 8px;
    color: #fff;
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    margin-bottom: 20px;
}

#save-comment, #confirm-delete-comment, #cancel-delete-comment {
    background-color: #3399ff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 1rem;
    text-transform: uppercase;
    cursor: pointer;
    border-radius: 8px;
    box-shadow: 0 0 5px #3399ff, 0 0 15px #66ccff;
    transition: background-color 0.3s, box-shadow 0.3s;
    margin-right: 10px;
}

#save-comment:hover, #confirm-delete-comment:hover, #cancel-delete-comment:hover {
    background-color: #66ccff;
    box-shadow: 0 0 20px #66ccff, 0 0 30px #3399ff;
}
.rating-stars .star {
    font-size: 24px;
    color: #ccc; /* Siva boja za neaktivne zvjezdice */
    cursor: pointer;
}

.rating-stars .star.active {
    color: gold; /* Zlatna boja za aktivne zvjezdice */
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editModal = document.getElementById("edit-comment-modal");
    var deleteModal = document.getElementById("delete-comment-modal");
    var editBtns = document.querySelectorAll(".edit-comment");
    var deleteBtns = document.querySelectorAll(".delete-comment");
    var closeBtns = document.querySelectorAll(".close");
    var saveBtn = document.getElementById("save-comment");
    var confirmDeleteBtn = document.getElementById("confirm-delete-comment");
    var cancelDeleteBtn = document.getElementById("cancel-delete-comment");
    var currentCommentId;

    editBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentCommentId = this.getAttribute("data-comment-id");
            editModal.style.display = "block";
        });
    });

    deleteBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentCommentId = this.getAttribute("data-comment-id");
            deleteModal.style.display = "block";
        });
    });

    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            editModal.style.display = "none";
            deleteModal.style.display = "none";
        });
    });

    saveBtn.addEventListener('click', function() {
        // AJAX request to save the comment
        editModal.style.display = "none";
    });

    confirmDeleteBtn.addEventListener('click', function() {
        // AJAX request to delete the comment
        deleteModal.style.display = "none";
    });

    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.style.display = "none";
    });

    window.addEventListener('click', function(event) {
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
        if (event.target == deleteModal) {
            deleteModal.style.display = "none";
        }
    });
});
</script>

<?php
function custom_comments_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <footer class="comment-meta">
            <div class="comment-rating">
                    <?php
                    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
                    if ($rating) {
                        echo str_repeat('&#9733;', $rating);
                    }
                    ?>
                </div>
                <div class="comment-author vcard">
                    <?php echo get_avatar($comment, 50); ?>
                    <?php printf(__('%s <span class="says">kaže:</span>', 'textdomain'), sprintf('<b class="fn">%s</b>', get_comment_author_link())); ?>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php printf(__('%1$s u %2$s', 'textdomain'), get_comment_date(), get_comment_time()); ?>
                        </time>
                    </a>
                </div><!-- .comment-metadata -->
            </footer><!-- .comment-meta -->

            <div class="comment-content" data-original-content="<?php echo esc_attr(get_comment_text($comment->comment_ID)); ?>">
                <?php comment_text(); ?>
            </div><!-- .comment-content -->

            <?php if (is_user_logged_in() && $comment->user_id == get_current_user_id()) : ?>
                <div class="comment-edit-link">
                    <a href="#" class="edit-comment" data-comment-id="<?php comment_ID(); ?>"><?php _e('Uredi', 'textdomain'); ?></a>
                    <a href="#" class="delete-comment" data-comment-id="<?php comment_ID(); ?>"><?php _e('Obriši', 'textdomain'); ?></a>
                </div>
            <?php endif; ?>

            <!-- .reply -->
        </article><!-- .comment-body -->
    </li>
    <?php
}
?>