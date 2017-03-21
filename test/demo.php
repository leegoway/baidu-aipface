<?php

namespace leegoway\aipface\test;

use leegoway\aipface\FaceFacade;

class Test 
{
	// 定义常量
	const ACCESS_KEY = 'de2f9f2cee4b411****';
	const SECRET_KEY = '9d86ea3276fc427*****';

	public function testGroup()
	{
		$facade = new FaceFacade();
		$facade->access_key = SELF::ACCESS_KEY;
		$facade->secret_key = SELF::SECRET_KEY;
		$res = $facade->GetGroup(0, 10);
		var_dump($res);die;
	}

}

$t = new Test();
$t->testGroup();
