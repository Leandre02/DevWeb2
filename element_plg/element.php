<?php
/**
 * Plugin Name: My Basics Plugin
 * Description: Ce plugin My Basics Plugin enrichit votre site WordPress en ajoutant un tableau de données avec des messages aléatoires issus d'une liste. Une fois activé, il affichera l'un de ces messages sur l'admin panel pour une touche de variété et d'amusement.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Léandre Kanmegne
 */
?>
<?php
// Activation du plugin
function activate_my_basics_plugin() {
    // Marque le plugin comme activé
    add_option('Activated_Plugin', 'my-basics-plugin');

    // Crée la table de données lors de l'activation
    create_my_basics_data_table();

    // Insère des données aléatoires dans la table
    insert_random_message_into_table();
}

// Désactivation du plugin
function deactivate_my_basics_plugin() {
    // Supprime l'affichage du message aléatoire sur l'admin panel
    remove_action('admin_notices', 'display_random_message_on_admin_panel');
}

// Désinstallation du plugin
function uninstall_my_basics_plugin() {
    // Supprime la table de données lors de la désinstallation
    drop_my_basics_data_table();
}

// Crée la table de données
function create_my_basics_data_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'my_basics_data';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        message text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Insère des données aléatoires dans la table
function insert_random_message_into_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'my_basics_data';

    $messages = array("John Dear", "Max Payne", "Léon Bergeron");
    $random_message = $messages[array_rand($messages)];

    $wpdb->insert(
        $table_name,
        array(
            'date' => current_time('mysql'),
            'message' => $random_message
        )
    );
}

// Affiche un message aléatoire sur l'admin panel
function display_random_message_on_admin_panel() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'my_basics_data';
    $random_message = $wpdb->get_var("SELECT message FROM $table_name ORDER BY RAND() LIMIT 1");

    echo "<div class='notice notice-info'><p>$random_message</p></div>";
}

// Supprime la table de données lors de la désinstallation
function drop_my_basics_data_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'my_basics_data';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Enregistrement du hook d'activation
register_activation_hook(__FILE__, 'activate_my_basics_plugin');

// Enregistrement du hook de désactivation
register_deactivation_hook(__FILE__, 'deactivate_my_basics_plugin');

// Enregistrement du hook de désinstallation
register_uninstall_hook(__FILE__, 'uninstall_my_basics_plugin');

// Ajout du hook d'affichage du message sur l'admin panel
add_action('admin_notices', 'display_random_message_on_admin_panel');
?>  