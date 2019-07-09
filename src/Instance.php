<?php

namespace ChromeDevToolsPdf;

use ChromeDevtoolsProtocol\{Context, ContextInterface, Session};
use ChromeDevtoolsProtocol\Instance\Instance as DevToolsInstance;
use ChromeDevtoolsProtocol\Instance\{InstanceInterface, Launcher};

class Instance
{
    protected $instance;
    protected $context;

    protected function __construct(InstanceInterface $instance, ContextInterface $context)
    {
        $this->instance = $instance;
        $this->context = $context;
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Connect to an existing Chrome instance
     *
     * @method connect
     * @param  integer $port            DevTools port
     * @param  string  $host            Hostname or IP address
     * @param  integer $context_timeout Timeout in seconds
     * @return self
     */
    public static function connect(int $port, string $host = '127.0.0.1', int $context_timeout = 30): self
    {
        $context = new Context(Context::background(), $context_timeout);
        $instance = new DevToolsInstance($host, $port);
        return new self($instance, $context);
    }

    /**
     * Launch a new Chrome instance
     *
     * @method launch
     * @param  string  $executable      Path to Chrome executable
     * @param  integer $context_timeout Timeout in seconds
     * @return self
     */
    public static function launch(?string $executable = null, int $context_timeout = 30): self
    {
        $context = new Context(Context::background(), $context_timeout);

        $launcher = new Launcher();
        if (!is_null($executable)) $launcher->setExecutable($executable);
        $instance = $launcher->launch($context);

        return new self($instance, $context);
    }

    /**
     * Load HTML source code from a string.
     *
     * @method loadHtml
     * @param  string   $source The HTML source
     * @return Pdf
     */
    public function loadHtml(string $source): Pdf
    {
        $pdf = new Pdf($this);
        $pdf->loadHtml($source);
        return $pdf;
    }

    /**
     * Load a page from a URL.
     *
     * @method loadUrl
     * @param  string  $url The URL refering to the page to print
     * @return Pdf
     */
    public function loadUrl(string $url): Pdf
    {
        $pdf = new Pdf($this);
        $pdf->loadUrl($url);
        return $pdf;
    }

    /**
     * Terminate the Chrome instance
     *
     * @method close
     */
    public function close(): void
    {
        if ($this->instance && is_a($this->instance, 'ChromeDevtoolsProtocol\\CloseableResourceInterface')) {
            $this->instance->close();
            $this->instance = null;
        }
    }

    public function getDevToolsInstance(): InstanceInterface
    {
        return $this->instance;
    }

    public function getDevToolsContext(): ContextInterface
    {
        return $this->context;
    }

    public function createDevToolsSession(): Session
    {
        return $this->instance->createSession($this->context);
    }
}
