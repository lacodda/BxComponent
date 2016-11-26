<?php
    /**
     * @link      http://lacodda.com
     * @copyright Copyright © 2016 Kirill Lahtachev
     * @license   MIT
     */

    namespace Lacodda\BxComponent;

    use Plasticbrain\FlashMessages\FlashMessages;

    /**
     * Abstraction basis component
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    abstract class Basis
        extends \CBitrixComponent
    {
        use Traits\Common;

        /**
         * @var bool Auto executing methods of prolog / epilog in the traits
         */
        public $traitsAutoExecute = true;

        /**
         * @var array Used traits
         */
        private $usedTraits;

        /**
         * @var
         */
        public $msg;

        /**
         * Executing methods prolog, getResult and epilog included traits
         *
         * @param string $type prolog, getResult or epilog
         */
        private function executeTraits ($type)
        {
            if (empty($this->usedTraits))
            {
                return;
            }

            switch ($type)
            {
                case 'prolog':
                    $type = 'Prolog';
                    break;

                case 'main':
                    $type = 'Main';
                    break;

                default:
                    $type = 'Epilog';
                    break;
            }

            foreach ($this->usedTraits as $trait => $name)
            {
                $method = 'execute' . $type . $name;

                if (method_exists ($trait, $method))
                {
                    $this->$method();
                }
            }
        }

        /**
         * Set to $this->usedTraits included traits
         */
        private function readUsedTraits ()
        {
            if ($this->traitsAutoExecute)
            {
                $calledClass = get_called_class ();

                $classes = class_parents ($calledClass);
                $classes[] = $calledClass;

                foreach ($classes as $class)
                {
                    foreach (class_uses ($class) as $trait)
                    {
                        $this->usedTraits[$trait] = bx_basename ($trait);
                    }
                }
            }
        }

        /**
         *
         */
        final protected function executeBasis ()
        {
            $this->msg = new FlashMessages();
            $this->readUsedTraits ();
            $this->includeModules ();
            $this->configurate ();
            $this->checkAutomaticParams ();
            $this->checkParams ();
            $this->startAjax ();
            $this->executeTraits ('prolog');
            $this->executeProlog ();

            if ($this->startCache ())
            {
                $this->executeMain ();
                $this->executeTraits ('main');

                if ($this->cacheTemplate)
                {
                    $this->returnDatas ();
                }

                $this->writeCache ();
            }

            if (!$this->cacheTemplate)
            {
                $this->returnDatas ();
            }

            $this->executeTraits ('epilog');
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
