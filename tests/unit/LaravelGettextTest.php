<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;
use \Xinax\LaravelGettext\LaravelGettext;
use \Xinax\LaravelGettext\Config\ConfigManager;
use \Xinax\LaravelGettext\Adapters\LaravelAdapter;
use \Xinax\LaravelGettext\FileSystem;
use \Xinax\LaravelGettext\Translators\Symfony;

use Illuminate\Foundation\Testing\TestCase;

class LaravelGettextTest extends TestCase
{
    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';

        $app->register('Xinax\LaravelGettext\LaravelGettextServiceProvider');

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    /**
     * Gettext wrapper
     * @var \Xinax\LaravelGettext\Translators\Symfony
     */
    protected $translator;

    public function setUp()
    {
        parent::setUp();

        $testConfig = include __DIR__ . '/../config/config.php';
        $config = ConfigManager::create($testConfig);

        $adapter = new LaravelAdapter;

        $fileSystem = new FileSystem($config->get(), app_path(), storage_path());

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $fileSystem
        );

        $this->translator = $translator;
    }

    /**
     * Test setting locale.
     */
    public function testSetLocale()
    {
        $response = $this->translator->setLocale('en_US');

        $this->assertEquals('en_US', $response);
    }

    /**
     * Test getting locale.
     * It should receive locale from mocked config.
     */
    public function testGetLocale()
    {
        $response = $this->translator->getLocale();

        $this->assertEquals('en_US', $response);
    }

    public function testIsLocaleSupported()
    {
        $this->assertTrue($this->translator->isLocaleSupported('en_US'));
    }

    /**
     * Test dumping locale to string
     */
    public function testToString()
    {
        $response = $this->translator->__toString();

        $this->assertEquals('en_US', $response);
    }

    public function testGetEncoding()
    {
        $response = $this->translator->getEncoding();
        $this->assertNotEmpty($response);
        $this->assertEquals('UTF-8', $response);
    }

    public function testSetEncoding()
    {
        $response = $this->translator->setEncoding('UTF-8');
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('Xinax\LaravelGettext\Translators\Symfony', $response);
    }

    public function tearDown()
    {
        m::close();
    }
}