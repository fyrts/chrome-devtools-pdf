<?php

namespace ChromeDevToolsPdf;

use ChromeDevtoolsProtocol\Model\Page\{NavigateRequest, SetDocumentContentRequest};
use ChromeDevtoolsProtocol\Model\Network\SetCacheDisabledRequest;
use ChromeDevtoolsProtocol\Domain\PageDomainInterface;
use ChromeDevtoolsProtocol\Session;

class Pdf
{
    use PageSetupTrait;

    protected $instance;
    protected $session;
    protected $page;
    protected $isReady = true;

    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Load HTML source code from a string.
     *
     * @method loadHtml
     * @param  string   $source The HTML source
     * @return self
     */
    public function loadHtml(string $source): self
    {
        $context = $this->instance->getDevToolsContext();

        $this->isReady = false;
        $page = $this->getPage();
        $frame_id = $page->getResourceTree($context)->frameTree->frame->id;
        $page->setDocumentContent($context, SetDocumentContentRequest::builder()->setFrameId($frame_id)->setHtml($source)->build());

        return $this;
    }

    /**
     * Load a page from a URL.
     *
     * @method loadUrl
     * @param  string  $url The URL refering to the page to print
     * @return self
     */
    public function loadUrl(string $url): self
    {
        $context = $this->instance->getDevToolsContext();

        $this->isReady = false;
        $this->getPage()->navigate($context, NavigateRequest::builder()->setUrl($url)->build());

        return $this;
    }

    /**
     * Generate a PDF file from the loaded page.
     *
     * @method generate
     * @return string   Binary data representing the PDF file
     */
    public function generate(): string
    {
        $context = $this->instance->getDevToolsContext();
        $page = $this->getPage();
        $this->await();

        $response = $page->printToPDF($context, $this->buildPdfRequest());
        $this->close();
        return base64_decode($response->data);
    }

    /**
     * Asynchronously generate a PDF file from the loaded page. Use `await()` to prevent the script from terminating.
     * prematurely.`
     *
     * @method generateAsync
     * @param  callable      $callback A callback function that accepts binary data as its first argument
     */
    public function generateAsync(callable $callback): void
    {
        $context = $this->instance->getDevToolsContext();
        $page = $this->getPage();

        if ($this->isReady) {
            $response = $page->printToPDF($context, $this->buildPdfRequest());
            $callback(base64_decode($response->data));
            $this->close();
        } else {
            $page->addLoadEventFiredListener(function() use ($page, $context, $callback) {
                $response = $page->printToPDF($context, $this->buildPdfRequest());
                $callback(base64_decode($response->data));
                $this->close();
            });
        }
    }

    /**
     * Save the PDF to a file.
     *
     * @method saveFile
     * @param  string   $path The location and filename of the file to save
     * @return bool           Whether or not the file was saved successfully
     */
    public function saveFile(string $path): bool
    {
        return file_put_contents($path, $this->generate()) !== false;
    }

    /**
     * Asynchronously save the PDF to a file.
     *
     * @method saveFileAsync
     * @param  string        $path     The location and filename of the file to save
     * @param  callable      $callback A callback function that accepts a success/failure boolean as its first argument
     */
    public function saveFileAsync(string $path, ?callable $callback = null): void
    {
        $this->generateAsync(function($data) use ($path, $callback) {
            $success = file_put_contents($path, $data) !== false;
            if (!is_null($callback)) $callback($success);
        });
    }

    /**
     * Wait for de PDF to complete generation.
     *
     * @method await
     */
    public function await(): void
    {
        if (!$this->isReady) {
            if ($page = $this->getPage()) {
                $page->awaitLoadEventFired($this->instance->getDevToolsContext());
            }
        }
    }

    /**
     * Disable the browser cache for current session
     *
     * @method setCacheDisabled
     * @param  boolean  $cacheDisabled
     * @return self
     */
    public function setCacheDisabled(?bool $cacheDisabled = true): self
    {
        $request = SetCacheDisabledRequest::builder()->setCacheDisabled($cacheDisabled)->build();
        $this->getSession()->network()->setCacheDisabled($this->instance->getDevToolsContext(), $request);
        return $this;
    }

    /**
     * Clear the browser cache
     *
     * @method clearCache
     * @return self
     */
    public function clearCache(): self
    {
        $this->getSession()->network()->clearBrowserCache($this->instance->getDevToolsContext());
        return $this;
    }

    /**
     * Terminate the Chrome session
     *
     * @method close
     * @return self
     */
    public function close(): self
    {
        $this->isReady = true;
        if (!is_null($this->page)) {
            $this->page->close($this->instance->getDevToolsContext());
            $this->page = null;
        }
        if (!is_null($this->session)) {
            $this->session->close();
            $this->session = null;
        }
        return $this;
    }

    public function getPage(): ?PageDomainInterface
    {
        $context = $this->instance->getDevToolsContext();
        if (is_null($this->page)) {
            $this->page = $this->getSession()->page();
            $this->page->addLoadEventFiredListener(function() {
                $this->isReady = true;
            });
        }
        $this->page->enable($context);
        return $this->page;
    }

    protected function getSession(): Session
    {
        if (is_null($this->session)) {
            $this->session = $this->instance->createDevToolsSession();
        }
        return $this->session;
    }
}
