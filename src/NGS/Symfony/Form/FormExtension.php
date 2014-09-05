<?php

namespace NGS\Symfony\Form;

use Symfony\Component\Form\FormExtensionInterface;

class FormExtension implements FormExtensionInterface
{
    private $typeMap;

    public function __construct($formTypeMap)
    {
        $this->typeMap = $formTypeMap;
    }

    public function getType($name)
    {
        if (!isset($this->typeMap[$name])) {
            throw new \InvalidArgumentException('No defined form type for form name "'.$name.'"');
        }
        $typeClass = $this->typeMap[$name];
        return new $typeClass();
    }

    /**
     * Returns whether the given type is supported.
     *
     * @param string $name The name of the type
     *
     * @return Boolean Whether the type is supported by this extension
     */
    public function hasType($name)
    {
        return isset($this->typeMap[$name]);
    }

    /**
     * Returns the extensions for the given type.
     *
     * @param string $name The name of the type
     *
     * @return FormTypeExtensionInterface[] An array of extensions as FormTypeExtensionInterface instances
     */
    public function getTypeExtensions($name)
    {
        return array();
    }

    /**
     * Returns whether this extension provides type extensions for the given type.
     *
     * @param string $name The name of the type
     *
     * @return Boolean Whether the given type has extensions
     */
    public function hasTypeExtensions($name)
    {
        return false;
    }

    /**
     * Returns the type guesser provided by this extension.
     *
     * @return FormTypeGuesserInterface|null The type guesser
     */
    public function getTypeGuesser()
    {
        return null;
    }
}
