<?php


namespace Eccube\Service;


use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ComposerService
{

    /**
     * @var Application $consoleApplication
     */
    private $consoleApplication;

    private $workingDir;

    function __construct(\Eccube\Application $app)
    {
        if (!(getenv('HOME') ?: getenv('COMPOSER_HOME'))) {
            // TODO どうする
            putenv('COMPOSER_HOME='.$app['config']['plugin_realdir'].'/.composer');
        }
    }

    private function init()
    {
        $consoleApplication = new Application();
        $consoleApplication->resetComposer();
        $consoleApplication->setAutoExit(false);
        $this->consoleApplication = $consoleApplication;
        $this->workingDir = $this->workingDir ?: dirname(dirname(dirname(__DIR__)));

    }

    private function runCommand($commands)
    {

        $this->init();
        $commands['--working-dir'] = $this->workingDir;
        $commands['--no-ansi'] = 1;
        $input = new ArrayInput($commands);
        $output = new BufferedOutput();

        $exitCode = $this->consoleApplication->run($input, $output);

        $log = $output->fetch();
        if ($exitCode) {
            log_error($log);
            throw new \RuntimeException($log);
        }
        log_info($log, $commands);
        return $log;
    }

    public function execInfo($pluginName)
    {
        $output = $this->runCommand(array(
            'command' => 'info',
            'package' => $pluginName
        ));
        $parser = new InfoCommandParser($output);
        return $parser->parse();
    }

    public function execRequire($packageName)
    {
        $output = $this->runCommand(array(
            'command' => 'require',
            'packages' => array($packageName),
        ));
        $parser = new RequireCommandParser($output);
        return $parser->parse();
    }

    public function execRemove($packageName)
    {
        $this->runCommand(array(
            'command' => 'remove',
            'packages' => array($packageName),
            '--no-update-with-dependencies' => true,
        ));
    }

    public function foreachRequires($packageName, $callback, $typeFilter = null)
    {
        $info = $this->execInfo($packageName);
        if (isset($info['requires'])) {
            foreach ($info['requires'] as $name=>$version) {
                $package = $this->execInfo($name);
                if (is_null($typeFilter) || @$package['type'] === $typeFilter) {
                    $callback($package);
                }
            }
        }
    }

    /**
     * @param string|$key
     * @param array|$value
     * @return array|mixed
     */
    public function execConfig($key, $value = null)
    {
        $commands = array(
            'command' => 'config',
            'setting-key' => $key,
            'setting-value' => $value
        );
        if ($value) {
            $commands['setting-value'] = $value;
        }
        $output = $this->runCommand($commands);
        $parser = new ConfigCommandParser($output);
        return $parser->parse();
    }

    public function getConfig()
    {
        $output = $this->runCommand(array(
            'command' => 'config',
            '--list' => true
        ));
        $parser = new ConfigListParser($output);
        return $parser->parse();
    }

    public function setWorkingDir($workingDir)
    {
        $this->workingDir = $workingDir;
    }
}


class InfoCommandParser {
    private $output;

    function __construct($output)
    {
        $this->output = $output;
    }

    public function parse()
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $this->output));
        $infoLogs = array_filter(array_map(function($line) {
            $matches = array();
            preg_match('/^(name|descrip.|keywords|versions|type|license|source|dist|names)\s*:\s*(.*)$/', $line, $matches);
            return $matches;
        }, $rowArray));

        // 'name' => 'value'
        $result = array_column($infoLogs, 2, 1);
        $result['requires'] = $this->parseArrayOutput($rowArray, 'requires');
        $result['requires (dev)'] = $this->parseArrayOutput($rowArray, 'requires (dev)');
        return $result;
    }

    private function parseArrayOutput($rowArray, $key)
    {
        $result = array();
        $start = false;
        foreach ($rowArray as $line) {
            if ($line === $key) {
                $start = true;
                continue;
            }
            if ($start) {
                if (empty($line)) {
                    break;
                }
                $parts = explode(' ', $line);
                $result[$parts[0]] = $parts[1];
            }
        }

        return $result;
    }
}

class RequireCommandParser {
    private $output;

    function __construct($output)
    {
        $this->output = $output;
    }

    public function parse()
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $this->output));
        $installedLogs = array_filter(array_map(function($line) {
            $matches = array();
            preg_match('/^  - Installing (.*?) \((.*?)\) .*/', $line, $matches);
            return $matches;
        }, $rowArray));

        // 'package name' => 'version'
        return array('installed' => array_column($installedLogs, 2, 1));
    }
}

class ConfigCommandParser {
    private $output;

    function __construct($output)
    {
        $this->output = $output;
    }

    public function parse()
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $this->output));
        $rowArray = array_filter($rowArray, function($line) {
            return !preg_match('/^<warning>.*/', $line);
        });
        return $rowArray ? json_decode(array_shift($rowArray), true) : array();
    }
}

class ConfigListParser {
    private $output;

    function __construct($output)
    {
        $this->output = $output;
    }

    public function parse()
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $this->output));
        $rawConfig = array_map(function($line) {
            $matches = array();
            preg_match('/^\[(.*?)\]\s?(.*)$/', $line, $matches);
            return $matches;
        }, $rowArray);

        $rawConfig = array_column($rawConfig, 2, 1);

        $result = array();

        foreach ($rawConfig as $path=>$value) {
            $arr = &$result;
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                $arr = &$arr[$key];
            }
            $arr = $value;
        }

        return $result;
    }
}