<?php

namespace WP_Forge\Command\Directives;

use WP_CLI;
use WP_Forge\Command\Concerns\Config;
use WP_Forge\Command\Concerns\Filesystem;
use WP_Forge\Command\Concerns\Registry;
use WP_Forge\Command\Concerns\Scaffolding;

/**
 * Class Copy
 */
class Copy extends AbstractDirective {

	use Config, Filesystem, Registry, Scaffolding;

	/**
	 * Type of copy action. Can be copyDir or copyFile.
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * File or directory path relative to the source path.
	 *
	 * @var string
	 */
	protected $from;

	/**
	 * File or directory path relative to the target directory.
	 *
	 * @var string
	 */
	protected $to;

	/**
	 * The full path to the directory containing files to be copied.
	 *
	 * @var string
	 */
	protected $sourceDir;

	/**
	 * The full path to the directory into which files will be copied.
	 *
	 * @var string
	 */
	protected $targetDir;

	/**
	 * Data to be used for template replacements.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Initialize properties for the directive.
	 *
	 * @param array $args Directive arguments.
	 */
	public function initialize( array $args ) {
		$this->from      = data_get( $args, 'from' );
		$this->to        = data_get( $args, 'to' );
		$this->targetDir = data_get( $args, 'relativeTo' ) === 'projectRoot' ? $this->projectConfig()->path() : getcwd();
		$this->sourceDir = $this->appendPath( $this->container( 'template_dir' ), $this->registry()->get( 'template' ) );
		$this->action    = is_dir( $this->appendPath( $this->sourceDir, $this->from ) ) ? 'copyDir' : 'copyFile';
		$this->data      = $this->registry()->get( 'data' )->toArray();
	}

	/**
	 * Validate the directive properties.
	 */
	public function validate() {

		if ( empty( $this->from ) ) {
			$this->error( 'Source path is missing!' );
		}

		if ( empty( $this->to ) ) {
			$this->error( 'Target path is missing!' );
		}

		if ( ! file_exists( $this->appendPath( $this->sourceDir, $this->from ) ) ) {
			$this->error( "Source path is invalid: {$this->from}" );
		}

	}

	/**
	 * Execute the directive.
	 */
	public function execute() {

		// Copy file(s)
		$this
			->scaffold()
			->withSourceDir( $this->sourceDir )
			->withTargetDir( $this->targetDir )
			->overwrite( $this->shouldOverwrite() )
			->{$this->action}( $this->from, $this->to, $this->data );
	}

	/**
	 * Check if we should overwrite files.
	 *
	 * @return bool
	 */
	protected function shouldOverwrite() {
		return (bool) data_get( WP_CLI::get_runner()->assoc_args, 'force', false );
	}

}
