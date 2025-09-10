<?php
/**
 * Plugin Name: Custom prijava i registracija
 * Description: Custom prijava i registracija za korisnika
 * Version: 1.3
 * Author: Anamarija Bošnjak
 */

// Block direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Start output buffering to prevent header issues
ob_start();

function custom_login_form() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        return '<p>Prijavljeni ste kao: ' . esc_html($current_user->first_name . ' ' . $current_user->last_name) . '</p>';
    }

    if (isset($_GET['register'])) {
        return ''; // Sakrij login formu ako je register=true u URL-u
    }

    $login_error = isset($_GET['login_error']) ? $_GET['login_error'] : '';
    $registration_success = isset($_GET['registration_success']) ? $_GET['registration_success'] : '';

    $output = '
    <div id="custom-login-form" class="custom-auth-form">
        <h2>Respawn Reality</h2>
        <form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">
            <label for="username">Korisničko ime</label>
            <input type="text" name="username" required>
            <div class="error-message">' . ($login_error == 'incorrect' ? 'Netočno korisničko ime ili lozinka.' : '') . '</div>
            <div class="success-message">' . ($registration_success == 'true' ? 'Uspješna registracija. Sada se možete prijaviti.' : '') . '</div>

            <label for="password">Lozinka</label>
            <input type="password" name="password" required>

            <button type="submit" name="submit_login">Prijavi se</button>
        </form>
        <a href="' . esc_url(add_query_arg('register', 'true')) . '">Još nemate račun? Registrirajte se!</a>
    </div>';

    if (isset($_POST['submit_login'])) {
        $creds = array(
            'user_login'    => $_POST['username'],
            'user_password' => $_POST['password'],
            'remember'      => false
        );
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            wp_redirect(add_query_arg('login_error', 'incorrect', $_SERVER['REQUEST_URI']));
            exit;
        } else {
            wp_redirect(home_url());
            exit;
        }
    }

    return $output;
}
add_shortcode('custom_login_form', 'custom_login_form');


function custom_registration_form() {
    if (is_user_logged_in()) {
        return '';
    }

    if (!isset($_GET['register'])) {
        return ''; // Sakrij registracijsku formu ako nema register=true u URL-u
    }

    $registration_error = isset($_GET['registration_error']) ? $_GET['registration_error'] : '';

    $output = '
    <div id="custom-register-form" class="custom-auth-form">
        <h2>Respawn Reality</h2>
        <form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">
            <label for="first_name">Ime</label>
            <input type="text" name="first_name" required>

            <label for="last_name">Prezime</label>
            <input type="text" name="last_name" required>

            <label for="username">Korisničko ime</label>
            <input type="text" name="username" required>
            <div class="error-message">' . ($registration_error == 'username_exists' ? 'Korisničko ime već postoji.' : '') . '</div>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="password">Lozinka</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Potvrdite lozinku</label>
            <input type="password" name="confirm_password" required>
            <div class="error-message">' . ($registration_error == 'password_mismatch' ? 'Lozinke se ne podudaraju.' : '') . '</div>

            <button type="submit" name="submit_registration">Registriraj se</button>
        </form>
        <a href="' . esc_url(remove_query_arg('register')) . '">Već imate račun? Prijavite se</a>
    </div>';

    if (isset($_POST['submit_registration'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            wp_redirect(add_query_arg('registration_error', 'password_mismatch', $_SERVER['REQUEST_URI']));
            exit;
        }

        $user_data = array(
            'user_login' => $_POST['username'],
            'user_email' => $_POST['email'],
            'user_pass'  => $_POST['password'],
            'first_name' => $_POST['first_name'],
            'last_name'  => $_POST['last_name'],
            'role'       => 'subscriber',
        );

        if (username_exists($user_data['user_login'])) {
            wp_redirect(add_query_arg('registration_error', 'username_exists', $_SERVER['REQUEST_URI']));
            exit;
        }

        $user_id = wp_insert_user($user_data);

        if (!is_wp_error($user_id)) {
            wp_redirect(add_query_arg('registration_success', 'true', remove_query_arg('register')));
            exit;
        }
    }

    return $output;
}
add_shortcode('custom_registration_form', 'custom_registration_form');

function custom_auth_scripts() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const loginForm = document.getElementById("custom-login-form");
            const registerForm = document.getElementById("custom-register-form");

            // Show login form by default
            loginForm.classList.add("active");

            document.getElementById("switch-to-register").addEventListener("click", function(e) {
                e.preventDefault();
                loginForm.classList.remove("active");
                registerForm.classList.add("active");
            });

            document.getElementById("switch-to-login").addEventListener("click", function(e) {
                e.preventDefault();
                registerForm.classList.remove("active");
                loginForm.classList.add("active");
            });
        });
    </script>';
}
add_action('wp_footer', 'custom_auth_scripts');


// Remove "Prijavi se" menu item if user is logged in
function remove_login_menu_item($items, $args) {
    if (is_user_logged_in()) {
        foreach ($items as $key => $item) {
            if ($item->title == 'Prijavi se') {
                unset($items[$key]);
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'remove_login_menu_item', 10, 2);

// Add custom CSS for the forms
function custom_auth_styles() {
    echo '<style>
        .custom-auth-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #333;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            color: #f1f1f1;
        }

        .custom-auth-form h2 {
            text-align: center;
            color: #ff33ff;
            animation: neon-flicker 1.5s infinite alternate;
        }

        .custom-auth-form label {
            display: block;
            margin-bottom: 5px;
            font-size: 1rem;
            color: #00ff99;
        }

        .custom-auth-form input[type="text"],
        .custom-auth-form input[type="password"],
        .custom-auth-form input[type="email"],
        .custom-auth-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #222;
            color: #f1f1f1;
        }

        .custom-auth-form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #00ff99;
            color: #333;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .custom-auth-form button:hover {
            background-color: #00cc7a;
        }

        .custom-auth-form .error-message {
            color: #ff3333;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .custom-auth-form .success-message {
            color: #33ff33;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .custom-auth-form a {
            display: block;
            text-align: center;
            color: #00ff99;
            margin-top: 15px;
            text-decoration: none;
        }

        .custom-auth-form a:hover {
            text-decoration: underline;
        }

        /* Neon Flicker Animation */
        @keyframes neon-flicker {
            0%, 100% {
                text-shadow: 0 0 5px #ff33ff, 0 0 15px #00ff99, 0 0 20px #9900ff;
            }
            50% {
                text-shadow: 0 0 3px #00ffff, 0 0 10px #33ccff, 0 0 15px #ff66ff;
            }
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .custom-auth-form {
                padding: 15px;
            }

            .custom-auth-form input[type="text"],
            .custom-auth-form input[type="password"],
            .custom-auth-form input[type="email"],
            .custom-auth-form input[type="file"] {
                padding: 8px;
            }

            .custom-auth-form button {
                padding: 8px;
            }
        }
    </style>';
}
add_action('wp_footer', 'custom_auth_styles');