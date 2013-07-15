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

namespace Isotope\Factory;

class Document
{

    /**
     * Cache of document classes
     * @var array
     */
    private static $arrClasses;

    /**
     * Build a document based on given data
     * @param string
     * @param \Isotope\Interface\IsotopeProductCollection
     * @return Isotope\Interface\IsotopeDocument
     */
    public static function build($strClass, $objCollection)
    {
        // Try config class if none is given
        if ($strClass == '' || !class_exists('\Isotope\Document\\' . $strClass)) {
            //$strClass = Isotope::getInstance()->getConfig()->gallery;
        }

        // Use Standard class if no other is available
        if ($strClass == '' || !class_exists('\Isotope\Document\\' . $strClass)) {
            $strClass = 'Standard';
        }

        $strClass = '\Isotope\Document\\' . $strClass;

        return new $strClass($objCollection);
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

            if (is_array($arrNamespaces['Isotope/Document'])) {
                foreach ($arrNamespaces['Isotope/Document'] as $strPath) {
                    foreach (scan($strPath . '/Isotope/Document') as $strFile) {

                        $strClass = pathinfo($strFile, PATHINFO_FILENAME);
                        $strNamespacedClass = '\Isotope\Document\\' . $strClass;

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
