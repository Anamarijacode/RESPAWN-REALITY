<?php
// Funkcija za učitavanje stilova i skripti
function moja_tema_enqueue_scripts() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    
    // Enqueue vašeg vlastitog CSS
    wp_enqueue_style('moja-tema-style', get_stylesheet_uri());
    
    // Enqueue Bootstrap JS za responzivne funkcionalnosti (menu toggle, itd.)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    
    // Enqueue custom JS for comment editing and deleting
    wp_enqueue_script('comment-edit-delete', get_template_directory_uri() . '/js/comment-edit-delete.js', array('jquery'), null, true);
    
}
add_action('wp_enqueue_scripts', 'moja_tema_enqueue_scripts');

require_once get_template_directory() . '/inc/wp-bootstrap-navwalker-master/class-wp-bootstrap-navwalker.php';

add_filter('wp_get_nav_menu_items', function($items) {
    foreach ($items as $item) {
        error_log("Menu Item: " . $item->title . " - URL: " . $item->url);
    }
    return $items;
});

function moja_tema_setup() {
    register_nav_menus(array(
        'primary' => __('Glavni izbornik', 'moja-tema'),
    ));
}
add_action('after_setup_theme', 'moja_tema_setup');

function add_ajaxurl_to_script() {
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
add_action('wp_head', 'add_ajaxurl_to_script');
function fix_menu_page_links($items, $args) {
    foreach ($items as $item) {
        if ($item->object == 'page') { 
            $categories = get_the_category($item->object_id);
            if (!empty($categories)) {
                $item->url = get_permalink($item->object_id); // Dodjeljuje ispravan URL
            }
        }
    }
    return $items;
}
    
add_filter('wp_get_nav_menu_items', 'fix_menu_page_links', 10, 2);
function enable_categories_for_pages() {
    register_taxonomy_for_object_type('category', 'page');
}
add_action('init', 'enable_categories_for_pages');

function get_pages_with_categories() {
    $args = array(
        'post_type'   => 'page', // Tražimo samo stranice
        'post_status' => 'publish',
        'numberposts' => -1,
    );

    $pages = get_posts($args);
    $pages_with_categories = [];

    foreach ($pages as $page) {
        $categories = get_the_category($page->ID); // Dohvaća kategorije povezane sa stranicom
        if (!empty($categories)) { // Ako stranica ima kategorije
            $pages_with_categories[] = get_permalink($page->ID);
        }
    }

    return $pages_with_categories;
}
add_action('init', 'enable_categories_for_pages');
// Prikaz u WordPress predlošku
$urls = get_pages_with_categories();
foreach ($urls as $url) {
    echo '<p>Stranica s kategorijama: <a href="' . esc_url($url) . '">' . esc_url($url) . '</a></p>';
}


// Funkcija za registraciju widget područja (sidebar)
function moja_tema_widgets_init() {
    register_sidebar(array(
        'name' => 'Bočna traka',
        'id' => 'sidebar-1',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'moja_tema_widgets_init');


function register_video_games_post_type() {
    $labels = array(
        'name'               => 'Video Igre',
        'singular_name'      => 'Video Igra',
        'menu_name'          => 'Video Igre',
        'name_admin_bar'     => 'Video Igra',
        'add_new'            => 'Dodaj novu',
        'add_new_item'       => 'Dodaj novu igru',
        'new_item'           => 'Nova igra',
        'edit_item'          => 'Uredi igru',
        'view_item'          => 'Pogledaj igru',
        'all_items'          => 'Sve igre',
        'search_items'       => 'Pretraži igre',
        'not_found'          => 'Nema igara',
        'not_found_in_trash' => 'Nema igara u smeću'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'videoigre'),
        'supports'           => array('title', 'editor', 'thumbnail', 'comments', 'custom-fields'),
        'taxonomies'         => array('category'), // Ensure 'category' is included here
        'menu_icon'          => 'dashicons-games',
        'show_in_rest'       => true
    );

    register_post_type('videoigre', $args);
}
add_action('init', 'register_video_games_post_type');

// 2. Dodavanje prilagođenih polja pomoću meta boxa
function add_video_game_meta_box() {
    add_meta_box(
        'video_game_meta',
        'Detalji o Video Igri',
        'video_game_meta_callback',
        'videoigre',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_video_game_meta_box');

function video_game_meta_callback($post) {
    wp_nonce_field('save_video_game_meta', 'video_game_meta_nonce');

    $datum_izrade = get_post_meta($post->ID, '_datum_izrade', true);
    $datum_objavljivanja = get_post_meta($post->ID, '_datum_objavljivanja', true);
    $autor = get_post_meta($post->ID, '_autor', true);
    $game_image = get_post_meta($post->ID, 'game_image', true);
    $pegi_rating = get_post_meta($post->ID, '_pegi_rating', true);

    echo '<label for="datum_izrade">Datum Izrade:</label> ';
    echo '<input type="date" id="datum_izrade" name="datum_izrade" value="' . esc_attr($datum_izrade) . '" /><br><br>';

    echo '<label for="datum_objavljivanja">Datum Objavljivanja:</label> ';
    echo '<input type="date" id="datum_objavljivanja" name="datum_objavljivanja" value="' . esc_attr($datum_objavljivanja) . '" /><br><br>';

    echo '<label for="autor">Autor:</label> ';
    echo '<input type="text" id="autor" name="autor" value="' . esc_attr($autor) . '" /><br><br>';

    echo '<label for="game_image">URL slike igre:</label> ';
    echo '<input type="text" id="game_image" name="game_image" value="' . esc_attr($game_image) . '" />';
    echo '<input type="button" id="upload_image_button" class="button" value="Odaberi sliku" /><br><br>';

    echo '<label for="pegi_rating">PEGI Ocjena:</label> ';
    echo '<select id="pegi_rating" name="pegi_rating">
            <option value="3" ' . selected($pegi_rating, '3', false) . '>3</option>
            <option value="7" ' . selected($pegi_rating, '7', false) . '>7</option>
            <option value="12" ' . selected($pegi_rating, '12', false) . '>12</option>
            <option value="16" ' . selected($pegi_rating, '16', false) . '>16</option>
            <option value="18" ' . selected($pegi_rating, '18', false) . '>18</option>
          </select><br><br>';
}
function enqueue_media_uploader() {
    wp_enqueue_media();
    wp_enqueue_script('media-uploader', get_template_directory_uri() . '/js/media-uploader.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_media_uploader');

function save_video_game_meta($post_id) {
    if (!isset($_POST['video_game_meta_nonce']) || !wp_verify_nonce($_POST['video_game_meta_nonce'], 'save_video_game_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['datum_izrade'])) {
        update_post_meta($post_id, '_datum_izrade', sanitize_text_field($_POST['datum_izrade']));
    }

    if (isset($_POST['datum_objavljivanja'])) {
        update_post_meta($post_id, '_datum_objavljivanja', sanitize_text_field($_POST['datum_objavljivanja']));
    }

    if (isset($_POST['autor'])) {
        update_post_meta($post_id, '_autor', sanitize_text_field($_POST['autor']));
    }

    if (isset($_POST['game_image'])) {
        update_post_meta($post_id, 'game_image', esc_url_raw($_POST['game_image']));
    }

    if (isset($_POST['pegi_rating'])) {
        update_post_meta($post_id, '_pegi_rating', sanitize_text_field($_POST['pegi_rating']));
    }
}
add_action('save_post', 'save_video_game_meta');
/*function display_pegi_rating() {
    if (is_singular('videoigre')) {
        $pegi_rating = get_post_meta(get_the_ID(), '_pegi_rating', true);
        if ($pegi_rating) {
            echo '<div class="pegi-rating">PEGI Ocjena: ' . esc_html($pegi_rating) . '</div>';
        }
    }
}
add_action('the_content', 'display_pegi_rating');*/

// 3. Implementacija ocjenjivanja zvjezdicama
function add_rating_to_comments($comment_text, $comment) {
    if ($comment->comment_type == 'comment') {
        $rating = get_comment_meta($comment->comment_ID, 'rating', true);
        if ($rating) {
            $stars = str_repeat('&#9733;', $rating);
        }
    }
    return $comment_text;
}
add_filter('comment_text', 'add_rating_to_comments', 10, 2);

/* function add_rating_field_to_comment_form() {
    echo '<p class="comment-form-rating">
            <label for="rating">Ocjena:</label>
            <select name="rating" id="rating">
                <option value="">Izaberite ocjenu</option>
                <option value="1">1 zvjezdica</option>
                <option value="2">2 zvjezdice</option>
                <option value="3">3 zvjezdice</option>
                <option value="4">4 zvjezdice</option>
                <option value="5">5 zvjezdica</option>
            </select>
          </p>';
}
add_action('comment_form_logged_in_after', 'add_rating_field_to_comment_form');
add_action('comment_form_after_fields', 'add_rating_field_to_comment_form');
 */
function save_comment_rating($comment_id) {
    if (isset($_POST['rating']) && is_numeric($_POST['rating'])) {
        add_comment_meta($comment_id, 'rating', intval($_POST['rating']));
    }
}
add_action('comment_post', 'save_comment_rating');

function display_video_games_by_category($atts) {
    $atts = shortcode_atts(array('category' => ''), $atts, 'videoigre_kategorije');

    ob_start(); // Start output buffering
    ?>
    <style>
        /* Add your CSS styles here */
        #game-filters, #genre-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        #game-filters select, #genre-filters select, #game-filters input, #genre-filters input {
            flex: 1 1 40%;
            margin: 5px;
            padding: 10px;
            border: 2px solid #3399ff;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: #ffffff;
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        #game-filters select:focus, #genre-filters select:focus, #game-filters input:focus, #genre-filters input:focus {
            border-color: #00ffff;
            box-shadow: 0 0 5px #00ffff;
        }

        .game-item {
            margin-bottom: 20px;
        }

        .game-item .card {
            background-color: #1e1e1e;
            border: 2px solid #3399ff;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s, border 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .game-item .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(51, 153, 255, 0.6);
            border: 2px solid #66ccff;
        }

        .game-item .card-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #66ccff;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
        }

        .game-item .btn-primary {
            background-color: #3399ff;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-family: 'Press Start 2P', cursive;
            padding: 10px 15px;
            text-transform: uppercase;
            transition: all 0.3s;
            box-shadow: 0 0 5px #3399ff, 0 0 15px #66ccff;
            margin-top: auto;
        }

        .game-item .btn-primary:hover {
            background-color: #66ccff;
            color: #000000;
            box-shadow: 0 0 20px #66ccff, 0 0 30px #3399ff;
        }

        @media (max-width: 768px) {
            #game-filters select, #genre-filters select, #game-filters input, #genre-filters input {
                flex: 1 1 100%;
            }
        }

        .selected-categories {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .selected-category {
            background-color: #3399ff;
            color: #ffffff;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px;
            display: flex;
            align-items: center;
        }

        .selected-category .remove-category {
            margin-left: 10px;
            cursor: pointer;
            color: #ff0000;
        }
    </style>
    <div class="container mt-5">
        <div id="game-filters" class="mb-4">
           
            <select id="filter-rating">
                <option value="">Sve Ocjene</option>
                <option value="5">Prosjek 5 </option>
                <option value="4">Prosjek 4 </option>
                <option value="3">Prosjek 3 </option>
                <option value="2">Prosjek 2 </option>
                <option value="1">Prosjek 1 </option>
            </select>
            <select id="filter-pegi">
                <option value="">Sve PEGI Oznake</option>
                <option value="3">PEGI 3</option>
                <option value="7">PEGI 7</option>
                <option value="12">PEGI 12</option>
                <option value="16">PEGI 16</option>
                <option value="18">PEGI 18</option>
            </select>
            <input type="text" id="search-games" placeholder="Pretraži igre">
        </div>
        <div class="selected-categories" id="selected-categories"></div>
        <div id="games-list" class="row">
            <?php
            $query = new WP_Query(array(
                'post_type' => 'videoigre',
                'category_name' => $atts['category'],
                'posts_per_page' => -1,
            ));

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $game_image_url = get_post_meta(get_the_ID(), 'game_image', true);
                    if (!$game_image_url) {
                        $game_image_url = get_template_directory_uri() . '/path/to/default-image.jpg';
                    }
                    $categories = get_the_category();
                    $category_slugs = array_map(function($category) {
                        return strtolower($category->slug);
                    }, $categories);
                    ?>
                    <div class="col-md-4 game-item"  data-rating="<?php echo get_average_rating(get_the_ID()); ?>" data-pegi="<?php echo get_post_meta(get_the_ID(), '_pegi_rating', true); ?>">
                        <div class="card mb-4 shadow-lg">
                            <img src="<?php echo esc_url($game_image_url); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php the_title(); ?></h5>
                                <p class="card-text"><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                                <div class="rating">
                                    <?php
                                    $average_rating = get_average_rating(get_the_ID());
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $average_rating) {
                                            echo '<span class="star">&#9733;</span>';
                                        } else {
                                            echo '<span class="star">&#9734;</span>';
                                        }
                                    }
                                    ?>
                                    <span class="average-rating">(<?php echo $average_rating; ?>)</span>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm mt-auto">Pročitaj više</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else : ?>
                <p class="text-center text-muted">Nema igara u ovoj kategoriji.</p>
            <?php endif;
            wp_reset_postdata(); ?>
        </div>
        <p id="no-games-message" class="text-center text-muted" style="display: none;">Nema traženih igara.</p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            var filterRating = document.getElementById('filter-rating');
            var filterPegi = document.getElementById('filter-pegi');
            var searchGames = document.getElementById('search-games');
            var gamesList = document.getElementById('games-list');
            var selectedCategories = document.getElementById('selected-categories');
            var noGamesMessage = document.getElementById('no-games-message');

            function filterGames() {
                
                var ratingValue = filterRating.value.toLowerCase();
                var pegiValue = filterPegi.value.toLowerCase();
                var searchValue = searchGames.value.toLowerCase();

                var games = gamesList.getElementsByClassName('game-item');
                var gamesFound = false;

                Array.from(games).forEach(game => {
                    var gameTitle = game.querySelector('.card-title').innerText.toLowerCase();
                    var gameContent = game.querySelector('.card-text').innerText.toLowerCase();
                    
                    var gameRating = game.getAttribute('data-rating').toLowerCase();
                    var gamePegi = game.getAttribute('data-pegi').toLowerCase();

                    
                    var ratingMatch = ratingValue === '' || gameRating === ratingValue;
                    var pegiMatch = pegiValue === '' || gamePegi === pegiValue;
                    var searchMatch = searchValue === '' || gameTitle.includes(searchValue) || gameContent.includes(searchValue);

                    if (ratingMatch && pegiMatch && searchMatch) {
                        game.style.display = 'block';
                        gamesFound = true;
                    } else {
                        game.style.display = 'none';
                    }
                });

                noGamesMessage.style.display = gamesFound ? 'none' : 'block';
            }

            function addFilter(tagClass, filterElement, attribute) {
                var selectedOption = filterElement.options[filterElement.selectedIndex];
                if (selectedOption.value) {
                    var tag = document.createElement('div');
                    tag.className = 'selected-category ' + tagClass;
                    tag.setAttribute(attribute, selectedOption.value);
                    tag.innerHTML = selectedOption.text + ' <span class="remove-category">&times;</span>';
                    selectedCategories.appendChild(tag);
                    filterElement.selectedIndex = 0;
                    filterGames();
                }
            }

           

            filterRating.addEventListener('change', filterGames);
            filterPegi.addEventListener('change', filterGames);
            searchGames.addEventListener('input', filterGames);

            selectedCategories.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-category')) {
                    var tag = event.target.parentElement;
                    selectedCategories.removeChild(tag);
                    filterGames();
                }
            });

            function sortGamesByRating() {
                var games = Array.from(gamesList.getElementsByClassName('game-item'));

                games.sort((a, b) => {
                    var ratingA = parseFloat(a.getAttribute('data-rating')) || 0;
                    var ratingB = parseFloat(b.getAttribute('data-rating')) || 0;
                    return ratingB - ratingA;
                });

                games.forEach(game => gamesList.appendChild(game));
            }

            sortGamesByRating();
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('videoigre_kategorije', 'display_video_games_by_category');

