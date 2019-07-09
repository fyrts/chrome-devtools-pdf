<?php

namespace ChromeDevToolsPdf;

use ChromeDevtoolsProtocol\Model\Page\PrintToPDFRequest;

trait PageSetupTrait
{
    protected $pdfRequest;

    protected function getPdfRequest()
    {
        if (is_null($this->pdfRequest)) $this->pdfRequest = new PrintToPDFRequest();
        return $this->pdfRequest;
    }

    /**
     * @param bool|null $landscape
     *
     * @return self
     */
    public function setLandscape(?bool $landscape): self
    {
        $this->getPdfRequest()->landscape = $landscape;
        return $this;
    }

    /**
     * @param bool|null $displayHeaderFooter
     *
     * @return self
     */
    public function setDisplayHeaderFooter(?bool $displayHeaderFooter): self
    {
        $this->getPdfRequest()->displayHeaderFooter = $displayHeaderFooter;
        return $this;
    }

    /**
     * @param bool|null $printBackground
     *
     * @return self
     */
    public function setPrintBackground(?bool $printBackground): self
    {
        $this->getPdfRequest()->printBackground = $printBackground;
        return $this;
    }

    /**
     * @param int|float|null $scale
     *
     * @return self
     */
    public function setScale($scale): self
    {
        $this->getPdfRequest()->scale = $scale;
        return $this;
    }

    /**
     * @param int|float|null $paperWidth
     *
     * @return self
     */
    public function setPaperWidth($paperWidth): self
    {
        $this->getPdfRequest()->paperWidth = $paperWidth;
        return $this;
    }

    /**
     * @param int|float|null $paperHeight
     *
     * @return self
     */
    public function setPaperHeight($paperHeight): self
    {
        $this->getPdfRequest()->paperHeight = $paperHeight;
        return $this;
    }

    /**
     * @param int|float|null $marginTop
     *
     * @return self
     */
    public function setMarginTop($marginTop): self
    {
        $this->getPdfRequest()->marginTop = $marginTop;
        return $this;
    }

    /**
     * @param int|float|null $marginBottom
     *
     * @return self
     */
    public function setMarginBottom($marginBottom): self
    {
        $this->getPdfRequest()->marginBottom = $marginBottom;
        return $this;
    }

    /**
     * @param int|float|null $marginLeft
     *
     * @return self
     */
    public function setMarginLeft($marginLeft): self
    {
        $this->getPdfRequest()->marginLeft = $marginLeft;
        return $this;
    }

    /**
     * @param int|float|null $marginRight
     *
     * @return self
     */
    public function setMarginRight($marginRight): self
    {
        $this->getPdfRequest()->marginRight = $marginRight;
        return $this;
    }

    /**
     * @param string|null $pageRanges
     *
     * @return self
     */
    public function setPageRanges(?string $pageRanges): self
    {
        $this->getPdfRequest()->pageRanges = $pageRanges;
        return $this;
    }

    /**
     * @param bool|null $ignoreInvalidPageRanges
     *
     * @return self
     */
    public function setIgnoreInvalidPageRanges(?bool $ignoreInvalidPageRanges): self
    {
        $this->getPdfRequest()->ignoreInvalidPageRanges = $ignoreInvalidPageRanges;
        return $this;
    }

    /**
     * @param string|null $headerTemplate
     *
     * @return self
     */
    public function setHeaderTemplate(?string $headerTemplate): self
    {
        $this->getPdfRequest()->headerTemplate = $headerTemplate;
        return $this;
    }

    /**
     * @param string|null $footerTemplate
     *
     * @return self
     */
    public function setFooterTemplate(?string $footerTemplate): self
    {
        $this->getPdfRequest()->footerTemplate = $footerTemplate;
        return $this;
    }

    /**
     * @param bool|null $preferCSSPageSize
     *
     * @return self
     */
    public function setPreferCSSPageSize(?bool $preferCSSPageSize): self
    {
        $this->getPdfRequest()->preferCSSPageSize = $preferCSSPageSize;
        return $this;
    }
}
