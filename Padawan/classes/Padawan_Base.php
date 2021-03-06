<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <florian.anderiasch at mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
class Padawan {
    // the config of all rules
    private $config;
    // xml data
    private $xml;
    // a list of messages
    private $stack;
    
    private $element;
    
    //const STRIP_XMLNS = ' xmlns="http://www.phpcompiler.org/phc-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
    const STRIP_XMLNS = ' xmlns="http://www.phpcompiler.org/phc-1.0"';
    
    const TEST_COUNT = 1;
    const TEST_MATCH = 2;
    const TEST_STEP  = 4;
    
    const P_ERROR    = 1;
    const P_WARNING  = 2;
    const P_NOTICE   = 4;
    
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config) {
        $this->config = $config;
        $this->stack = array();
    }
    
    /**
     * Loads an XML file
     * 
     * @param string $filename
     * @return bool
     */
    public function loadFile($filename) {
        if (is_file($filename) && is_readable($filename)) {
            $xml = file_get_contents($filename);
            if (false === $xml) {
                return false;
            }
            
            return $this->setXml($xml);
        } else {
            $this->stack[] = array('cannot read file '.$filename,self::P_ERROR);
            return false;
        }
    }
    
    /**
     * Evaluates an XPath expression
     *
     * @param string $query
     * @param int $test
     * @param mixed $expected
     * @return bool
     */
    public function xpath($query, $test = null, $expected = null) {
        if (1 > strlen($query)) {
            return false;
        } else {
            return $this->query(array('query' => $query, 'test' => $test, 'expected' => $expected));
        }
    }
    
    /**
     * Executes a named test
     *
     * @param string $name
     * @param bool $details
     * @return bool
     */
    public function test($name, $details = false) {
        if (!isset($this->config[$name])) {
            return false;
        } else {
            return $this->query($this->config[$name], $details);
        }
    }
    
    /**
     * Executes a query
     *
     * @param array $query
     * @param bool $details
     * @return bool
     */
    private function query($query, $details = false) {
        $q_file = '/attrs/attr[@key="phc.filename"]/string';
        $q_line = '/attrs/attr[@key="phc.line_number"]/integer';
        
        if (self::TEST_COUNT == $query['test']) {
            $result = $this->element->xpath($query['query']);
            if ($result !== false && count($result) == $query['expected']) {
                if ($details) {
                    $file = false;
                    $line = false;
                    // loop upwards (with ..) to find the nearest match of a line number
                    $i = 0;
                    $fixpath = '';
                    while ($file == false) {
                        $q = $query['query'].$fixpath.$q_file;
                        $file = $this->element->xpath($q);
                        $fixpath .= '/..';
                        $i++;
                        if ($i > 4) break;
                    }
                    $i = 0;
                    $fixpath = '';
                    while ($line == false) {
                        $q = $query['query'].$fixpath.$q_line;
                        $line = $this->element->xpath($q);
                        $fixpath .= '/..';
                        $i++;
                        if ($i > 4) break;
                    }
                    return array((string)$file[0][0], (string) $line[0][0]);
                }
                return true;
            }
            return false;
        } elseif (self::TEST_STEP == $query['test'] ) {
            $base = array_shift($query['query']);
            $tmp = $this->element->xpath($base['query']);
            // if the first step fails, we can't match
            if (false === $tmp) {
                return false;
            }
            $return = true;
            
            foreach ($query['query'] as $key => $val) {
                $res = null;
                $q = sprintf($val['query'], $tmp[0][0]);
                $res = $this->element->xpath($q);
                $return = $return && empty($res);
                
                $ql = $q.$q_line;
                $line = $this->element->xpath($ql);
                if (false === $line) {
                    $ql = $base['query'].'/../..'.$q_line;
                    $line = $this->element->xpath($ql);
                }
                
                $qf = $q.$q_file;
                $file = $this->element->xpath($qf);
                if (false === $file) {
                    $qf = $base['query'].'/../..'.$q_file;
                    $file = $this->element->xpath($qf);
                }
                $retFile = (string)$file[0];
                $retLine = (string)$line[0];
                if (1 > strlen($retFile)){
                    $retFile = $file[0][0];
                }
                if (1 > strlen($retLine)){
                    $retLine = $line[0][0];
                }
                $result[$key] = array($retFile, $retLine);
            }
            
            if ($return != $query['expected']) {
                return false;
            } else {
                if ($details) {
                    return $result;
                } else {
                    return true;
                }
            }
        }
    }
    
    /**
     * Returns the description of a test case
     *
     * @param string $testName
     * @return string
     */
    public function getHint($testName) {
        return 'Found '.$this->config[$testName]['hint'];
    }
    
    /**
     * Returns the config
     *
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }
    
    public function setXml($data = "") {
        $data = str_replace(self::STRIP_XMLNS, '', $data);
        if (1 > strlen($data)) {
            return false;
        }
        $this->xml = $data;
        try {
            $this->element = new SimpleXMLElement($this->xml);
            $this->element->registerxpathnamespace('AST', 'http://www.phpcompiler.org/phc-1.1');
        } catch (Exception $e) {
            $this->stack[] = array($e->__toString(), self::P_ERROR);
            return false;
        }
        return true;
    }
}
?>
