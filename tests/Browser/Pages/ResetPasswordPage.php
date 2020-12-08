<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ResetPasswordPage extends Page
{
    /**
     * @var string
     */
    private $token;

    /**
     * ResetPasswordPage constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return "/password/reset/{$this->token}";
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());

        $browser->assertRouteIs('password.reset', ['token' => $this->token]);

        /** @noinspection PhpUndefinedMethodInspection */
        $browser->disableClientSideValidation();
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email' => 'input[name=email]',
            '@password' => 'input[name=password]',
            '@password-confirm' => 'input[name=password_confirmation]',
        ];
    }
}
