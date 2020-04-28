<?php

namespace Updater;

use Symfony\Component\Process\Process;
use RuntimeException;
use Exception;

require_once( "vendor/autoload.php" );

$folders_to_update = [
	'event-tickets',
	'event-tickets-plus',
	'events-community',
	'events-community-tickets',
	'events-filterbar',
	'events-pro',
	'event-tickets/common',
	'the-events-calendar/common',
];

foreach ( $folders_to_update as $folder_to_update ) {
	// (ノ°▽°)ノ
	echo (string) file_get_contents( "http://artii.herokuapp.com/make?text=$folder_to_update" ) . PHP_EOL;

	$path = __DIR__ . '/../' . $folder_to_update;

	try {
		$folder_to_update = new Folder_To_Update( $path );
		$folder_to_update->stash_existing_changes();
		$folder_to_update->checkout_master();
		$folder_to_update->git_pull();
		$folder_to_update->nvm_use( "8.9.4" );
		$folder_to_update->install_dependencies();
		if ( $folder_to_update !== "events-filterbar" ) {
			$folder_to_update->npm_build();
		}
		$folder_to_update->apply_stash();
	} catch ( Exception $e ) {
		echo $e->getMessage();
		continue;
	}
}

class Folder_To_Update {
	private $path;

	public function __construct( $path ) {
		if ( ! file_exists( $path ) ) {
			throw new RuntimeException( "Plugin folder does not exist: " . $path );
		}

		if ( ! file_exists( $path . '/composer.json' ) ) {
			throw new RuntimeException( "Plugin folder does not have composer.json, therefore does not seem to be a valid plugin folder: " . $path );
		}

		$this->path = $path;
	}

	public function stash_existing_changes() {
		$this->run_sync(
			'Stash Existing Changes',
			'git stash push --quiet --include-untracked -m "Stashing before automatic plugin update"'
		);
	}

	public function checkout_master() {
		$this->run_sync(
			'Checkout Master',
			'git checkout master'
		);
	}

	public function git_pull() {
		$this->run_sync(
			'Git Pull',
			'git pull'
		);
	}

	public function install_dependencies() {
		$composer_install = new Process( 'composer install' );
		$composer_install->setWorkingDirectory( $this->path );

		$npm_install = new Process( 'npm install' );
		$npm_install->setWorkingDirectory( $this->path );

		try {
			$this->print_in_red_background( "composer install" );
			$composer_install->start();

			$this->print_in_red_background( "npm install" );
			$npm_install->start();

			$this->print_in_red_background( "composer install 2" );
			$composer_install->wait( function ( $type, $buffer ) {
				echo $buffer;
			} );

			$this->print_in_red_background( "npm install 2" );
			$npm_install->wait( function ( $type, $buffer ) {
				echo $buffer;
			} );
		} catch ( Exception $e ) {
			throw new RuntimeException( sprintf( '%s failed on step "Composer and NPM Install" with message %s', $this->path, $e->getMessage() ) );
		}
	}

	/**
	 * @param string $version
	 */
	public function nvm_use( $version ) {
		$nvm_check = new Process( 'command -v nvm' );

		// Print the command being executed in a red background.
		$this->print_in_red_background( "Checking if NVM is available..." );
		$nvm_check->run( function ( $type, $buffer ) use ( $version ) {
			if ( $buffer !== "nvm" ) {
				throw new RuntimeException( "NVM is not available." );
			}

			$this->run_sync(
				"NVM Use Version $version",
				"nvm use $version"
			);
		} );
	}

	public function npm_build() {
		$this->run_sync(
			'NPM Run Build',
			'npm run build'
		);
	}

	public function apply_stash() {
		$this->run_sync(
			'Apply stash',
			'git stash apply{0}'
		);
	}

	private function run_sync( $step_name, $command ) {
		$process = new Process( $command );
		$process->setWorkingDirectory( $this->path );
		$process->setTty( true );

		try {
			$this->print_in_red_background( $command );
			$process->run( function ( $type, $buffer ) {
				echo $buffer;
			} );
		} catch ( Exception $e ) {
			throw new RuntimeException( sprintf( '%s failed on step "%s" with message %s', $this->path, $step_name, $e->getMessage() ) );
		}
	}

	private function print_in_red_background( $message ) {
		echo "\033[31;7m$message\e[0m" . PHP_EOL;
	}
}
