<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * @test
     * @group login
     * @group view
     * @return void
     * @throws \Throwable
     */
    public function testLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('LOGIN')
                ->clickLink('Login')
                ->visit(new LoginPage)
                ->assertSee('Login')
                ->assertSee('E-Mail Address')
                ->assertSee('Password')
                ->assertButtonEnabled('Login');
        });
    }

    /**
     * @test
     * @group login
     * @return void
     * @throws \Throwable
     */
    public function testLoginUser()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new LoginPage)
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs(\App\Providers\RouteServiceProvider::HOME)
                ->assertSee('Dashboard')
                ->assertSee('You are logged in!')
                ->logout();
        });
    }

    /**
     * @test
     * @group login
     * @return void
     * @throws \Throwable
     */
    public function testLoginEmailNotFound()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->type('email', $this->faker->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertSee(trans('auth.failed'));;
        });
    }

    /**
     * @test
     * @group login
     * @return void
     * @throws \Throwable
     */
    public function testLoginPasswordInvalid()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new LoginPage)
                ->type('email', $user->email)
                ->type('password', $this->faker->password(8))
                ->press('Login')
                ->assertSee(trans('auth.failed'));;
        });
    }

    /**
     * @test
     * @group login
     * @return void
     * @throws \Throwable
     */
    public function testLoginEmailEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->type('password', $this->faker->password(8))
                ->press('Login')
                ->assertSee(__('validation.required', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group login
     * @return void
     * @throws \Throwable
     */
    public function testLoginPasswordEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->type('email', $this->faker->safeEmail)
                ->press('Login')
                ->assertSee(__('validation.required', ['attribute' => 'password']));
        });
    }
}
