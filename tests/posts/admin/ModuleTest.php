<?php

namespace luya\posts\tests\admin;

class ModuleTest extends \poststests\BaseWebTestCase
{
    public function testRegisterComponents()
    {
        $comp = $this->app->postsautopost;
        $this->assertNotNull($comp);
    }
}
