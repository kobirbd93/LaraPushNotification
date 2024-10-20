<?php

namespace Kobir\PushNotification;

abstract class PushNotificationService
{
    /**
     * Server Url for push notification server
     *
     * @var string
     */
    protected $url = '';

    /**
     * Server Url for add device token into topic
     *
     * @var string
     */
    protected $topicAddUrl = '';

    /**
     * Server Url for remove device token from topic
     *
     * @var string
     */
    protected $topicRemoveUrl = '';

    /**
     * Server Url for get specific device_token attached into how many topic
     *
     * @var string
     */
    protected $topicInfoUrl = '';

    /**
     * Generated oauth token for server request
     *
     * @var string
     */
    protected $oauthToken = '';

    /**
     * Config details
     * By default priority is set to high and dry_run to false
     *
     * @var array
     */
    protected $config = [];

    /**
     *
     * Store all Failed device tokens
     *
     * @var array
     */
    protected $failedDevicesTokens = [];

    /**
     * Push Server Response
     * @var object
     */
    protected $feedback;

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $topicAddUrl
     */
    public function setTopicAddUrl($topicAddUrl)
    {
        $this->topicAddUrl = $topicAddUrl;
    }

    /**
     * @param string $topicRemoveUrl
     */
    public function setTopicRemoveUrl($topicRemoveUrl)
    {
        $this->topicRemoveUrl = $topicRemoveUrl;
    }

    /**
     * @param string $topicInfoUrl
     */
    public function setTopicInfoUrl($topicInfoUrl)
    {
        $this->topicInfoUrl = $topicInfoUrl;
    }

    /**
     * @param object $feedback
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * set failed device token
     * @param array $devicesTokens
     */
    public function setFailedDeviceToken(array $devicesTokens)
    {
        $this->failedDevicesTokens = $devicesTokens;
    }

    /**
     * Update the values by key on config array from the passed array. If any key doesn't exist, it's added.
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = array_replace($this->config, $config);
    }

    /**
     * Initialize the configuration for the chosen push service // gcm,etc..
     *
     * @param $service
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function initializeConfig($service)
    {
        if (
            function_exists('config_path') &&
            file_exists(config_path('larapushnotification.php')) &&
            function_exists('app')
        ) {
            $configuration = app('config')->get('larapushnotification');
        } else {
            $configuration = include __DIR__ . '/Config/config.php';
        }

        if (!array_key_exists($service, $configuration)) {
            throw new \Exception("Service '$service' missed in config/larapushnotification.php");
        }
        return $configuration[$service];
    }

    /**
     * Return property if exit otherwise null.
     *
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        return property_exists($this, $property) ? $this->$property : null;
    }
}
