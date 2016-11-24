<?php
    /**
     * @link      http://lacodda.com
     * @copyright Copyright © 2016 Kirill Lahtachev
     * @license   MIT
     */

    namespace Lacodda\BxComponent;

    use Bitrix\Main;

    /**
     * Abstraction basis router component
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    abstract class BasisRouter
        extends \CBitrixComponent
    {
        use Traits\Common;

        /**
         * @var array Paths of templates default
         */
        protected $defaultUrlTemplates404;

        /**
         * @var array Variables template paths
         */
        protected $componentVariables;

        /**
         * @var string Template page default
         */
        protected $defaultPage = 'list';

        /**
         * @var string Template page default for SEF mode
         */
        protected $defaultSefPage = 'list';

        /**
         * @var string Value of the parameter `SEF_FOLDER`
         */
        protected $sefFolder;

        /**
         * @var
         */
        protected $urlTemplates;

        /**
         * @var
         */
        protected $variables;

        /**
         * @var
         */
        protected $variableAliases;

        /**
         * @var
         */
        protected $models;

        /**
         * @var
         */
        protected $componentModels;

        /**
         * Set default parameters for SEF URL's
         */
        protected function setSefDefaultParams ()
        {
            $this->defaultUrlTemplates404 = [
                'list'   => '',
                'detail' => '#ELEMENT_ID#/',
            ];

            $this->componentVariables = ['ELEMENT_ID'];

            $this->componentModels = [''];
        }

        /**
         * Is search request
         *
         * @return bool
         */
        protected function isSearchRequest ()
        {
            if (strlen ($_GET['q']) > 0 && $this->templatePage !== 'detail')
            {
                return true;
            }

            return false;
        }

        /**
         * Set type of the page
         */
        protected function setPage ()
        {
            $urlTemplates = [];

            if ($this->arParams['SEF_MODE'] === 'Y')
            {
                $variables = [];

                $urlTemplates = \CComponentEngine::MakeComponentUrlTemplates (
                    $this->defaultUrlTemplates404,
                    $this->arParams['SEF_URL_TEMPLATES']
                );

                $variableAliases = \CComponentEngine::MakeComponentVariableAliases (
                    $this->defaultUrlTemplates404,
                    $this->arParams['VARIABLE_ALIASES']
                );

                $this->templatePage = \CComponentEngine::ParseComponentPath (
                    $this->arParams['SEF_FOLDER'],
                    $urlTemplates,
                    $variables
                );

                if (!$this->templatePage)
                {
                    if ($this->arParams['SET_404'] === 'Y')
                    {
                        $folder404 = str_replace ('\\', '/', $this->arParams['SEF_FOLDER']);

                        if ($folder404 != '/')
                        {
                            $folder404 = '/' . trim ($folder404, "/ \t\n\r\0\x0B") . "/";
                        }

                        if (substr ($folder404, - 1) == '/')
                        {
                            $folder404 .= 'index.php';
                        }

                        if ($folder404 != Main\Context::getCurrent ()->getRequest ()->getRequestedPage ())
                        {
                            $this->return404 ();
                        }
                    }

                    $this->templatePage = $this->defaultSefPage;
                }

                if ($this->isSearchRequest () && $this->arParams['USE_SEARCH'] === 'Y')
                {
                    $this->templatePage = 'search';
                }

                \CComponentEngine::InitComponentVariables (
                    $this->templatePage,
                    $this->componentVariables,
                    $variableAliases,
                    $variables
                );

                $models = $this->setModels ();
            } else
            {
                $this->templatePage = $this->defaultPage;
            }

            $this->sefFolder = $this->arParams['SEF_FOLDER'];
            $this->urlTemplates = $urlTemplates;
            $this->variables = $variables;
            $this->variableAliases = $variableAliases;
            $this->models = $models;
        }

        /**
         * @return array|bool
         */
        protected function setModels ()
        {
            $models = false;

            if (is_array ($this->componentModels))
            {
                $models = [];
                foreach ($this->componentModels as $module => $moduleModels)
                {
                    $module = trim ($module);

                    $models['module'][] = $module;

                    \CModule::IncludeModule ($module);

                    foreach ($moduleModels as $model => $className)
                    {
                        $model = strtolower (trim ($model));
                        if (class_exists ($className))
                        {
                            $models['model'][$model] = $className;
                        }
                    }
                }
            }

            return $models;
        }

        /**
         *
         */
        protected function executeMain ()
        {
            $this->arResult['FOLDER'] = $this->sefFolder;
            $this->arResult['URL_TEMPLATES'] = $this->urlTemplates;
            $this->arResult['VARIABLES'] = $this->variables;
            $this->arResult['ALIASES'] = $this->variableAliases;
            $this->arResult['MODELS'] = $this->models;
        }

        /**
         *
         */
        final public function executeBasis ()
        {
            $this->includeModules ();
            $this->configurate ();
            $this->checkAutomaticParams ();
            $this->checkParams ();
            $this->startAjax ();

            $this->setSefDefaultParams ();
            $this->setPage ();
            $this->executeProlog ();

            $this->executeMain ();
            $this->returnDatas ();

            $this->executeEpilog ();
            $this->executeFinal ();
            $this->stopAjax ();
        }

        /**
         *
         */
        public function executeComponent ()
        {
            try
            {
                $this->executeBasis ();
            } catch (\Exception $e)
            {
                $this->catchException ($e);
            }
        }
    }