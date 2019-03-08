<?php

namespace luya\posts\tests;

use Yii;
use luya\testsuite\cases\WebApplicationTestCase;
use luya\testsuite\traits\MessageFileCompareTrait;
use luya\testsuite\traits\MigrationFileCheckTrait;

class PostsTest extends WebApplicationTestCase
{
	use MessageFileCompareTrait, MigrationFileCheckTrait;
	
	public function getConfigArray()
	{
		return [
			'id' => 'poststest',
			'basePath' => dirname(__DIR__),
			'modules' => [
				'postsadmin' => [
                    'class' => 'luya\posts\admin\Module',
                    'encryptStoredTokens' => false,
                ],
			],
			'components' => [
				'db' => [
					'class' => 'yii\db\Connection',
					'dsn' => 'fake',
				],
			]
		];
	}
	
	public function testMessageFiles()
	{
		$this->compareMessages(Yii::getAlias('@postsadmin/messages'), 'en');
	}

	/*
	public function testMigrationFiles()
	{
		// missing mysqli config
		$this->checkMigrationFolder('@postsadmin/migrations');
	}
	*/
}
