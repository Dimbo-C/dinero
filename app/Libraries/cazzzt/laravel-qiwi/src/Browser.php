<?php 

namespace Cazzzt\Qiwi;

use Closure;
use Exception;
use Throwable;
use Laravel\Dusk\Browser as DuskBrowser;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class Browser
{
	use SupportsChrome;

    /**
     * The callbacks that should be run on class tear down.
     *
     * @var array
     */
    protected static $afterClassCallbacks = [];

    /**
     * Register an "after class" tear down callback.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function afterClass(Closure $callback)
    {
        static::$afterClassCallbacks[] = $callback;
    }


	 /**
     * @var \Laravel\Dusk\Browser
     */
    protected $browser;
	
	public function browse(Closure $callback)
    {
        if (!$this->browser) {
             
            $this->startChromeDriver();
            $this->browser = $this->newBrowser($this->createWebDriver());
        }
        try {
            $callback($this->browser);
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    function __destruct()
    {
        if ($this->browser) {
            $this->closeBrowser();
        }
    }

    protected function closeBrowser()
    {
        if (!$this->browser) {
            throw new Exception("The browser hasn't been initialized yet");
        }
        $this->browser->quit();
        $this->browser = null;
    }

    protected function newBrowser($driver)
    {
       
        return new DuskBrowser($driver);
    }

    /**
     * Create the remote web driver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function createWebDriver()
    {
        return retry(10, function () {
            return $this->driver();
        }, 100);
    }

    protected function driver()
    {
       return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()
        );
    }
}