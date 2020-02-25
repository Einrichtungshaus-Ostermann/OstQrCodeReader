<?php declare(strict_types=1);

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - QrCodeReader
 *
 * @package   OstQrCodeReader
 *
 * @author    Tim Windelschmidt <tim.windelschmidt@ostermann.de>
 * @copyright 2019 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_OstQrCodeReader extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * ...
     *
     * @throws Exception
     */
    public function preDispatch()
    {
        // ...
        $viewDir = $this->container->getParameter('ost_qr_code_reader.view_dir');
        $this->get('template')->addTemplateDir($viewDir);
        parent::preDispatch();
    }

    /**
     * ...
     *
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        // return all actions
        return array_values(array_filter(
            array_map(
                function ($method) {
                    return (substr($method, -6) === 'Action') ? substr($method, 0, -6) : null;
                },
                get_class_methods($this)
            ),
            function ($method) {
                return !in_array((string)$method, ['', 'index', 'load', 'extends'], true);
            }
        ));
    }

    /**
     * ...
     */
    public function indexAction()
    {
    }

    /**
     * ...
     */
    public function openAction()
    {
        $q = $this->Request()->getParam('q');

        if (strpos($q, 'http') === 0) {
            $path = parse_url($q)['path'];
            $pathParts = explode('/', $path);

            if (count($pathParts) !== 3) {
                goto search;
            }

            //Remove the empty string in the beginning
            [, $type, $number] = $pathParts;

            switch ($type) {
                case 'artikel':
                    $q = $number;
                    break;
                case 'koje':
                    return $this->forward('index', 'OstExhibitAreaListing', 'Frontend', ['koje' => $number]);
            }
        } else {
            $re = '/^\d[0]+(\d+)(\d{5})\d$/m';

            preg_match_all($re, $q, $matches, PREG_SET_ORDER);
            if (count($matches) > 0) {
                [, $number, $variantNumber] = $matches[0];

                $variantNumber = ltrim($variantNumber, '0');
                if (trim($variantNumber, '0') !== '') {
                    $number .= '-' . $variantNumber;
                }

                $q = $number;
            }
        }

        search:
        $this->forward('index', 'search', 'frontend', ['sSearch' => $q]);
    }

}
