<?php

declare(strict_types=1);

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Reader;

class XmlReader implements ReaderInterface
{
    /**
     * @var string
     */
    final public const CONTEXT_XPATH = 'xml_xpath';

    /**
     * @var string
     */
    final public const CONTEXT_NAMESPACES = 'xml_namespaces';

    private readonly \SplFileInfo $file;

    private \SimpleXMLIterator $iterator;

    private int $index = 1;

    private array $defaultContext = [
        self::CONTEXT_XPATH => null,
        self::CONTEXT_NAMESPACES => [],
    ];

    public function __construct(
        string $filePath,
        private readonly ?string $dto = null,
        array $defaultContext = [],
    ) {
        $this->file = new \SplFileInfo($filePath);
        if (!$this->file->isReadable()) {
            throw new \InvalidArgumentException('The file '.$this->file->getFilename().' is not readable.');
        }

        $this->defaultContext = \array_merge($this->defaultContext, $defaultContext);

        if (null === $this->defaultContext[self::CONTEXT_XPATH]) {
            $this->iterator = new \SimpleXMLIterator($this->file->getPathname(), 0, true);
        } else {
            $element = new \SimpleXMLElement($this->file->getPathname(), 0, true);
            $xpath = (string) $this->defaultContext[self::CONTEXT_XPATH];

            foreach ($this->defaultContext[self::CONTEXT_NAMESPACES] as $prefix => $uri) {
                $element->registerXPathNamespace($prefix, $uri);
            }

            [$rootSegment, $subPath] = \array_pad(\explode('/', $xpath, 2), 2, null);

            $localRootName = \str_contains((string) $rootSegment, ':')
                ? \substr((string) $rootSegment, \strpos((string) $rootSegment, ':') + 1)
                : $rootSegment;

            if ($localRootName !== $element->getName()) {
                throw new \InvalidArgumentException('The path "'.$xpath.'" is incorrect.');
            }

            if (null !== $subPath) {
                $results = $element->xpath($subPath);
                if (empty($results)) {
                    throw new \InvalidArgumentException('The path "'.$xpath.'" is incorrect.');
                }

                $element = $results[0];
            }

            $dom = \dom_import_simplexml($element);
            $doc = new \DOMDocument();
            $doc->appendChild($doc->importNode($dom, true));
            $this->iterator = new \SimpleXMLIterator($doc->saveXML());
        }

        $this->rewind();
    }

    public function getDto(): ?string
    {
        return $this->dto;
    }

    public function isDenormalizable(): bool
    {
        return null !== $this->dto;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function index(): mixed
    {
        return $this->index;
    }

    public function current(): array
    {
        if (!$this->valid()) {
            return [];
        }

        return self::transformToArray($this->iterator->current());
    }

    public function next(): void
    {
        $this->iterator->next();
        ++$this->index;
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function count(): int
    {
        return $this->iterator->count();
    }

    /**
     * Transform SimpleXMLIterator into array.
     */
    private static function transformToArray(\SimpleXMLIterator $iterator): array
    {
        $result = [];

        foreach ((array) $iterator as $index => $node) {
            $result[$index] = \is_object($node) ? self::transformToArray($node) : $node;
        }

        return $result;
    }
}
