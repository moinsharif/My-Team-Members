<?php
/**
* Plugin Name: My Team Members
* Plugin URI: https://wordpress.org/plugins/my-team-members/
* Description: Just task for Team Members
* Version: 1.0.0
* Requires at least: 5.2
* Requires PHP: 7.2
* Author: Moin Sharif
* Author URI: https://github.com/moinsharif
* License: GPLv3 or later
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
* Text Domain: myteam
*/

/*
Use [team_members]
or [team_members size=5 position="top/bottom" button="0"]
*/

// Start Register Custom Post
function custom_post_teammembers(){
	$postType = 'teammembers';
/*	if(get_option('myteam-postname') == null){
		$postType = 'teammembers';
	}else{
		$postType = get_option('myteam-postname');
	}*/
	register_post_type($postType,
	array(
	'labels' => array(
		'name' => ('Team Member List'),
		'singular_name' => ($postType),
		'add_new' => ('Add Team Member'),
		'add_new_item' => ('Add Team'),
		'edit_item' => ('Edit Team Members'),
		'new_item' => ('New Team Members'),
		'view_item' => ('View Team Members'),
		'not_found' => ('Sorry, Not Found'),
		'featured_image'        => 'Portfolio Image',
        'set_featured_image'    => 'Set Portfolio image',
        'remove_featured_image' => 'Remove Portfolio image',
        'use_featured_image'    => 'Use as Portfolio image',
	),
	'menu_icon' => 'dashicons-arrow-right-alt',
	'public' => true,
	'publicly_queryable' => true,
	'exclude_from_search' => true,
	'menu_position' => 5,
	'has_archive' => true,
	'hierarchial' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'rewrite' => array('slag' => $postType),
	'supports' => array('title', 'thumbnail', 'editor', 'excerpt'),
	)
	);
}
add_action('init', 'custom_post_teammembers');
// End Register Custom Post

// Start Custom Post Title Change
function myteam_change_title_text($title){
	$screen = get_current_screen();
	if  ( 'teammembers' == $screen->post_type ) {
		$title = 'Enter Team Member Name';
	}
	return $title;
}
add_filter( 'enter_title_here', 'myteam_change_title_text' );
// End Custom Post Title Change

// Start Register ShortCode
add_shortcode( 'team_members', 'myteam_shortcode' );
function myteam_shortcode( $atts ) {
	if(!isset($atts['size'])){$size = 3;}
		else{$size = $atts['size'];}
	if(!isset($atts['image'])){$image = "Top";}
		else{$image = $atts['image'];}
	if(!isset($atts['button'])){$button = 1;}
		else{$button = $atts['button'];}
	query_posts("post_type=teammembers&post_status=publish&posts_per_page=$size&order=DESC&paged=". get_query_var('post'));
	$designs = '<div class="team-members container" style="background:#D7D6D5;border-radius:15px;"><div class="owl-carousel row">';
	if(have_posts()) :
	while(have_posts()) : the_post();
	$designs .= '<div class="item col-sm-4">';
	if($image == "top"){ // For Image top design
		$designs .= '<a href="'.site_url().'/teammembers/'.get_post_field('post_name',get_post()).'"><div class="row" style="width: 100%;"><img class="team-img rounded-circle" src="'.get_the_post_thumbnail_url().'" alt="'.get_the_title().'" /></div>';
		$designs .= '<a href="'.site_url().'/teammembers/'.get_post_field('post_name',get_post()).'"><h2 class="text-center">'.get_the_title().'</h2></a>';
		$designs .= '<div class="text-center">'.get_the_excerpt().'</div>';
	}else{ // For Image bottom design
		$designs .= '<a href="'.site_url().'/teammembers/'.get_post_field('post_name',get_post()).'"><h2 class="text-center">'.get_the_title().'</h2></a>';
		$designs .= '<div class="text-center">'.get_the_excerpt().'</div>';
		$designs .= '<a href="'.site_url().'/teammembers/'.get_post_field('post_name',get_post()).'"><div class="row" style="width: 100%;"><img class="team-img rounded-circle" src="'.get_the_post_thumbnail_url().'" alt="'.get_the_title().'" /></div>';
	}
	$designs .= '</div>';
	// get_the_post_thumbnail_url(); represent Image
	// get_the_title(); represent Name
	// get_the_content(); represent Bio
	// get_the_excerpt(); represent Position
	endwhile;
	endif;
	$designs .= '</div>';
	if($button == 1){
		$designs .= '<div class="row text-center"><div class="col-sm-4 offset-sm-4"><a href="'.site_url().'?post_type=teammembers" class="btn btn-secondary" role="button">See All</a></div></div>';
	}
	$designs .= '</div>';
	return $designs;
}
// End Register ShortCode

