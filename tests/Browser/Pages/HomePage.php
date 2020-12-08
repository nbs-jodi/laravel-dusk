<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class HomePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
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

        $browser->assertTitle(config('app.name'));

        $browser->assertSeeIn('@title', 'Laravel');

        $browser->assertSeeIn('@links:nth-child(1)', 'DOCS')
            ->assertAttribute('@links:nth-child(1)', 'href', 'https://laravel.com/docs');
        $browser->assertSeeIn('@links:nth-child(2)', 'LARACASTS')
            ->assertAttribute('@links:nth-child(2)', 'href', 'https://laracasts.com/');
        $browser->assertSeeIn('@links:nth-child(3)', 'NEWS')
            ->assertAttribute('@links:nth-child(3)', 'href', 'https://laravel-news.com/');
        $browser->assertSeeIn('@links:nth-child(4)', 'BLOG')
            ->assertAttribute('@links:nth-child(4)', 'href', 'https://blog.laravel.com/');
        $browser->assertSeeIn('@links:nth-child(5)', 'NOVA')
            ->assertAttribute('@links:nth-child(5)', 'href', 'https://nova.laravel.com/');
        $browser->assertSeeIn('@links:nth-child(6)', 'FORGE')
            ->assertAttribute('@links:nth-child(6)', 'href', 'https://forge.laravel.com/');
        $browser->assertSeeIn('@links:nth-child(7)', 'VAPOR')
            ->assertAttribute('@links:nth-child(7)', 'href', 'https://vapor.laravel.com/');
        $browser->assertSeeIn('@links:nth-child(8)', 'GITHUB')
            ->assertAttribute('@links:nth-child(8)', 'href', 'https://github.com/laravel/laravel');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@title' => 'div > div > div.title',
            '@links' => 'div > div > div.links > a',
        ];
    }
}
