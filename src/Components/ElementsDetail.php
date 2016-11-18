<?php
    /**
     * @link      http://lacodda.com
     * @copyright Copyright © 2016 Kirill Lahtachev
     * @license   MIT
     */

    namespace Lacodda\BxComponent\Components;

    use Lacodda\BxComponent\Basis;

    /**
     * Show page with element of the info-block
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class ElementsDetail
        extends Basis
    {
        use \Lacodda\BxComponent\Traits\Elements;

        protected $needModules = array ('iblock');

        protected $checkParams = array (
            'IBLOCK_TYPE'  => array ('type' => 'string'),
            'IBLOCK_ID'    => array ('type' => 'int'),
            'ELEMENT_ID'   => array ('type' => 'int', 'error' => false),
            'ELEMENT_CODE' => array ('type' => 'string', 'error' => false),
        );

        protected function executeProlog ()
        {
            if (!$this->arParams['ELEMENT_ID'] && !$this->arParams['ELEMENT_CODE'])
            {
                $this->return404 (true);
            }
        }

        protected function executeMain ()
        {
            $rsElement = \CIBlockElement::GetList (
                array (),
                $this->getParamsFilters (),
                false,
                false,
                $this->getParamsSelected ()
            );

            $processingMethod = $this->getProcessingMethod ();

            if ($element = $rsElement->$processingMethod())
            {
                if ($arElement = $this->processingElementsResult ($element))
                {
                    $this->arResult = array_merge ($this->arResult, $arElement);
                }

                $this->setResultCacheKeys (
                    array (
                        'ID',
                        'IBLOCK_ID',
                        'CODE',
                        'NAME',
                        'IBLOCK_SECTION_ID',
                        'IBLOCK',
                        'LIST_PAGE_URL',
                        'SECTION_URL',
                        'SECTION',
                    )
                );
            } elseif ($this->arParams['SET_404'] === 'Y')
            {
                $this->return404 ();
            }
        }
    }