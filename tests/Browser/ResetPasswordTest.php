<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ResetPasswordPage;
use Tests\DuskTestCase;

class ResetPasswordTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * @test
     * @group reset-password
     * @group view
     * @return void
     * @throws \Throwable
     */
    public function testView()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/password/reset/{$this->faker->slug}")
                ->assertSee('Reset Password')
                ->assertSee('E-Mail Address')
                ->assertSee('Password')
                ->assertSee('Confirm Password')
                ->assertButtonEnabled('Reset Password');
        });
    }

    /**
     * @test
     * @group reset-password
     * @group view
     * @return void
     * @throws \Throwable
     */
    public function testPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->assertSee('Reset Password')
                ->assertSee('E-Mail Address')
                ->assertSee('Password')
                ->assertSee('Confirm Password')
                ->assertButtonEnabled('Reset Password');
        });
    }

    /**
     * @test
     * @group reset-password
     * @group submit
     * @return void
     * @throws \Throwable
     */
    public function testSubmit()
    {
        $user = factory(\App\User::class)->create();

        /** @noinspection PhpUndefinedMethodInspection */
        $token = Password::broker()->createToken($user);

        $this->browse(function (Browser $browser) use ($user, $token) {
            $browser->visit(new ResetPasswordPage($token))
                ->type('@email', $user->email)
                ->type('@password', $password = $this->faker->password(8))
                ->type('@password-confirm', $password)
                ->press('Reset Password')
                ->assertSee(__('passwords.reset'));
        });
    }

    /**
     * @test
     * @group reset-password
     * @group credentials
     * @return void
     * @throws \Throwable
     */
    public function testEmailNotFound()
    {
        $user = factory(\App\User::class)->create();

        /** @noinspection PhpUndefinedMethodInspection */
        $token = Password::broker()->createToken($user);

        $this->browse(function (Browser $browser) use ($token) {
            $browser->visit(new ResetPasswordPage($token))
                ->type('@email', $this->faker->email)
                ->type('@password', $password = $this->faker->password(8))
                ->type('@password-confirm', $password)
                ->press('Reset Password')
                ->assertSee(__('passwords.user'));
        });
    }

    /**
     * @test
     * @group reset-password
     * @group credentials
     * @return void
     * @throws \Throwable
     */
    public function testTokenNotFound()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->type('@email', $user->email)
                ->type('@password', $password = $this->faker->password(8))
                ->type('@password-confirm', $password)
                ->press('Reset Password')
                ->assertSee(__('passwords.token'));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testEmailInvalid()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->type('@email', $this->faker->name)
                ->type('@password', $password = $this->faker->password(8))
                ->type('@password-confirm', $password)
                ->press('Reset Password')
                ->assertSee(__('validation.email', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testPasswordInvalidLength()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->type('@email', $this->faker->email)
                ->type('@password', $password = $this->faker->password(1, 7))
                ->type('@password-confirm', $password)
                ->press('Reset Password')
                ->assertSee(__('validation.min.string', ['attribute' => 'password', 'min' => 8]));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testPasswordDoesNotMatch()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->type('@email', $this->faker->email)
                ->type('@password', $this->faker->password(8))
                ->type('@password-confirm', $this->faker->password(8))
                ->press('Reset Password')
                ->assertSee(__('validation.confirmed', ['attribute' => 'password']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testTokenEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($token = $this->faker->slug))
                ->type('@email', $email = $this->faker->email)
                ->type('@password', $password = $this->faker->password(8))
                ->type('@password-confirm', $password)
                ->script(["var element = document.getElementsByName('token')[0];element.parentNode.removeChild(element)"]);

            $browser->press('Reset Password')
                ->assertRouteIs('password.reset', ['token' => $token])
                ->assertValue('@email', $email);
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testEmailEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->type('@password', $password = $this->faker->password(8))
                ->type('@password-confirm', $password)
                ->press('Reset Password')
                ->assertSee(__('validation.required', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testPasswordEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ResetPasswordPage($this->faker->slug))
                ->type('@email', $this->faker->email)
                ->type('@password-confirm', $this->faker->password(8))
                ->press('Reset Password')
                ->assertSee(__('validation.required', ['attribute' => 'password']));
        });
    }
}
