<?php
/**
 * AlpineBot Primary
 *
 * Holds paramaters and settings specific to this plugin
 * Some universal functions, but mostly unique
 *
 */
class PhotoTileForPinterestPrimary {

  /* Set constants for plugin */
  private $url;
  private $dir;
  private $cacheUrl;
  private $cacheDir;
  private $ver = '1.2.7';
  private $vers = '1-2-7';
  private $domain = 'APTFPINbyTAP_domain';
  private $settings = 'alpine-photo-tile-for-pinterest-settings'; // All lowercase
  private $name = 'Alpine PhotoTile for Pinterest';
  private $info = 'http://thealpinepress.com/alpine-phototile-for-pinterest/';
  private $wplink = 'http://wordpress.org/extend/plugins/alpine-photo-tile-for-pinterest/';
  private $donatelink = 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eric%40thealpinepress%2ecom&lc=US&item_name=Alpine%20PhotoTile%20for%20Pinterest%20Donation&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted';
  private $page = 'AlpineTile: Pinterest';
  private $src = 'pinterest';
  private $hook = 'APTFPINbyTAP_hook';
  private $plugins = array('flickr','tumblr','instagram','smugmug');
  private $termsofservice = "By using this plugin, you are agreeing to the Pinterest <a href='http:http://about.pinterest.com/use/' target='_blank'>Acceptable Use Policy</a>.";

  private $root = 'AlpinePhotoTiles';
  private $wjs = 'AlpinePhotoTiles_script';
  private $wcss = 'AlpinePhotoTiles_style';
  private $ajs = 'AlpinePhotoTiles_menu_script';
  private $acss = 'AlpinePhotoTiles_admin_style';
  private $wdesc = 'Add images from Pinterest to your sidebar';
//####### DO NOT CHANGE #######//
  private $short = 'alpine-phototile-for-pinterest';
  private $id = 'APTFPIN_by_TAP';
//#############################//
  private $expiryInterval = 360; //1*60*60;  1 hour
  private $cleaningInterval = 1209600; //14*24*60*60;  2 weeks

  // Output Constants
  private $options = array(); // includes 'rel'
  private $results = array('photos'=>array(),'feed_found'=>false,'success'=>false,'userlink'=>'','hidden'=>'','message'=>'');
  private $output = '';
  private $wid; // Widget id

  private $userlink = '';
  private $cacheLimit = 2;
  private $cacheAttempts = 0;

