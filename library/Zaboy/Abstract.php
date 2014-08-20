<?php
/**
 * Zaboy_Abstract
 * 
 * @category   Zaboy
 * @package    Zaboy
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Zaboy_Abstract. It has only {@see setOptions()} and geters.
 * 
 * @todo Merged Options
 * @category   Zaboy
 * @package    Zaboy
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
abstract class Zaboy_Abstract
{
    /**
     * metadata and attributes
     * see config.ini for example
     * @var array
     */
    protected $_attribs = array();
    
    /**
    * 
    * param array
    * @return void
    */  
    public function __construct( array $options=array() ) 
    {        
        if (!empty($options))
        {
            $this->setOptions($options);
        }    
        return;
    }
   
     /**
      * Call setters for elements $options if exist and rest copy to {@see Zaboy_Abstract::_attribs}
      * 
      * May be two cases for property $options['oneProperty'] = value
      * If method setOneProperty is exist - it will be call, else $this->_attribs['oneProperty'] = value
      *
      * @param  array $options
      * @return Zaboy_Abstract
      */
    public function setOptions(array $options)
    {
        if (isset($options['attribs'])) {
            $this->addAttribs($options['attribs']);
            unset($options['attribs']);
        }
        
        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);

            $method = 'set' . $normalized;
            $isMethodExists=method_exists($this, $method);
            $isCorectCall = !($normalized == 'Options');
            if ($isMethodExists && $isCorectCall) {
                $this->$method($value);
            } else {
                $this->setAttrib($key, $value);
            }
        }
        return $this;
    }
    
    /**
     * Add multiple attributes at once
     *
     * @param  array $attribs
     * @return Zaboy_Abstract
     */
    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }
    
     /**
     * Set multiple attributes at once
     * Overwrites any previously set attributes.
     *
     * @param  array $attribs
     * @return Zaboy_Abstract
     */
    public function setAttribs(array $attribs)
    {
        $this->_attribs = array();
        return $this->addAttribs($attribs);
    }

    /**
     * Retrieve all attributes/metadata
     *
     * @return array
     */
    public function getAttribs()
    { 
        return $this->_attribs;
    }

    /**
     * Set attribute
     *
     * @param  string $key
     * @param  mixed $value
     * @return Zaboy_Abstract
     */
    public function setAttrib($key, $value)
    {
        $key = (string) $key;
        $this->_attribs[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a single attribute
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttrib($key)
    {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    /**
     * Remove attribute
     *
     * @param  string $key
     * @return bool
     */
    public function removeAttrib($key)
    {
        if (isset($this->_attribs[$key])) {
            unset($this->_attribs[$key]);
            return true;
        }
        return false;
    }
 
}