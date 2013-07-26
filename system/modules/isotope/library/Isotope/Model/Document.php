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

namespace Isotope\Model;
use Isotope\Interfaces\IsotopeProductCollection;

/**
 * Class Document
 *
 * Parent class for all documents.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 */
abstract class Document extends TypeAgent
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_iso_document';

    /**
     * Interface to validate document
     * @var string
     */
    protected static $strInterface = '\Isotope\Interfaces\IsotopeDocument';

    /**
     * List of types (classes) for this model
     * @var array
     */
    protected static $arrModelTypes = array();

    /*
    * Collection
    * @var array
    */
    protected $collection = null;

    /*
     * Config
     * @var array
     */
    protected $config = null;

    /**
     * Set the collection
     * @param IsotopeProductCollection
     * @return Standard
     */
    public function setCollection(IsotopeProductCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Set the store config
     * @param Config
     * @return Standard
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }
}
