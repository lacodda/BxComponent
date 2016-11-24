<?php

    namespace Lacodda\BxComponent;

    /**
     * Class Url
     *
     * @package Lacodda\BxComponent
     */
    class Url
    {
        /**
         * @var string Value of the parameter `SEF_FOLDER`
         */
        protected $sefFolder;

        /**
         * @var
         */
        protected $url;

        /**
         * Url constructor.
         *
         * @param $url
         */
        public function __construct ($sefFolder)
        {
            $sefFolder = explode ('/', $sefFolder);

            $sefFolder = array_values (array_diff ($sefFolder, ['']));

            $this->sefFolder = $sefFolder;
        }

        /**
         * @param $model
         *
         * @return $this
         */
        public function url ($model)
        {
            $this->url = implode ('/', array_merge ($this->sefFolder, [$model]));

            return $this;
        }

        /**
         * @param $id
         *
         * @return $this
         */
        public function id ($id)
        {
            $this->url = implode ('/', [$this->url, $id]);

            return $this;
        }

        /**
         * @return $this
         */
        public function edit ()
        {
            $this->url = implode ('/', [$this->url, 'edit']);

            return $this;
        }

        /**
         * @return string
         */
        public function get ()
        {
            return sprintf ('/%s/', $this->url);
        }

    }