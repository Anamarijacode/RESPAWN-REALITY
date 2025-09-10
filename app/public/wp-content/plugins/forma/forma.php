<?php
/*
Plugin Name: Jednostavni Kontakt Formular
Description: Jednostavni plugin za kontakt formu u WordPressu.
Version: 1.2
Author: Anamarija Bošnjak
*/

/* Shortcode za prikaz kontakt forme */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function scf_display_form() {
    // Dohvaćanje postavki iz baze
    $form_title = get_option('scf_form_title', 'Kontaktirajte nas');
    $name_label = get_option('scf_form_name_label', 'Ime');
    $email_label = get_option('scf_form_email_label', 'Email');
    $message_label = get_option('scf_form_message_label', 'Poruka');
    $button_text = get_option('scf_form_button_text', 'Pošalji');
    $thank_you_message = get_option('scf_form_thank_you_message', 'Hvala na vašoj poruci! Kontaktirat ćemo vas uskoro.');

    // Initialize the response message
    $response_message = '';

    // Handle form submission
    if (isset($_POST['scf_submit'])) {
        // Sanitize and validate the form input
        $name = sanitize_text_field($_POST['scf_name']);
        $email = sanitize_email($_POST['scf_email']);
        $message = sanitize_textarea_field($_POST['scf_message']);

        // Validate email and input fields
        if (is_email($email) && !empty($name) && !empty($message)) {
            // Inkludiraj PHPMailer klasu
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                require 'path_to_phpmailer/PHPMailer.php';  
                require 'path_to_phpmailer/Exception.php';
                require 'path_to_phpmailer/SMTP.php';
            }

            // Create a new PHPMailer instance
         
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();  // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com';  // SMTP server (npr. SMTP server tvojeg providera)
                $mail->SMTPAuth = true;  // Enable SMTP authentication
                $mail->Username = 'anci990601@gmail.com';  // Tvoj SMTP korisnički email
                $mail->Password = 'mtxt oibm tien jujl';  // Tvoja SMTP lozinka
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption
                $mail->Port = 465;  // SMTP port

                //Recipients
                $mail->setFrom($email, 'Mailer');// From email and name
                $mail->addAddress('anci990601@gmail.com', 'Joe User'); // Tvoja email adresa (primaoc)

                // Content
                $mail->isHTML(true);  // Set email format to plain text
                $mail->Subject = 'Nova poruka sa kontakt forme';
                $mail->Body    = "Ime: $name\nEmail: $email\n\nPoruka:\n$message";

                // Send email
                $mail->send();
                $response_message = '<div id="scf-thank-you-message" class="scf-thank-you-message">
                                        <span class="scf-close-button" onclick="document.getElementById(\'scf-thank-you-message\').style.display=\'none\'">&times;</span>
                                        ' . esc_html($thank_you_message) . '
                                      </div>';
            } catch (Exception $e) {
                $response_message = '<div class="scf-error-message">Došlo je do greške pri slanju poruke: ' . $mail->ErrorInfo . '</div>';
            }
        } else {
            // Handle errors: show a message if the validation fails
            $response_message = '<div class="scf-error-message">Molimo, provjerite sve podatke i pokušajte ponovo.</div>';
        }
    }

    ob_start();

    // Output response message if any
    echo $response_message;

    // Form output
    echo '<div class="scf-container">';
    echo '<h2 class="scf-title">' . esc_html($form_title) . '</h2>';
    echo '<form method="post" action="" class="scf-form">';
    echo '<label for="scf_name" class="scf-label">' . esc_html($name_label) . ':</label><br>';
    echo '<input type="text" name="scf_name" class="scf-input" required><br><br>';

    echo '<label for="scf_email" class="scf-label">' . esc_html($email_label) . ':</label><br>';
    echo '<input type="email" name="scf_email" class="scf-input" required><br><br>';

    echo '<label for="scf_message" class="scf-label">' . esc_html($message_label) . ':</label><br>';
    echo '<textarea name="scf_message" class="scf-textarea" rows="5" required></textarea><br><br>';

    echo '<input type="submit" name="scf_submit" class="scf-submit" value="' . esc_html($button_text) . '">';
    
    echo '</form>';
    echo '</div>';

    return ob_get_clean();
}

add_shortcode('simple_contact_form', 'scf_display_form');





