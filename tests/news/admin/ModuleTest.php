<?php

namespace luya\news\tests\admin;

class ModuleTest extends \newstests\BaseWebTestCase
{
    public function testRegisterComponents()
    {
        $comp = $this->app->newsautopost;
        $this->assertNotNull($comp);
    }
}
