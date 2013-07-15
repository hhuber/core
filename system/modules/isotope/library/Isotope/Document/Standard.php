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

namespace Isotope\Document;

use Isotope\Interfaces\IsotopeDocument;
use Isotope\Interfaces\IsotopeProductCollection;

/**
 * Class Standard
 *
 * Provide methods to handle Isotope galleries.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Yanick Witschi <yanick.witschi@terminal42.ch>
 */
class Standard implements IsotopeDocument
{
    /*
     * Collection
     * @var array
     */
    protected $collection = null;


    /**
     * Construct the object
     * @param string
     * @param array
     */
    public function __construct(IsotopeProductCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function printToBrowser()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function store($path)
    {

    }

    /**
     * {@inheritdoc}
     */
    public static function getClassLabel()
    {
        return $GLOBALS['TL_LANG']['DOCUMENTS'][strtolower(str_replace('Isotope\Document\\', '', get_called_class()))];
    }
}
