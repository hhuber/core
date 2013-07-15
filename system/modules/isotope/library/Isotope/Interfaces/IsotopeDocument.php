<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2012 Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://www.isotopeecommerce.com
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace Isotope\Interfaces;


/**
 * Documents print a collection
 */
interface IsotopeDocument
{

    /**
     * Generate the document and send it to browser
     */
    public function printToBrowser();

    /**
     * Generate the document and store it to a given path
     */
    public function store($path);

    /**
     * Return the name and description for this document
     * @return array
     */
    public static function getClassLabel();
}
