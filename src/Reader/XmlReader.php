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
    public const CONTEXT_XPATH = 'xml_xpath';

    private ?string $dto;
    private \SplFileInfo $file;
    private \SimpleXMLIterator $iterator;
    private int $index = 1;
    private array $defaultContext = [
        self::CONTEXT_XPATH => null,
    ];

    public function __construct(string $filePath, ?string $dto = null, array $defaultContext = [])
    {
        // create a new SplInfo from path
        $this->file = new \SplFileInfo($filePath);

        // check if file is readable
        if (!$this->file->isReadable()) {
            throw new \InvalidArgumentException('The file '.$this->file->getFilename().' is not readable.');
        }

        // update default context
        $this->defaultContext = \array_merge($this->defaultContext, $defaultContext);

        if (null === $this->defaultContext[self::CONTEXT_XPATH]) {
            $this->iterator = new \SimpleXMLIterator($this->file->getPathname(), 0, true);
        } else {
            // init SimpleXMLElement from path
            $element = new \SimpleXMLElement($this->file->getPathname(), 0, true);

            // explode string into array
            $nodes = \explode('/', $this->defaultContext[self::CONTEXT_XPATH]);

            // get first node (current element node)
            $rootNode = \array_shift($nodes);

            if ($rootNode !== $element->getName()) {
                throw new \InvalidArgumentException('The path "'.$this->defaultContext[self::CONTEXT_XPATH].'" is incorrect.');
            }

            // go to the asked node
            foreach ($nodes as $node) {
                // check if child exist
                if (!isset($element->{$node})) {
                    throw new \InvalidArgumentException('The path "'.$this->defaultContext[self::CONTEXT_XPATH].'" is incorrect.');
                }

                // update current element
                $element = $element->{$node};
            }

            $this->iterator = new \SimpleXMLIterator($element->asXML());
        }

        // set dto
        $this->dto = $dto;

        // must rewind before use
        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function getDto(): ?string
    {
        return $this->dto;
    }

    /**
     * {@inheritdoc}
     */
    public function isDenormalizable(): bool
    {
        return null !== $this->dto;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): array
    {
        if (!$this->valid()) {
            return [];
        }

        return self::transformToArray($this->iterator->current());
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        $this->iterator->next();
        ++$this->index;
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->iterator->count();
    }

    /**
     * Transform SimpleXMLIterator into array.
     *
     * TODO: move this method to a helper or an util class
     */
    private static function transformToArray(\SimpleXMLIterator $iterator): array
    {
        $result = [];

        foreach ((array) $iterator as $index => $node) {
            if (\is_object($node)) {
                $result[$index] = self::transformToArray($node);
            } else {
                $result[$index] = $node;
            }
        }

        return $result;
    }
}
