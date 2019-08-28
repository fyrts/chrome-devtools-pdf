<?php

namespace ChromeDevToolsPdf;

class HtmlTemplateBuilder
{
    protected $pageWidth;
    protected $pageHeight;

    public function __construct(float $pageWidth, float $pageHeight)
    {
        $this->pageWidth = $pageWidth;
        $this->pageHeight = $pageHeight;
    }

    public function createTemplate(?string $header, ?string $footer): string
    {
        $template = '<style type="text/css">
            * {
                -webkit-print-color-adjust: exact;
                margin: 0; padding: 0;
                border: 0;
                text-decoration: none;
                list-style: none;
                font-size: 16px;
            }
            #_chrome_container {
                width: ' . number_format($this->pageWidth, 3, '.', '') . 'in;
                height: ' . number_format($this->pageHeight, 3, '.', '') . 'in;
                position: absolute;
                top: 0;
                transform: scale(0.75);
                transform-origin: top left;
            }
            #_chrome_header {
                position: relative;
                width: 100%;
            }
            #_chrome_footer {
                position: absolute;
                width: 100%;
                bottom: 0;
            }
        </style>
        <div id="_chrome_container">';
        if ($header) $template .= '<div id="_chrome_header">' . $header . '</div>';
        if ($footer) $template .= '<div id="_chrome_footer">' . $footer . '</div>';
        $template .= '</div>';

        return $template;
    }
}
