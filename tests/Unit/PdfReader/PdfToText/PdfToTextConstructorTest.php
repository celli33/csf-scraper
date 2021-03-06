<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper\Tests\Unit\PdfReader\PdfToText;

use PhpCfdi\CsfScraper\PdfReader\PdfToText;
use PhpCfdi\CsfScraper\Tests\TestCase;
use RuntimeException;

class PdfToTextConstructorTest extends TestCase
{
    /**
     * @requires OSFAMILY Linux
     */
    public function test_pdftotext_is_installed_default_path(): void
    {
        $csfExtractor = new PdfToText();
        $this->assertInstanceOf(PdfToText::class, $csfExtractor);
    }

    public function test_throw_exception_when_path_not_found(): void
    {
        $csfExtractor = new PdfToText('aaa');
        $this->expectException(RuntimeException::class);
        $csfExtractor->extract('path');
    }
}
