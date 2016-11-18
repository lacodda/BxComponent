<?php
    /**
     * @link      http://lacodda.com
     * @copyright Copyright © 2016 Kirill Lahtachev
     * @license   MIT
     */

    namespace Lacodda\BxComponent\Components;

    use Lacodda\BxComponent\BasisRouter;

    /**
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class ElementsRouter
        extends BasisRouter
    {
        protected $defaultSefPage = 'index';

        protected function setSefDefaultParams ()
        {
            $this->defaultUrlTemplates404 = [
                'index'   => '',
                'section' => '#SECTION_ID#/',
                'detail'  => '#SECTION_ID#/#ELEMENT_ID#/',
            ];

            $this->componentVariables = [
                'SECTION_ID',
                'SECTION_CODE',
                'ELEMENT_ID',
                'ELEMENT_CODE',
            ];
        }
    }