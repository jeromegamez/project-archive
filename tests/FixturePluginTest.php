<?php
/**
 * This file is part of the Guzzle Fixture plugin.
 *
 * (c) Jérôme Gamez <jerome@gamez.name>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Guzzle\Plugin\Fixture;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Plugin\Mock\MockPlugin;

/**
 * Tests for the FixturePlugin Class
 */
class FixturePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Where to write the test files to
     *
     * @var string
     */
    protected $workspace;

    /**
     * The directory in which the "real" fixtures for the tests reside
     *
     * @var string
     */
    protected $fixturesDir;

    /**
     * If set to true, all generated fixtures will also be stored to tests/_fixtures
     *
     * This is a manual setting while developing on the plugin, to generate new fixtures
     *
     * @var bool
     */
    protected $backupFixtures = false;

    /**
     * Constructs the test case with the given name.
     *
     * If {@see $backupFixtures} is set to true, makes sure that the _fixtures dir is present
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if ($this->backupFixtures) {
            $this->fixturesDir = __DIR__ . DIRECTORY_SEPARATOR . '_fixtures';
            if (!file_exists($this->fixturesDir)) {
                mkdir($this->fixturesDir, 0777, true);
            }
        }
    }

    /**
     * Workspace preparation before each test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    /**
     * Workspace cleanup after each test
     */
    public function tearDown()
    {
        $this->clean($this->workspace);

        parent::tearDown();
    }

    /**
     * Recursively deletes a directory/file
     *
     * @param string $file
     * @see https://github.com/symfony/Filesystem/blob/master/Tests/FilesystemTestCase.php Copied from the symfony filesystem component
     */
    protected function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }

            rmdir($file);
        } else {
            unlink($file);
        }
    }

    public function testConstructorCreatesFixturesDir()
    {
        $fixturesDir = $this->workspace . DIRECTORY_SEPARATOR . '_fixtures' . __FUNCTION__;

        $this->assertFalse(file_exists($fixturesDir));

        new FixturePlugin($fixturesDir);

        $this->assertTrue(file_exists($fixturesDir));
        $this->assertTrue(is_dir($fixturesDir));
        $this->assertTrue(is_writable($fixturesDir));
    }

    public function testConstructorDoesNotNeedToCreateFixturesDir()
    {
        $fixturesDir = $this->workspace . DIRECTORY_SEPARATOR . '_fixtures' . __FUNCTION__;
        mkdir($fixturesDir, 0777);
        new FixturePlugin($fixturesDir);

        $this->assertTrue(file_exists($fixturesDir));
    }

    /**
     * @param string $url
     * @dataProvider urlProvider
     */
    public function testGetFixture($url)
    {
        $fixturesDir = $this->workspace . DIRECTORY_SEPARATOR . '_fixtures' . __FUNCTION__;

        $client = new HttpClient();

        $fixturePlugin = new FixturePlugin($fixturesDir);
        $client->addSubscriber($fixturePlugin);

        $md5Url = md5($url);

        $originalFixture = __DIR__ . DIRECTORY_SEPARATOR . '_fixtures' . DIRECTORY_SEPARATOR . $md5Url;
        $expectedFixture = $fixturesDir . DIRECTORY_SEPARATOR . $md5Url;

        // The original fixture might not be available when developing on the plugin and new fixtures
        // need to be created
        if (file_exists($originalFixture)) {
            $mockPlugin = new MockPlugin(array($originalFixture));
            $client->addSubscriber($mockPlugin);
        }

        $this->assertFileNotExists($expectedFixture);
        $request = $client->get($url);
        $response = $request->send();
        $this->assertFileExists($expectedFixture);

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $response);
        $this->assertTrue(file_exists($expectedFixture));

        // The original fixture might not be available when developing on the plugin and new fixtures
        // need to be created
        if(file_exists($originalFixture)) {
            $this->assertEquals(file_get_contents($originalFixture), file_get_contents($expectedFixture));
        }

        if($this->backupFixtures) {
            file_put_contents($this->fixturesDir . DIRECTORY_SEPARATOR . $md5Url, file_get_contents($expectedFixture));
        }

        // Get fixture a second time, now should get result from fixture directly
        $request->send();
    }

    public function testCreateAndReadFromFixtureThatNotAlreadyExisted()
    {

    }

    public function urlProvider()
    {
        /**
         * @see http://httpstat.us/
         */
        $statusCodes = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
        );

        $return = array();
        foreach(array_keys($statusCodes) as $statusCode) {
            $return[] = array('http://httpstat.us/' . $statusCode);
        }

        return $return;
    }
}
 