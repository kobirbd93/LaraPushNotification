<?php

namespace Kobir\PushNotification;

use Carbon\Carbon;
use Google\Client as GoogleApiClient;
use Google\Service\FirebaseCloudMessaging;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Kobir\PushNotification\Contracts\PushNotificationInterface;
use Kobir\PushNotification\PushNotificationService;

class FcmHttpV1 extends PushNotificationService implements PushNotificationInterface
{

    /**
     * token cache time
     *
     * @var time in second
     */

    protected $token_cache_time;

    /**
     * Client to do the request
     *
     * @var \GuzzleHttp\Client $client
     */
    protected $client;

    /**
     * Gcm constructor.
     */
    public function __construct()
    {
        $this->config = $this->initializeConfig('fcm');

        $this->url = "https://fcm.googleapis.com/v1/projects/" . $this->config['firebase_project_id'] . "/messages:send";
        $this->topicAddUrl = "https://iid.googleapis.com/iid/v1:batchAdd";
        $this->topicRemoveUrl = "https://iid.googleapis.com/iid/v1:batchRemove";
        $this->client = new Client($this->config['guzzle'] ?? []);
        $this->token_cache_time = $this->config['token_cache_time'] ?? 3500;
    }

    /**
     * Set the projectId for the notification
     * @param string $projectId
     */
    public function setProjectId($projectId)
    {
        $this->config['firebase_project_id'] = $projectId;

        $this->url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
    }

    /**
     * Set the jsonFile path for the notification
     * @param string $jsonFile
     */
    public function setJsonCredential($jsonFile)
    {
        $this->config['certificate'] = $jsonFile;
    }

    /**
     * Generate Oauth2 Token
     *
     * @return token
     */
    public function getOauthToken()
    {
        return Cache::remember(
            Str::slug('fcm-http-v1-oauth-token-' . $this->config['firebase_project_id']),
            Carbon::now()->addSeconds($this->token_cache_time),
            function () {
                $jsonFilePath = $this->config['certificate'];

                $googleClient = new GoogleApiClient();

                $googleClient->setAuthConfig($jsonFilePath);
                $googleClient->addScope(FirebaseCloudMessaging::FIREBASE_MESSAGING);

                $accessToken = $googleClient->fetchAccessTokenWithAssertion();

                return $accessToken['access_token'];
            }
        );
    }

    public function sendPushByHttpV1(array $message)
    {

        if (!$this->checkCertificate()) {
            return $this->feedback;
        }

        $apiToken = $this->getOauthToken();
        try {
            $response = $this->client->post($this->url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiToken,
                ],
                'body' => json_encode(['message' => $message]),
            ]);

            $mgs = json_decode((string) $response->getBody(), true, 512, JSON_BIGINT_AS_STRING);

