<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();

        \Laravel\Dusk\Browser::macro('disableClientSideValidation', function () {
            $this->script('for(var f=document.forms,i=f.length;i--;)f[i].setAttribute("novalidate",i)');

            return $this;
        });
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                windows_os() ? ChromeOptions::CAPABILITY_W3C : ChromeOptions::CAPABILITY,
                (new ChromeOptions)->addArguments([
                    '--no-sandbox',
                    '--disable-gpu',
                    '--headless',
                    '--window-size=1920,1080',
                    '--disable-dev-shm-usage',
                ])
            )
        );
    }
}
