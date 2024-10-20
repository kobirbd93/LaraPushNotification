<?php

namespace Kobir\PushNotification;

class PushNotification
{

    /**
     * Push Service Provider
     * @var PushService
     */
    protected $service;

    /**
     * List of the available Push service providers
     *
     * @var PushService[]
     */
    protected $AvailableServices = [
        'fcm' => FcmHttpV1::class,
    ];

    /**
     * The default push service to use.
     *
     * @var string
     */
    private $defaultServiceName = 'fcm';

    /**
     * Devices' Token where send the notification
     *
     * @var array
     */
    protected $deviceTokens = [];

    /**
     * data to be sent.
     *
     * @var array
     */
    protected $notification = [
        'title' => 'Notification Alert',
        'body' => 'Notification alert description for you',
        'image' => null,
    ];

    /**
     * set others data to be sent.
     *
     * @var array
     */
    protected $data = [
        'id' => '0',
        'type' => 'DEFAULT',
        'sound' => 'default',
        'click_action' => 'NOTIFICATION_CLICK',
    ];

    /**
     * PushNotification constructor.
     * @param String / a service name of the services list.
     */
    public function __construct($service = null)
    {
        if (!array_key_exists($service, $this->AvailableServices)) {
            $service = $this->defaultServiceName;
        }

        $this->service = is_null($service) ? new $this->AvailableServices[$this->defaultServiceName]
        : new $this->AvailableServices[$service];
    }

    /**
     * Set the Push Service to be used.
     *
     * @param $serviceName
     * @return $this
     */
    public function setService($serviceName)
    {
        if (!array_key_exists($serviceName, $this->AvailableServices)) {
            $serviceName = $this->defaultServiceName;
        }

        $this->service = new $this->AvailableServices[$serviceName];

        return $this;
    }

    /**
     * Set the message of the notification.
     *
     * @param array $data
     * @return $this
     */
    public function setNotification(array $notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Set the other data of the notification.
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array/string $deviceTokens
     * @return $this
     */
    public function setDevicesToken($deviceTokens)
    {
        $this->deviceTokens = is_array($deviceTokens) ? $deviceTokens : array($deviceTokens);

        return $this;
    }

    /**
     * Set the Push service configuration
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->service->setConfig($config);

        return $this;
    }

    /**
     * Set the Push service url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->service->setUrl($url);

        return $this;
    }

    /**
     * Set the Push service project id
     *
     * @param string $projectId
     * @return $this
     */
    public function setProjectId($projectId)
    {
        $this->service->setProjectId($projectId);

        return $this;
    }

    /**
     * Set the jsonFile path for the notification
     * @param string $filePath
     */
    public function setJsonCredential($filePath)
    {
        $this->service->setJsonCredential($filePath);

        return $this;
    }

    /**
     * Set the topic add url
     *
     * @param string $topicAddUrl
     * @return $this
     */
    public function setTopicAddUrl($topicAddUrl)
    {
        $this->service->setTopicAddUrl($topicAddUrl);

        return $this;
    }

    /**
     * Set the topic removal url
     *
     * @param string $topicRemoveUrl
     * @return $this
     */
    public function setTopicRemoveUrl($topicRemoveUrl)
    {
        $this->service->setTopicRemoveUrl($topicRemoveUrl);

        return $this;
    }

    /**
     * Set the topic info url
     *
     * @param string $topicInfoUrl
     * @return $this
     */
    public function setTopicInfoUrl($topicInfoUrl)
    {
        $this->service->setTopicInfoUrl($topicInfoUrl);

        return $this;
    }

    /**
     * Send Push Notification
     *
     * @return $this
     */
    public function send()
    {
        $this->service->sendPushNotification($this->deviceTokens, $this->notification, $this->data);

        return $this;
    }

    /**
     * @param $topic
     * @param $isCondition
     * @return $this
     */
    public function sendByTopic($topic, $isCondition = false)
    {
        if ($this->service instanceof FcmHttpV1) {
            $this->service->sendPushNotificationByTopic($topic, $this->notification, $this->data, $isCondition);
        }

        return $this;
    }

    /**
     * Give the Push Notification Feedback after sending a notification.
     *
     * @return mixed
     */
    public function getFeedback()
    {
        return $this->service->feedback;
    }

    /**
     *Get the unregistered tokens of the notification sent.
     *
     * @return array $tokenUnRegistered
     */
    public function getFailedDeviceTokens()
    {
        return $this->service->failedDevicesTokens;
    }

    /**
     * Add Device Token into Topic
     * @param string $topic
     * @return mixed
     */
    public function addDeviceTokenToTopic($topic)
    {
        return $this->service->addDeviceTokenToTopic($topic, $this->deviceTokens);
    }

    /**
     * Remove Device Token from Topic
     * @param string $topic
     * @return mixed
     */
    public function removeDeviceTokenFromTopic($topic)
    {
        return $this->service->removeDeviceTokenFromTopic($topic, $this->deviceTokens);
    }

    /**
     *Get Topic Info
     * @param string $deviceToken
     * @return mixed
     */
    public function getTopicInfo($deviceToken)
    {
        return $this->service->getTopicInfo($deviceToken);
    }

    /**
     * Return property if exit here or in service object, otherwise null.
     *
     * @param $property
     * @return mixed / null
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists($this->service, $property)) {
            return $this->service->$property;
        }

        return null;
    }
}
