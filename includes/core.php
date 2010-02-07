<?php
// Bugdar 2
// Copyright (c) 2010 Blue Static
// 
// This program is free software: you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the Free
// Software Foundation, either version 3 of the License, or any later version.
// 
// This program is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
// FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
// more details.
//
// You should have received a copy of the GNU General Public License along with
// this program.  If not, see <http://www.gnu.org/licenses/>.

class Bugdar
{
    // The active database connection.
    static public $db = NULL;

    // The authentication system.
    static public $auth = NULL;

    // Bootstraps the database.
    static public function BootstrapDatabase($config)
    {
        define('TABLE_PREFIX', $config->{'database.prefix'});
        try
        {
            self::$db = new PDO($config->{'database.dsn'}, $config->{'database.username'}, $config->{'database.password'});
            self::$db->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            throw new CoreException('Database error: ' . $e->GetMessage());
        }
    }

    // Loads the proper authentication module.
    static public function BootstrapAuthentication($config)
    {
        // Load the authentication system.
        $auth_module = BUGDAR_ROOT . '/includes/auth/auth_' . $config->{'auth.module'} . '.php';
        if (!file_exists($auth_module) || !is_readable($auth_module))
            throw new CoreException('Could not load authentication module ' . $config->{'auth.module'});
        require $auth_module;
        $name = phalanx\base\UnderscoreToCamelCase($config->{'auth.module'});
        $class_name = 'Authentication' . $name;
        if (!class_exists($class_name))
            throw new CoreException('Could not find class ' . $class_name);
        self::$auth = new $class_name($config->auth);
    }
}

class CoreException extends \Exception
{}