<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ForgotPasswordPage;
use Tests\DuskTestCase;

class ForgotPasswordTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * @test
     * @group forgot-password
     * @group view
     * @return void
     * @throws \Throwable
     */
    public function testView()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('LOGIN')
                ->clickLink('Login')
                ->clickLink('Forgot Your Password?')
                ->assertSee('Reset Password')
                ->assertSee('E-Mail Address')
                ->assertButtonEnabled('Send Password Reset Link');
        });
    }

    /**
     * @test
     * @group forgot-password
     * @group view
     * @return void
     * @throws \Throwable
     */
    public function testPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ForgotPasswordPage)
                ->assertSee('Reset Password')
                ->assertSee('E-Mail Address')
                ->assertButtonEnabled('Send Password Reset Link');
        });
    }

    /**
     * @test
     * @group forgot-password
     * @group submit
     * @return void
     * @throws \Throwable
     */
    public function testSubmit()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new ForgotPasswordPage)
                ->type('@email', $user->email)
                ->press('Send Password Reset Link')
                ->assertSee(__('passwords.sent'));
        });
    }

    /**
     * @test
     * @group forgot-password
     * @group credentials
     * @return void
     * @throws \Throwable
     */
    public function testEmailNotFound()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ForgotPasswordPage)
                ->type('@email', $this->faker->email)
                ->press('Send Password Reset Link')
                ->assertSee(__('passwords.user'));
        });
    }

    /**
     * @test
     * @group forgot-password
     * @group middleware
     * @return void
     * @throws \Throwable
     */
    public function testThrottled()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new ForgotPasswordPage)
                ->type('@email', $user->email)
                ->press('Send Password Reset Link')
                ->type('@email', $user->email)
                ->press('Send Password Reset Link')
                ->assertSee(__('passwords.throttled'));
        });
    }

    /**
     * @test
     * @group forgot-password
     * @group validation
     * @return void
     * @throws \Throwable
     */
    public function testEmptyEmail()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ForgotPasswordPage)
                ->press('Send Password Reset Link')
                ->assertSee(__('validation.required', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group forgot-password
     * @group validation
     * @return void
     * @throws \Throwable
     */
    public function testInvalidEmail()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ForgotPasswordPage)
                ->type('@email', $this->faker->name)
                ->press('Send Password Reset Link')
                ->assertSee(__('validation.email', ['attribute' => 'email']));
        });
    }
}
