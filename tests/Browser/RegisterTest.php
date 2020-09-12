<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\RegisterPage;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * @test
     * @group register
     * @group view
     * @return void
     * @throws \Throwable
     */
    public function testRegisterPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('REGISTER')
                ->clickLink('Register')
                ->visit(new RegisterPage)
                ->assertSee('Register')
                ->assertSee('Name')
                ->assertSee('E-Mail Address')
                ->assertSee('Password')
                ->assertSee('Confirm Password')
                ->assertButtonEnabled('Register');
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterUser()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $this->faker->email)
                ->type('password', $password = $this->faker->password(8))
                ->type('password_confirmation', $password)
                ->press('Register')
                ->assertPathIs(\App\Providers\RouteServiceProvider::HOME)
                ->assertSee('Dashboard')
                ->assertSee('You are logged in!')
                ->logout();
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterEmailAlreadyExist()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $user->email)
                ->type('password', $password = $this->faker->password(8))
                ->type('password_confirmation', $password)
                ->press('Register')
                ->assertSee(trans('validation.unique', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterEmailInvalid()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $this->faker->name)
                ->type('password', $password = $this->faker->password(8))
                ->type('password_confirmation', $password)
                ->press('Register')
                ->assertSee(trans('validation.email', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterPasswordInvalidLength()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $this->faker->email)
                ->type('password', $password = $this->faker->password(1, 7))
                ->type('password_confirmation', $password)
                ->press('Register')
                ->assertSee(__('validation.min.string', ['attribute' => 'password', 'min' => 8]));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterPasswordDoesNotMatch()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $this->faker->email)
                ->type('password', $this->faker->password(8))
                ->type('password_confirmation', $this->faker->password(8))
                ->press('Register')
                ->assertSee(__('validation.confirmed', ['attribute' => 'password']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterNameEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('email', $this->faker->email)
                ->type('password', $password = $this->faker->password(8))
                ->type('password_confirmation', $password)
                ->press('Register')
                ->assertSee(__('validation.required', ['attribute' => 'name']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterEmailEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('password', $password = $this->faker->password(8))
                ->type('password_confirmation', $password)
                ->press('Register')
                ->assertSee(__('validation.required', ['attribute' => 'email']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterPasswordEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $this->faker->safeEmail)
                ->type('password_confirmation', $this->faker->password(8))
                ->press('Register')
                ->assertSee(__('validation.required', ['attribute' => 'password']));
        });
    }

    /**
     * @test
     * @group register
     * @return void
     * @throws \Throwable
     */
    public function testRegisterPasswordConfrimationEmpty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('name', $this->faker->name)
                ->type('email', $this->faker->safeEmail)
                ->type('password', $this->faker->password(8))
                ->press('Register')
                ->assertSee(__('validation.confirmed', ['attribute' => 'password']));
        });
    }
}