/* Obrada slanja forme */
function scf_handle_form_submission() {
    if (isset($_POST['scf_submit'])) {
        $name = sanitize_text_field($_POST['scf_name']);
        $email = sanitize_email($_POST['scf_email']);
        $message = sanitize_textarea_field($_POST['scf_message']);

        // Spremanje poruke u bazu podataka
        scf_save_message($name, $email, $message);

        // Slanje emaila
        $to = get_option('admin_email');
        $subject = "Nova poruka s kontakt forme od $name";
        $headers = ["From: $name <$email>", "Reply-To: $email"];
        wp_mail($to, $subject, $message, $headers);
    }
}
add_action('init', 'scf_handle_form_submission');

/* Spremanje poruke u bazu podataka */
function scf_save_message($name, $email, $message) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'scf_messages';

    $wpdb->insert(
        $table_name,
        [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'date' => current_time('mysql'),
        ]
    );
}

/* Kreiranje tabele za poruke prilikom aktivacije plugina */
function scf_create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'scf_messages';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            message text NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
register_activation_hook(__FILE__, 'scf_create_table');

// Dodavanje CSS stila u plugin
function scf_add_styles() {
    echo '<style>
        /* General Styling for Contact Form */
        .scf-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(45deg, #1e1e1e, #121212);
            padding: 20px;
            box-sizing: border-box;
        }

        .scf-title {
            color: #00ffff;
            font-family: "Orbitron", sans-serif;
            margin-bottom: 20px;
        }

        .scf-form {
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 30px;
            border: 2px solid #3399ff;
            box-shadow: 0 0 10px #33ccff, 0 0 20px #00ffff;
            width: 100%;
            max-width: 500px;
            color: #ffffff;
            font-family: "Orbitron", sans-serif;
            text-align: center;
            transition: all 0.3s ease;
        }

        .scf-label {
            color: #ffffff;
            font-size: 1.1rem;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .scf-input, .scf-textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background-color: #333;
            border: 1px solid #3399ff;
            border-radius: 8px;
            color: #fff;
            font-family: "Orbitron", sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .scf-input:focus, .scf-textarea:focus {
            outline: none;
            border-color: #00ffff;
            box-shadow: 0 0 5px #00ffff;
        }

        .scf-submit {
            background-color: #3399ff;
            color: #fff;
            border: none;
            padding: 15px 25px;
            font-size: 1.1rem;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 0 5px #3399ff, 0 0 15px #66ccff;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .scf-submit:hover {
            background-color: #66ccff;
            box-shadow: 0 0 20px #66ccff, 0 0 30px #3399ff;
        }

        .scf-thank-you-message {
            background-color: rgba(0, 255, 255, 0.1);
            border: 1px solid #00ffff;
            border-radius: 8px;
            padding: 15px;
            color: #00ffff;
            font-size: 1.1rem;
            margin: 20px auto;
            max-width: 500px;
            position: relative;
            text-align: center;
            animation: neon-flicker 1.5s infinite alternate;
        }

        .scf-close-button {
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #00ffff;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .scf-close-button:hover {
            color: #ff33ff;
        }

        @keyframes neon-flicker {
            0%, 100% {
                text-shadow: 0 0 5px #ff33ff, 0 0 15px #00ff99, 0 0 20px #9900ff;
            }
            50% {
                text-shadow: 0 0 3px #00ffff, 0 0 10px #33ccff, 0 0 15px #ff66ff;
            }
        }
    </style>';
}
add_action('wp_head', 'scf_add_styles');

/* Dodavanje opcija za uređivanje forme u admin panel */
function scf_add_admin_menu() {
    add_menu_page(
        'Kontakt Poruke',
        'Kontakt Poruke',
        'manage_options',
        'scf_messages',
        'scf_display_messages',
        'dashicons-email',
        6
    );
    add_submenu_page(
        'scf_messages',
        'Uredi Formu',
        'Uredi Formu',
        'manage_options',
        'scf_edit_form',
        'scf_edit_form_page'
    );
}
add_action('admin_menu', 'scf_add_admin_menu');

function scf_edit_form_page() {
    if (isset($_POST['scf_save_form_settings'])) {
        update_option('scf_form_title', sanitize_text_field($_POST['scf_form_title']));
        update_option('scf_form_name_label', sanitize_text_field($_POST['scf_form_name_label']));
        update_option('scf_form_email_label', sanitize_text_field($_POST['scf_form_email_label']));
        update_option('scf_form_message_label', sanitize_text_field($_POST['scf_form_message_label']));
        update_option('scf_form_button_text', sanitize_text_field($_POST['scf_form_button_text']));
        update_option('scf_form_thank_you_message', sanitize_text_field($_POST['scf_form_thank_you_message']));
        echo '<div class="updated"><p>Postavke su uspješno spremljene!</p></div>';
    }

    $form_title = get_option('scf_form_title', 'Kontaktirajte nas');
    $name_label = get_option('scf_form_name_label', 'Ime');
    $email_label = get_option('scf_form_email_label', 'Email');
    $message_label = get_option('scf_form_message_label', 'Poruka');
    $button_text = get_option('scf_form_button_text', 'Pošalji');
    $thank_you_message = get_option('scf_form_thank_you_message', 'Hvala na vašoj poruci! Kontaktirat ćemo vas uskoro.');

    echo '<div class="wrap">';
    echo '<h1>Uredi Kontakt Formu</h1>';
    echo '<form method="post" action="">';
    echo '<table class="form-table">';
    echo '<tr><th scope="row"><label for="scf_form_title">Naslov Forme</label></th>';
    echo '<td><input type="text" name="scf_form_title" value="' . esc_attr($form_title) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="scf_form_name_label">Labela za Ime</label></th>';
    echo '<td><input type="text" name="scf_form_name_label" value="' . esc_attr($name_label) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="scf_form_email_label">Labela za Email</label></th>';
    echo '<td><input type="text" name="scf_form_email_label" value="' . esc_attr($email_label) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="scf_form_message_label">Labela za Poruku</label></th>';
    echo '<td><input type="text" name="scf_form_message_label" value="' . esc_attr($message_label) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="scf_form_button_text">Tekst na Dugmetu</label></th>';
    echo '<td><input type="text" name="scf_form_button_text" value="' . esc_attr($button_text) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="scf_form_thank_you_message">Poruka Zahvale</label></th>';
    echo '<td><textarea name="scf_form_thank_you_message" class="regular-text" rows="3">' . esc_textarea($thank_you_message) . '</textarea></td></tr>';
    echo '</table>';
    echo '<p class="submit"><input type="submit" name="scf_save_form_settings" id="submit" class="button button-primary" value="Spremi Promjene"></p>';
    echo '</form>';
    echo '</div>';
}
// Funkcija za prikazivanje poruka u admin panelu
function scf_display_messages() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'scf_messages';
    $messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date DESC");

    echo '<div class="wrap"><h1>Kontakt Poruke</h1>';
    
    if (empty($messages)) {
        echo '<p>Nemate novih poruka.</p>';
    } else {
        echo '<table class="wp-list-table widefat fixed striped messages">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Ime</th>
                    <th scope="col">Email</th>
                    <th scope="col">Poruka</th>
                    <th scope="col">Datum</th>
                    <th scope="col">Akcija</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($messages as $message) {
            echo '<tr>';
            echo '<td>' . esc_html($message->id) . '</td>';
            echo '<td>' . esc_html($message->name) . '</td>';
            echo '<td>' . esc_html($message->email) . '</td>';
            echo '<td>' . esc_html(wp_trim_words($message->message, 20)) . '</td>';
            echo '<td>' . esc_html($message->date) . '</td>';
            echo '<td>
                    <a href="' . admin_url('admin.php?page=scf_messages&action=reply&id=' . $message->id) . '">Odgovori</a> | 
                    <a href="' . admin_url('admin.php?page=scf_messages&action=delete&id=' . $message->id) . '" onclick="return confirm(\'Jeste li sigurni da želite obrisati ovu poruku?\')">Obriši</a>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }
    echo '</div>';
}


// Funkcija za brisanje poruka
function scf_handle_delete_message() {
    global $wpdb;

    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $table_name = $wpdb->prefix . 'scf_messages';

        // Brisanje poruke iz baze
        $wpdb->delete($table_name, array('id' => $id));

        // Preusmjeravanje natrag na stranicu s porukama
        wp_redirect(admin_url('admin.php?page=scf_messages'));
        exit;
    }
}
add_action('admin_init', 'scf_handle_delete_message');


