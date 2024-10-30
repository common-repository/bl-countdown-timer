<?php
/**
	Plugin Name: BL Countdown Timer
	Description: Plugin that allows you to create and manage event countdown with a variety of options.
	Author: Strategies
	Author URI: http://www.strategies.co.uk
	Version: 2.4
*/


/**
 * Class used to manage countdown timers in WordPress and provide a WordPress admin panel for easy management-
 *
 * @author Ben Lacey
 */ 
class BlCountdownTimer {
	/**
	 * @var blCountdownTimer
	 */ 
  	private static $instance = null; 	
	
  	/**
  	 * This is the text used in the wordpress sidebar menu
  	 * @var string $themeTitle
  	 */
  	private $themeTitle			= 	'Countdown Timer';
	
	/**
  	 * This is the plugin name as it appears in the plugins folder
  	 * @var string $themeName
  	 */
  	private $themeName			=	'bl_countdown_timer';
	
	/**
  	 * This variable is set in the constructor and is used to set the path of the wp-content folder used by WordPress
  	 * @var string $wp_content_dir
  	 */
  	private $wp_content_dir		=	null;
	
	/**
  	 * This variable is set in the constructor and is used to set the plugin directory, using $wp_content_dir as the base path
  	 * @var string $themeTitle
  	 */
  	private $wp_plugin_dir      =   null;
	
	/**
  	 * This is set in the constructor and is used to set the full path to the plugin directory, using $wp_plugin_dir as the base path
  	 * @var string $themeTitle
  	 */
  	private $plugin_dir 		=	null;
	
	/**
  	 * This is set in the initDatabaseStructure method and is used to dynamically create the table name (if its a single or multi-site WordPress install)
  	 * @var string $themeTitle
  	 */
  	private $tableName 			=	null;

  	
	/**
	 * 	Store if there are any user validation errors when adding / updating data on the form
	 *	@var string error
	 */
	private $error				=	false;
	
  	/**
  	 * @desc The constructor will register the actions for the admin panel and sidebar option as well as load in the required JavaScript and CSS files in the front and back end for the plugin to work.
  	 * 
  	 * @param wpdb $this->wpdb
  	 * @var $this->wpdb This is the WordPress database object, for more information consult: http://codex.wordpress.org/Class_Reference/wpdb
  	 * @throws Exce
  	 */
  	public function __construct(wpdb $wpdb){
		$this->wpdb				= 	$wpdb;
	
  		$this->wp_content_dir 	= 	'/wp-content';
  		$this->wp_plugin_dir 	=	$this->wp_content_dir . '/plugins';
  		$this->plugin_dir		=	$this->wp_plugin_dir . '/bl-countdown-timer';
  		$this->tableName 		= 	$this->wpdb->prefix . $this->themeName;
		
  		$this->setup();
  	}
	
	
	
	/**
  	 * See if this class has been instantiated, if it has it will reference the original instance of this class. See http://en.wikipedia.org/wiki/Singleton_pattern for more information.
  	 * @return blCountdownTimer::instance Get the current instance of the class.
  	 */
  	public static function getInstance(wpdb $wpdb){
    	if(self::$instance == null){
			/* This class accepts 2 params and they must be present! */
      		self::$instance = new self($wpdb);
    	}
    	return self::$instance;   	
 	}	
	
	
	
	/**
	  *	Setup the WordPress plugin hooks
	  *
	  */
	public function setup(){
		// register my activate hook to setup the plugin
		register_activation_hook(__FILE__, array($this, 'activate'));
  		  		
  		// Add the dashboard sidebar link and admin panel
  		add_action('admin_menu'	, array($this, 'pluginAddAdmin'));
  		add_action('admin_init'	, array($this, 'pluginAddInit'));
		
		// Add the front-end includes
		add_action('wp_head', array($this, 'wpThemeHeader' ));
		
		// Add the back-end included
		add_action('admin_head', array($this, 'wpAdminHeader' ));
	}
	
	
 	
