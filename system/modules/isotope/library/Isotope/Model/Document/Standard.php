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

namespace Isotope\Model\Document;

use Isotope\Interfaces\IsotopeDocument;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Model\Config;

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

    /**
     * {@inheritdoc}
     */
    public function printToBrowser()
    {
/*
        $strInvoiceTitle = 'invoice_' . $objOrder->order_id;
        $pdf->Output(standardize(ampersand($strInvoiceTitle, false), true) . '.pdf', 'D');
*/
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function store($path)
    {

    }
}