function custom_video_game_permalink($permalink, $post, $leavename) {
    if ($post->post_type == 'videoigre') {
        $terms = wp_get_post_terms($post->ID, 'category');
        if ($terms && !is_wp_error($terms)) {
            $categories = array_map(function ($term) {
                return $term->slug;
            }, $terms);

            $permalink = str_replace('%category%', implode('/', $categories), $permalink);
        }
    }
    return $permalink;
}
add_filter('post_type_link', 'custom_video_game_permalink', 10, 3);

function display_comment_rating($comment_text, $comment) {
    // Get the rating meta for the comment
    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
    
    if ($rating) {
        // Ensure the rating is a valid integer, and cap it between 1 and 5
        $rating = absint($rating);
        $rating = min(5, max(1, $rating)); // Ensure it's between 1 and 5 stars

        // Add the rating stars to the comment text
        $stars = str_repeat('&#9733;', $rating);
    }

    return $comment_text;
}

function enqueue_rating_script() {
    wp_enqueue_script('rating-js', get_template_directory_uri() . '/js/rating.js', [], false, true);
}
add_action('wp_enqueue_scripts', 'enqueue_rating_script');

// Omogućiti komentiranje samo za prijavljene korisnike
function restrict_comments_to_logged_in_users() {
    if (!is_user_logged_in()) {
        add_filter('comments_open', '__return_false');
    }
}
add_action('template_redirect', 'restrict_comments_to_logged_in_users');


