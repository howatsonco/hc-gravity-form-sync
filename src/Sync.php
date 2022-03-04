<?php

namespace HC\GravityFormsSync;

use HC\GravityFormsSync\Traits\SingletonTrait;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * The core plugin class.
 */
final class Sync
{
	use SingletonTrait;

	/**
	 * Path to store JSON files in.
	 */
	private $path;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct()
	{
		$this->path = get_stylesheet_directory() . "/gf-json";
		$this->import();
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_action("gform_after_save_form", array($this, "export"), 10, 2);
		add_action("gform_post_form_activated", array($this, "export"), 10, 2);
		add_action("gform_post_form_deactivated", array($this, "export"), 10, 2);
		add_action("gform_after_delete_form", array($this, "remove"), 10, 2);
		add_action("gform_post_form_trashed", array($this, "remove"), 10, 2);
	}

	/**
	 * Scan JSON folder for files to be imported.
	 */
	public function scan()
	{
		$json_files = array();

		if (is_dir($this->path)) {
			$files = scandir($this->path);

			if ($files) {
				foreach ($files as $filename) {

					// Ignore hidden files.
					if ($filename[0] === '.') {
						continue;
					}

					// Ignore sub directories.
					$file = untrailingslashit($this->path) . '/' . $filename;
					if (is_dir($file)) {
						continue;
					}

					// Ignore non JSON files.
					$ext = pathinfo($filename, PATHINFO_EXTENSION);
					if ($ext !== 'json') {
						continue;
					}

					// Read JSON data.
					$json = json_decode(file_get_contents($file), true);
					if (!is_array($json) || !isset($json['id'])) {
						continue;
					}

					// Append data.
					$json_files[$json['id']] = $file;
				}
			}
		}

		return $json_files;
	}

	/**
	 * Save JSON file.
	 */
	public function save($file, $form)
	{
		$result = file_put_contents($file, wp_json_encode($form, JSON_PRETTY_PRINT));
		return is_int($result);
	}

	/**
	 * Load JSON file from local theme directory.
	 */
	public function import()
	{
		$files = $this->scan();

		foreach ($files as $id => $file) {
			$form_data = json_decode(file_get_contents($file), true);

			if (!empty($form_data) && isset($form_data)) {
				if (\GFAPI::get_form($id)) {
					$result = \GFAPI::update_form($form_data);
				} else {
					$result = \GFAPI::add_form($form_data);

					// Update ID in saved file
					$form_data['id'] = $result;
					$this->save($file, $form_data);
				}
			}
		}
	}

	/**
	 * Remove JSON file.
	 */
	public function remove($form_id)
	{
		$files = $this->scan();

		foreach ($files as $id => $file) {
			if ((int)$id === (int)$form_id) {
				unlink($file);
				return;
			}
		}
	}

	/**
	 * Export JSON file to local theme directory.
	 */
	public function export($form)
	{
		// $form could either be a form object or a form id, so normalise here
		$form_id = is_numeric($form) ? $form : $form['id'];

		if (!is_writable($this->path)) {
			return false;
		}

		$form = \GFAPI::get_form($form_id);
		$file = untrailingslashit($this->path) . '/form_' . $form['id'] . '.json';
		$this->save($file, $form);
	}
}
