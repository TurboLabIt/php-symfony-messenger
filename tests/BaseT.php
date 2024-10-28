<?php
namespace TurboLabIt\MessengersBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;


abstract class BaseT extends TestCase
{
    const TESTED_SERVICE_FQN = null;

    protected static KernelT $kernelT;


    protected function setUp() :void
    {
        static::$kernelT = new KernelT('test', true);
        static::$kernelT->boot();
    }


    protected function getInstance()
    {
        $service = $this->getService(static::TESTED_SERVICE_FQN);
        $this->assertInstanceOf(static::TESTED_SERVICE_FQN, $service);
        return $service;
    }


    protected static function getService(string $name)
    {
        // boots the kernel and prevents LogicException:
        // Booting the kernel before calling "WebTestCase::createClient()" is not supported, the kernel should only be booted once.
        if(false && empty(static::$client) ) {

            static::$client = static::createClient();
            static::$client->setServerParameter('HTTP_HOST', $_ENV["APP_SITE_DOMAIN"]);
            static::$client->setServerParameter('HTTPS', 'https');
        }

        $container = static::$kernelT->getContainer();
        $service = $container->get($name);
        return $service;
    }




    /*protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        static::$crawler = null;
    }*/
}


//<editor-fold defaultstate="collapsed" desc="*** SYMFONY KERNEL ***">
use Symfony\Component\HttpKernel\Kernel;
use TurboLabIt\MessengersBundle\TurboLabItMessengersBundle;

class KernelT extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TurboLabItMessengersBundle()
        ];
    }
}
//</editor-fold>
