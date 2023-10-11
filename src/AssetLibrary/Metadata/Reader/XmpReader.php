<?php

declare(strict_types=1);

namespace Concrete5\AssetLibrary\Metadata\Reader;

use Concrete5\AssetLibrary\Metadata\FileMetadata;
use DOMDocument;
use DOMXPath;

class XmpReader implements MetadataReaderInterface
{

    public function read($resource): ?FileMetadata
    {
        if (!class_exists(DOMDocument::class)) {
            // Unsupported
            return null;
        }

        $xmp = $this->extractXmpString($resource);
        if (!$xmp) {
            return null;
        }

        $xml = new DOMDocument();
        $xml->loadXML($xmp);

        $title = null;
        $description = null;
        $keywords = null;

        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');

        $elements = $xml->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'RDF');
        foreach ($elements as $rdfNode) {
            $title = $this->xpathContentFirst($xpath, '//rdf:Description//dc:title//rdf:li', $rdfNode);
            $description = $this->xpathContentFirst($xpath, '//rdf:Description//dc:description//rdf:li', $rdfNode);
            $keywords = iterator_to_array(
                $this->xpathContent($xpath, '//rdf:Description//dc:subject//rdf:li', $rdfNode)
            );
            break;
        }

        return new FileMetadata(
            $title ?: null,
            $description ?: null,
            $keywords ?: []
        );
    }

    /**
     * @param resource $readStream
     * @return string
     */
    private function extractXmpString($readStream): ?string
    {
        assert(is_resource($readStream), '$readStream must be a resource');

        // 10kb at a time
        $chunkLength = 10000;
        $buffer = '';
        $startTag = '<x:xmpmeta';
        $endTag = '</x:xmpmeta>';
        $start = null;

        $minStartLen = strlen($startTag) - 1;

        do {
            $chunk = fread($readStream, $chunkLength);
            if ($chunk === '') {
                // If we have an empty string there's no more to read
                break;
            }

            $buffer .= $chunk;
            unset($chunk);

            // Phase 1: Searching for the $startTag
            if ($start === null) {
                $pos = strpos($buffer, $startTag);
                if ($pos !== false) {
                    $start = 0;
                    $buffer = substr($buffer, $pos);
                    continue;
                }

                // Trim the buffer, we only need the last few characters to ensure we don't miss a split tag
                $buffer = substr($buffer, -1 * $minStartLen);
                continue;
            }

            // Phase 2: Reading until we reach the end tag
            $pos = strpos($buffer, $endTag);
            if ($pos !== false) {
                return substr($buffer, 0, $pos + strlen($endTag));
            }
        } while (!feof($readStream));

        return null;
    }

    private function xpathContent(DOMXPath $xpath, $path, $node = null): \Generator
    {
        foreach ($xpath->query($path, $node) as $elem) {
            yield $elem->nodeValue;
        }
    }

    private function xpathContentFirst(DOMXPath $xpath, $path, $node = null): ?string
    {
        foreach ($this->xpathContent($xpath, $path, $node) as $first) {
            return $first;
        }

        return null;
    }

}