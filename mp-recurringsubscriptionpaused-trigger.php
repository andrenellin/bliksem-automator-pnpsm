<?php
/*
 * Hooks into Uncanny Automator to create a trigger for MemberPress
 * Trigger: Recurring Membership is paused
 * */

use Uncanny_Automator\Recipe;

/**
 * Class BS_Automator_Membership_Paused_Trigger
 */
class MP_RECURRINGSUBSCRIPTIONPAUSED_TRIGGER {
	use Recipe\Triggers;

	/**
	 * Bliksem_Automator_Pnpsm_Trigger constructor.
	 */
	public function __construct() {
		$this->setup_trigger();
	}

	/**
	 *
	 */
	protected function setup_trigger() {
		$memberpress_recurring = new \Uncanny_Automator\Memberpress_Helpers();

		$this->set_integration( 'MP' );
		$this->set_trigger_code( 'PAUSEPRODUCTRECURRING' );
		$this->set_trigger_meta( 'MPPRODUCT' );
		/* Translators: Some information for translators */
		$this->set_sentence( sprintf( esc_attr__( 'A user pauses {{a recurring subscription product:%1$s}}', 'uncanny-automator' ), $this->get_trigger_meta() ) );
		/* Translators: Some information for translators */
		$this->set_readable_sentence( esc_attr__( 'A user pauses {{a recurring subscription product}}', 'uncanny-automator' ) );

		$this->add_action( 'mepr-event-subscription-paused', 20, 1 );

		$options = array(
			$memberpress_recurring->all_memberpress_products_recurring( null, $this->get_trigger_meta(), array( 'uo_include_any' => true ) ),
		);

		$this->set_options( $options );

		$this->register_trigger();
	}

	/**
	 * @param $args
	 *
	 * @return array
	 */
	protected function do_action_args( $args ) {
		return array_shift( $args );
	}

	/**
	 * @param $args
	 */
	protected function validate_trigger( $event ) {
		if ( ! $event instanceof \MeprEvent ) {
			return false;
		}
		/** @var \MeprTransaction $transaction */
		$transaction = $event->get_data();
		/** @var \MeprProduct $product */
		$product = $transaction->product();
		if ( 'lifetime' === (string) $product->period_type ) {
			return false;
		}
	}

	/**
	 * @param mixed $event
	 */
	protected function prepare_to_run( $event ) {
		/** @var \MeprTransaction $transaction */
		$transaction = $event->get_data();
		$user_id     = absint( $transaction->user()->ID );
		$this->set_user_id( $user_id );
		$this->set_is_signed_in( true );
		$this->set_ignore_post_id( true );
		$this->set_conditional_trigger( true );
	}

	/**
	 * @param $event
	 *
	 * @return array|mixed
	 */
	protected function validate_conditions( $event ) {

		$matched_recipe_ids = array();
		/*
		 * Get recipes that matches the current trigger.
		 */
		$recipes = $this->trigger_recipes();
		/** @var \MeprTransaction $transaction */
		$transaction = $event->get_data();
		/** @var \MeprProduct $product */
		$product    = $transaction->product();
		$product_id = $product->ID;

		if ( empty( $recipes ) ) {
			return $matched_recipe_ids;
		}
		$required_product = Automator()->get->meta_from_recipes( $recipes, $this->get_trigger_meta() );

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( absint( $required_product[ $recipe_id ][ $trigger_id ] ) === $product_id || intval( '-1' ) === intval( $required_product[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
				}
			}
		}
		if ( empty( $matched_recipe_ids ) ) {
			return array();
		}

		return $matched_recipe_ids;
	}
}