  function __construct() {
    $this->url = untrailingslashit( plugins_url( '' , dirname(__FILE__) ) );
    $this->dir = untrailingslashit( plugin_dir_path( dirname(__FILE__) ) );

    $this->cacheUrl = $this->url . '/cache';
    $this->cacheDir = $this->dir . '/cache';
  }
/**
 * Prevent errors by avoiding direct calls to functions
 *
 * @ Since 1.2.5
 *
 */
  function do_alpine_method($function, $input=array()){
    //echo $function.'() called<br>';
    if( method_exists( $this, $function )){
      if( empty($input) ){
        $this->$function();
      }else{
        $this->$function($input);
      }
    }
  }
/**
 * Prevent errors by avoiding direct calls to functions
 *
 * @ Since 1.2.5
 *
 */
  function get_alpine_method($function, $input=array()){
    echo $function.'() with return called<br>';
    if( method_exists( $this, $function )){
      if( empty($input) ){
        $return = $this->$function();
      }else{
        $return = $this->$function($input);
      }
    }
    if( isset($return) ){
      return $return;
    }
    return null;
  }
/**
 * Simple get function
 *
 * @ Since 1.2.5
 *
 */
  function get_private($string){
    if(isset($this->$string)){
      return $this->$string;
    }else{
      return null;
    }
  }
/**
 * Simple set function
 *
 * @ Since 1.2.5
 *
 */
  function set_private($string,$val){
    $this->$string = $val;
  }
/**
 * Simple set function
 *
 * @ Since 1.2.5
 *
 */
  function check_private($string){
    if( !empty($this->$string)){
      return true;
    }else{
      return false;
    }
  }
/**
 * Simple get function
 *
 * @ Since 1.2.5
 *
 */
  function get_active_option($string){
    if(isset($this->options[$string])){
      return $this->options[$string];
    }else{
      return false;
    }
  }
/**
 * Simple set function
 *
 * @ Since 1.2.5
 *
 */
  function set_active_option($string,$val){
    $this->options[$string] = $val;
  }
/**
 * Simple check function
 *
 * @ Since 1.2.5
 *
 */
  function check_active_option($string){
    if(!empty($this->options[$string])){
      return true;
    }else{
      return false;
    }
  }
/**
 * Simply get function for search results that returns content
 *
 * @ Since 1.2.5
 *
 */
  function get_active_result($string){
    if(isset($this->results[$string])){
      return $this->results[$string];
    }else{
      return '';
    }
  }
/**
 * Simple set function
 *
 * @ Since 1.2.5
 *
 */
  function set_active_result($string,$val){
    $this->results[$string] = $val;
  }
/**
 * Simply check function for search results that returns boolean
 *
 * @ Since 1.2.5
 *
 */
  function check_active_result($string){
    if(empty($this->results[$string])){
      return false;
    }else{
      return true;
    }
  }
/**
 * Function for appending to specific result
 *
 * @ Since 1.2.5
 *
 */
  function append_active_result($string,$add){
    if(isset($this->results[$string])){
      $this->results[$string] = ($this->results[$string]).$add;
    }
  }
/**
 * Push photo to results
 *
 * @ Since 1.2.5
 *
 */
  function push_photo($array){
    $this->results['photos'][] = $array;
  }
/**
 * Get photo information
 *
 * @ Since 1.2.5
 *
 */
  function get_photo_info($i,$string){
    if( isset($this->results['photos'][$i][$string]) ){
      return $this->results['photos'][$i][$string];
    }
    return null;
  }
/**
 * Append to output
 *
 * @ Since 1.2.5
 *
 */
  function add($string){
    $this->output = ($this->output).$string;
  }
//////////////////////////////////////////////////////////////////////////////////////
/////////////////////      Style/Script Functions        /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
/**
 * Register styles and scripts
 *
 * @ Since 1.2.3
 * @ Updated 1.2.5
 *
 */
  function register_style_and_script(){
    wp_register_script($this->get_private('wjs'),$this->get_script('widget'),'',$this->get_private('ver'));
    wp_register_style($this->get_private('wcss'),$this->get_style('widget'),'',$this->get_private('ver'));

    $lightbox = $this->get_option('general_lightbox');
    $prevent = $this->get_option('general_lightbox_no_load');

    $script = $this->get_script( $lightbox );
    $css = $this->get_style( $lightbox );

    if( !empty( $script ) && !empty( $css ) && empty($prevent) ){
      wp_register_script( $lightbox, $script, '', '', true );
      wp_register_style( $lightbox.'-stylesheet', $css, false, '', 'screen' );
    }

    // Load scripts in header
    $headerload = $this->get_option('general_load_header');
    if( !empty($headerload) ){
      if( !empty( $script ) && !empty( $css ) && empty($prevent) ){
        wp_enqueue_script( $lightbox );
        wp_enqueue_style( $lightbox.'-stylesheet' );
      }
      wp_enqueue_script($this->get_private('wjs'));
      wp_enqueue_style($this->get_private('wcss'));
    }
  }
/**
 * Enqueue styles and scripts
 *
 * @ Since 1.2.3
 * @ Updated 1.2.5
 *
 */
  function enqueue_style_and_script(){
    // Check link destination
    $link = $this->get_active_option( $this->get_private('src').'_image_link_option' );
    if( !empty($link) && $link == 'fancybox' ){
      $lightbox = $this->get_option('general_lightbox');
      $prevent = $this->get_option('general_lightbox_no_load');
      if( empty($prevent) ){
        wp_enqueue_script( $lightbox );
        wp_enqueue_style( $lightbox.'-stylesheet' );
      }
    }
    wp_enqueue_style( $this->get_private('wcss') );
    wp_enqueue_script( $this->get_private('wjs') );
  }
/**
 * Simply get function for JS files
 *
 * @ Since 1.2.5
 * @ Updated 1.2.6.1
 *
 */
  function get_script($string){
    if( 'admin' == $string ){
      return $this->url.'/js/'.$this->ajs.'.js?ver='.$this->ver;
    }elseif( 'widget' == $string ){
      return $this->url.'/js/'.$this->wjs.'.js?ver='.$this->ver;
    }elseif( 'fancybox' == $string ){
      return $this->url.'/js/fancybox/jquery.fancybox-1.3.4.pack.js?ver=1.3.4';
    }elseif( 'prettyphoto' == $string ){
      return $this->url.'/js/prettyPhoto/js/jquery.prettyPhoto.js?ver=3.1.6';
    }elseif( 'colorbox' == $string ){
      return $this->url.'/js/colorbox/jquery.colorbox-min.js?ver=1.4.33';
    }elseif( 'alpine-fancybox' == $string ){
      return $this->url.'/js/fancybox-alpine-safemode/jquery.fancyboxForAlpine-1.3.4.pack.js?ver=1.3.4';
    }
    return false;
  }
/**
 * Simply get function for CSS files
 *
 * @ Since 1.2.5
 * @ Update 1.2.6.1
 *
 */
  function get_style($string){
    if( 'admin' == $string ){
      return $this->url.'/css/'.$this->acss.'.css?ver='.$this->ver;
    }elseif( 'widget' == $string ){
      return $this->url.'/css/'.$this->wcss.'.css?ver='.$this->ver;
    }elseif( 'fancybox' == $string ){
      return $this->url.'/js/fancybox/jquery.fancybox-1.3.4.css?ver=1.3.4';
    }elseif( 'prettyphoto' == $string ){
      return $this->url.'/js/prettyPhoto/css/prettyPhoto.css?ver=3.1.5';
    }elseif( 'colorbox' == $string ){
      return $this->url.'/js/colorbox/colorbox.css?ver=1.4.33';
    }elseif( 'alpine-fancybox' == $string ){
      return $this->url.'/js/fancybox-alpine-safemode/jquery.fancyboxForAlpine-1.3.4.css?ver=1.3.4';
    }
    return false;
  }
//////////////////////////////////////////////////////////////////////////////////////
/////////////////////////      Option Functions      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
/**
 *  Simple function to get option setting
 *
 *  @ Since 1.2.0
 *  @ Updated 1.2.5
 */
  function get_option( $option_string ){
    $options = get_option( $this->settings );
    // No need to initialize options since defaults are applied as needed
    $this->options[$option_string] = ( isset($options[$option_string]) ? $options[$option_string] : $this->set_default_option( $options, $option_string ) );
    return $this->options[$option_string];
  }
/**
 *  Simple function to array of all option settings
 *
 *  @ Since 1.2.0
 *  @ Updated 1.2.5
 */
  function get_all_options(){
    $options = get_option( $this->settings );
    $defaults = $this->option_defaults();
    foreach( $defaults as $option_string => $details ){
      if( !isset($options[$option_string]) && !empty($defaults[$option_string]) && isset($defaults[$option_string]['default']) ){
        $options[$option_string] = $defaults[$option_string]['default'];
      }elseif( !isset($options[$option_string]) && !empty($defaults[$option_string]) && !isset($defaults[$option_string]['default']) ){
        $options[$option_string] = '';
      }
    }
    update_option( $this->settings, $options ); //Unnecessary since options will soon be updated if this fuction was called
    return $options;
  }
/**
 *  Correctly set and save the option's default setting
 *
 *  @ Since 1.2.0
 */
  function set_default_option( $options, $option_string ){
    $default_options = $this->option_defaults();
    if( !empty($default_options[$option_string]) && isset($default_options[$option_string]['default']) ){
      $options[$option_string] = $default_options[$option_string]['default'];
      update_option( $this->settings, $options );
      return $options[$option_string];
    }else{
      return '';
    }
  }
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////      Admin Option Functions       /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
/**
 * Option positions for widget page
 *
 * @ Since 1.2.0
 *
 */
  function admin_widget_positions(){
      $options = array(
      'top' => '',
      'left' => 'Pinterest Settings',
      'right' => 'Style Settings',
      'bottom' => 'Format Settings'
    );
    return $options;
  }

/**
 * Option positions for settings pages
 *
 * @ Since 1.2.0
 * @ Updated 1.2.5
 */
  function admin_option_positions(){
    $positions = array(
      'generator' => array(
        'left' => array( 'title' => 'Pinterest Settings' ),
        'right' => array( 'title' => 'Style Settings' ),
        'bottom' => array( 'title' => 'Format Settings' )
      ),
      'plugin-settings' => array(
        'top' => array( 'title' => 'Global Style Options', 'description' => 'Below are style settings that will be applied to every instance of the plugin.' ),
        'center' => array( 'title' => 'Hidden Options', 'description' => 'Below are additional options that you can choose to enable by checking the box. <br>Once enabled, the option will appear in the Widget Menu and Shortcode Generator.' ),
        'bottom' => array( 'title' => 'Cache Options', 'description' => 'The plugin is capable of storing the url addresses to the photos in your feed. Please note that the plugin does not store the image files and that if your website has a cache plugin like WP Super Cache or W3 Total Cache, the cache feature of the Alpine PhotoTile will have no effect.')
      )
    );
    return $positions;
  }
/**
 * Plugin Admin Settings Page Tabs
 *
 * @ Since 1.2.0
 *
 */
  function admin_settings_page_tabs() {
    $tabs = array(
      'general' => array(
        'name' => 'general',
        'title' => 'General',
      ),
      'generator' => array(
        'name' => 'generator',
        'title' => 'Shortcode Generator',
      ),
      'plugin-settings' => array(
        'name' => 'plugin-settings',
        'title' => 'Plugin Settings',
      )
    );
    return $tabs;
  }
/**
 * Option Parameters and Defaults
 *
 * @ Since 1.0.0
 * @ Updated 1.2.6.1
 */
  function option_defaults(){
    $options = array(
      'widget_title' => array(
        'name' => 'widget_title',
        'title' => 'Title : ',
        'type' => 'text',
        'description' => '',
        'since' => '1.1',
        'widget' => true,
        'tab' => '',
        'position' => 'top',
        'default' => ''
      ),
      'pinterest_source' => array(
        'name' => 'pinterest_source',
        'short' => 'src',
        'title' => 'Retrieve Photos From : ',
        'type' => 'select',
        'valid_options' => array(
          'user' => array(
            'name' => 'user',
            'title' => 'User'
          ),
          'board' => array(
            'name' => 'board',
            'title' => 'Board'
          )
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'pinterest_source',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => 'user'
      ),
      'pinterest_user_id' => array(
        'name' => 'pinterest_user_id',
        'short' => 'uid',
        'title' => 'Pinterest Username : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => "",
        'child' => 'pinterest_source',
        'hidden' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),
      'pinterest_user_board' => array(
        'name' => 'pinterest_user_board',
        'title' => 'Pinterest Board Tag: ',
        'short' => 'board',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'remove' => '&amp;', // First delete '&',
        'replace' => '-', // Then replace spaces with '-'
        'description' => '',
        'child' => 'pinterest_source',
        'hidden' => 'user',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),
      'pinterest_image_link_option' => array(
        'name' => 'pinterest_image_link_option',
        'short' => 'imgl',
        'title' => 'Image Links : ',
        'type' => 'select',
        'valid_options' => array(
          'none' => array(
            'name' => 'none',
            'title' => 'Do not link images'
          ),
          'pinterest' => array(
            'name' => 'pinterest',
            'title' => 'Link to Pinterest Page'
          ),
          'link' => array(
            'name' => 'link',
            'title' => 'Link to URL Address'
          ),
          'fancybox' => array(
            'name' => 'fancybox',
            'title' => 'Use Lightbox'
          )
        ),
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'pinterest_image_link_option',
        'default' => 'pinterest'
      ),
      // 'custom_lightbox_rel' => array(
      //   'name' => 'custom_lightbox_rel',
      //   'short' => 'crel',
      //   'title' => 'Custom Lightbox "rel" (Optional): ',
      //   'type' => 'text',
      //   'sanitize' => 'nospaces',
      //   'encode' => array("["=>"{ltsq}","]"=>"{rtsq}"),
      //   'description' => '',
      //   'child' => 'pinterest_image_link_option',
      //   'hidden' => 'none original pinterest link',
      //   'widget' => true,
      //   'hidden-option' => true,
      //   'check' => 'hidden_lightbox_custom_rel',
      //   'tab' => 'generator',
      //   'position' => 'left',
      //   'since' => '1.2.3',
      //   'default' => ''
      // ),
      'custom_link_url' => array(
        'name' => 'custom_link_url',
        'title' => 'Custom Link URL : ',
        'short' => 'curl',
        'type' => 'text',
        'sanitize' => 'url',
        'description' => '',
        'child' => 'pinterest_image_link_option',
        'hidden' => 'none original pinterest fancybox',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => ''
      ),
      'pinterest_pin_it_button' => array(
        'name' => 'pinterest_pin_it_button',
        'short' => 'pinit',
        'title' => 'Include Pin It Button.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),
      'pinterest_display_link' => array(
        'name' => 'pinterest_display_link',
        'short' => 'dl',
        'title' => 'Display link to Pinterest page.',
        'type' => 'checkbox',
        'description' => '',
        'child' => 'pinterest_source',
        'hidden' => 'community',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_display_link',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => ''
      ),
      'pinterest_display_link_style' => array(
        'name' => 'pinterest_display_link_style',
        'short' => 'dlstyle',
        'title' => 'Pinterest link style : ',
        'type' => 'select',
        'valid_options' => array(
          'large' => array(
            'name' => 'large',
            'title' => 'Large'
          ),
          'medium' => array(
            'name' => 'medium',
            'title' => 'Medium'
          ),
          'small' => array(
            'name' => 'small',
            'title' => 'Small'
          ),
          'tiny' => array(
            'name' => 'tiny',
            'title' => 'Tiny'
          ),
          'text' => array(
            'name' => 'text',
            'title' => 'Text'
          )
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'pinterest_display_link_style',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_display_link',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => 'medium'
      ),
      'pinterest_display_link_text' => array(
        'name' => 'pinterest_display_link_text',
        'short' => 'dltext',
        'title' => 'Link Text : ',
        'type' => 'text',
        'sanitize' => 'nohtml',
        'description' => '',
        'child' => 'pinterest_display_link_style',
        'hidden' => 'large medium small tiny',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_display_link',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => 'Pinterest'
      ),

      'style_option' => array(
        'name' => 'style_option',
        'short' => 'style',
        'title' => 'Style : ',
        'type' => 'select',
        'valid_options' => array(
          'carousel' => array(
            'name' => 'carousel',
            'title' => 'Carousel'
          ),
          'vertical' => array(
            'name' => 'vertical',
            'title' => 'Vertical'
          ),
          'windows' => array(
            'name' => 'windows',
            'title' => 'Windows'
          ),
          'bookshelf' => array(
            'name' => 'bookshelf',
            'title' => 'Bookshelf'
          ),
          'rift' => array(
            'name' => 'rift',
            'title' => 'Rift'
          ),
          'floor' => array(
            'name' => 'floor',
            'title' => 'Floor'
          ),
          'wall' => array(
            'name' => 'wall',
            'title' => 'Wall'
          ),
          'cascade' => array(
            'name' => 'cascade',
            'title' => 'Cascade'
          ),
          'gallery' => array(
            'name' => 'gallery',
            'title' => 'Gallery'
          )
        ),
        'description' => 'If nothing displays, try Vertical or Cascade. Also, try clicking the box for "Load Styles and Scripts in Header" on the <a href="options-general.php?page='.$this->get_private('settings').'&tab=plugin-settings" target="_blank">settings page</a>.',
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'style_option',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'vertical'
      ),
      'style_shape' => array(
        'name' => 'style_shape',
        'short' => 'shape',
        'title' => 'Shape : ',
        'type' => 'select',
        'valid_options' => array(
          'rectangle' => array(
            'name' => 'rectangle',
            'title' => 'Rectangle'
          ),
          'square' => array(
            'name' => 'square',
            'title' => 'Square'
          )
        ),
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade floor wall rift bookshelf gallery carousel webstory',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'vertical'
      ),
      'style_photo_per_row' => array(
        'name' => 'style_photo_per_row',
        'short' => 'row',
        'title' => 'Photos per row : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '30',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows carousel webstory',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'style_photo_height' => array(
        'name' => 'style_photo_height',
        'short' => 'pheight',
        'title' => 'Photos Height : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '30',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows carousel webstory',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '16'
      ),
      'style_column_number' => array(
        'name' => 'style_column_number',
        'short' => 'col',
        'title' => 'Number of columns (Desktop) : ',
        'type' => 'range',
        'min' => '1',
        'max' => '12',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '5'
      ),
      'tab_column_number' => array(
        'name' => 'tab_column_number',
        'short' => 'tcol',
        'title' => 'Number of columns (Tablet) : ',
        'type' => 'range',
        'min' => '1',
        'max' => '12',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery cascade',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '3'
      ),
      'mob_column_number' => array(
        'name' => 'mob_column_number',
        'short' => 'mcol',
        'title' => 'Number of columns (Mobile) : ',
        'type' => 'range',
        'min' => '1',
        'max' => '12',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery cascade',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '2'
      ),
      'equal_height' => array(
        'name' => 'equal_height',
        'short' => 'equal_height',
        'title' => 'Equal Height : ',
          'type' => 'select',
          'valid_options' => array(
            'true' => array(
              'name' => "true",
              'title' => 'True'
            ),
            'false' => array(
              'name' => "false",
              'title' => 'False'
            )
          ),

        'widget' => true,
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery cascade webstory',
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'false'
      ),
  'auto_play' => array(
        'name' => 'auto_play',
        'short' => 'auto_play',
        'title' => 'Autoplay : ',
          'type' => 'select',
          'valid_options' => array(
            'true' => array(
              'name' => "true",
              'title' => 'True'
            ),
            'false' => array(
              'name' => "false",
              'title' => 'False'
            )
          ),
          'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery cascade',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'false'
      ),
'autoplaytimeout' => array(
        'name' => 'autoplaytimeout',
        'short' => 'autoplaytimeout',
        'title' => 'Autoplay Timeout : ',
        'type' => 'text',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows gallery rift wall bookshelf',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '3000'
      ),
      'image_margin' => array(
        'name' => 'image_margin',
        'short' => 'margin',
        'title' => 'Margin : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '80',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows gallery rift wall bookshelf cascade',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '10'
      ),

      'navigation' => array(
        'name' => 'navigation',
        'short' => 'nav',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery cascade',
        'title' => 'Navigation : ',
          'type' => 'select',
          'valid_options' => array(
            'true' => array(
              'name' => "true",
              'title' => 'True'
            ),
            'false' => array(
              'name' => "false",
              'title' => 'False'
            )
          ),

        'widget' => true,
        'child' => 'style_option',
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'false'
      ),
      'style_gallery_ratio_width' => array(
        'name' => 'style_gallery_ratio_width',
        'short' => 'grwidth',
        'title' => 'Aspect Ratio Width : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "",
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift cascade carousel webstory',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'since' => '1.2.3',
        'default' => '800'
      ),
      'style_gallery_ratio_height' => array(
        'name' => 'style_gallery_ratio_height',
        'short' => 'grheight',
        'title' => 'Aspect Ratio Height : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "Set the Aspect Ratio of the gallery display. <br>(Default: 800 by 600)",
        'widget' => true,
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift cascade webstory',
        'tab' => 'generator',
        'position' => 'right',
        'since' => '1.2.3',
        'default' => '600'
      ),
      'pinterest_photo_size' => array(
        'name' => 'pinterest_photo_size',
        'short' => 'size',
        'title' => 'Photo Size : ',
        'type' => 'select',
        'valid_options' => array(
          '75' => array(
            'name' => 75,
            'title' => '75px'
          ),
          '192' => array(
            'name' => 192,
            'title' => '192px'
          ),
          '236' => array(
            'name' => 236,
            'title' => '236px'
          ),
          '554' => array(
            'name' => 554,
            'title' => '554px'
          ),
          '600' => array(
            'name' => 600,
            'title' => '600px'
          )
        ),
        'description' => 'If images appear blank, try setting size to 236px.',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '236'
      ),
      'pinterest_photo_number' => array(
        'name' => 'pinterest_photo_number',
        'short' => 'num',
        'title' => 'Number of photos : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '25',
        'description' => 'Maximum of 25.',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'style_shadow' => array(
        'name' => 'style_shadow',
        'short' => 'shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
         'hidden' => 'carousel webstory',
        'position' => 'right',
        'default' => ''
      ),
      'style_border' => array(
        'name' => 'style_border',
        'short' => 'border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),
      'style_highlight' => array(
        'name' => 'style_highlight',
        'short' => 'highlight',
        'title' => 'Highlight when hovering.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'short' => 'curve',
        'title' => 'Add border radius to corners.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '7px',
      ),
      'border_radius_textbox' => array(
        'name' => 'border_radius_textbox',
        'short' => 'radius_textbox',
        'title' => 'Border Radius',
        'type' => 'text',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift cascade',
        'default' => 7,
      ),
      'radius_ex' => array(
        'name' => 'radius_ex',
        'short' => 'radius_ex',
        'title' => ' ',
          'type' => 'select',
          'valid_options' => array(
            'pixel' => array(
              'name' => "pixel",
              'title' => 'px'
            ),
            'percent' => array(
              'name' => "percent",
              'title' => '%'
            )
          ),

        'widget' => true,
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift cascade',
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'px'
      ),
      'widget_alignment' => array(
        'name' => 'widget_alignment',
        'short' => 'align',
        'title' => 'Photo alignment : ',
        'type' => 'select',
        'valid_options' => array(
          'left' => array(
            'name' => 'left',
            'title' => 'Left'
          ),
          'center' => array(
            'name' => 'center',
            'title' => 'Center'
          ),
          'right' => array(
            'name' => 'right',
            'title' => 'Right'
          )
        ),
        'hidden-option' => true,
        'check' => 'hidden_widget_alignment',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'since' => '1.2.3',
        'default' => 'center'
      ),
      'widget_max_width' => array(
        'name' => 'widget_max_width',
        'short' => 'max',
        'title' => 'Maximum widget width : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'max' => '100',
        'description' => "Percentage (%) between 1 and 100.",
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => '100'
      ),
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'short' => 'nocredit',
        'title' => 'Disable the tiny "TAP" link in the bottom left corner, though I would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => ''
      ),
      'general_disable_right_click' => array(
        'name' => 'general_disable_right_click',
        'title' => 'Disable Right-Click: ',
        'type' => 'checkbox',
        'description' => 'Prevent visitors from right-clicking and downloading images.',
        'since' => '1.2.4',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),
      'general_loader' => array(
        'name' => 'general_loader',
        'title' => 'Disable Loading Icon: ',
        'type' => 'checkbox',
        'description' => 'Remove the icon that appears while images are loading.',
        'since' => '1.2.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),
      'general_highlight_color' => array(
        'name' => 'general_highlight_color',
        'title' => 'Highlight Color:',
        'type' => 'color',
        'description' => 'Click to choose link color.',
        'section' => 'settings',
        'tab' => 'general',
        'since' => '1.2.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => '#64a2d8'
      ),
      'general_hide_message' => array(
        'name' => 'general_hide_message',
        'title' => 'Hide error messages: ',
        'type' => 'checkbox',
        'description' => 'Prevent the plugin from displaying error messages.',
        'since' => '1.2.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),
      'general_load_header' => array(
        'name' => 'general_load_header',
        'title' => 'Load Styles and <br>Scripts in Header: ',
        'type' => 'checkbox',
        'description' => 'For themes without a wp_footer() call, load the plugin CSS styles and JS scripts in the head of every page.',
        'since' => '1.2.5',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),
      'general_lightbox_no_load' => array(
        'name' => 'general_lightbox_no_load',
        'title' => 'Prevent Lightbox Loading: ',
        'type' => 'checkbox',
        'description' => 'Already using the below jQuery Lightbox Plugin? Prevent this plugin from loading it again.',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'since' => '1.2.3',
        'default' => ''
      ),
      'general_lightbox' => array(
        'name' => 'general_lightbox',
        'title' => 'Choose jQuery Lightbox Plugin : ',
        'type' => 'select',
        'valid_options' => array(
          'alpine-fancybox' => array(
            'name' => 'alpine-fancybox',
            'title' => 'Fancybox (Safemode)'
          ),
          'fancybox' => array(
            'name' => 'fancybox',
            'title' => 'Fancybox'
          ),
          'colorbox' => array(
            'name' => 'colorbox',
            'title' => 'ColorBox'
          ),
          'prettyphoto' => array(
            'name' => 'prettyphoto',
            'title' => 'prettyPhoto'
          )
        ),
        'tab' => 'plugin-settings',
        'position' => 'top',
        'since' => '1.2.3',
        'default' => 'alpine-fancybox'
      ),
      // 'general_lightbox_params' => array(
      //   'name' => 'general_lightbox_params',
      //   'title' => 'Custom Lightbox Parameters:',
      //   'type' => 'textarea',
      //   'sanitize' => 'stripslashes',
      //   'description' => 'Add custom parameters to the lighbox call.',
      //   'section' => 'settings',
      //   'tab' => 'general',
      //   'since' => '1.2.3',
      //   'tab' => 'plugin-settings',
      //   'position' => 'top',
      //   'default' => ''
      // ),

      'hidden_display_link' => array(
        'name' => 'hidden_display_link',
        'title' => 'Link Below Widget: ',
        'type' => 'checkbox',
        'description' => 'Place a link with custom text below widget display.',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => true
      ),
      'hidden_widget_alignment' => array(
        'name' => 'hidden_widget_alignment',
        'title' => 'Photo Alignment: ',
        'type' => 'checkbox',
        'description' => 'Align photos to the left, right, or center.',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => ''
      ),
      // 'hidden_lightbox_custom_rel' => array(
      //   'name' => 'hidden_lightbox_custom_rel',
      //   'title' => 'Custom "rel" for Lightbox: ',
      //   'type' => 'checkbox',
      //   'description' => 'Set custom "rel" to widget options.',
      //   'since' => '1.2.3',
      //   'tab' => 'plugin-settings',
      //   'position' => 'center',
      //   'default' => ''
      // ),
      'cache_disable' => array(
        'name' => 'cache_disable',
        'title' => 'Disable feed caching: ',
        'type' => 'checkbox',
        'description' => 'Fetch the photo feed each time someone visits your website.',
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'bottom',
        'default' => ''
      ),
      'cache_time' => array(
        'name' => 'cache_time',
        'title' => 'Cache time (hours) : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "Set the number of hours that a feed will be stored.",
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'bottom',
        'default' => '4'
      )
    );
    return $options;
  }
}
