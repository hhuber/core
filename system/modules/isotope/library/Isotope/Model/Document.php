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

use Isotope\Isotope;
use Isotope\Factory\ProductCollectionSurcharge as SurchargeFactory;
use Isotope\Interfaces\IsotopeProductCollection;


/**
 * Class Document
 *
 * Parent class for all payment gateway modules.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 */
abstract class Document extends \Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_iso_document';


    /**
     * Return a model or collection based on the database result type
     */
    protected static function find(array $arrOptions)
    {
        if (static::$strTable == '')
        {
            return null;
        }

        $arrOptions['table'] = static::$strTable;
        $strQuery = \Model\QueryBuilder::find($arrOptions);

        $objStatement = \Database::getInstance()->prepare($strQuery);

        // Defaults for limit and offset
        if (!isset($arrOptions['limit']))
        {
            $arrOptions['limit'] = 0;
        }
        if (!isset($arrOptions['offset']))
        {
            $arrOptions['offset'] = 0;
        }

        // Limit
        if ($arrOptions['limit'] > 0 || $arrOptions['offset'] > 0)
        {
            $objStatement->limit($arrOptions['limit'], $arrOptions['offset']);
        }

        $objStatement = static::preFind($objStatement);
        $objResult = $objStatement->execute($arrOptions['value']);

        if ($objResult->numRows < 1) {

            return null;
        }

        $objResult = static::postFind($objResult);

        if ($arrOptions['return'] == 'Model') {
            $strClass = '\Isotope\Model\Document\\' . $objResult->type;

            return new $strClass($objResult);
        } else {

            return new \Isotope\Model\Collection\Document($objResult, static::$strTable);
        }
    }
}
