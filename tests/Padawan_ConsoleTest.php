<?php
require_once '../classes/Padawan_Console.php';
require_once '../classes/creation.php';
require_once '../classes/profiler.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

/**
 * Padawan_Console test case.
 */
class Padawan_ConsoleTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var Padawan_Console
     */
    private $Padawan_Console;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Padawan_Console = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {}

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecHelp ()
    {
        $argv = array('./padawan.php', '-h');
        $fix = array('code' => 0, 'value' => 'showHelpMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
        
        $argv = array('./padawan.php', '--help');
        $fix = array('code' => 0, 'value' => 'showHelpMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecVersion ()
    {
        $config = array();
        $argv = array('./padawan.php', '-V');
        $fix = array('code' => 0, 'value' => 'showVersionMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
        
        $argv = array('./padawan.php', '--version');
        $fix = array('code' => 0, 'value' => 'showVersionMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecTests ()
    {
        $argv = array('./padawan.php', '-l');
        $fix = array('code' => 0, 'value' => 'showTestsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecTags ()
    {
        $argv = array('./padawan.php', '-t');
        $fix = array('code' => 0, 'value' => 'showTagsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecCreate ()
    {
        $argv = array('./padawan.php', '-c');
        $fix = array('code' => 0, 'value' => 'doCreateMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

/**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecParse ()
    {
        $argv = array('./padawan.php', '-p');
        $fix = array('code' => 0, 'value' => 'doParseMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecMissing ()
    {
        $argv = array('./padawan.php');
        $fix = array('code' => 2, 'value' => 'showMissingParamsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecWrong ()
    {
        $argv = array('./padawan.php', '--very-wrong');
        $fix = array('code' => 1, 'value' => 'showWrongParamsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->doCreate()
     */
    public function testDoCreate ()
    {
        // put info into tempdir
        $tmpname = serialize($_SERVER).time();
        $tmpname = "padawan_test_".md5($tmpname);
        $sys_tmp_dir = sys_get_temp_dir();
        $path_base= $sys_tmp_dir.DIRECTORY_SEPARATOR.$tmpname;
        
        $path_in  = $path_base.DIRECTORY_SEPARATOR."in".DIRECTORY_SEPARATOR;
        $path_out = $path_base.DIRECTORY_SEPARATOR."out".DIRECTORY_SEPARATOR;
        
        mkdir($path_base);
        mkdir($path_in);
        mkdir($path_out);
        
        $data_in_1 = '<?php
$abcdefghijklmnop = 3.14;
?>';
        $data_in_2 = '<?php
$abcdefghijklmno = 3.14;
?>';
        file_put_contents($path_in."LongVariable.php", $data_in_1);
        file_put_contents($path_in."LongVariable_ok.php", $data_in_2);
        
        // create object
        $argv = array('./padawan.php', '-c', $path_in, $path_out, '--skip-dot');
        $config = array();
        $config['phc'] = trim(`which phc 2> /dev/null`);
        $config['skip_dot']    = false;
        $config['skip_xml']    = false;
        $config['extensions']  = array('php', 'php3', 'php4', 'php5', 'phtml');
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->doCreate();
        
        // verify file contents
        
        $read_1 = file_get_contents($path_out."LongVariable.xml");
        $read_2 = file_get_contents($path_out."LongVariable_ok.xml");
        
        unlink($path_out."LongVariable.xml");
        unlink($path_out."LongVariable_ok.xml");
        rmdir($path_out);
        
        unlink($path_in."LongVariable.php");
        unlink($path_in."LongVariable_ok.php");
        rmdir($path_in);
        
        rmdir($path_base);
        
        $pat = '((.*)<attr key="phc.line_number"><integer>2</integer></attr>(.*)'.
        '<attr key="phc.unparser.source_rep"><string>3.14</string></attr>(.*))s';
        
        $pat_output = "(creating XML for 'LongVariable.php'...(.*)done(.*)".
                        "creating XML for 'LongVariable_ok.php'...(.*)done(.*)".
                        "Padawan: finished creating 2 XML files in ([0-9\.]+) sec(.*)".
                        "Padawan: total runtime: ([0-9\.]+) sec(.*))s";
        
        $this->assertRegExp($pat, $read_1);
        $this->assertRegExp($pat, $read_2);
        $this->expectOutputRegex($pat_output);
    }

    /**
     * Tests Padawan_Console->doParse()
     */
    public function testDoParse ()
    {
        // TODO Auto-generated Padawan_ConsoleTest->testDoParse()
        $this->markTestIncomplete("doParse test not implemented");
        $this->Padawan_Console->doParse(/* parameters */);
    }

    /**
     * Tests Padawan_Console->printOutput()
     */
    public function testPrintOutput ()
    {
        // TODO Auto-generated Padawan_ConsoleTest->testPrintOutput()
        $this->markTestIncomplete("testPrintOutput test not implemented");
        $argv = array('./padawan.php');
        $this->Padawan_Console = new Padawan_Console($argv);
        
        $test = array('code' => 0, 'value' => "foo");
        
        $dump = $this->Padawan_Console->printOutput($test);
        
        
        $this->expectOutputString($test['value']);
    }
    
    /**
     * Tests Padawan_Console->showMissingParams()
     */
    public function testShowMissingParams ()
    {
        $argv = array('./padawan.php');
        $this->Padawan_Console = new Padawan_Console($argv);
        
        $ret = $this->Padawan_Console->showMissingParams();
        
        $pat = sprintf('(%s: missing arguments(.*))',  $argv[0]); 
        
        $this->assertEquals(2, $ret['code']);
        $this->assertRegExp($pat, $ret['value']);
    }
    
/**
     * Tests Padawan_Console->showWrongParams()
     */
    public function testShowWrongParams ()
    {
        $argv = array('./padawan.php', '--very-wrong');
        $this->Padawan_Console = new Padawan_Console($argv);
        $ret = $this->Padawan_Console->showWrongParams();
        
        $pat = sprintf('(%s: unknown arguments(.*))',  $argv[0]); 
        
        $this->assertEquals(1, $ret['code']);
        $this->assertRegExp($pat, $ret['value']);
    }

    /**
     * Tests Padawan_Console->showHelp()
     */
    public function testShowHelpLong ()
    {
        $argv = array('./padawan.php');
        $pat = sprintf('((.*)Usage: %s \[ -l \] \[ -t \] \[--version \](.*))',  $argv[0]);
        $this->Padawan_Console = new Padawan_Console($argv);
        $ret = $this->Padawan_Console->showHelp();
        
        $this->assertEquals(0, $ret['code']);
        $this->assertRegExp($pat, $ret['value']);
        
    }

    /**
     * Tests Padawan_Console->showVersion()
     */
    public function testShowVersion ()
    {
        $argv = array('./padawan.php');
        $config = array('version' => 23);
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->showVersion();
        
        $pat = sprintf('(PADAWAN %s - PHP AST-based Detection of Antipatterns,' 
                . ' Workarounds And general Nuisances)',
                $config['version']);
        
        $this->assertRegExp($pat, $ret['value']);
    }

    /**
     * Tests Padawan_Console->showTests()
     */
    public function testShowTests ()
    {
        $argv = array('./padawan.php');
        $config['patterns'] = array(
            'TestFoo' => array(
                'hint' => 'a Foo test',
            ),
            'TestBar' => array(
                'hint' => 'a Bar test',
            ),
        );
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->showTests();
        
        $pat = '((.*)TestFoo(.*)- a Foo test(.*)TestBar(.*)- a Bar test(.*))s';
        
        $this->assertRegExp($pat, $ret['value']);
        
    }

    /**
     * Tests Padawan_Console->showTags()
     */
    public function testShowTags ()
    {
        $argv = array('./padawan.php');
        $config['patterns'] = array(
            'TestFoo' => array(
                'tags' => array('foo', 'baz'),
            ),
            'TestBar' => array(
                'tags' => array('bar', 'baz'),
            ),
        );
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->showTags();
        
        $pat = '((.*)available tags:(.*)bar baz foo(.*))s';
        
        $this->assertRegExp($pat, $ret['value']);
        
    }
}

class Padawan_Console_Mock extends Padawan_Console
{
    function showHelp()
    {
        return array('code' => 0, 'value' => "showHelpMock");
    }

    function showVersion()
    {
        return array('code' => 0, 'value' => "showVersionMock");
    }

    function showTests()
    {
        return array('code' => 0, 'value' => "showTestsMock");
    }

    function showTags()
    {
        return array('code' => 0, 'value' => "showTagsMock");
    }

    function doCreate()
    {
        return array('code' => 0, 'value' => "doCreateMock");
    }

    function doParse()
    {
        return array('code' => 0, 'value' => "doParseMock");
    }

    function showMissingParams()
    {
        return array('code' => 2, 'value' => "showMissingParamsMock");
    }

    function showWrongParams()
    {
        return array('code' => 1, 'value' => "showWrongParamsMock");
    }
}