// Omogućiti korisnicima da uređuju i brišu svoje komentare
function allow_comment_editing() {
    if (is_user_logged_in()) {
        add_filter('comment_text', 'add_edit_delete_links', 10, 2);
    }
}
add_action('template_redirect', 'allow_comment_editing');

function add_edit_delete_links($comment_text, $comment) {
   
    return $comment_text;
}

// Prikazivanje oznake "Uređeno" za uređene komentare
function mark_edited_comments($comment_id) {
    if (isset($_POST['comment']) && !empty($_POST['comment'])) {
        $comment = get_comment($comment_id);
        if ($comment->comment_content != $_POST['comment']) {
            update_comment_meta($comment_id, 'edited', true);
        }
    }
}
add_action('edit_comment', 'mark_edited_comments');

function display_edited_comment($comment_text, $comment) {
    if (get_comment_meta($comment->comment_ID, 'edited', true)) {
        $comment_text .= '<p><em>Uređeno</em></p>';
    }
    return $comment_text;
}
add_filter('comment_text', 'display_edited_comment', 10, 2);

// AJAX handler za uređivanje komentara
function ajax_edit_comment() {
    if (!is_user_logged_in() || !isset($_POST['comment_ID']) || !isset($_POST['comment_content'])) {
        wp_send_json_error();
    }

    $comment_id = intval($_POST['comment_ID']);
    $comment_content = sanitize_text_field($_POST['comment_content']);

    $comment = get_comment($comment_id);
    if ($comment->user_id != get_current_user_id()) {
        wp_send_json_error();
    }

    wp_update_comment(array(
        'comment_ID' => $comment_id,
        'comment_content' => $comment_content
    ));

    mark_edited_comments($comment_id);

    wp_send_json_success();
}
add_action('wp_ajax_edit_comment', 'ajax_edit_comment');