 	/**
  	 * @desc This is called when the plugin is activated through WordPress
  	 * @var wpdb $this->wpdb Stores Get the current instance of the class.
  	 */
	public function activate() {
		if (function_exists('is_multisite') && is_multisite()) {
			$old_blog = $this->wpdb->blogid;
			
			$blogids = $this->wpdb->get_col($this->wpdb->prepare("SELECT blog_id FROM blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				$this->initDatabaseStructure();
			}
			switch_to_blog($old_blog);
			
		} else {
			$this->initDatabaseStructure();
		}
	}

	
	
	/**
  	 * This protected method is used by this class only, and will create the database table structure if it doesn't already exist.
  	 */
 	protected function initDatabaseStructure() {
		$this->tableName = $this->wpdb->prefix . $themeName;
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->tableName . " (
			  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL,
			  `desc` varchar(255), 
			  `timerdatetime` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  `status` tinyint DEFAULT 1,
			UNIQUE KEY id (id));";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}	
 	
	
	/**
  	 * This is the code that is inserted into the <head> of the theme (front-end)
  	 */
 	public static function wpThemeHeader() { ?>
 		<script type="text/javascript" src="/wp-content/plugins/bl-countdown-timer/js/countdown.js"></script>
   		<link href="/wp-content/plugins/bl-countdown-timer/css/countdown.css" rel="stylesheet" />
	<?php }
	
	
	/**
  	 * add the javascript and css files to the wordpress admin <head> so it can be used in the plugin
  	 */
	public static function wpAdminHeader() { ?>
		<script type="text/javascript" src="/wp-content/plugins/bl-countdown-timer/js/jquery1.6.2.js"></script>
		<script type="text/javascript" src="/wp-content/plugins/bl-countdown-timer/js/jquery-ui-1.8.14.custom.min.js"></script>
	   	<link href="/wp-content/plugins/bl-countdown-timer/css/cupertino/jquery-ui-1.8.14.custom.css" rel="stylesheet" media="screen" />
	   	
	   	<script type="text/javascript">
			$().ready(function(){
				$(".datepicker").datepicker({dateFormat: 'dd-mm-yy'});
			});
	   	</script>
	<?php }

	
	public function pluginAddAdmin(){
		add_menu_page($this->themeTitle, $this->themeTitle, 'administrator', basename(__FILE__), array($this, 'plugin_admin'));
	}
	
	public function pluginAddInit(){
		wp_enqueue_style("styles", "/wp-content/plugins/bl-countdown-timer/styles.css", false, "1.0", "all");
	}
	

	/**
  	 * List all the active timers entered by the user
  	 */
	public function get_timers(){
		$result = $this->wpdb->get_results("SELECT * FROM " . $this->tableName . " WHERE timerdatetime >= NOW() ORDER BY timerdatetime ASC");
		return $result;
	}
	
	
	/**
  	 * Get the first active timer from the database to show within the widget
  	 */
	public function get_timer(){
		$result = $this->wpdb->get_results("SELECT * FROM " . $this->tableName . " WHERE timerdatetime >= NOW() AND status = 1 ORDER BY timerdatetime ASC LIMIT 1");
		return $result;
	}


	
	/**
  	 * The admin panel HTML.
  	 */
	public function plugin_admin(){
		// ensure we are logged in and allowed to view the admin area
		if(!is_admin()){ die("You are not allowed to view this page"); }
		
		if( isset($_POST['save']) && $_POST['save'] ){			
			if(isset($_POST['title']) == "" || empty($_POST['title'])){ $this->error = true;	$titleError = 'You must enter a title'; }
			if(isset($_POST['counterdate']) == "" || empty($_POST['counterdate'])){ $this->error = true;	$dateError = 'You must enter a date'; }
			if(isset($_POST['countertime']) == "" || empty($_POST['countertime'])){ $this->error = true;	$timeError = 'You must enter a time'; }
			if(isset($_POST['desc']) == "" || empty($_POST['desc'])){ $this->error = true;	$descError = 'You must enter a description'; }
			
			if(preg_match("/1970/i", $_POST['counterdate'])){
				$error = true;
				$dateError = 'Please enter a value date';
			}
			
			if($this->error == 0 || $this->error == false){			
				//if($this->current_user->ID){  // make sure there is a session set for the current user
					$datetime = date('Y-m-d', strtotime($_POST['counterdate'])) . ' ' . date('H:i:s', strtotime($_POST['countertime']));
					if($this->wpdb->insert($this->tableName, 
										array( 
											'title' => stripslashes($_POST['title']), 
											'desc' => stripslashes($_POST['desc']), 
											'timerdatetime' => stripslashes($datetime),
											'status' => '1'
										) )){
						echo '<div id="message" class="updated fade"><p><strong>Timer added successfully</strong></p></div>';
					} else {
						echo '<div id="message" class="error fade"><p><strong>Error adding timer to the database!</strong></p></div>';
					}
				//}
			}
		}
		
		if( isset($_POST['update']) && $_POST['update']){
			//if($current_user->ID){  // make sure there is a session set for the current user
				$datetime = date('Y-m-d', strtotime($_POST['update_date'])) . ' ' . date('H:i:s', strtotime($_POST['update_time']));
				$sql = "UPDATE " . $this->tableName . " 
						SET `title` = '" . $this->wpdb->escape($_POST['update_title']) . "', 
							`desc` = '" . $this->wpdb->escape($_POST['update_desc']) . "', 
							`timerdatetime` = '" . $this->wpdb->escape($datetime) . "', 
							`status` = '" . $this->wpdb->escape($_POST['update_status']) . "' 
						WHERE `id` = '" . (int)$_POST['id'] . "'";
				if($this->wpdb->query( $sql )){ echo '<div id="message" class="updated fade"><p><strong>Timer was updated successfully</strong></p></div>'; }
			//}
		}
		
		
		if( isset($_POST['delete']) && $_POST['delete']){
			//if($current_user->ID){  // make sure there is a session set for the current user
				$query = "DELETE FROM " . $this->tableName . " WHERE id=" . (int)$_POST['id'];
				if($this->wpdb->query( $query )){
					echo '<div id="message" class="updated fade"><p><strong>Timer was removed successfully</strong></p></div>';
				}
			//}
		}
	?>
		<div class="wrap bl_wrap">
			<h2 id="pluginAdminTitle"><?php echo $this->themeTitle; ?> Settings</h2>
			<form id="addTimer" method="post">
				<?php include_once('views/addCountdownForm.html'); ?>				
			</form>
			
			<?php include_once('views/showCountdownTimers.html'); ?>
		</div>
	<?php }	
} // end of class



/**
* @desc Load an instance of the class. $current_user = wp_get_current_user();
*/
global $wpdb;
$blCountdownTimer = blCountdownTimer::getInstance($wpdb);




class countdownTimerWidget extends WP_Widget {
  function countdownTimerWidget(){
	parent::WP_Widget( false, 'Countdown Timer' );
  }

