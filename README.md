<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# Posts Module

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)
[![Slack Support](https://img.shields.io/badge/Slack-luyadev-yellowgreen.svg)](https://slack.luya.io/)

The posts module provides standard blog/news functionality, including categories, articles, tags, wysiwyg, social networks integration.

This module is a fork of the [luya news module](https://github.com/luyadev/luya-module-news)

## Stability

The module is under development, so no stable version is currently available yet

## Installation

For the installation of modules Composer is required.

```sh
composer require denyadzi/luya-module-posts: ~2.0-dev
```

### Configuration

After installation via Composer include the module to your configuration file within the modules section.

```php
'modules' => [
    // ...
    'posts' => [
    	'class' => 'luya\posts\frontend\Module',
    	'useAppViewPath' => false, // When enabled the views will be looked up in the @app/views folder, otherwise the views shipped with the module will be used.
    ],
    'postsadmin' => 'luya\posts\admin\Module',
]
```

### Initialization 

After successfully installation and configuration run the migrate, import and setup command to initialize the module in your project.

1.) Migrate your database.

```sh
./vendor/bin/luya migrate
```

2.) Import the module and migrations into your LUYA project.

```sh
./vendor/bin/luya import
```

After adding the persmissions to your group you will be able to edit and add new posts.

## Example Views

As the module will try to render a view for the post overview, here is what this could look like this in a very basic way:

#### views/posts/default/index.php

```php
<?php
use yii\widgets\LinkPager;

/* @var $this \luya\web\View */
/* @var $provider \yii\data\ActiveDataProvider */
?>
<h2>Latest Posts</h2>
<?php foreach($provider->models as $item): ?>
    <?php /* @var $item \luya\posts\models\Article */ ?>
    <pre>
        <?php print_r($item->toArray()); ?>
    </pre>
    <p>
        <a href="<?= $item->detailUrl; ?>">Post Detail Link</a>
    </p>
<?php endforeach; ?>

<?= LinkPager::widget(['pagination' => $provider->pagination]); ?>
```

#### views/posts/default/detail.php

```php
<?php
/* @var $this \luya\web\View */
/* @var $model \luya\posts\models\Article */
?>
<h1><?= $model->title; ?></h1>
<pre>
<?php print_r($model->toArray()); ?>
</pre>
```

The above examples will just dump all the data from the model active records.

## TODO

* Test token encryption  
* Document  
