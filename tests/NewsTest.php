<?php

namespace luya\news\tests;

use Yii;
use luya\testsuite\cases\WebApplicationTestCase;
use luya\testsuite\traits\MessageFileCompareTrait;
use luya\testsuite\traits\MigrationFileCheckTrait;

class NewsTest extends WebApplicationTestCase
{
	use MessageFileCompareTrait, MigrationFileCheckTrait;
	
	public function getConfigArray()
	{
		return [
			'id' => 'newstest',
			'basePath' => dirname(__DIR__),
			'modules' => [
				'newsadmin' => 'luya\news\admin\Module',
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
		$this->compareMessages(Yii::getAlias('@newsadmin/messages'), 'en');
	}

	/*
	public function testMigrationFiles()
	{
		// missing mysqli config
		$this->checkMigrationFolder('@newsadmin/migrations');
	}
	*/
}