// AJAX handler za brisanje komentara
function ajax_delete_comment() {
    if (!is_user_logged_in() || !isset($_POST['comment_ID'])) {
        wp_send_json_error();
    }

    $comment_id = intval($_POST['comment_ID']);

    $comment = get_comment($comment_id);
    if ($comment->user_id != get_current_user_id()) {
        wp_send_json_error();
    }

    wp_delete_comment($comment_id, true);

    wp_send_json_success();
}
add_action('wp_ajax_delete_comment', 'ajax_delete_comment');

// Dodavanje dozvola za uređivanje i brisanje vlastitih komentara
function add_comment_capabilities() {
    $roles = array('subscriber', 'author');

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('edit_comment', true);
            $role->add_cap('delete_comment', true);
        }
    }
}
add_action('init', 'add_comment_capabilities');
// Računanje prosječne ocjene
function get_average_rating($post_id) {
    $comments = get_approved_comments($post_id);
    $total_rating = 0;
    $total_comments = 0;

    foreach ($comments as $comment) {
        $rating = get_comment_meta($comment->comment_ID, 'rating', true);
        if ($rating) {
            $total_rating += intval($rating);
            $total_comments++;
        }
    }

    if ($total_comments > 0) {
        return round($total_rating / $total_comments, 1);
    } else {
        return 0;
    }
}

// Prikaz prosječne ocjene na stranici igre
function display_average_rating($content) {
    if (is_singular('videoigre')) {
        $comments = get_approved_comments(get_the_ID());
        if (count($comments) > 0) {
            $average_rating = get_average_rating(get_the_ID());
            $content .= '<div class="average-rating">Prosječna ocijena: ' . $average_rating . '</div>';
        }
    }
    return $content;
}
add_filter('the_content', 'display_average_rating');