  function widget( $args, $instance ){
  	// access the plugin to get the data required for the widget
	global $wpdb, $current_user;
	$plugin = blCountdownTimer::getInstance($wpdb, $current_user);
	$timer = $plugin->get_timer();
	
	if($timer):	
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] ); ?>
		
		<?php echo $before_widget; ?>
			<?php if ($title) { echo $before_title . $title . $after_title; } ?>		
			<div class="countdownTimer">
				<!-- This is what is shown on the front-end for the widget -->
				<h2><?php echo $timer[0]->title; ?></h2>
				<?php $datetime = date('y', strtotime($timer[0]->timerdatetime)) . ', ' . date('m', strtotime($timer[0]->timerdatetime)) . ', ' . date('d', strtotime($timer[0]->timerdatetime)) . ', ' . date('H', strtotime($timer[0]->timerdatetime)) . ', ' . date('i', strtotime($timer[0]->timerdatetime)) ?>
				<script type="text/javascript">				
					countdown_clock(<?php echo $datetime; ?>, 1, "<?php echo date('y-m-d H:i:s'); ?>");
				</script>
				<div class="timerDesc"><?php echo $timer[0]->desc; ?></div>
			</div>
		<?php echo $after_widget; ?>			
	<?php endif;	
  }


  function form( $instance ) {
	$title = esc_attr( $instance['title'] ); ?>
	<!-- What is shown on the widget once it's dragged into a widget area... -->
	<?php
  }
}

function countdownTimerWidget_register_widgets() {
	register_widget( 'countdownTimerWidget' );
}
add_action( 'widgets_init', 'countdownTimerWidget_register_widgets' );
