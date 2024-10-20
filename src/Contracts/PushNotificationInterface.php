<?php

namespace Kobir\PushNotification\Contracts;

interface PushNotificationInterface
{

    /**
     * Set the url to connect with the Push service provider.
     *
     * @param $url
     * @return mixed
     */
    public function setUrl($url);

    /**
     * Set the Push service provider configuration.
     *
     * @param array $config
     * @return mixed
     */
    public function setConfig(array $config);

    /**
     * Set the Push Notification Response.
     *
     * @param $feedback
     * @return mixed
     */
    public function setFeedback($feedback);

    /**
     * Send push notification
     *
     * @param array $deviceTokens
     * @param array $notification
     * @param array $data
     * @return mixed
     */
    public function sendPushNotification(array $deviceTokens, array $notification, array $data);

    /**
     * Set the device tokes that couldn't receive the message from the push notification.
     *
     * @param array $devices_token
     * @return mixed
     */
    public function setFailedDeviceToken(array $devicesTokens);
}