// Prikaz PEGI oznake na stranici igre
function display_pegi_rating($content) {
    if (is_singular('videoigre')) {
        $pegi_rating = get_post_meta(get_the_ID(), '_pegi_rating', true);
        if ($pegi_rating) {
            $content .= '<div class="pegi-rating">PEGI Ocjena: ' . esc_html($pegi_rating) . '</div>';
        }
    }
    return $content;
}
add_filter('the_content', 'display_pegi_rating');
// Računanje prosječne ocjene
/*function get_average_rating($post_id) {
    $comments = get_approved_comments($post_id);
    $total_rating = 0;
    $total_comments = 0;

    foreach ($comments as $comment) {
        $rating = get_comment_meta($comment->comment_ID, 'rating', true);
        if ($rating) {
            $total_rating += intval($rating);
            $total_comments++;
        }
    }

    if ($total_comments > 0) {
        return round($total_rating / $total_comments, 1);
    } else {
        return 0;
    }
}


// Prikaz prosječne ocjene na stranici igre
function display_average_rating() {
    if (is_singular('videoigre')) {
        $comments = get_approved_comments(get_the_ID());
        if (count($comments) > 0) {
            $average_rating = get_average_rating(get_the_ID());
            echo '<div class="average-rating">Prosječna ocijena: ' . $average_rating . '</div>';
        }
    }
}
add_action('the_content', 'display_average_rating');*/

//PROFIL
function custom_user_profile() {
    if (!is_user_logged_in()) {
        return '<p>Morate biti prijavljeni da biste vidjeli ovu stranicu.</p>';
    }

    $current_user = wp_get_current_user();
    $output = '
    <div class="custom-user-profile">
        <h2>Profil korisnika</h2>
        <p><strong>Korisničko ime:</strong> ' . esc_html($current_user->user_login) . '</p>
        <p><strong>Email:</strong> ' . esc_html($current_user->user_email) . '</p>
        <p><strong>Ime:</strong> ' . esc_html($current_user->first_name) . '</p>
        <p><strong>Prezime:</strong> ' . esc_html($current_user->last_name) . '</p>
        <a href="' . wp_logout_url(home_url()) . '" class="custom-logout-button">Odjavi se</a>
    </div>';

    return $output;
}
add_shortcode('custom_user_profile', 'custom_user_profile');


