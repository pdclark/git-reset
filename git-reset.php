<?php
/*
Plugin Name: Git Reset
Plugin URI: https://github.com/brainstormmedia/git-reset
Description: Reset WordPress installs and database using Git. <strong>This plugin will cause data loss</strong>. The POINT of this plugin is to cause data loss! <em>Requirements:</em> Git installed on your server and being used to manage your WordPress installation. PHP allowed to run server commands via <code>exec()</code>. Non-senstive data, or deny access to <code>.sql</code> files via <code>.htaccess</code> (this plugin makes a database dump in your WordPress directory).
Version: 1.0
Author: Paul Clark, 10up
Author URI: http://pdclark.com 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

$Storm_Git_Reset = new Storm_Git_Reset();

class Storm_Git_Reset {

	var $commit_message = 'Git Reset Plugin: Update defaults';

	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 999 );

		// Logged in users only
		add_action('wp_ajax_git_reset', array( $this, 'git_reset' ) );
		add_action('wp_ajax_git_commit', array( $this, 'git_commit' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

	}

	public function admin_bar_menu() {
		if ( !is_admin_bar_showing() ) { return; }

		global $wp_admin_bar;

		$parent_id = 'storm-git-reset';

		$wp_admin_bar->add_menu( array( 'id' => $parent_id, 'title' => 'Reset', 'href' => false ));

		$wp_admin_bar->add_menu( array( 'parent' => $parent_id, 'title' => 'Update Defaults', 'href' => admin_url( 'admin-ajax.php?action=git_commit' ) ) );
		$wp_admin_bar->add_menu( array( 'parent' => $parent_id, 'title' => 'Reset WordPress', 'href' => admin_url( 'admin-ajax.php?action=git_reset'  ) ) );

	}

	/**
	 * Revert to last git commit, remove untracked files, and import the database dump.
	 */
	public function git_reset() {
		$command = 'cd "' . ABSPATH . '"; git reset --hard; git clean -df; cat db.sql | mysql ' . DB_NAME ;

		exec( $command );

		$url = add_query_arg( 'git_reset_notice', 'reset', admin_url() );
		wp_redirect( $url );
		exit;
	}

	/**
	 * Run a database dump and Git commit. Does not protect database dump!!!
	 */
	public function git_commit() {
		$user = wp_get_current_user();

		$command = 'cd "' . ABSPATH . '" && mysqldump --add-drop-table -u ' . DB_USER . ' -p\'' . DB_PASSWORD . '\' -h ' . DB_HOST . ' ' . DB_NAME . ' > db.sql && git add . && git commit -am "' . $this->commit_message . '" --author="'. $user->display_name .' <'. $user->user_email .'>"' ;
 
 		exec( $command );

		$url = add_query_arg( 'git_reset_notice', 'commit', admin_url() );
		wp_redirect( $url );
		exit;
	}

	public function admin_notices() {

		switch( @$_GET['git_reset_notice'] ) {
			case 'commit':
				$message = '<p>New defaults set.</p>';
				break;
			case 'reset':
				$message = '<p>Reset complete.</p>';
				break;
		}

		if ( !empty( $message ) ){ 
			echo "<div class='updated fade' id='git-reset-notice'>$message</div>";
		}

	}

}
