<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$I = new AcceptanceTester($scenario);
$vaddyVerificationFile = 'vaddy-'.getenv('VADDY_CODE').'.html';
$vaddyProjectId = getenv('VADDY_PROJECT_ID');
$I->amOnPage("/${vaddyVerificationFile}?action=begin&project_id=${vaddyProjectId}");
$I->see(getenv('VADDY_CODE'));
