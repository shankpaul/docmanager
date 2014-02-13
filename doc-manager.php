<?php
/*
Plugin Name: Doc Manager
Plugin URI: http://www.nuevalgo.com
Description: For easily create and manage various documents.
Author: Shan K Paul
Version: 1.0
Author URI: 
*/
define('DOC_MANAGER_PLUGIN_URL',  get_option('siteurl').'/wp-content/plugins/doc-manager/');
global $wpdb;

/*
 * Insatll Doc Manager pugin 
 * Code Works when Plugin Activated
 */
function install_doc_manager()
{
    /*
     * install tables to database
     */
   
        include('doc_config.php');
        add_option("DocManagerVersion",'1.0');
        add_option("DocManagerFileSize",'2-MB');
		$size=2*1024*1024;
		add_option("DocManagerUploadFileSize",$size);
        
          
}

/*
 * Uninstall Doc Manager pugin 
 * Code Works when Plugin Deactivated
 */
function uninstall_doc_manager()
{
   delete_option("DocManagerVersion");
  
}



function doc_manager_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
            
	case 'upload':
			include('views/doc-manager-upload.php');
			break;
	case 'settings':
			include('views/doc-manager-settings.php');
			break;
    case 'edit':
			include('views/doc-manager-edit.php');
			break;
    default:
			include('views/doc-manager-home.php');
			break;
	}
}
if (is_admin()) 
{
	add_action('admin_menu', 'doc_manager_add_to_menu');

	if(!class_exists('pagination'))
	include_once ('pagination.class.php');
	include('ajax/handle-doc-upload.php');
	add_action('wp_ajax_docupload', 'handle_upload_dm');

}
if (!is_admin())
{
	 add_action( 'wp_enqueue_scripts', 'add_stylesheets_dm' );
	 add_action('wp_enqueue_scripts', "add_javascript_dm");
	  include('doc-manager-front-end.php');
	 add_shortcode('doc_manager', 'get_documents');
}

function add_stylesheets_dm() {
 
 /*Data table Plugin styles
 */
	$css_path=DOC_MANAGER_PLUGIN_URL . 'thirdparty/datatables/data_table.css';
	 wp_register_style( 'DocManagerThirdPartyStyles', $css_path ); 
    // loads your stylesheet
    wp_enqueue_style( 'DocManagerThirdPartyStyles' );
	
	$css_path=DOC_MANAGER_PLUGIN_URL . 'css/icon.css';
	 wp_register_style( 'DocManagerIconStyles', $css_path ); 
    // loads your stylesheet
    wp_enqueue_style( 'DocManagerIconStyles' );
	
}
function add_javascript_dm() {
	
	/*
	 * Load Jquery
	 */
	
	$js_path = DOC_MANAGER_PLUGIN_URL . 'js/jquery.js';
    wp_register_script( 'DocManagerJquery', $js_path ); 
    // loads your stylesheet
    wp_enqueue_script( 'DocManagerJquery' ); 
	
	
	/*
	 * Data table script
	 */
	$js_path = DOC_MANAGER_PLUGIN_URL . 'thirdparty/datatables/jquery.dataTables.min.js';
    wp_register_script( 'DocManagerThirdPartyScript', $js_path ); 
    // loads your stylesheet
    wp_enqueue_script( 'DocManagerThirdPartyScript' ); 
	
	
	/*
	 * Doc manager script
	 */
	$js_path = DOC_MANAGER_PLUGIN_URL . 'js/doc-manager-front-end.js';
    wp_register_script( 'DocManagerScript', $js_path ); 
    // loads your stylesheet
    wp_enqueue_script( 'DocManagerScript' ); 
	
}
function doc_manager_add_to_menu() 
{
        /*
         * call back function : easy_gallery_admin_options();
         */
	add_menu_page('Doc Manager', 'Doc Manager', 'manage_options', 'doc-manager-home', 'doc_manager_admin_options',DOC_MANAGER_PLUGIN_URL.'images/icon.png',63.32 );
}


register_activation_hook(__FILE__, 'install_doc_manager');
register_deactivation_hook( __FILE__, 'uninstall_doc_manager' );


?>
