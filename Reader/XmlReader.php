<?php

namespace IQ2i\DataImporter\Reader;

use IQ2i\DataImporter\Traits\SerializerTrait;

class XmlReader implements ReaderInterface
{
    use SerializerTrait;

    const FILE_REGEX_KEY = 'xml_file_regex';
    const XPATH_KEY = 'xml_xpath';

    private $file;
    private $index = 1;
    private $defaultContext = [
        self::FILE_REGEX_KEY  => '/.xml/',
        self::XPATH_KEY       => null,
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(\SplFileObject $file): void
    {
        if (null === $this->defaultContext[self::XPATH_KEY]) {
            $this->file = new \SimpleXMLIterator($file->getPathname(), null, true);
        } else {
            // init SimpleXMLElement from path
            $element = new \SimpleXMLElement($file->getPathname(), null, true);

            // explode string into array
            $nodes = explode('/', $this->defaultContext[self::XPATH_KEY]);

            // get first node (current element node)
            $rootNode = array_shift($nodes);

            if ($rootNode !== $element->getName()) {
                throw new \InvalidArgumentException('The path "'.$this->defaultContext[self::XPATH_KEY].'" is incorrect.');
            }

            // go to the asked node
            foreach ($nodes as $node) {
                // check if child exist
                if (!isset($element->{$node})) {
                    throw new \InvalidArgumentException('The path "'.$this->defaultContext[self::XPATH_KEY].'" is incorrect.');
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
    public function getDefaultFileRegex(): string
    {
        return $this->defaultContext[self::FILE_REGEX_KEY];
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
