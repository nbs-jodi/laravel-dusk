<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ForgotPasswordPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/password/reset';
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

        $browser->assertUrlIs(route('password.request'));

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
        ];
    }
}
