<?php
/*
Plugin Name: Niz Ajax Load More Products for Woocommerce
Plugin URI: https://nizolas.izac.pro/woocommerce-products-carousel-plugin/
Description: Niz Ajax Load More Products for Woocommerce allows you to setup quickly ajax loading for products instead of pagination!
Version: 1.0.0
Author: Nicolas
Author URI: https://nizolas.izac.pro
Text Domain: niz-woo-ajload
Domain Path: /languages/
License: GPLv2
*/
if (!defined('ABSPATH')){
    exit("Do not access this file directly.");
}

define( 'NIZ_WOO_AJLOAD_URL', plugins_url( '/', __FILE__ ) );
define( 'NIZ_WOO_AJLOAD_PATH', plugin_dir_path(__FILE__) );
define( 'NIZ_WOO_AJLOAD_PLUGIN_NAME','Niz Ajax Load More Products for Woocommerce');

require_once(NIZ_WOO_AJLOAD_PATH.'inc/class.niz-dependency-checker.php');
require_once(NIZ_WOO_AJLOAD_PATH.'inc/class.niz-set-ajax.php');

class NizWooAjaxLoadMore{
    public static $_instance=null;

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('admin_menu', array($this, 'setup_menu' ) );
        add_action('init', array($this, 'set_ajax_load_more' ));
        add_action('update_option_niz_woo_ajload', array($this, 'sanitize_settings'));
    }
    public static function get_instance() {
        self::$_instance = empty(self::$_instance) ? new NizWooAjaxLoadMore() : self::$_instance;
        return self::$_instance;
    }
    public static function get_settings(){
        return get_option('niz_woo_ajload');
    }

    public function admin_scripts(){
        wp_enqueue_script( 'niz_woo_ajload-owl-admin', NIZ_WOO_AJLOAD_URL  . 'assets/js/admin.js', array('jquery'));
        wp_localize_script('niz_woo_ajload-owl-admin','niz_ad_params',array('prefix'=>'niz_woo_ajload'));
    }

    public function frontend_scripts() {
        wp_enqueue_style( 'niz_woo_ajload-style', NIZ_WOO_AJLOAD_URL  . 'assets/css/style.css');

        wp_enqueue_script( 'niz_woo_ajload-script', NIZ_WOO_AJLOAD_URL  . 'assets/js/script.js', array('jquery'));
        wp_localize_script( 'niz_woo_ajload-script', 'ajax_loadmore', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ), // WordPress AJAX
            'loadmore_nonce' => wp_create_nonce('loadmore_nonce_value'),
        ) );
    }
    public function set_ajax_load_more(){
        $ajax_load_more= new NizSetAjax();
    }
    /* ADMIN */
    public function setup_menu(){
        $menu = add_menu_page( 
            'Woo Ajax Load More Products', 
            'Niz Woo Ajax Load More Products', 
            'manage_options', 
            'niz-woo-ajax-load-more-products', 
            array($this, 'admin_page'),
            'dashicons-images-alt' );
        add_action( 'admin_print_scripts-' . $menu, array($this, 'admin_scripts') );
        add_action( 'admin_init',array(&$this, 'register_settings' ) );
    }
    public function register_settings(){
        register_setting( 'niz_woo_ajload', 'niz_woo_ajload', array( $this, 'sanitize' ) );
    }
    public function sanitize($input){
        if( isset( $input['button_class'] ) ){
            $sanitize_class = create_function('$value', 'return sanitize_title(trim($value));');
            $button_class=array_map($sanitize_class, explode(',', $input['button_class']) );
            $input['button_class'] = implode(',', $button_class);
        }
        return $input;

    }

    public function admin_page(){
        niz_get_template_part('admin.php');
    }

    public static function activate(){    }
    public static function deactivate(){    }
}

$dependency=new Nyz_Woo_Ajax_Dependency_Checker();
if($dependency->check())
    $nizWpc=new NizWooAjaxLoadMore();


register_activation_hook(NIZ_WOO_AJLOAD_PATH.'/niz_woo_ajload.php','NizWooAjaxLoadMore::activate');
register_deactivation_hook(NIZ_WOO_AJLOAD_PATH.'/niz_woo_ajload.php','NizWooAjaxLoadMore::deactivate');

if( !function_exists('niz_get_template_part') ){

    function niz_get_template_part( $template_path, $variables = array(), $print = true){
      $filePath=NIZ_WOO_AJLOAD_PATH."/templates/$template_path";
      $output = NULL;
        if(file_exists($filePath)){
            // Extract the variables to a local namespace
            extract($variables);
            // Start output buffering
            ob_start();
            // Include the template file
            include $filePath;
            // End buffering and return its contents
            $output = ob_get_clean();
        }
        if ($print) {
            echo $output;
        }
        return $output;
    }
}




























/**
 *  Get all scripts and styles from Wordpress
 */
// function print_scripts_styles() {

//     $result = [];
//     $result['scripts'] = [];
//     $result['styles'] = [];

//     // Print all loaded Scripts
//     global $wp_scripts;
//     foreach( $wp_scripts->queue as $script ) :
//         $result['scripts'][] =  $wp_scripts->registered[$script]->src . ";";
//     endforeach;

//     // // Print all loaded Styles (CSS)
//     // global $wp_styles;
//     // foreach( $wp_styles->queue as $style ) :
//     //     $result['styles'][] =  $wp_styles->registered[$style]->src . ";";
//     // endforeach;

//     var_dump( $result );
// }

// add_action( 'wp_head', 'print_scripts_styles');

