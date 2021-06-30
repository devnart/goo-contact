<?php
/*
Plugin Name: Goo Contact
Plugin URI: http://github.com/devnart/go-contact
Description: Simple WordPress Contact Form
Version: 1.0
Author: Hamza Bouchikhi
Author URI: http://github.com/devnart
*/

function add_my_stylesheet() 
{
    wp_enqueue_style( 'style', plugins_url( 'includes/css/style.css', __FILE__ ) );
}

add_action('admin_print_styles', 'add_my_stylesheet');

wp_register_style( 'namespace', 'includes/css/style.css' );

add_action('admin_menu', 'go_contact_setup_menu');

function go_contact_setup_menu()
{
    add_menu_page('Goo Contact Page', 'Go Contact', 'manage_options', 'go-contact', 'goo_contact_init','dashicons-database-add',5);
}


function goo_contact_init()
{

    add_my_stylesheet();

   

    $html = '';
    $html .= '<div class="form-container">';
    echo '<h1>Goo Contact</h1>';
    $html .= '<form method ="post" action = "">';
    $html .= '<div class="input-container">';
    $html .= '<h2>Name : </h2>';
    $html .= '<div>';
    $html .= '<input  id="name" type = "checkbox" name ="name" value="true" />';
        $html .= '<label class="form-label" for ="name"> Name : </label>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="input-container">';
    $html .= '<h2>Email : </h2>';
    $html .= '<div>';
    $html .= '<input type = "checkbox" name ="email" value="true" id="email" class="form-control"/>';
        $html .= '<label for ="email"> Email : </label>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="input-container">';
    $html .= '<h2>Subject : </h2>';
    $html .= '<div>';
    $html .= '<input type = "checkbox" name ="subject" id ="subject" value="true" class="form-control"/>';
        $html .= '<label for ="subject"> Subject : </label>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="input-container">';
    $html .= '<h2>Message : </h2>';
    $html .= '<div>';
    $html .= '<input type = "checkbox" name ="message"  id ="message" value="true"  class="form-control"/>';
        $html .= '<label for ="message"> Message : </label>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<input class="btn btn-primary" type="submit" name="submit-contact" class=" btn btn-md" value= "Submit"/>';

    $html .= '</form>';
    $html .= '</div>';
    echo $html;

    if (isset($_POST['submit-contact'])) {

        echo '<code>copy this shortcode to your page : [go_contact]</code>';
    }

}

function html_form_code()
{

    getData();


    echo '<form action="" method="post">';

    if (getData()->name) {

        echo 'Your Name (required) <br />';
        echo '<input type="text" name="cname" size="40" /><br>';
    }
    if (getData()->email) {

        echo 'Your Email (required) <br />';
        echo '<input type="email" name="email" size="40" /><br>';
    }
    if (getData()->subject) {

        echo 'Subject (required) <br />';
        echo '<input type="text" name="subject" size="40" /><br>';
    }
    if (getData()->message) {

        echo 'Your Message (required) <br />';
        echo '<textarea rows="10" cols="35" name="message"></textarea><br>';
    }

    echo '<p><input type="submit" name="cf-submitted" value="Send"/></p>';
    echo '</form>';
}

function createtable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tablename = 'goo_contact_fields';
    $sql = "CREATE TABLE $wpdb->base_prefix$tablename (
        id INT,
        name BOOLEAN,
        email BOOLEAN,
        subject BOOLEAN,
        message BOOLEAN
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    maybe_create_table($wpdb->base_prefix . $tablename, $sql);
}

function createDataTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tablename = 'goo_contact_data';
    $sql = "CREATE TABLE $wpdb->base_prefix$tablename (
         id INT AUTO_INCREMENT,
        name varchar(255) DEFAULT null,
        email varchar(255) DEFAULT null,
        subject varchar(255) DEFAULT null,
        message varchar(255) DEFAULT null,
        PRIMARY key(id)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    maybe_create_table($wpdb->base_prefix . $tablename, $sql);
}


function insertData()
{
    global $wpdb;
    $wpdb->insert(
        'wp_goo_contact_fields',
        [
            'id' => 1,
            'name' => true,
            'email' => true,
            'subject' => true,
            'message' => true
        ]
    );
}

function getData()
{

    global $wpdb;
    $fields = $wpdb->get_row("SELECT * FROM wp_goo_contact_fields WHERE id = 1;");
    return $fields;
}


if (isset($_POST['submit-contact'])) {

    $name = filter_var($_POST['name'], FILTER_VALIDATE_BOOLEAN);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_BOOLEAN);
    $subject = filter_var($_POST['subject'], FILTER_VALIDATE_BOOLEAN);
    $message = filter_var($_POST['message'], FILTER_VALIDATE_BOOLEAN);

    global $wpdb;
    $wpdb->update(
        'wp_goo_contact_fields',
        [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ],
        ['id' => 1]
    );
}

if (isset($_POST['cf-submitted'])) {
    $arr = $_POST;
    unset($arr['cf-submitted']);


    global $wpdb;
    $wpdb->insert(
        'wp_goo_contact_data',
        $arr
    );
}
function cf_shortcode()
{
    html_form_code();

    return ob_get_clean();
}

add_shortcode('go_contact', 'cf_shortcode');


register_activation_hook(__FILE__, 'createDataTable');
register_activation_hook(__FILE__, 'createtable');
register_activation_hook(__FILE__, 'insertData');
