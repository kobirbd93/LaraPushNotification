# Laravel Push Notification

Simple Laravel package for sending push notification to mobile devices (android, ios) using fcm http v1.

### Requirements

- php version ^7.2 or higher
- Google API Client Library ^2.14.0 or higher
- Laravel version ^6.0 or higher

## Installation

Use composer to download and install the package and its dependencies.

```sh
composer require kobir/larapush-notification
```
if you are facing dependency version conflict related issues please run

```sh
composer require kobir/larapush-notification -W
```

The package will automatically register its service provider.

To publish the package's configuration file run the following command

```sh
php artisan vendor:publish --provider="Kobir\PushNotification\Providers\PushNotificationServiceProvider" --tag="config"
```

### Configuration

After publishing the configuration, you can find the 'larapushnotification.php' as well as 'fcmCertificates' empty directory into the config folder.

The default configuration parameters for **FCM** are :

- `'priority' => 'normal'`,
- `'certificate' => __DIR__ . '/fcmCertificates/fcm-admin-sdk.json',`
- `'dry_run' => false,`
- `'firebase_project_id' => 'FIREBASE_PROJECT_ID',`
- `'token_cache_time' => 3500, //in seconds & must be <= 3500`
- `'guzzle' => [],`

You can dynamically update those values or adding new ones calling the method setConfig like so:

```php
$push->setConfig([
    'priority' => 'high',
    'firebase_project_id' => 'my-project-id',
    'certificate' => 'path/to/fcm-admin-sdk.json'
    'token_cache_time' => 3000
]);
```

To generate a credentials fcm-admin-sdk json file for your service account:

