<?php
Event Object
MeprEvent Object
(
    [rec:protected] => stdClass Object
        (
            [id] => 704
            [args] => 
            [event] => subscription-paused
            [evt_id] => 42
            [evt_id_type] => subscriptions
            [created_at] => 
        )

    [attrs:protected] => Array
        (
            [0] => id
            [1] => args
            [2] => event
            [3] => evt_id
            [4] => evt_id_type
            [5] => created_at
        )

    [defaults:protected] => 
)

Transaction Object
MeprSubscription Object
(
    [statuses] => Array
        (
            [0] => pending
            [1] => active
            [2] => suspended
            [3] => cancelled
        )

    [object_type:MeprBaseMetaModel:private] => subscription
    [meta_table:MeprBaseMetaModel:private] => subscription_meta
    [rec:protected] => stdClass Object
        (
            [id] => 42
            [subscr_id] => sub_J3V5lKmFw0zIUm
            [gateway] => qpcs9b-3o3
            [user_id] => 37
            [product_id] => 487
            [coupon_id] => 0
            [price] => 197.00
            [period] => 1
            [period_type] => months
            [limit_cycles] => 0
            [limit_cycles_num] => 2
            [limit_cycles_action] => expire
            [limit_cycles_expires_after] => 1
            [limit_cycles_expires_type] => days
            [prorated_trial] => 0
            [trial] => 0
            [trial_days] => 0
            [trial_amount] => 0.00
            [status] => suspended
            [created_at] => 2021-03-04 19:01:00
            [total] => 197.00
            [tax_rate] => 0.000
            [tax_amount] => 0.00
            [tax_desc] => 
            [tax_class] => standard
            [cc_last4] => 4242
            [cc_exp_month] => 12
            [cc_exp_year] => 2030
            [token] => 
            [tax_compound] => 0
            [tax_shipping] => 1
            [response] => 
        )

    [attrs:protected] => Array
        (
            [0] => id
            [1] => subscr_id
            [2] => gateway
            [3] => user_id
            [4] => product_id
            [5] => coupon_id
            [6] => price
            [7] => period
            [8] => period_type
            [9] => limit_cycles
            [10] => limit_cycles_num
            [11] => limit_cycles_action
            [12] => limit_cycles_expires_after
            [13] => limit_cycles_expires_type
            [14] => prorated_trial
            [15] => trial
            [16] => trial_days
            [17] => trial_amount
            [18] => status
            [19] => created_at
            [20] => total
            [21] => tax_rate
            [22] => tax_amount
            [23] => tax_desc
            [24] => tax_class
            [25] => cc_last4
            [26] => cc_exp_month
            [27] => cc_exp_year
            [28] => token
        )

    [defaults:protected] => 
)