<?php
/*
 * Adding a trigger for Memberpress Subscription Paused
 * =========
 *
 */

/* SECTION 2 - PART A */
// Add trigger during the "uncanny_automator_add_integration_triggers_actions_tokens" do_action()
add_action('uncanny_automator_add_integration_triggers_actions_tokens', 'uncanny_automator_triggers_mepr_subscription_paused');

/**
 * Define and register the trigger by pushing it into the Automator object
 */
function uncanny_automator_triggers_mepr_subscription_paused()
{
    global $uncanny_automator;

    $trigger = array(

        'author' => 'Bliksem LLC',
        'support_link' => 'https://github.com/andrenellin',
        'integration' => 'MP',
        'code' => 'PAUSEPRODUCTRECURRING',
        'sentence' => sprintf(esc_attr__('A user pauses {{a recurring subscription product:%1$s}}', 'uncanny-automator'), 'MPPRODUCT'),
        'select_option_name' => __esc_attr('A user pauses {{a recurring subscription product}}', 'uncanny-automator'),
        'action' => 'mepr-event-subscription-paused',
        'priority' => 20,
        'accepted_args' => 1,
        'validation_function' => 'mp_product_paused',
        'options' => [
            $uncanny_automator->helpers->recipe->memberpress->options->all_memberpress_products_recurring(null, 'MPPRODUCT', ['uo_include_any' => true]),
        ],
    );

    $uncanny_automator->register_trigger($trigger);

    return;
}

/**
 * @param \MeprEvent $event
 */
function mp_product_paused(\MeprEvent $event)
{
    global $uncanny_automator;

    /** @var \MeprTransaction $transaction */
    $transaction = $event->get_data();
    /** @var \MeprProduct $product */
    $product = $transaction->product();
    $product_id = $product->ID;
    $user_id = absint($transaction->user()->ID);
    if ('lifetime' === (string) $product->period_type) {
        return;
    }

    $recipes = $uncanny_automator->get->recipes_from_trigger_code('MPPRODUCT');
    if (empty($recipes)) {
        return;
    }
    $required_product = $uncanny_automator->get->meta_from_recipes($recipes, 'MPPRODUCT');
    $matched_recipe_ids = array();
    //Add where option is set to Any product
    foreach ($recipes as $recipe_id => $recipe) {
        foreach ($recipe['triggers'] as $trigger) {
            $trigger_id = $trigger['ID']; //return early for all products
            if (absint($required_product[$recipe_id][$trigger_id]) === $product_id || intval('-1') === intval($required_product[$recipe_id][$trigger_id])) {
                $matched_recipe_ids[] = [
                    'recipe_id' => $recipe_id,
                    'trigger_id' => $trigger_id,
                ];
            }
        }
    }
    if (empty($matched_recipe_ids)) {
        return;
    }
    foreach ($matched_recipe_ids as $matched_recipe_id) {
        $recipe_args = [
            'code' => 'PAUSEPRODUCTRECURRING',
            'meta' => 'MPPRODUCT',
            'user_id' => $user_id,
            'recipe_to_match' => $matched_recipe_id['recipe_id'],
            'trigger_to_match' => $matched_recipe_id['trigger_id'],
            'ignore_post_id' => true,
            'is_signed_in' => true,
        ];

        $results = $uncanny_automator->maybe_add_trigger_entry($recipe_args, false);
        if (empty($results)) {
            continue;
        }
        foreach ($results as $result) {
            if (true === $result['result']) {
                $trigger_meta = [
                    'user_id' => $user_id,
                    'trigger_id' => $result['args']['trigger_id'],
                    'trigger_log_id' => $result['args']['get_trigger_id'],
                    'run_number' => $result['args']['run_number'],
                ];

                $trigger_meta['meta_key'] = 'MPPRODUCT';
                $trigger_meta['meta_value'] = $product_id;
                $uncanny_automator->insert_trigger_meta($trigger_meta);
                update_user_meta($user_id, 'MPPRODUCT', $product_id);

                $uncanny_automator->maybe_trigger_complete($result['args']);
            }
        }
    }
}