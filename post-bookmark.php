<?php declare(strict_types=1); defined('WPINC') || die;

/*
  Plugin Name: Post Bookmark
  Plugin URI: #
  Author: Gennaro Di Fiandra
  Author URI: #
  Description: Allows the logged in users to save each post in a personal list
  Version: 1.0.0
  Text Domain: post-bookmark
  Domain Path: /languages
  Requires at least: 6.0
  Requires PHP: 7.4
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define('POST_BOOKMARK_PATH', plugin_dir_path(__FILE__));
define('POST_BOOKMARK_URL', plugin_dir_url(__FILE__));


use PostBookmark\Templates\BookmarkMe;
use PostBookmark\Ajax\BookmarkMeButton as BookmarkMeButtonRequest;
use PostBookmark\Templates\BookmarkedPosts;
use PostBookmark\Ajax\BookmarkedPostsList as BookmarkedPostsListRequest;

use PostBookmark\Script;
use PostBookmark\InlineScript;
use PostBookmark\Nonce;

use PostBookmark\TranslationsLoader;
use PostBookmark\HooksActivator;

use PostBookmark\PluginStatus;

final class PostBookmark
{
  private static ?PostBookmark $instance = null;

  private BookmarkMe $bookmark_me;
  private BookmarkMeButtonRequest $bookmark_me_button_request;
  private BookmarkedPosts $bookmarked_posts;
  private BookmarkedPostsListRequest $bookmarked_posts_list_request;

  private array $scripts;
  private array $inline_scripts;
  private array $nonces;

  private TranslationsLoader $translations_loader;
  private HooksActivator $activator;
  private array $hooks_book = [];

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
  // others themes and plugins execute
  // remove_action and remove_filter
  // over the callbacks hooked into current plugin
  public function hooks_book()
  {
    return $this->hooks_book;
  }

  private function add_to_hooks_book($id, $object)
  {
    $this->hooks_book[$id] = $object;
  }

  public function init()
  {
    $this->require_resources();

    // handle "Bookmark Me" button and logic
    $this->bookmark_me = new BookmarkMe();
    $this->add_to_hooks_book('bookmark_me', $this->bookmark_me);
    // setups ajax request for "Bookmark Me" button
    $this->bookmark_me_button_request = new BookmarkMeButtonRequest();
    $this->add_to_hooks_book('bookmark_me_button_request', $this->bookmark_me_button_request);

    // handle "Bookmarked Posts" button, list, view and logic
    $this->bookmarked_posts = new BookmarkedPosts();
    $this->add_to_hooks_book('bookmarked_posts', $this->bookmarked_posts);
    // setups ajax request for "Bookmarked Posts" list
    $this->bookmarked_posts_list_request = new BookmarkedPostsListRequest();
    $this->add_to_hooks_book('bookmarked_posts_list_request', $this->bookmarked_posts_list_request);

    // add compiled js files
    $this->scripts['generated_scripts'] = new Script('post-bookmark-generated-scripts', POST_BOOKMARK_URL.'assets/_dist_/generated-scripts.js', []);
    foreach ($this->scripts as $k => $script)
    {
      $this->add_to_hooks_book($k, $script);
    }

    // add inline js scripts
    $this->inline_scripts['bookmark_me_button_script'] = new InlineScript(
      'post-bookmark-generated-scripts',
      'BookmarkMeButtonData',
      ['ajaxUrl' => admin_url('admin-ajax.php'),],
      'before'
    );
    $this->inline_scripts['bookmarked_posts_list_script'] = new InlineScript(
      'post-bookmark-generated-scripts',
      'BookmarkedPostsListData',
      ['ajaxUrl' => admin_url('admin-ajax.php'),],
      'before'
    );
    foreach ($this->inline_scripts as $k => $inline_script)
    {
      $this->add_to_hooks_book($k, $inline_script);
    }

    // setup nonces
    $this->nonces['bookmark_me_button_nonce'] = new Nonce('BookmarkMeButtonData', 'bookmark_me_button_nonce');
    $this->nonces['bookmarked_posts_list_nonce'] = new Nonce('BookmarkedPostsListData', 'bookmarked_posts_list_nonce');
    foreach ($this->nonces as $k => $nonce)
    {
      $this->add_to_hooks_book($k, $nonce);
    }

    // load strings translations
    $this->translations_loader = new TranslationsLoader();
    $this->add_to_hooks_book('translations_loader', $this->translations_loader);

    // activate hooks
    $this->activator = new HooksActivator();
    foreach ($this->hooks_book as $object)
      $this->activator->activate_hooks($object);
  }
}
PostBookmark::instance()->init();
register_deactivation_hook(__FILE__, [PluginStatus::instance(), 'on_deactivation']);