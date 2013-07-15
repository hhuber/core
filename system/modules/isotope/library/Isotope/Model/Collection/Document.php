<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2008-2012 Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://www.isotopeecommerce.com
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace Isotope\Model\Collection;


class Document extends \Model\Collection
{

    /**
     * Cache of payment method classes
     * @var array
     */
    private static $arrClasses;

    /**
     * Fetch the next result row and create the model
     *
     * @return boolean True if there was another row
     */
    protected function fetchNext()
    {
        if ($this->objResult->next() == false)
        {
            return false;
        }

        $strClass = '\Isotope\Model\Document\\' . $this->objResult->type;

        if (!class_exists($strClass)) {
            throw new \UnexpectedValueException('Class "' . $this->objResult->type . '" for document ID ' . $this->objResult->id . ' not found.');
        }

        $this->arrModels[$this->intIndex + 1] = new $strClass($this->objResult);

        return true;
    }

    /**
     * Find all classes and cache the result
     * @return array
     */
    public static function getClasses()
    {
        if (null === static::$arrClasses) {

            static::$arrClasses = array();
            $arrNamespaces = \NamespaceClassLoader::getClassLoader()->getPrefixes();

            if (is_array($arrNamespaces['Isotope/Model/Document'])) {
                foreach ($arrNamespaces['Isotope/Model/Document'] as $strPath) {
                    foreach (scan($strPath . '/Isotope/Model/Document') as $strFile) {

                        $strClass = pathinfo($strFile, PATHINFO_FILENAME);
                        $strNamespacedClass = '\Isotope\Model\Document\\' . $strClass;

                        if (is_a($strNamespacedClass, 'Isotope\Interfaces\IsotopeDocument', true)) {
                            static::$arrClasses[$strClass] = $strNamespacedClass;
                        }
                    }
                }
            }
        }

        return static::$arrClasses;
    }

    /**
     * Return labels for all documents
     * @return array
     */
    public static function getClassLabels()
    {
        $arrLabels = array();

        foreach (static::getCLasses() as $strClass => $strNamespacedClass) {
            $arrLabels[$strClass] = call_user_func(array($strNamespacedClass, 'getClassLabel'));
        }

        return $arrLabels;
    }
}
