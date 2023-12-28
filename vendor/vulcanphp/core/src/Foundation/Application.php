<?php

namespace VulcanPhp\Core\Foundation;

use VulcanPhp\PhpRouter\Router;
use VulcanPhp\Core\Foundation\Exceptions\AppException;
use VulcanPhp\Core\Foundation\Exceptions\KernelException;
use VulcanPhp\Core\Foundation\Interfaces\IKernel;

/**
 * This Application Class is a Packet of Router, Request, Response
 * and many other library to create a MVC architecture.
 * Application is a Base Class if this Micro Framework
 * 
 * @package VulcanPhp\Core\Foundation
 * @since 1.0
 */
class Application
{
    /**
     * application instance static variable
     *
     * @var Application $app
     */
    public static Application $app;

    /**
     * application http routing system variable
     *
     * @var Router $router
     */
    protected Router $router;

    /**
     * application dynamic component
     *
     * @var array $components
     */
    protected array $components = [];

    /**
     * application kernels
     *
     * @var array $components
     */
    protected array $kernels = [
        // kernel to setup application
        \VulcanPhp\Core\Foundation\Kernels\BootstrapKernel::class,

        // kernel to load routes
        \VulcanPhp\Core\Foundation\Kernels\RouterKernel::class,
    ];

    /**
     * New Application __constructor
     *
     * @param string $rootDir
     */
    public function __construct(public string $rootDir)
    {
        /**
         * create a new static variable of application instance
         *
         * @access public
         * @since 1.0
         * @var Application $app
         */
        self::$app = $this;

        /**
         * create application routing system
         *
         * @access public
         * @since 1.0
         * @var Router $router
         */
        self::$app->router = Router::init();
    }

    /**
     * Create Application Instance statically
     * @param mixed $args 
     * @return Application 
     */
    public static function create(...$args): Application
    {
        return new Application(...$args);
    }

    /**
     * Run The Application
     * @return void
     */
    public static function run(): void
    {
        // load application initial kernels
        self::$app->loadKernels('boot');

        // render router result
        echo self::$app->router->resolve();

        // load application initial kernels
        self::$app->loadKernels('shutdown');
    }

    /**
     * Check Anu Component added to the Application
     * @param string $key 
     * @return bool 
     */
    public static function hasComponent(string $key): bool
    {
        return isset(self::$app->components[$key]);
    }

    /**
     * Get Any Component from Application
     * @param string $key 
     * @return mixed 
     * @throws AppException 
     */
    public static function getComponent(string $key)
    {
        if (self::$app->hasComponent($key)) {
            return self::$app->components[$key];
        }

        throw new AppException('App Component: ' . $key . ' does not initialed.');
    }

    /**
     * Set Runtime Dynamic Component to the Application 
     * @param string $key 
     * @param mixed $component 
     * @return Application 
     */
    public static function setComponent(string $key, $component): Application
    {
        self::$app->components[$key] = $component;
        return self::$app;
    }

    /**
     * Get the Application Router Instance
     * @return Router 
     */
    public static function getRouter(): Router
    {
        return self::$app->router;
    }

    /**
     * Register a New Application Kernel
     * @param IKernel $kernel 
     * @return Application 
     */
    public static function registerKernel($kernel): Application
    {
        self::$app->kernels[] = $kernel;

        return self::$app;
    }

    /**
     * Load Application Setup Kernels to get started
     * 
     * @param mixed $kernels 
     * @throws KernelException 
     * @return void
     */
    protected static function loadKernels(string $action): void
    {
        foreach ((array) self::$app->kernels as $kernel) {
            $kernel = new $kernel;

            if (!$kernel instanceof IKernel) {
                throw new KernelException('Kernel: ' . get_class($kernel) . ' must be implement interface: ' . IKernel::class);
            }

            call_user_func([$kernel, $action]);
        }
    }


    /**
     * Abort Application Execution if gets any exceptions
     * 
     * @param mixed $type 
     * @param null|int $code 
     * @return void 
     */
    public static function abort($type, ?int $code = null): void
    {
        if ($code === null && __is_int($type)) {
            $code = $type;
        }

        if ($code !== null) {
            self::$app->router->response->httpCode($code);
        }

        echo view($type);

        exit;
    }
}