function custom_profile_styles() {
    echo '<style>
        .custom-user-profile {
            max-width: 400px; /* Reduced width */
            margin: 50px auto; /* Center the div with more space at the top and bottom */
            padding: 50px 30px; /* Increased padding */
            border: 2px solid #3399ff; /* Updated border color */
            border-radius: 10px;
            background: #1e1e1e; /* Updated background color */
            text-align: left; /* Align text to the left */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .custom-user-profile h2 {
            color: #66ccff; /* Updated color */
            font-family: "Orbitron", sans-serif; /* Updated font */
            text-align: left; /* Align text to the left */
        }
        .custom-user-profile p {
            margin: 10px 0;
            color: #eeeeee; /* Updated color */
            text-align: left; /* Align text to the left */
        }
        .custom-logout-button {
            display: block;
            margin-top: 20px;
            background: #3399ff; /* Updated background color */
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center; /* Center text */
            box-shadow: 0 0 5px #3399ff, 0 0 15px #66ccff; /* Added box-shadow */
        }
        .custom-logout-button:hover {
            background: #66ccff; /* Updated hover background color */
            color: #000;
            box-shadow: 0 0 20px #66ccff, 0 0 30px #3399ff; /* Updated hover box-shadow */
        }
    </style>';
}
add_action('wp_head', 'custom_profile_styles');

function remove_admin_bar() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');

function remove_profile_menu_item($items, $args) {
    if (!is_user_logged_in()) {
        foreach ($items as $key => $item) {
            if ($item->title == 'Profil') {
                unset($items[$key]);
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'remove_profile_menu_item', 10, 2);

// Funkcija za prikaz igara s filterima
function display_games_with_filters() {
    ob_start(); // Početak output bufferinga
    ?>
    <style>
        /* Stilizacija filtera */
        #game-filters, #genre-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        #game-filters select, #genre-filters select, #game-filters input, #genre-filters input {
            flex: 1 1 30%;
            margin: 5px;
            padding: 10px;
            border: 2px solid #3399ff;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: #ffffff;
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        #game-filters select:focus, #genre-filters select:focus, #game-filters input:focus, #genre-filters input:focus {
            border-color: #00ffff;
            box-shadow: 0 0 5px #00ffff;
        }

        /* Stilizacija kartica igara */
        .game-item {
            margin-bottom: 20px;
        }

        .game-item .card {
            background-color: #1e1e1e;
            border: 2px solid #3399ff;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s, border 0.3s;
            height: 100%; /* Ensure all cards have the same height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .game-item .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(51, 153, 255, 0.6);
            border: 2px solid #66ccff;
        }

        .game-item .card-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #66ccff;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
        }

        .game-item .btn-primary {
            background-color: #3399ff;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-family: 'Press Start 2P', cursive;
            padding: 10px 15px;
            text-transform: uppercase;
            transition: all 0.3s;
            box-shadow: 0 0 5px #3399ff, 0 0 15px #66ccff;
            margin-top: auto; /* Push the button to the bottom */
        }

        .game-item .btn-primary:hover {
            background-color: #66ccff;
            color: #000000;
            box-shadow: 0 0 20px #66ccff, 0 0 30px #3399ff;
        }

        /* Prilagodba za mobilne uređaje */
        @media (max-width: 768px) {
            #game-filters select, #genre-filters select, #game-filters input, #genre-filters input {
                flex: 1 1 100%;
            }
        }

        /* Stilizacija za odabrane kategorije */
        .selected-categories {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .selected-category {
            background-color: #3399ff;
            color: #ffffff;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px;
            display: flex;
            align-items: center;
        }

        .selected-category .remove-category {
            margin-left: 10px;
            cursor: pointer;
            color: #ff0000;
        }
    </style>
    <div class="container mt-5">
        <h1 class="text-center">Sve Igre</h1>
        <div id="game-filters" class="mb-4">
            
            <select id="filter-genre">
                <option value="">Svi Žanrovi</option>
                <!-- Dodajte opcije za žanrove -->
                <?php
                $categories = get_categories(array('exclude' => 1, 'hide_empty' => false)); // Izostavi nekategorizirane
                foreach ($categories as $category) {
                    
                        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                    
                }
                ?>
            </select>
            <select id="filter-rating">
                <option value="">Sve Ocjene</option>
                <!-- Dodajte opcije za ocjene -->
                <option value="5">5 Zvjezdica</option>
                <option value="4">4 Zvjezdice</option>
                <option value="3">3 Zvjezdice</option>
                <option value="2">2 Zvjezdice</option>
                <option value="1">1 Zvjezdica</option>
            </select>
            <select id="filter-pegi">
                <option value="">Sve PEGI Oznake</option>
                <!-- Dodajte opcije za PEGI oznake -->
                <option value="3">PEGI 3</option>
                <option value="7">PEGI 7</option>
                <option value="12">PEGI 12</option>
                <option value="16">PEGI 16</option>
                <option value="18">PEGI 18</option>
            </select>
            <input type="text" id="search-games" placeholder="Pretraži igre">
        </div>
        <div class="selected-categories" id="selected-categories"></div>
        <div id="games-list" class="row">
            <!-- Prikaz igara -->
            <?php
            $args = array(
                'post_type' => 'videoigre',
                'posts_per_page' => -1,
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post(); 
                    $game_image_url = get_post_meta(get_the_ID(), 'game_image', true);
                    if (!$game_image_url) {
                        $game_image_url = get_template_directory_uri() . '/path/to/default-image.jpg'; // Putanja do zadane slike
                    }
                    $categories = get_the_category();
                    $category_slugs = array_map(function($category) {
                        return strtolower($category->slug);
                    }, $categories);
                    ?>
                    <div class="col-md-4 game-item" data-genre="<?php echo implode(' ', $category_slugs); ?>" data-rating="<?php echo get_average_rating(get_the_ID()); ?>" data-pegi="<?php echo get_post_meta(get_the_ID(), '_pegi_rating', true); ?>">
                        <div class="card mb-4 shadow-lg">
                            <img src="<?php echo esc_url($game_image_url); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php the_title(); ?></h5>
                                <p class="card-text"><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                                <div class="rating">
                                    <?php
                                    $average_rating = get_average_rating(get_the_ID());
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $average_rating) {
                                            echo '<span class="star">&#9733;</span>';
                                        } else {
                                            echo '<span class="star">&#9734;</span>';
                                        }
                                    }
                                    ?>
                                    <span class="average-rating">(<?php echo $average_rating; ?>)</span>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm mt-auto">Pročitaj više</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else : ?>
                <p class="text-center text-muted">Trenutno nema dostupnih igara.</p>
            <?php endif;
            wp_reset_postdata(); ?>
        </div>
        <p id="no-games-message" class="text-center text-muted" style="display: none;">Nema traženih igara.</p>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {

    var filterGenre = document.getElementById('filter-genre');
    var filterRating = document.getElementById('filter-rating');
    var filterPegi = document.getElementById('filter-pegi');
    var searchGames = document.getElementById('search-games');
    var gamesList = document.getElementById('games-list');
    var selectedCategories = document.getElementById('selected-categories');
    var noGamesMessage = document.getElementById('no-games-message');

    function filterGames() {
        
        var genreValues = Array.from(selectedCategories.getElementsByClassName('selected-genre'))
            .map(tag => tag.getAttribute('data-genre').toLowerCase());
        var ratingValue = filterRating.value.toLowerCase();
        var pegiValue = filterPegi.value.toLowerCase();
        var searchValue = searchGames.value.toLowerCase();

        var games = gamesList.getElementsByClassName('game-item');
        var gamesFound = false;

        Array.from(games).forEach(game => {
            var gameTitle = game.querySelector('.card-title').innerText.toLowerCase();
            var gameContent = game.querySelector('.card-text').innerText.toLowerCase();
            var gameGenres = game.getAttribute('data-genre').toLowerCase().split(' ');
            var gameRating = game.getAttribute('data-rating').toLowerCase();
            var gamePegi = game.getAttribute('data-pegi').toLowerCase();

            // 1. Konzolni filter – traži se konzola u učitanim žanrovima
            

            // 2. Žanrovski filter – igra mora imati sve navedene žanrove
            var genreMatch = genreValues.length === 0 || genreValues.every(genre => gameGenres.includes(genre));

            // 3. Ostali filteri
            var ratingMatch = ratingValue === '' || gameRating === ratingValue;
            var pegiMatch = pegiValue === '' || gamePegi === pegiValue;

            // 4. Pretraga po nazivu ili opisu igre
            var searchMatch = searchValue === '' || gameTitle.includes(searchValue) || gameContent.includes(searchValue);

            if (genreMatch && ratingMatch && pegiMatch && searchMatch) {
                game.style.display = 'block';
                gamesFound = true;
            } else {
                game.style.display = 'none';
            }
        });

        noGamesMessage.style.display = gamesFound ? 'none' : 'block';
    }

    function addFilter(tagClass, filterElement, attribute) {
        var selectedOption = filterElement.options[filterElement.selectedIndex];
        if (selectedOption.value) {
            var tag = document.createElement('div');
            tag.className = 'selected-category ' + tagClass;
            tag.setAttribute(attribute, selectedOption.value);
            tag.innerHTML = selectedOption.text + ' <span class="remove-category">&times;</span>';
            selectedCategories.appendChild(tag);
            filterElement.selectedIndex = 0;
            filterGames();
        }
    }

    

    filterGenre.addEventListener('change', function() {
        addFilter('selected-genre', filterGenre, 'data-genre');
    });

    filterRating.addEventListener('change', filterGames);
    filterPegi.addEventListener('change', filterGames);
    searchGames.addEventListener('input', filterGames);

    selectedCategories.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-category')) {
            var tag = event.target.parentElement;
            selectedCategories.removeChild(tag);
            filterGames();
        }
    });

    function sortGamesByRating() {
        var games = Array.from(gamesList.getElementsByClassName('game-item'));

        games.sort((a, b) => {
            var ratingA = parseFloat(a.getAttribute('data-rating')) || 0;
            var ratingB = parseFloat(b.getAttribute('data-rating')) || 0;
            return ratingB - ratingA;
        });

        games.forEach(game => gamesList.appendChild(game));
    }

    sortGamesByRating();
});


    </script>
    <?php
    return ob_get_clean(); 
}
add_shortcode('games_with_filters', 'display_games_with_filters');

