<?php

namespace ViktorMiller\LaravelConfirmation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @author Viktor Miller <v.miller@forty-four.de>
 */
class Email extends Facade
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const CONFIRM_LINK_SENT = 'confirmation.sent';

    /**
     * Constant representing a successfully confirm email.
     *
     * @var string
     */
    const EMAIL_CONFIRMED = 'confirmation.confirmed';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'confirmation.user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'confirmation.token';

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'confirmation.email';
    }
}
