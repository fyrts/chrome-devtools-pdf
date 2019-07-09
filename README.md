ChromeDevToolsPdf
=================

ChromeDevToolsPdf provides a wrapper around [jakubkulhan/chrome-devtools-protocol](https://github.com/jakubkulhan/chrome-devtools-protocol) for fast and predictable generation of PDF files using the Chrome DevTools Protocol.

## Setup
Start off by installing a recent version of Chrome/Chromium, then install the package through [composer](http://getcomposer.org):
```
composer require fyrts/chrome-devtools-pdf
```

You can choose to manage the Chrome process yourself (preferably using a process manager like [Supervisord](http://supervisord.org)), or have the library fire up a process on request.

To share a single process, start Chrome with the `remote-debugging-port` flag and use the `connect()` method:
```php
$instance = ChromeDevToolsPdf\Instance::connect(9222);
```

To launch Chrome on demand, use the `launch()` method, providing the executable path if needed:
```php
$instance = ChromeDevToolsPdf\Instance::launch('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome');
```

## Usage
PDF files can be generated from either publicly accessible URLs, or raw HTML source code.

```php
$instance = ChromeDevToolsPdf\Instance::launch();
$pdf = $instance->loadUrl('https://www.google.com');
$pdf->saveFile(__DIR__ . '/filename.pdf');
$instance->close();
```

```php
$instance = ChromeDevToolsPdf\Instance::launch();
$pdf = $instance->loadHtml('<h1>Example</h1>');
$pdf->saveFile(__DIR__ . '/filename.pdf');
$instance->close();
```

To receive raw data instead of directly saving a file, use the `generate()` method instead:
```php
$instance = ChromeDevToolsPdf\Instance::launch();
$pdf = $instance->loadUrl('https://www.google.com');
Storage::put($__DIR__ . '/filename.pdf', $pdf->generate());
$instance->close();
```

Change page layout settings by calling setters on the PDF object:
```php
$instance = ChromeDevToolsPdf\Instance::launch();
$pdf = $instance->loadUrl('https://www.google.com');
$pdf->setLandscape(true)->setMarginLeft(3);
$pdf->setDisplayHeaderFooter(true)->setHeaderTemplate('Title: <span class="title"></span>');
$pdf->saveFile(__DIR__ . '/filename.pdf');
$instance->close();
```

Available options are as follows:
- `setLandscape(true|false)` – Paper orientation. Defaults to false.
- `setDisplayHeaderFooter(true|false)` – Display header and footer. Defaults to false.
- `setPrintBackground(true|false)` – Print background graphics. Defaults to false.
- `setScale(int|float)` – Scale of the webpage rendering. Defaults to 1.
- `setPaperWidth(int|float)` and `setPaperHeight(int|float)` – Paper width or height in inches. Defaults to 8.5 and 11 inches respectively.
- `setMarginTop(int|float)`, `setMarginBottom(int|float)`, `setMarginLeft(int|float)` and `setMarginRight(int|float)` – Top, bottom, left or right margin in inches. Defaults to 1cm (~0.4 inches).
- `setPageRanges(string)` – Paper ranges to print, e.g., '1-5, 8, 11-13'. Defaults to the empty string, which means print all pages.
- `setIgnoreInvalidPageRanges(true|false)` – Whether to silently ignore invalid but successfully parsed page ranges, such as '3-2'. Defaults to false.
- `setHeaderTemplate(string)` – HTML template for the print header. Should be valid HTML markup with following classes used to inject printing values into them (for example, <span class=title></span> would generate span containing the title):
    - date: formatted print date
    - title: document title
    - url: document location
    - pageNumber: current page number
    - totalPages: total pages in the document
- `setFooterTemplate(string)` – HTML template for the print footer. Should use the same format as the headerTemplate.
- `setPreferCSSPageSize(true|false)` – Whether or not to prefer page size as defined by css. Defaults to false, in which case the content will be scaled to fit the paper size.


The library can be implemented asynchronously. This can be useful when printing multiple pages. When doing so, use the `await()` method to prevent the script from terminating prematurely.
```php
$instance = ChromeDevToolsPdf\Instance::launch();

$pdf1 = $instance->loadUrl('https://www.google.com');
$pdf1->saveFileAsync(__DIR__ . '/filename-1.pdf');

$pdf2 = $instance->loadUrl('https://www.github.com');
$pdf2->saveFileAsync(__DIR__ . '/filename-2.pdf');

$pdf1->await();
$pdf2->await();

$instance->close();
```
