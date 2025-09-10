<?php
/*
Plugin Name: Respawn Reality Registration Form
Plugin URI: http://localhost/respawnReality/registration-form
Description: Registracijska forma s prilagođenim stilovima i validacijom.
Version: 1.0
Author: Anamarija Bošnjak
Author URI: 
*/

function rr_handle_registration() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Provjera je li korisničko ime, email i lozinka uneseno
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
            $username = sanitize_text_field($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Provjera lozinke i potvrde lozinke
            if ($password !== $confirm_password) {
                $error_message = "Lozinke se ne podudaraju.";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
                $error_message = "Lozinka mora imati najmanje 8 znakova, uključujući jedno slovo i jedan broj.";
            } elseif (username_exists($username)) {
                $error_message = "Korisničko ime već postoji.";
            } elseif (email_exists($email)) {
                $error_message = "Email adresa već postoji.";
            } else {
                // Kreiraj korisnika
                $user_id = wp_create_user($username, $password, $email);
                if (is_wp_error($user_id)) {
                    $error_message = "Došlo je do pogreške pri registraciji.";
                } else {
                    // Automatski prijavi korisnika
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect(home_url());
                    exit;
                }
            }
        } else {
            $error_message = "Obavezna polja: korisničko ime, email, lozinka i potvrda lozinke.";
        }
    }
}

add_action('admin_post_nopriv_rr_registration_form', 'rr_handle_registration');
add_action('admin_post_rr_registration_form', 'rr_handle_registration');

function rr_registration_form_shortcode() {
    ob_start();
    ?>
    <style>
        .scf-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .scf-form h2 {
            text-align: center;
            color: #33ccff;
        }
        .scf-label {
            font-size: 14px;
            color: #333;
        }
        .scf-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .scf-submit {
            background-color: #33ccff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
        }
        .scf-submit:hover {
            background-color: #0099cc;
        }
        .scf-link {
            color: #33ccff;
            text-decoration: none;
            font-size: 14px;
        }
        .scf-link:hover {
            text-decoration: underline;
        }
    </style>
    
    <div class="scf-container">
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST" class="scf-form" id="registration-form">
            <input type="hidden" name="action" value="rr_registration_form">
            <h2>Registracija</h2>
            <div>
                <label for="username" class="scf-label">Korisničko ime</label>
                <input type="text" id="username" name="username" class="scf-input" required />
            </div>
            <div>
                <label for="email" class="scf-label">Email adresa</label>
                <input type="email" id="email" name="email" class="scf-input" required />
            </div>
            <div>
                <label for="password" class="scf-label">Lozinka</label>
                <input type="password" id="password" name="password" class="scf-input" required />
            </div>
            <div>
                <label for="confirm_password" class="scf-label">Potvrdi lozinku</label>
                <input type="password" id="confirm_password" name="confirm_password" class="scf-input" required />
            </div>
            <button type="submit" class="scf-submit">Registracija</button>
            <div>
                <p><a href="<?php echo esc_url(home_url('/prijava')); ?>" class="scf-link">Već imate račun? Prijavite se ovdje</a></p>
            </div>
        </form>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}

add_shortcode('respawn_registration_form', 'rr_registration_form_shortcode');
?>
