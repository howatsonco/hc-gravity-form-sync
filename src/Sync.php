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
	 * Define the core functionality of the plugin.
	 */
	public function __construct()
	{
		add_action("gform_entry_created", array($this, "export"), 10);
	}

  private function export() {
    var_dump("test");
    die();
  }
  private function import() {}

}