function load_and_log_categories() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Sve kategorije:");
            <?php
            $categories = get_categories(array(
                'hide_empty' => false, // Učitaj sve kategorije, uključujući one bez postova
            ));
            array_shift($categories); // Izostavi prvu kategoriju (nekategorizirane)
            foreach ($categories as $category) {
                echo 'console.log("' . esc_js($category->name) . '");';
            }
            ?>
        });
    </script>
    <?php
}
add_action('wp_footer', 'load_and_log_categories');

function handle_uncategorized_posts($query) {
    if ($query->is_main_query() && $query->is_single() && in_category('nekategorizirano', get_the_ID())) {
        // Allow uncategorized posts to be displayed
        $query->set('post_type', 'post');
        $query->set('category_name', ''); // Ensure no category filter is applied
    }
}
add_action('pre_get_posts', 'handle_uncategorized_posts');

// Enqueue the script
function enqueue_comment_edit_delete_script() {
    wp_enqueue_script('comment-edit-delete', get_template_directory_uri() . '/js/comment-edit-delete.js', array('jquery'), null, true);
    wp_localize_script('comment-edit-delete', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_comment_edit_delete_script');

function display_kategorije_with_filters() {
    ob_start(); // Početak output bufferinga
    ?>
    <style>
        /* Stilizacija filtera */
        #game-filters, #genre-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        #game-filters select, #genre-filters select, #game-filters input, #genre-filters input {
            flex: 1 1 30%;
            margin: 5px;
            padding: 10px;
            border: 2px solid #3399ff;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: #ffffff;
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        #game-filters select:focus, #genre-filters select:focus, #game-filters input:focus, #genre-filters input:focus {
            border-color: #00ffff;
            box-shadow: 0 0 5px #00ffff;
        }

        /* Stilizacija kartica igara */
        .game-item {
            margin-bottom: 20px;
        }

        .game-item .card {
            background-color: #1e1e1e;
            border: 2px solid #3399ff;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s, border 0.3s;
            height: 100%; /* Ensure all cards have the same height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .game-item .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(51, 153, 255, 0.6);
            border: 2px solid #66ccff;
        }

        .game-item .card-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #66ccff;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
        }

        .game-item .btn-primary {
            background-color: #3399ff;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-family: 'Press Start 2P', cursive;
            padding: 10px 15px;
            text-transform: uppercase;
            transition: all 0.3s;
            box-shadow: 0 0 5px #3399ff, 0 0 15px #66ccff;
            margin-top: auto; /* Push the button to the bottom */
        }

        .game-item .btn-primary:hover {
            background-color: #66ccff;
            color: #000000;
            box-shadow: 0 0 20px #66ccff, 0 0 30px #3399ff;
        }

        /* Prilagodba za mobilne uređaje */
        @media (max-width: 768px) {
            #game-filters select, #genre-filters select, #game-filters input, #genre-filters input {
                flex: 1 1 100%;
            }
        }

        /* Stilizacija za odabrane kategorije */
        .selected-categories {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .selected-category {
            background-color: #3399ff;
            color: #ffffff;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px;
            display: flex;
            align-items: center;
        }

        .selected-category .remove-category {
            margin-left: 10px;
            cursor: pointer;
            color: #ff0000;
        }
    </style>
    <div class="container mt-5">
        <h1 class="text-center">Svi žanrovi</h1>
        <div id="game-filters" class="mb-4">
            
            <select id="filter-genre">
                <option value="">Svi Žanrovi</option>
                <!-- Dodajte opcije za žanrove -->
                <?php
                $categories = get_categories(array('exclude' => 1, 'hide_empty' => false)); // Izostavi nekategorizirane
                foreach ($categories as $category) {
                    
                        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                    
                }
                ?>
            </select>
            <select id="filter-rating">
                <option value="">Sve Ocjene</option>
                <!-- Dodajte opcije za ocjene -->
                <option value="5">5 Zvjezdica</option>
                <option value="4">4 Zvjezdice</option>
                <option value="3">3 Zvjezdice</option>
                <option value="2">2 Zvjezdice</option>
                <option value="1">1 Zvjezdica</option>
            </select>
            <select id="filter-pegi">
                <option value="">Sve PEGI Oznake</option>
                <!-- Dodajte opcije za PEGI oznake -->
                <option value="3">PEGI 3</option>
                <option value="7">PEGI 7</option>
                <option value="12">PEGI 12</option>
                <option value="16">PEGI 16</option>
                <option value="18">PEGI 18</option>
            </select>
            <input type="text" id="search-games" placeholder="Pretraži igre">
        </div>
        <div class="selected-categories" id="selected-categories"></div>
        <div id="games-list" class="row">
            <!-- Prikaz igara -->
            <?php
            $args = array(
                'post_type' => 'videoigre',
                'posts_per_page' => -1,
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post(); 
                    $game_image_url = get_post_meta(get_the_ID(), 'game_image', true);
                    if (!$game_image_url) {
                        $game_image_url = get_template_directory_uri() . '/path/to/default-image.jpg'; // Putanja do zadane slike
                    }
                    $categories = get_the_category();
                    $category_slugs = array_map(function($category) {
                        return strtolower($category->slug);
                    }, $categories);
                    ?>
                    <div class="col-md-4 game-item" data-genre="<?php echo implode(' ', $category_slugs); ?>" data-rating="<?php echo get_average_rating(get_the_ID()); ?>" data-pegi="<?php echo get_post_meta(get_the_ID(), '_pegi_rating', true); ?>">
                        <div class="card mb-4 shadow-lg">
                            <img src="<?php echo esc_url($game_image_url); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php the_title(); ?></h5>
                                <p class="card-text"><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                                <div class="rating">
                                    <?php
                                    $average_rating = get_average_rating(get_the_ID());
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $average_rating) {
                                            echo '<span class="star">&#9733;</span>';
                                        } else {
                                            echo '<span class="star">&#9734;</span>';
                                        }
                                    }
                                    ?>
                                    <span class="average-rating">(<?php echo $average_rating; ?>)</span>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm mt-auto">Pročitaj više</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else : ?>
                <p class="text-center text-muted">Trenutno nema dostupnih igara.</p>
            <?php endif;
            wp_reset_postdata(); ?>
        </div>
        <p id="no-games-message" class="text-center text-muted" style="display: none;">Nema traženih igara.</p>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {

    var filterGenre = document.getElementById('filter-genre');
    var filterRating = document.getElementById('filter-rating');
    var filterPegi = document.getElementById('filter-pegi');
    var searchGames = document.getElementById('search-games');
    var gamesList = document.getElementById('games-list');
    var selectedCategories = document.getElementById('selected-categories');
    var noGamesMessage = document.getElementById('no-games-message');

    function filterGames() {
        
        var genreValues = Array.from(selectedCategories.getElementsByClassName('selected-genre'))
            .map(tag => tag.getAttribute('data-genre').toLowerCase());
        var ratingValue = filterRating.value.toLowerCase();
        var pegiValue = filterPegi.value.toLowerCase();
        var searchValue = searchGames.value.toLowerCase();

        var games = gamesList.getElementsByClassName('game-item');
        var gamesFound = false;

        Array.from(games).forEach(game => {
            var gameTitle = game.querySelector('.card-title').innerText.toLowerCase();
            var gameContent = game.querySelector('.card-text').innerText.toLowerCase();
            var gameGenres = game.getAttribute('data-genre').toLowerCase().split(' ');
            var gameRating = game.getAttribute('data-rating').toLowerCase();
            var gamePegi = game.getAttribute('data-pegi').toLowerCase();

            // 1. Konzolni filter – traži se konzola u učitanim žanrovima
            

            // 2. Žanrovski filter – igra mora imati sve navedene žanrove
            var genreMatch = genreValues.length === 0 || genreValues.every(genre => gameGenres.includes(genre));

            // 3. Ostali filteri
            var ratingMatch = ratingValue === '' || gameRating === ratingValue;
            var pegiMatch = pegiValue === '' || gamePegi === pegiValue;

            // 4. Pretraga po nazivu ili opisu igre
            var searchMatch = searchValue === '' || gameTitle.includes(searchValue) || gameContent.includes(searchValue);

            if (genreMatch && ratingMatch && pegiMatch && searchMatch) {
                game.style.display = 'block';
                gamesFound = true;
            } else {
                game.style.display = 'none';
            }
        });

        noGamesMessage.style.display = gamesFound ? 'none' : 'block';
    }

    function addFilter(tagClass, filterElement, attribute) {
        var selectedOption = filterElement.options[filterElement.selectedIndex];
        if (selectedOption.value) {
            var tag = document.createElement('div');
            tag.className = 'selected-category ' + tagClass;
            tag.setAttribute(attribute, selectedOption.value);
            tag.innerHTML = selectedOption.text + ' <span class="remove-category">&times;</span>';
            selectedCategories.appendChild(tag);
            filterElement.selectedIndex = 0;
            filterGames();
        }
    }

    

    filterGenre.addEventListener('change', function() {
        addFilter('selected-genre', filterGenre, 'data-genre');
    });

    filterRating.addEventListener('change', filterGames);
    filterPegi.addEventListener('change', filterGames);
    searchGames.addEventListener('input', filterGames);

    selectedCategories.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-category')) {
            var tag = event.target.parentElement;
            selectedCategories.removeChild(tag);
            filterGames();
        }
    });

    function sortGamesByRating() {
        var games = Array.from(gamesList.getElementsByClassName('game-item'));

        games.sort((a, b) => {
            var ratingA = parseFloat(a.getAttribute('data-rating')) || 0;
            var ratingB = parseFloat(b.getAttribute('data-rating')) || 0;
            return ratingB - ratingA;
        });

        games.forEach(game => gamesList.appendChild(game));
    }

    sortGamesByRating();
});


    </script>
    <?php
    return ob_get_clean(); 
}
add_shortcode('kategorije_with_filters', 'display_kategorije_with_filters');