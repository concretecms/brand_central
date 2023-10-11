<?php

use Concrete5\AssetLibrary\Metadata\FileMetadata;
use Concrete5\AssetLibrary\Metadata\Reader;

$readers = fn() => [
    'IptcReader' => [new Reader\IptcReader()],
    'XmpReader' => [new Reader\XmpReader()],
];

it('Reads data', function (Reader\MetadataReaderInterface $reader) {
    $handle = fopen(TEST_ROOT . '/fixtures/iptc-test.jpg', 'rb+');
    $result = $reader->read($handle);
    fclose($handle);

    expect($result)
        ->toBeInstanceOf(FileMetadata::class)
        ->title->toBe('Portrait of Edouard Branly')
        ->description->toBe('Scientific Identity, Portrait of Edouard Branly')
        ->keywords->toBe(['Edouard Branly', 'Physics', 'Physicist'])
    ;
})->with($readers);

it('Handles file with no data', function (Reader\MetadataReaderInterface $reader) {
    $handle = fopen(TEST_ROOT . '/fixtures/iptc-test3.jpg', 'rb+');
    $result = $reader->read($handle);
    fclose($handle);

    expect($result)->toBeNull();
})->with($readers);


it('Handles empty data', function (Reader\MetadataReaderInterface $reader) {
    $handle = fopen(TEST_ROOT . '/fixtures/iptc-test2.jpg', 'rb+');
    $result = $reader->read($handle);
    fclose($handle);

    expect($result)
        ->toBeInstanceOf(FileMetadata::class)
        ->title->toBeNull()
        ->description->toBeNull()
        ->keywords->toBe([])
    ;
})->with([[new Reader\IptcReader()]]); // I couldn't find a public domain file that had empty XMP data quickly.
