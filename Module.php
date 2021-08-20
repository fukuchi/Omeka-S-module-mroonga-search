<?php
/**
 * Mroonga search
 *
 * Enabling Mroonga engine for CJK full-text search. Mroonga plugin for MySQL
 * or MariaDB must be installed and configured.
 *
 *
 * @copyright Copyright (c) 2020, 2021 Kentaro Fukuchi
 * @license MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace MroongaSearch;

use Omeka\Module\AbstractModule;
use Omeka\Module\Exception\ModuleCannotInstallException;
use Omeka\Stdlib\Message;
use Omeka\Mvc\Controller\Plugin\Messenger;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\ResultSetMapping;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $this->manageSettings($serviceLocator->get('Omeka\Settings'), 'install');

        $connection = $serviceLocator->get('Omeka\Connection');

        $this->checkMroongaPlugin($connection);

        $sm = $connection->getSchemaManager();
        $fkeyName = $this->getForeignKeyName($sm, 'Installation');
        $idxName = $this->getFulltextKeyName($sm, 'Installation');

        $sql = "ALTER TABLE fulltext_search DROP FOREIGN KEY $fkeyName";
        $result = $connection->executeQuery($sql);

        $sql = "ALTER TABLE fulltext_search ENGINE = Mroonga COMMENT = 'engine \"InnoDB\"'";
        $result = $connection->executeQuery($sql);

        $sql = "ALTER TABLE fulltext_search ADD CONSTRAINT $fkeyName FOREIGN KEY ( `owner_id` ) REFERENCES `user` ( `id` ) ON DELETE SET NULL";
        $result = $connection->executeQuery($sql);
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $this->manageSettings($serviceLocator->get('Omeka\Settings'), 'uninstall');

        $connection = $serviceLocator->get('Omeka\Connection');
        $sm = $connection->getSchemaManager();
        $fkeyName = $this->getForeignKeyName($sm, 'Uninstallation');
        $idxName = $this->getFulltextKeyName($sm, 'Uninstallation');

        $sql = "ALTER TABLE fulltext_search DROP FOREIGN KEY $fkeyName";
        $result = $connection->executeQuery($sql);

        $sql = "ALTER TABLE fulltext_search ENGINE = InnoDB COMMENT = ''";
        $result = $connection->executeQuery($sql);

        $sql = "ALTER TABLE fulltext_search ADD CONSTRAINT $fkeyName FOREIGN KEY ( `owner_id` ) REFERENCES `user` ( `id` ) ON DELETE SET NULL";
        $result = $connection->executeQuery($sql);
    }

    protected function manageSettings($settings, $process, $key = 'config')
    {
        $config = require __DIR__ . '/config/module.config.php';
        $defaultSettings = $config[strtolower(__NAMESPACE__)][$key];
        foreach ($defaultSettings as $name => $value) {
            switch ($process) {
            case 'install':
                $settings->set($name, $value);
                break;
            case 'uninstall':
                $settings->delete($name);
                break;
            }
        }
    }

    protected function getForeignKeyName($schemaManager, $process)
    {
        $fkeys = $schemaManager->listTableForeignKeys('fulltext_search');
        if ($fkeys[0]->getLocalColumns()[0] !== 'owner_id') {
            $message = new Message("The table schema of 'fulltext_search' is different from what is expected. %s aborted.", $process);
            throw new ModuleCannotInstallException($message);
        }
        return $fkeys[0]->getName();
    }

    protected function getFulltextKeyName($schemaManager, $process)
    {
        $ikeys = $schemaManager->listTableIndexes('fulltext_search');
        foreach ($ikeys as $ikey) {
            if ($ikey->getColumns() === ['title', 'text']) {
                return $ikey->getName();
            }
        }
        $message = new Message("The table schema of 'fulltext_search' is different from what is expected. %s aborted.", $process);
        throw new ModuleCannotInstallException($message);
    }

    /**
     * Check the database and throws an error if Mroonga is not installed.
     *
     * @throws ModuleCannotInstallException
     */
    protected function checkMroongaPlugin(Connection $connection)
    {
        $sql = "SELECT PLUGIN_STATUS FROM information_schema.PLUGINS WHERE PLUGIN_NAME='Mroonga'";
        $result = $connection->fetchColumn($sql);
        if ($result !== 'ACTIVE') {
            $message = new Message('Mroonga plugin is not installed or activated.');
            throw new ModuleCannotInstallException($message);
        }
    }
}