            $response = ['success' => true, 'message' => $mgs, 'code' => $response->getStatusCode()];
            return $response;
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()];
            return $response;
        }
    }

    /**
     * Send push notification
     *
     * @param array $deviceTokens
     * @param array $notification
     * @param array $data
     * @return mixed
     */
    public function sendPushNotification(array $devices, array $notification, array $data)
    {
        if (count($devices) <= 0) {
            return $this->deviceTokensNotFound();
        }

        $others = [];
        if (isset($notification['image']) && $notification['image']) {
            $others = [
                "android" => [
                    "notification" => [
                        "image" => $notification['image'],
                    ],
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "mutable-content" => 1,
                        ],
                        "image" => $notification['image'],
                    ],
                    "fcm_options" => [
                        "image" => $notification['image'],
                    ],
                ],
                "webpush" => [
                    "headers" => [
                        "image" => $notification['image'],
                    ],
                ],
            ];
        }

        $results = $failedDeviceTokens = [];

        foreach ($devices as $index => $token) {
            $basic = [
                'token' => (string) $token,
                'notification' => $notification,
                'data' => $data,
            ];

            $message = array_merge($basic, $others);

            $response = $this->sendPushByHttpV1($message);

            $results[$index]['response'] = $response;
            // $results[$index]['token'] = $token;

            if (isset($response['code']) && in_array($response['code'], [400, 404])) {
                $failedDeviceTokens[] = $token;
            }
        }

        if (count($failedDeviceTokens) > 0) {
            $this->setFailedDeviceToken($failedDeviceTokens);
        }

        return $this->setFeedback(json_encode($results));
    }

    /**
     * Check if the certificate file exist.
     * @return bool
     */
    private function checkCertificate()
    {
        if (isset($this->config['certificate'])) {
            $certificate = $this->config['certificate'];
            if (!file_exists($certificate)) {
                $this->certificateFileNotFound();
                return false;
            }

            return true;
        }

        $this->certificateFileNotFound();
        return false;
    }

    /**
     * Set the feedback with certificate not exist.
     *
     * @return mixed|void
     */
    private function certificateFileNotFound()
    {
        $response = [
            'success' => false,
            'message' => "Please, add your FCM certificate to the fcmCertificates folder.",
            'code' => 401,
        ];

        $this->setFeedback(json_encode($response));
    }

    /**
     * Set the feedback with token not found.
     *
     * @return mixed|void
     */
    private function deviceTokensNotFound()
    {
        $response = [
            'success' => false,
            'message' => "Please, add a device token to send push notification.",
            'code' => 404,
        ];

        $this->setFeedback(json_encode($response));
    }

    /**
     * Set the feedback with token not found.
     *
     * @return mixed|void
     */
    private function topicNotFound()
    {
        $response = [
            'success' => false,
            'message' => "Sorry! topic name not found.",
            'code' => 404,
        ];

        $this->setFeedback(json_encode($response));
    }

    /**
     * Send push notification
     *
     * @param string $topic
     * @param array $notification
     * @param array $data
     * @param bool $condition
     * @return mixed
     */
    public function sendPushNotificationByTopic(string $topic, array $notification, array $data, $condition = false)
    {
        if (!$topic) {
            $this->topicNotFound();
        }

        $others = [];
        if (isset($notification['image']) && $notification['image']) {
            $others = [
                "android" => [
                    "notification" => [
                        "image" => $notification['image'],
                    ],
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "mutable-content" => 1,
                        ],
                    ],
                    "fcm_options" => [
                        "image" => $notification['image'],
                    ],
                ],
                "webpush" => [
                    "headers" => [
                        "image" => $notification['image'],
                    ],
                ],
            ];
        }

        if ($condition) {
            // "condition"=> "'dogs' in topics || 'cats' in topics",
            $basic = [
                'condition' => (string) $topic,
                'notification' => $notification,
                'data' => $data,
            ];
        } else {
            $basic = [
                'topic' => (string) $topic,
                'notification' => $notification,
                'data' => $data,
            ];
        }

        $message = array_merge($basic, $others);

        $response = $this->sendPushByHttpV1($message);

        $results[]['response'] = $response;

        return $this->setFeedback(json_encode($results));
    }

    /**
     * Add Device Token into Topic
     * @param string $topic
     * @param array $deviceTokens
     * @return mixed
     */
    public function addDeviceTokenToTopic($topic, $deviceTokens = [])
    {

        if (!$this->checkCertificate()) {
            return $this->feedback;
        }

        if (count($deviceTokens) <= 0) {
            return $this->deviceTokensNotFound();
        }

        $body = [
            'to' => (string) 'topics/' . $topic,
            'registration_tokens' => (array) $deviceTokens,
        ];

        $apiToken = $this->getOauthToken();
        try {
            $response = $this->client->post($this->topicAddUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'access_token_auth' => true,
                ],
                'body' => json_encode($body),
            ]);

            $mgs = json_decode((string) $response->getBody(), true, 512, JSON_BIGINT_AS_STRING);

            $response = ['success' => true, 'message' => $mgs, 'code' => $response->getStatusCode()];
            return $response;
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()];
            return $response;
        }
    }

    /**
     * Remove Device Token from Topic
     * @param string $topic
     * @param array $deviceTokens
     * @return mixed
     */
    public function removeDeviceTokenFromTopic($topic, $deviceTokens = [])
    {
        if (!$this->checkCertificate()) {
            return $this->feedback;
        }

        if (count($deviceTokens) <= 0) {
            return $this->deviceTokensNotFound();
        }

        $body = [
            'to' => (string) 'topics/' . $topic,
            'registration_tokens' => (array) $deviceTokens,
        ];

        $apiToken = $this->getOauthToken();
        try {
            $response = $this->client->post($this->topicRemoveUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'access_token_auth' => true,
                ],
                'body' => json_encode($body),
            ]);

            $mgs = json_decode((string) $response->getBody(), true, 512, JSON_BIGINT_AS_STRING);

            $response = ['success' => true, 'message' => $mgs, 'code' => $response->getStatusCode()];
            return $response;
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()];
            return $response;
        }
    }
}
