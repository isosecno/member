<?php
class ISOSEC_Context
{
    private static $instance;
    private $params = [];
    private $DBObj;
    private $logObj;
    private $utilObj;
    private $htmlPartsObj;

    public $pluginDir;
    public $pluginUrl;
    public $htmlDir;
    public $imageUrl;
    public $uploadDir;
    public $cssUrl;
    public $jsUrl;


    public function __construct()
    {
        self::$instance = $this;
        // Make easy access - alternative to getParam for common used items
    }

    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

/**
* Hint to IDE
* @returns CHADM_Context
*/
    public static function getInstance()
    {
        return self::$instance;
    }

/**
* Hint to IDE
* @returns ITSW_html
*/
    public function getHtmlObj($path = null)
    {
        // Always make a new one
        $htmlDir = isset($path) ? $path : $this->htmlDir;
        return new ISOSEC_Html($htmlDir);
    }
/**
* Hint to IDE
* @returns CHADM_DB
*/
    public function getDBObj()
    {
        if (!isset($this->DBObj)) {
            $this->DBObj = new CHADM_DB();
        }
        return $this->DBObj;
    }
/**
* Hint to IDE
* @returns CHADM_Log
*/
    public function getLogObj()
    {
        if (!isset($this->logObj)) {
            $this->logObj = new CHADM_Log();
        }
        return $this->logObj;
    }
/**
* Hint to IDE
* @returns CHADM_Utilities
*/
    public function getUtilObj()
    {
        if (!isset($this->utilObj)) {
            $this->utilObj = new CHADM_Utils(/* self::$instance*/);
        }
        return $this->utilObj;
    }
/**
* Hint to IDE
* @returns CHADM_HtmlParts
*/
    public function getHtmlPartsObj()
    {
        if (!isset($this->htmlPartsObj)) {
            $this->htmlPartsObj = new CHADM_HtmlParts(self::$instance);
        }
        return $this->htmlPartsObj;
    }
}