- In the Firebase console, open **Settings** > [Service Accounts](https://console.firebase.google.com/project/_/settings/serviceaccounts/adminsdk).
- Click **Generate New Private Key**, then confirm by clicking **Generate Key**.
- Securely store the JSON file containing the key.

## Usage

To instantiate the Push Notification class

```php
$push = new PushNotification;
```

By default it will use **fcm** as Push Service provider or you can pass service name if you want as like...

```php
$push = new PushNotification('fcm');
```

Now you may use any method that you need. Please see the all methods list.

#### setNotification

`setNotification` method use to set the notification basic parameters, which you pass the values through parameter as an array.

**Syntax**

```php
object setNotification(array $data)
```

#### setData

`setData` method use to set the additional message parameters like id, type sound click_action etc.., which you pass the values through parameter as an array.

**Syntax**

```php
object setData(array $data)
```

#### setProjectId

`setProjectId` method use to set the Firebase Project ID of your App as a string.

**Syntax**

```php
object setProjectId($projectId)
```

#### setJsonCredential

`setJsonCredential` method use to set the path of credentials json file of your App.

**Syntax**

```php
object setJsonCredential($filePath)
```

#### setConfig

`setConfig` method sets the Push service configuration, which you pass the name through parameter as an array.

**Syntax**

```php
object setConfig(array $config)
```

#### setUrl

`setUrl` method sets the Push service url, which you pass the url through parameter as a string.

**Syntax**

```php
object setUrl($url)
```

> Not update the url unless it's really necessary.

#### setDevicesToken

`setDevicesToken` method use to set the devices' tokens, which you pass the token through parameter either array for multiple tokens or string if single token.

**Syntax**

```php
object setDevicesToken($deviceTokens)
```

#### send

`send` method is responsible for sending the push notification.

**Syntax**

```php
object send()
```

#### getFeedback

`getFeedback` method return the full notification response after sending a notification.

**Syntax**

```php
object getFeedback()
```

#### getFailedDeviceTokens

`getFailedDeviceTokens` method return the all devices' tokens that couldn't receive the notification or failed. Further you can make logic to your backend code for that device tokens.

**Syntax**

```php
array getFailedDeviceTokens()
```

#### sendByTopic

`sendByTopic` method use to send notification by topic. It also accepts the topic condition. more details [here](https://firebase.google.com/docs/cloud-messaging/android/topic-messaging)

> If isCondition is true, $topic will be treated as an expression

**Syntax**

```php
object sendByTopic($topic, $isCondition)
```

#### addDeviceTokenToTopic

`addDeviceTokenToTopic` method use to add device into a topic. To add a new topic you need a unique topic name & all devices would you want to add this topic.

**Syntax**

```php
object addDeviceTokenToTopic(string $topic)
```

#### removeDeviceTokenFromTopic

`removeDeviceTokenFromTopic` method use to remove devices from a topic.

**Syntax**

```php
object removeDeviceTokenFromTopic(string $topic)
```

#### getTopicInfo

`getTopicInfo` method use to get all topics name associate with the provided device token.

**Syntax**

```php
object getTopicInfo(string $deviceToken)
```

#### setTopicAddUrl

`setTopicAddUrl` method sets the topic add url, which you pass the url through parameter as a string.

**Syntax**

```php
object setTopicAddUrl($url)
```

> Not update the url unless it's really necessary.

#### setTopicRemoveUrl

`setTopicRemoveUrl` method sets the topic remove url, which you pass the url through parameter as a string.

**Syntax**

```php
object setTopicRemoveUrl($url)
```

> Not update the url unless it's really necessary.

#### setTopicInfoUrl

`setTopicInfoUrl` method sets the topic info url, which you pass the url through parameter as a string.

**Syntax**

```php
object setTopicInfoUrl($url)
```

> Not update the url unless it's really necessary.

### Usage samples

```php
$push = new PushNotification();
/***
 * (string) title
 * (string) body
 * (string) image, optional field image size must be less than 1mb
*/
$push->setNotification([
        'title' => (string) 'Title goes here',
        'body' => (string) 'Body text goes here',
        'image' => (string) 'image-url', //optional,
        ])
        ->setDevicesToken(['deviceToken1', 'deviceToken2', ...])
        ->send();
```

This is the basic data-set for notification. Here **send()** method used for sending push notification.

### Additional Payload

You can also pass the additional payload data by using **setData()** method

```php
/***
 * (string) id, if haven't id place '0'
*/
$feedback = $push->setNotification([
                    'title' => (string) 'Title goes here',
                    'body' => (string) 'Body text goes here',
                    'image' => (string) 'image-url', //optional
                    ])
                    ->setData([
                    'id' => (string) 'dynamic_id',
                    'type' => 'OFFER',
                    'sound' => 'default',
                    'click_action' => 'NOTIFICATION_CLICK',
                    ])
                    ->setDevicesToken(['deviceToken1', 'deviceToken2', ...])
                    ->send()
                    ->getFeedback();
```

### Get Notification Feedback

```php

$feedback = $push->setNotification([
                'title' => (string) 'Title goes here',
                'body' => (string) 'Body text goes here',
                'image' => (string) 'image-url', //optional
                ])
                ->setDevicesToken(['deviceToken1', 'deviceToken2', ...])
                ->send()
                ->getFeedback();
```

If you want get the failed device tokens use **getFailedDeviceTokens()** method as ...

```php
$failedTokens = $push->setNotification([
                    'title' => (string) 'Title goes here',
                    'body' => (string) 'Body text goes here',
                    'image' => (string) 'image-url', //optional
                    ])
                    ->setDevicesToken(['deviceToken1', 'deviceToken2', ...])
                    ->send()
                    ->getFailedDeviceTokens();
```

If you want send the notification to only 1 device, you may pass the value as string.

```php
$feedback = $push->setNotification([
                'title' => (string) 'Title goes here',
                'body' => (string) 'Body text goes here',
                'image' => (string) 'image-url', //optional
                ])
                ->setDevicesToken('deviceToken')
                ->send()
                ->getFeedback();

```

### Send Notification by Topic

```php
/***
 * (string) topic, name like 'foo', 'bar'
*/
$feedback = $push->setNotification([
                'title' => (string) 'Title goes here',
                'body' => (string) 'Body text goes here',
                'image' => (string) 'image-url', //optional
                ])
                ->sendByTopic('bar')
                ->getFeedback();

```

or with using a condition:

```php
/***
 * you must have pass the additional parameter as 'true'
*/
$feedback = $push->setNotification([
                'title' => (string) 'Title goes here',
                'body' => (string) 'Body text goes here',
                'image' => (string) 'image-url', //optional
                ])
                ->sendByTopic("'foo' in topics || 'bar' in topics", true)
                ->getFeedback();
```

### Add Device Tokens into Topic

```php

$response = $push->setDevicesToken(['deviceToken1', 'deviceToken2', ...])
                ->addDeviceTokenToTopic('foo');
```

### Remove Device Tokens from Topic

```php

$response = $push->setDevicesToken(['deviceToken1', 'deviceToken2', ...])
                ->removeDeviceTokenFromTopic('bar');
```

### Get Topic Information
If you want to get how many topic associated with a device token then call this **getTopicInfo()** method with device_token parameter.
```php

$response = $push->getTopicInfo('deviceToken1');

//successful response will be like
{
    "applicationVersion": "19",
    "application": "com.iid.example",
    "authorizedEntity": "123456782354",
    "platform": "ANDROID",
    "rel": {
        "topics": {
            "cats": {
                "addDate": "2024-10-20"
            },
            "dogs": {
                "addDate": "2024-10-20"
            }
        }
    }
}
```

### Contribution

#### Submitting issues and pull requests for bugs or new feature(s) requests are always welcome.
