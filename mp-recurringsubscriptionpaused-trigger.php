<?php
/*
 * Hooks into Uncanny Automator to create a trigger for MemberPress
 * Trigger: Recurring Membership is paused
 * */
use Uncanny_Automator\Recipe;

/**
 * Class BS_Automator_Membership_Paused_Trigger
 */
class BS_Automator_Membership_Paused_Trigger
{
    use Recipe\Triggers;

    /**
     * Bliksem_Automator_Pnpsm_Trigger constructor.
     */
    public function __construct()
    {
        $this->setup_trigger();
    }

    /**
     *
     */
    protected function setup_trigger()
    {
        $this->set_integration('MP');
        $this->set_trigger_code('PAUSEPRODUCTRECURRING');
        $this->set_trigger_meta('MPPRODUCT');
        /* Translators: Some information for translators */
        $this->set_sentence(sprintf(esc_attr__('A user pauses {{a recurring subscription product:%1$s}}', 'uncanny-automator'), $this->get_trigger_meta()));
        /* Translators: Some information for translators */
        $this->set_readable_sentence(esc_attr__('A user pauses {{a recurring subscription product}}', 'uncanny-automator'));

        $this->add_action('mepr-event-subscription-paused', 20, 1);

        $options = array(
            Automator()->helpers->recipe->memberpress->options->get_recipes(),
        );

        $this->set_options($options);

        $this->register_trigger();
    }

    /**
     * @param $args
     *
     * @return array
     */
    protected function do_action_args($args)
    {

        return array(
            'recipe_id' => $args[0],
            'user_id' => $args[1],
            'recipe_log_id' => $args[2],
            'args' => $args[3],
        );
    }

    /**
     * @param $args
     */
    protected function validate_trigger($args)
    {
        $recipe_log_id = absint($args['recipe_log_id']);
        global $wpdb;
        // get recipe actions
        $table_name = $wpdb->prefix . Automator()->db->tables->action;
        $errors = $wpdb->get_results($wpdb->prepare("SELECT automator_action_id FROM $table_name WHERE automator_recipe_log_id = {$recipe_log_id} AND error_message != ''")); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        if (empty($errors)) {
            // bail early
            return false;
        }

        return true;
    }

    /**
     * @param mixed ...$args
     */
    protected function prepare_to_run($args)
    {
        $recipe_id = absint($args['recipe_id']);
        $this->set_post_id($recipe_id);
    }
}