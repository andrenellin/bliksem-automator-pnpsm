<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * @wordpress-plugin
 * Plugin Name:       Bliksem Uncanny Automator PNPSM Trigger
 * Plugin URI:        https://github.com/andrenellin/bliksem-automator-pnpsm
 * Description:       Triggers for Uncanny Automator on PlugAndPlaySM.com
 * Version:           1.0.0
 * Author:            Bliksem LLC
 * Author URI:        https://www.simplifysmallbiz.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Bliksem_Uncanny_Automator_Trigger
{
    /**
     * Uncanny_Automator_Trigger_Only constructor.
     */
    public function __construct()
    {
        add_action('automator_configuration_complete', array($this, 'load_triggers'));
    }

    /**
     * @return bool|null
     */
    public function load_triggers()
    {
        // Let's find integration by name so that trigger can be added it's list.
        $add_to_integration = automator_get_integration_by_name('Uncanny Automator');
        if (empty($add_to_integration)) {
            return null;
        }
        $trigger = __DIR__ . '/mp-recurringsubscriptionpaused-trigger.php';
        automator_add_trigger($trigger, $add_to_integration);
        $trigger = __DIR__ . '/mp-recurringsubscriptionresumed-trigger.php';
        automator_add_trigger($trigger, $add_to_integration);
    }
}

new Bliksem_Uncanny_Automator_Trigger();