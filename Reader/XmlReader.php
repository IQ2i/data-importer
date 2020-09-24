<?php

namespace IQ2i\DataImporter\Reader;

use IQ2i\DataImporter\Traits\SerializerTrait;

class XmlReader implements ReaderInterface
{
    use SerializerTrait;

    const CONTEXT_XPATH = 'xml_xpath';

    private $file;
    private $index = 1;
    private $defaultContext = [
        self::CONTEXT_XPATH       => null,
    ];

    public function __construct(string $filePath, array $defaultContext = [])
    {
        // create a new SplInfo from path
        $fileInfo = new \SplFileInfo($filePath);

        // check if file is readable
        if (!$fileInfo->isReadable()) {
            throw new \InvalidArgumentException('The file '.$fileInfo->getFilename().' is not readable.');
        }

        // create SplObject from SplInfo
        $this->file = $fileInfo->openFile();

        // update default context
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);

        if (null === $this->defaultContext[self::CONTEXT_XPATH]) {
            $this->file = new \SimpleXMLIterator($this->file->getPathname(), null, true);
        } else {
            // init SimpleXMLElement from path
            $element = new \SimpleXMLElement($this->file->getPathname(), null, true);

            // explode string into array
            $nodes = explode('/', $this->defaultContext[self::CONTEXT_XPATH]);

            // get first node (current element node)
            $rootNode = array_shift($nodes);

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

            $this->file = new \SimpleXMLIterator($element->asXML());
        }

        // must rewind before use
        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function isDenormalizable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): \SplFileObject
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

        return self::transformToArray($this->file->current());
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->file->next();
        ++$this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->file->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->file->count();
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
            if (is_object($node)) {
                $result[$index] = self::transformToArray($node);
            } else {
                $result[$index] = $node;
            }
        }

        return $result;
    }
}
