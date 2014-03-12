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

use Guzzle\Common\AbstractHasDispatcher;
use Guzzle\Common\Event;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reads from and writes to local fixture files
 */
class FixturePlugin extends AbstractHasDispatcher implements EventSubscriberInterface
{
    /**
     * The directory in which the fixtures will be stored and read from
     *
     * @var string
     */
    private $fixturesDir;

    /**
     * Initializes the plugin
     *
     * @param string $fixturesDir
     */
    function __construct($fixturesDir)
    {
        if (!file_exists($fixturesDir)) {
            mkdir($fixturesDir, 0777, true);
        }

        $this->fixturesDir = $fixturesDir;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Use a number slightly lower than the MockPlugin
        return array(
            'request.before_send' => array('onRequestBeforeSend', -1000),
            'request.success' => array('onRequestAfterSuccess', -1000),
        );
    }

    /**
     * Checks if a local fixture exists and uses it to set a request's response
     *
     * @param Event $event
     */
    public function onRequestBeforeSend(Event $event)
    {
        /** @var RequestInterface $request */
        $request = $event['request'];

        $filename = $this->fixturesDir . DIRECTORY_SEPARATOR . md5($request->getUrl());

        if($response = $this->getFixture($filename)) {
            $request->setResponse($response);
        }
    }

    /**
     * Stores the response into a file after a successfully sent request
     *
     * @param Event $event
     */
    public function onRequestAfterSuccess(Event $event)
    {
        /** @var RequestInterface $request */
        $request = $event['request'];
        /** @var Response $response */
        $response = $event['response'];

        $filename = $this->fixturesDir . DIRECTORY_SEPARATOR . md5($request->getUrl());

        if (!$this->getFixture($filename)) {
            file_put_contents($filename, $response->getMessage());
        }
    }

    /**
     * Get a mock response from a fixture, if available
     *
     * @param string $filename
     * @return Response|null
     */
    private function getFixture($filename)
    {
        if (!file_exists($filename)) {
            return null;
        }

        return Response::fromMessage(file_get_contents($filename));
    }
}
