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
            'request.before_send' => array('onRequestBeforeSend', -998),
            'request.complete' => array('onRequestAfterComplete', -998),
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

        if($response = $this->getResponseFromFixture($request->getUrl())) {
            $request->setResponse($response);
            $event->stopPropagation();
        }
    }

    /**
     * Stores the response into a file after a successfully sent request
     *
     * @param Event $event
     */
    public function onRequestAfterComplete(Event $event)
    {
        /** @var RequestInterface $request */
        $request = $event['request'];
        /** @var Response $response */
        $response = $event['response'];

        $this->storeResponseToFixture($request->getUrl(), $response);

        // We have what we want and don't need another exception
        // Remove default error listener, so that no exception will be thrown
        $request->getEventDispatcher()->removeListener('request.error', array('Guzzle\Http\Message\Request', 'onRequestError'));
    }


    /**
     * Stores the given response to a file, if it not already exists
     *
     * @param string $url
     * @param Response $response
     * @return bool
     */
    protected function storeResponseToFixture($url, Response $response)
    {
        $filename = $this->fixturesDir . DIRECTORY_SEPARATOR . md5($url);

        if (!file_exists($filename)) {
            return (bool) file_put_contents($filename, $response->getMessage());
        }

        return true;
    }

    /**
     * If a fixture file exists for the given URL, generates a response from
     * its contents
     *
     * @param string $url
     * @return Response|bool|null
     */
    protected function getResponseFromFixture($url)
    {
        $filename = $this->fixturesDir . DIRECTORY_SEPARATOR . md5($url);

        if (file_exists($filename)) {
            return Response::fromMessage(file_get_contents($filename));
        }

        return null;
    }
}
