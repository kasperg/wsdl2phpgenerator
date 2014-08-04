<?php


namespace Wsdl2PhpGenerator\Xml;


use DOMDocument;
use DOMElement;
use Exception;
use Zend\Uri\Uri;

/**
 * A SchemaDocument represents an XML element which contains type elements.
 *
 * The element may reference other schemas to generate a tree structure.
 */
class SchemaDocument extends XmlNode
{

    /**
     * The url or path representing the location of the schema.
     *
     * @var string
     */
    protected $uri;


    /**
     * The schemas which are imported by the current schema.
     *
     * @var SchemaDocument[]
     */
    protected $imports;

    /**
     * The urls of schemas which have already been loaded.
     *
     * We keep a record of these to avoid cyclic imports.
     *
     * @var string[]
     */
    protected static $loadedUris;

    public function __construct($xsdUri)
    {
        $this->uri = $this->resolveUri($xsdUri);

        $document = new DOMDocument();
        $loaded = $document->load($xsdUri);
        if (!$loaded) {
            throw new Exception('Unable to load XML from '. $xsdUri);
        }
        parent::__construct($document, $document->documentElement);

        // Register the schema to avoid cyclic imports.
        self::$loadedUris[] = $xsdUri;

        // Locate and instantiate schemas which are imported by the current schema.
        $this->imports = array();
        foreach ($this->xpath('//wsdl:import/@location|//s:import/@schemaLocation') as $import) {
            $importUri = $this->resolveUri($import->value, $xsdUri);
            if (!in_array($importUri, self::$loadedUris)) {
                $this->imports[] = new SchemaDocument($importUri);
            }
        }
    }

    /**
     * Parses the schema for a type with a specific name.
     *
     * @param string $name The name of the type
     * @return DOMElement|null Returns the type node with the provided if it is found. Null otherwise.
     */
    public function findTypeElement($name)
    {
        $type = null;

        $elements = $this->xpath('//s:simpleType[@name=%s]|//s:complexType[@name=%s]', $name, $name);
        if ($elements->length > 0) {
            $type = $elements->item(0);
        }

        if (empty($type)) {
            foreach ($this->imports as $import) {
                $type = $import->findTypeElement($name);
                if (!empty($type)) {
                    break;
                }
            }
        }

        return $type;
    }

    /**
     * Returns the absolute uri to a file.
     *
     * @param string $uri An absolute or relative url or path to a file.
     * @param string $baseUri The base for the uri. Cannot be null for relative uris.
     * @return string The absolute uri to the file.
     */
    protected function resolveUri($uri, $baseUri = null)
    {
        $absoluteUri = null;

        if (filter_var($uri, FILTER_VALIDATE_URL)) {
            $absoluteUri = $uri;
        } elseif (filter_var($baseUri, FILTER_VALIDATE_URL)) {
            $absoluteUri = Uri::merge($baseUri, $uri)->toString();
        } elseif (!empty($baseUri)) {
            $absoluteUri = realpath(dirname($baseUri) . DIRECTORY_SEPARATOR . $uri);
        } else {
            $absoluteUri = realpath($uri);
        }

        return $absoluteUri;
    }

}
