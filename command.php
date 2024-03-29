<?php

use WP_Forge\WP_Scaffolding_Tool\Package;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

new Package(
	array(
		'base_command'             => 'forge',
		'template_config_filename' => 'config.json',
		'project_config_filename'  => '.wp-forge.json',
		'global_config_filename'   => '.wp-forge.json',
		'default_template_repo'    => 'git@github.com:wp-forge/scaffolding-templates.git',
	)
);
