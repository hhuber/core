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
 * Invoice templates print a collection
 */
interface IsotopeInvoiceTemplate
{

    /**
     * Generate the invoice template
     * @return  string
     */
    public function generate();

    /**
     * Return the name and description for this invoice template
     * @return array
     */
    public static function getClassLabel();
}
