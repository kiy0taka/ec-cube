<?php


namespace Eccube\Tests\Composer;


use Eccube\Application;
use Eccube\Service\ComposerService;
use Eccube\Service\InfoCommandParser;
use Eccube\Service\RequireCommandParser;

class ComposerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ComposerService $service
     */
    private $service;

    protected function setUp()
    {
        $this->service = new ComposerService(Application::getInstance());
    }

    public function testParseRequreOutput()
    {
        $output = <<< EOT
<warning>You are running composer with xdebug enabled. This has a major impact on runtime performance. See https://getcomposer.org/xdebug</warning>
./composer.json has been updated
Loading composer repositories with package information
<warning>Warning: Accessing localhost over http which is an insecure protocol.</warning>
Updating dependencies
Package operations: 2 installs, 0 updates, 0 removals
  - Installing ec-cube/plugin-installer (1.0.0) Loading from cache
  - Installing ec-cube/mailtemplateedit (1.0.1) Loading from cache
Writing lock file
Generating autoload files

EOT;

        $parser = new RequireCommandParser($output);
        $actual = $parser->parse();

        self::assertEquals(
            array(
                'ec-cube/plugin-installer' => '1.0.0',
                'ec-cube/mailtemplateedit' => '1.0.1',
            ),
            $actual['installed']
        );
    }

    public function testParseInfoOutput()
    {

        $output = <<< EOT
name     : ec-cube/MailTemplateEdit
descrip. : メールテンプレート機能拡張プラグイン
keywords :
versions : * 1.0.0
type     : eccube-plugin
license  : LGPL
source   : []
dist     : [tar] http://localhost:8081/packages/ec-cube/MailTemplateEdit/4101/MailTemplateEdit.tgz
names    : ec-cube/mailtemplateedit

requires
ec-cube/plugin-installer ~1.0

requires (dev)
monolog/monolog ^1.4.1
EOT;

        $parser = new InfoCommandParser($output);
        $actual = $parser->parse();

        self::assertEquals(
            array(
                'name' => 'ec-cube/MailTemplateEdit',
                'descrip.' => 'メールテンプレート機能拡張プラグイン',
                'keywords' => '',
                'versions' => '* 1.0.0',
                'type' => 'eccube-plugin',
                'license' => 'LGPL',
                'source' => '[]',
                'dist' => '[tar] http://localhost:8081/packages/ec-cube/MailTemplateEdit/4101/MailTemplateEdit.tgz',
                'names' => 'ec-cube/mailtemplateedit',
                'requires' => array(
                    'ec-cube/plugin-installer' => '~1.0'
                ),
                'requires (dev)' => array(
                    'monolog/monolog' => '^1.4.1'
                )
            ),
            $actual
        );
    }

    public function testParseInfoOutputWitoutRequires()
    {
        $output = <<< EOT
name     : ec-cube/MailTemplateEdit
descrip. : メールテンプレート機能拡張プラグイン
keywords :
versions : * 1.0.0
type     : eccube-plugin
license  : LGPL
source   : []
dist     : [tar] http://localhost:8081/packages/ec-cube/MailTemplateEdit/4101/MailTemplateEdit.tgz
names    : ec-cube/mailtemplateedit
EOT;

        $parser = new InfoCommandParser($output);
        $actual = $parser->parse();

        self::assertEquals(
            array(
                'name' => 'ec-cube/MailTemplateEdit',
                'descrip.' => 'メールテンプレート機能拡張プラグイン',
                'keywords' => '',
                'versions' => '* 1.0.0',
                'type' => 'eccube-plugin',
                'license' => 'LGPL',
                'source' => '[]',
                'dist' => '[tar] http://localhost:8081/packages/ec-cube/MailTemplateEdit/4101/MailTemplateEdit.tgz',
                'names' => 'ec-cube/mailtemplateedit',
                'requires' => array(),
                'requires (dev)' => array()
            ),
            $actual
        );
    }

    public function testExecInfo()
    {
        $actual = $this->service->execInfo('twig/twig');

        self::assertEquals('twig/twig', $actual['name']);
        self::assertEquals(array(
            'php' => '>=5.2.7'
        ), $actual['requires']);
        self::assertEquals(array(
            'symfony/debug' => '~2.7',
            'symfony/phpunit-bridge' => '~3.2@dev'
        ), $actual['requires (dev)']);
    }

    public function testExecRequire()
    {
        $workingDir = $this->makeTemporaryDirectory('testGetRequires');
        file_put_contents($workingDir.'/composer.json', '{}');

        $this->service->setWorkingDir($workingDir);
        $actual = $this->service->execRequire('twig/twig:1.31.0');

        self::assertEquals(array(
            'twig/twig' => 'v1.31.0'
        ), $actual['installed']);
    }

    public function testExecConfig()
    {
        $workingDir = $this->makeTemporaryDirectory('testGetRequires');
        file_put_contents($workingDir.'/composer.json', '{}');

        $this->service->setWorkingDir($workingDir);
        $this->service->execConfig('repositories.foo', array('vcs', 'https://bar.com'));

        $expected = <<< EOT
{
    "repositories": {
        "foo": {
            "type": "vcs",
            "url": "https://bar.com"
        }
    }
}

EOT;

        self::assertEquals(
            $expected,
            file_get_contents($workingDir.'/composer.json')
        );
    }

    private function makeTemporaryDirectory($prefix)
    {
        $tmp_file_name= tempnam(sys_get_temp_dir(), $prefix);
        @unlink($tmp_file_name);
        @mkdir($tmp_file_name);
        return $tmp_file_name;
    }
}