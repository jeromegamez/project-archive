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

    public function testGetFreshFixture()
    {
        $fixturesDir = $this->workspace . DIRECTORY_SEPARATOR . '_fixtures' . __FUNCTION__;

        $url = 'http://www.example.com';
        $md5Url = md5($url);

        $originalFixture = __DIR__ . DIRECTORY_SEPARATOR . '_fixtures' . DIRECTORY_SEPARATOR . $md5Url;
        $createdFixture = $fixturesDir . DIRECTORY_SEPARATOR . $md5Url;

        $mockPlugin = new MockPlugin(array($originalFixture));
        $fixturePlugin = new FixturePlugin($fixturesDir);

        $client = new HttpClient();
        $client->addSubscriber($mockPlugin);
        $client->addSubscriber($fixturePlugin);

        $request = $client->get($url);
        $response = $request->send();

        $this->assertContainsOnly($request, $mockPlugin->getReceivedRequests());

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $response);
        $this->assertTrue(file_exists($createdFixture));
        $this->assertEquals(file_get_contents($originalFixture), file_get_contents($createdFixture));
    }

    public function testGetFixtureFromFilesystem()
    {
        $fixturesDir = $this->workspace . DIRECTORY_SEPARATOR . '_fixtures' . __FUNCTION__;

        $url = 'http://www.example.com';
        $md5Url = md5($url);

        $originalFixture = __DIR__ . DIRECTORY_SEPARATOR . '_fixtures' . DIRECTORY_SEPARATOR . $md5Url;
        $createdFixture = $fixturesDir . DIRECTORY_SEPARATOR . $md5Url;

        // Copy the prepared fixture to the test fixtures dir
        $originalFileMTime = strtotime('2000-01-01 00:00:00');
        mkdir($fixturesDir, 0777);
        copy($originalFixture, $createdFixture);
        touch($createdFixture, $originalFileMTime, $originalFileMTime);

        $this->assertEquals($originalFileMTime, filemtime($createdFixture));

        $fixturePlugin = new FixturePlugin($fixturesDir);

        $client = new HttpClient();
        $client->addSubscriber($fixturePlugin);

        $client->get($url)->send();

        $this->assertEquals($originalFileMTime, filemtime($createdFixture));

    }
}
 