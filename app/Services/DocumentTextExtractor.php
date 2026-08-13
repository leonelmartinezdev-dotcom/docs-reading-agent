<?php

namespace App\Services;

use App\Models\Document;
use Exception;
use Illuminate\Support\Facades\Storage;
use Shipfastlabs\Parsel;
use Spatie\PdfToText\Pdf;

use function PHPUnit\Framework\throwException;

class DocumentTextExtractor
{
    /**
     * Create a new class instance.
     */

    const TYPES_SUPPORT_WITH_PARSEL = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/bmp',
        'image/tiff',
        'image/webp',
    ];

    const TYPES_TEXT = [
        'text/plain',
        'text/csv'
    ];

    public function __construct()
    {
        //
    }

    public function extract(Document $document)
    {
        return $this->getExtractor($document);
    }

    public function getExtractor(Document $document)
    {
        if (in_array($document->extension, self::TYPES_SUPPORT_WITH_PARSEL)) {
            return $this->extractWithParsel($document);
        }
        if (in_array($document->extension, self::TYPES_TEXT)) {
            return $this->extractTxt($document);
        }

        throw new Exception('Unsupported Document Type');
    }

    public function extractPDF(Document $document)
    {
        $path = $this->getPath($document);
        return (new Pdf())
            ->setPdf($path)
            ->text();
    }


    public function extractWithParsel(Document $document)
    {
        $path = $this->getPath($document);

        $text = Parsel::file($path)
            ->withOcr()
            ->text();

        return $text;
    }

    public function extractTxt(Document $document)
    {
        return Storage::disk('public')->get($document->url);
    }

    private function getPath(Document $document)
    {
        return Storage::disk('public')->path($document->url);
    }
}
