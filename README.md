# Laravel Push Notification

Simple Laravel package for sending push notification to mobile devices (android, ios) using fcm http v1.

### Requirements

- php version ^7.2.5 or higher
- Google API Client Library ^2.14.0 or higher
- Laravel version ^7.0 or higher

## Installation

Use composer to download and install the package and its dependencies.

```sh
composer require kobir/larapush-notification
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
- `'token_cache_time' => 3500, //in seconds`
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
object addDeviceTokenToTopic(string $topic, array $deviceTokens)
```

#### removeDeviceTokenFromTopic

`removeDeviceTokenFromTopic` method use to remove devices from a topic.

**Syntax**

```php
object removeDeviceTokenFromTopic(string $topic, array $deviceTokens)
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

### Usage samples
