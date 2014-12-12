<?php
class Zaboy_Test_Abstract extends PHPUnit_Framework_TestCase 
{
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
}
