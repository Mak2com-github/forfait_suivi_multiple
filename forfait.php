<?php
/*
 * @link              http://mak2com.fr
 * @since             2.1.5
 * @package           Forfait_Suivi
 *
 * @wordpress-plugin
 * Plugin Name:       Forfait Suivi
 * Plugin URI:        http://mak2com.fr
 * Description:       Permet la création de forfait de suivi des intervention techniques effectués pour le site du client, ainsi que la création et la gestion des tâches effectués.
 * Version:           2.1.5
 * Author:            Mak2com
 * Author URI:        http://mak2com.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       forfait-suivi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

date_default_timezone_set('Europe/Paris');

register_activation_hook(__FILE__, 'create_db');
function create_db() {

    global $wpdb;
    global $forfait_db_version;

    $wpdb_collate = $wpdb->collate;
    $wbdb_charset = $wpdb->charset;
    $table_forfait = $wpdb->prefix.'forfait';
    $table_tasks = $wpdb->prefix.'tasks';

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_forfait'") != $table_forfait ) {
        $sql_forfait =
            "CREATE TABLE IF NOT EXISTS {$table_forfait} (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            `title` varchar(250) NULL,
            `total_time` time NULL,
            `description` varchar(250) NULL,
            `created_at` datetime NULL,
            `updated_at` datetime NULL 
            ) ENGINE=InnoDB DEFAULT CHARSET `$wbdb_charset` COLLATE `$wpdb_collate`";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql_forfait );
    }

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_tasks'") != $table_tasks ) {
        $sql_tasks =
            "CREATE TABLE IF NOT EXISTS {$table_tasks} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            forfait_id BIGINT UNSIGNED NOT NULL,
            title varchar(250) NULL,
            task_time time NULL,
            description varchar(500) NULL,
            created_at datetime NULL,
            updated_at datetime NULL,
            FOREIGN KEY (forfait_id) REFERENCES $table_forfait(id)
            ) ENGINE=InnoDB DEFAULT CHARSET {$wbdb_charset} COLLATE {$wpdb_collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql_tasks );
    }
}

/** INITIALISATION DU PLUGIN **/
add_action('admin_menu','init_plugin_menu');
function init_plugin_menu(){

    add_menu_page(
        'Forfait Suivi',
        'Forfait Suivi',
        'manage_options',
        'forfait_suivi',
        'forfait_overview',
        'dashicons-calendar-alt',
        3
    );

    add_submenu_page(
        'forfait_suivi',
        'Ajouter un Forfait',
        'Ajouter un Forfait',
        'manage_options',
        'ajouter_forfait',
        'forfait_create'
    );

    add_submenu_page(
        'forfait_suivi',
        'Modifier un Forfait',
        'Modifier un Forfait',
        'manage_options',
        'modifier_forfait',
        'forfait_update'
    );

    add_submenu_page(
        'forfait_suivi',
        'Ajouter une Tâche',
        'Ajouter une Tâche',
        'manage_options',
        'ajouter_tache',
        'tasks_create'
    );

    add_submenu_page(
        'forfait_suivi',
        'Modifier une Tâche',
        'Modifier une Tâche',
        'manage_options',
        'modifier_tache',
        'tasks_update'
    );
}

add_action('admin_init', 'dbOperatorFunctions');
function dbOperatorFunctions() {

    if (isset($_POST['save_forfait'])) {
        $DBAction = new DBActions();
        $DBAction->createForfait($_POST);
    }

    if (isset($_POST['delete_forfait'])) {
        $DBAction = new DBActions();
        $DBAction->deleteForfait($_POST['id']);
    }

    if (isset($_POST['update_forfait'])) {
        $DBAction = new DBActions();
        $DBAction->updateForfait($_POST);
    }

    if (isset($_POST['save_task'])) {
        $DBAction = new DBActions();
        $DBAction->createTask($_POST);
    }

    if (isset($_POST['delete_task'])) {
        $DBAction = new DBActions();
        $DBAction->deleteTask($_POST['id']);
    }

    if (isset($_POST['update_task'])) {
        $DBAction = new DBActions();
        $DBAction->updateTask($_POST);
    }

}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'forfait-overview.php');
require_once(ROOTDIR . 'forfait-create.php');
require_once(ROOTDIR . 'forfait-update.php');
require_once(ROOTDIR . 'tasks-create.php');
require_once(ROOTDIR . 'tasks-update.php');

/** ACTIVATION CSS / JS / BOOTSTRAP **/
add_action('admin_init', 'forfait_admin_js_css');
function forfait_admin_js_css(){
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/5397c1f880.js', null, null, true);
    wp_register_style('Forfait_css', plugins_url('css/admin-forfait.css', __FILE__));
    wp_enqueue_style('Forfait_css');
    wp_enqueue_script('Forfait_js', plugins_url('js/main.js', __FILE__), array('jquery'),'1.0',true);
    wp_enqueue_script('jQuery-Ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', null, null, true);
}

/** RECUPERATION LIST FORFAITS **/
function getListForfait(){
    global $wpdb;
    $table_forfait = $wpdb->prefix.'forfait';
    $sql = "SELECT * FROM ".$table_forfait;
    $forfaitlist = $wpdb->get_results($sql);
    return $forfaitlist;
}

function getTimeTotalsForTasks($forfait_id) {
    global $wpdb;
    $table_tasks = $wpdb->prefix.'tasks';
    $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( task_time ) ) ) FROM {$table_tasks} WHERE forfait_id=$forfait_id";
    $tasksTotalTime = $wpdb->get_var($sql);
    return $tasksTotalTime;
}

function getForfaitTitleByID($forfait_id) {
    global $wpdb;
    $table_forfait = $wpdb->prefix.'forfait';
    $sql = "SELECT title FROM {$table_forfait} WHERE id=$forfait_id";
    $forfaitTitle = $wpdb->get_var($sql);
    return $forfaitTitle;
}

/** RECUPERATION LIST TASKS **/
function getListTasks($forfait_id){
    global $wpdb;
    $table_tasks = $wpdb->prefix.'tasks';
    $sql = "SELECT * FROM {$table_tasks} WHERE forfait_id={$forfait_id} ORDER BY `created_at` DESC;";
    $tasksList = $wpdb->get_results($sql);
    return $tasksList;
}

// Si l'objet existe, on installe le plugin sur le menu//
if (isset($inst_forfait)){

}
