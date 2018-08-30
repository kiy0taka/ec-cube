<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Command;


use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginSchemaUpdateCommand extends Command
{
    use PluginCommandTrait;

    protected static $defaultName = 'eccube:plugin:schema-update';

    private $pluginRealDir;

    /**
     * PluginUpdateCommand constructor.
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        parent::__construct();
        $this->pluginRealDir = $eccubeConfig['plugin_realdir'];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var Plugin $Plugin */
        $Plugin = $this->pluginRepository->findByCode('Emperor');
        $config = $this->pluginService->readConfig($this->pluginRealDir.DIRECTORY_SEPARATOR.$Plugin->getCode());
        $this->pluginService->generateProxyAndUpdateSchema($Plugin, $config);
        $this->clearCache($io);

        $io->success('Schema Updated.');
    }
}