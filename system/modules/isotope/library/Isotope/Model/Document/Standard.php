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
     * Construct the object
     * @param string
     * @param array
     */
    public function __construct(IsotopeProductCollection $collection, Config $config)
    {
        $this->collection = $collection;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function printToBrowser()
    {

        // @todo make things like this configurable in a further version of Isotope
        $strInvoiceTitle = 'invoice_' . $objOrder->order_id;
        $pdf->Output(standardize(ampersand($strInvoiceTitle, false), true) . '.pdf', 'D');

        // Stop script execution
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
