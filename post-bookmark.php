<?php declare(strict_types=1); defined('WPINC') || die;

/*
  Plugin Name: Post Bookmark
  Plugin URI: #
  Author: Gennaro Di Fiandra
  Author URI: #
  Description: Allows the logged in users to save each post in a personal list.
  Version: 1.0.0
  Text Domain: post-bookmark
  Domain Path: /languages
  Requires at least: 6.0
  Requires PHP: 7.4
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define('POST_BOOKMARK_VERSION', '1.0.0');
define('POST_BOOKMARK_PATH', plugin_dir_path(__FILE__));
define('POST_BOOKMARK_URL', plugin_dir_url(__FILE__));

use PostBookmark\Ajax\BookmarkMeButton as BookmarkMeButtonRequest;
use PostBookmark\Ajax\BookmarkedPostsList as BookmarkedPostsListRequest;

use PostBookmark\Templates\BookmarkMe;
use PostBookmark\Templates\BookmarkedPosts;

use PostBookmark\Script;
use PostBookmark\InlineScript;
use PostBookmark\Nonce;

use PostBookmark\TranslationsLoader;

use PostBookmark\PluginStatus;

final class PostBookmark
{
  private static ?PostBookmark $instance = null;
  private array $hooked_objects = [];

  public static function instance():PostBookmark
  {
    if (self::$instance === null) self::$instance = new self();
    return self::$instance;
  }

  private function __construct() {}

  private function __clone() {}

  public function __wakeup() {}

  private function require_resources()
  {
    // autoload all classes from includes directory
    require_once __DIR__.'/vendor/autoload.php';
  }

  // this method has to be public to allow
  // other themes and plugins execute
  // remove_action and remove_filter
  // over the callbacks hooked inside
  public function get_hooked_objects()
  {
    return $this->hooked_objects;
  }

  private function register_hooked_objects($id, $object)
  {
    $this->hooked_objects[$id] = $object;
  }

  public function init()
  {
    $this->require_resources();

    // handle "Bookmark Me" button and logic
    $this->register_hooked_objects('bookmark_me', new BookmarkMe());
    add_filter('the_content', [$this->hooked_objects['bookmark_me'], 'print_button'], 10, 1);
    add_action('wp_head', [$this->hooked_objects['bookmark_me'], 'print_button_style'], 10, 0);

    // setups ajax request for "Bookmark Me" button
    $this->register_hooked_objects('bookmark_me_button_request', new BookmarkMeButtonRequest());
    add_action('wp_ajax_bookmark_me_button', [$this->hooked_objects['bookmark_me_button_request'], 'save_post_id'], 10, 0);

    // setup dependencies for "Bookmark Me" button (InlineScript and Nonce)
    $this->register_hooked_objects('bookmark_me_button_inline_script',  new InlineScript(
      'post-bookmark-generated-scripts',
      'BookmarkMeButtonData',
      ['ajaxUrl' => admin_url('admin-ajax.php'),],
      'before'
    ));
    add_action('wp_enqueue_scripts', [$this->hooked_objects['bookmark_me_button_inline_script'], 'register_script'], 11, 0);
    $this->register_hooked_objects('bookmark_me_button_nonce', new Nonce('BookmarkMeButtonData', 'bookmark_me_button_nonce'));
    add_filter('BookmarkMeButtonData', [$this->hooked_objects['bookmark_me_button_nonce'], 'add_nonce_to_array'], 10, 1);

    // handle "Bookmarked Posts" button, list, view and logic
    $this->register_hooked_objects('bookmarked_posts', new BookmarkedPosts());
    add_action('init', [$this->hooked_objects['bookmarked_posts'], 'register_route'], 10, 0);
    add_filter('query_vars', [$this->hooked_objects['bookmarked_posts'], 'register_query_var'], 10, 1);
    add_filter('template_include', [$this->hooked_objects['bookmarked_posts'], 'template'], 10, 1);
    add_action('wp_footer', [$this->hooked_objects['bookmarked_posts'], 'print_button'], 10, 0);
    add_action('wp_head', [$this->hooked_objects['bookmarked_posts'], 'print_button_style'], 10, 0);
    add_action('wp_head', [$this->hooked_objects['bookmarked_posts'], 'print_list_style'], 10, 0);

    // setups ajax request for "Bookmarked Posts" list
    $this->register_hooked_objects('bookmarked_posts_list_request', new BookmarkedPostsListRequest());
    add_action('wp_ajax_bookmarked_posts_list', [$this->hooked_objects['bookmarked_posts_list_request'], 'delete_post_id'], 10, 0);

    // setup dependencies for "Bookmarked Posts" list (InlineScript and Nonce)
    $this->register_hooked_objects('bookmarked_posts_list_inline_script', new InlineScript(
      'post-bookmark-generated-scripts',
      'BookmarkedPostsListData',
      ['ajaxUrl' => admin_url('admin-ajax.php'),],
      'before'
    ));
    add_action('wp_enqueue_scripts', [$this->hooked_objects['bookmarked_posts_list_inline_script'], 'register_script'], 11, 0);
    $this->register_hooked_objects('bookmarked_posts_list_nonce', new Nonce('BookmarkedPostsListData', 'bookmarked_posts_list_nonce'));
    add_filter('BookmarkedPostsListData', [$this->hooked_objects['bookmarked_posts_list_nonce'], 'add_nonce_to_array'], 10, 1);

    // add compiled js files
    $this->register_hooked_objects('generated_scripts', new Script('post-bookmark-generated-scripts', POST_BOOKMARK_URL.'/assets/_dist_/generated-scripts.js'));
    add_action('wp_enqueue_scripts', [$this->hooked_objects['generated_scripts'], 'register_script'], 10, 0);

    // load strings translations
    $this->register_hooked_objects('translations_loader', new TranslationsLoader());
    add_action('after_setup_theme', [$this->hooked_objects['translations_loader'], 'load_translations'], 10, 0);
  }
}
PostBookmark::instance()->init();
register_deactivation_hook(__FILE__, [PluginStatus::instance(), 'on_deactivation']);