// Start register_activation_hook for flush_rewrite_rules
register_activation_hook( __FILE__, 'myteam_plugin_activation' );
function myteam_plugin_activation(){
//	flush_rewrite_rules(true);
	add_option('myteam_plugin_do_activation_redirect', true);
}
add_action( 'admin_init', 'myteam_plugin_redirect');
function myteam_plugin_redirect(){
	if(get_option('myteam_plugin_do_activation_redirect', false)){
		delete_option('myteam_plugin_do_activation_redirect');
		if(!isset($_GET['active-multi'])){
			wp_safe_redirect(admin_url('options-permalink.php'));
			exit;
		}
	}
}
// End register_activation_hook for flush_rewrite_rules

// Start for single-teammember template
add_filter('single_template', 'myteam_custom_template');
function myteam_custom_template($single) {
    global $post;
    /* Checks for single template by post type */
    if ( $post->post_type == 'teammembers' ) {
		if ( file_exists( plugin_dir_path( __FILE__ ) . 'single-teammember.php' ) ) {
			return plugin_dir_path( __FILE__ ) . 'single-teammember.php';
		}
    }
    return $single;
}
// End for single-teammember template

// Start for enqueue CSS and JS
function myteam_front_script() {
//	wp_enqueue_style('team_members_owl_carousel_min_css', plugin_dir_url(__FILE__).'assets/css/owl.carousel.min.css');	
	wp_enqueue_style('team_members_bootstrap-min', plugin_dir_url(__FILE__).'assets/css/bootstrap.min.css');
	wp_enqueue_style('main-css', plugin_dir_url(__FILE__).'assets/css/main.css');
//	wp_enqueue_script('jquery');
//	wp_enqueue_script('team_members_owl_carousel_min_js', plugin_dir_url(__FILE__).'assets/js/owl.carousel.min.js');
}
add_action( 'wp_enqueue_scripts', 'myteam_front_script' );

function myteam_add_theme_css(){
  wp_enqueue_style( 'myteam-admin-style', plugins_url( 'assets/css/myteam-admin-style.css', __FILE__ ), false, "1.0.0");
}
add_action('admin_enqueue_scripts', 'myteam_add_theme_css');
// End for enqueue CSS and JS

// Start for setting page
function myteam_add_theme_page(){
//add_menu_page('Setting Page','Setting Pages','manage_options', 'myteam-plugin-option','myteam_create_page','dashicons-arrow-up-alt',5);
add_submenu_page(
    'edit.php?post_type=teammembers',
    'Help Pages',
    'Help Pages',
    'manage_options',
    'testsettings',
    'myteam_settings_page'
);
}
add_action('admin_menu','myteam_add_theme_page');
// End for setting page

function myteam_settings_page(){
    ?>
      <div class="stb_main_area">
        <div class="stb_body_area stb_common">
          <h3 id="title"><?php print esc_attr( '🎨 Help for Team Members' ); ?></h3>
          <form action="options.php" method="post">
            <?php wp_nonce_field('update-options'); ?>

<!-- Tips of Team Members -->
<label for="myteam-shortcode"><?php echo esc_attr(__('How to Use')); ?></label>
<small>Content represent Bio<br>Excerpt represent Position/Designation<br>After change postType please <a href="<?=admin_url('options-permalink.php')?>">visit here</a>.</small>

            <!-- ShortCode of Team Members -->
			<label for="myteam-shortcode"><?php echo esc_attr(__('Short Code')); ?></label>
            <small>Copy shortcode to paste any post or page</small>
              <input type="text" name="stb-scroll-speed" id="myteam-shortcode" value="[team_members]" />
              <input type="text" name="stb-scroll-speed" value="[team_members size=3 image='bottom' button=0]" />

<!-- Post Name -->
<label for="myteam-postname"><?php echo esc_attr(__('Post Name')); ?></label>
<small>Input post name and save</small>
<label class="radios">
<input type="text" name="myteam-postname" id="myteam-postname" value="<?php if( get_option('myteam-postname') == null){echo 'teammembers';} else {print get_option('myteam-postname');}?>" />
</label>

<input type="hidden" name="action" value="update">
<input type="hidden" name="page_options" value="myteam-postname">
<input type="submit" name="submit" value="<?php _e('Save Changes', 'clpwp') ?>">
          </form>
        </div>
        <div class="stb_sidebar_area stb_common">
          <h3 id="title"><?php print esc_attr( '📝 About Author' ); ?></h3>
          <p><img src="<?php print plugin_dir_url(__FILE__) . '/assets/image/author.png' ?>" alt=""></p>
          <p>I'm <strong><a href="https://github.com/moinsharif" target="_blank">Moin Sharif</a></strong> (Web developer).<br>Also I am a game developer.</p>
          <p><a href="https://bmc.link/moinsharif" target="_blank"><img src="<?php print plugin_dir_url(__FILE__) . '/assets/image/coffee.png' ?>" alt=""></a></p>
          <h5 id="title"><?php print esc_attr( 'Watch Help Video' ); ?></h5>
          <p><a href="javascript:alert('Video not complete');" class="btn">Watch On YouTube</a></p>
        </div>
      </div>
    <?php
  }

?>