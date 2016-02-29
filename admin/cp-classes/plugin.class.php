<?php/** * Plugin Helper class. * * Zenbership Membership Software * Copyright (C) 2013-2016 Castlamp, LLC * * This program is free software: you can redistribute it and/or modify * it under the terms of the GNU General Public License as published by * the Free Software Foundation, either version 3 of the License, or * (at your option) any later version. * * This program is distributed in the hope that it will be useful, * but WITHOUT ANY WARRANTY; without even the implied warranty of * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the * GNU General Public License for more details. * * You should have received a copy of the GNU General Public License * along with this program.  If not, see <http://www.gnu.org/licenses/>. * * @author      Castlamp * @link        http://www.castlamp.com/ * @link        http://www.zenbership.com/ * @copyright   (c) 2013-2016 Castlamp * @license     http://www.gnu.org/licenses/gpl-3.0.en.html * @project     Zenbership Membership Software */class plugin extends db{    public $id;    public $global;    public $path;    public $model;    /**     *     */    function __construct($id)    {        $this->id = $id;        $this->get_config();    }    /**     * Check for a flat-file     * config file and load it     * if necessary.     */    protected function get_config()    {        $this->path = PP_PATH . '/custom/plugins/' . $this->id;        $path = $this->path . '/conf/config.php';        if (file_exists($path)) {            require $path;            $this->global = $opts;        }    }    /**     * Load a model related to the plugin.     *     * @param $id   Must be prefixed with zp_     */    public function load($id)    {        $id = str_replace('zp_', '', $id);        $classId = 'zp_' . $id;        $file = $this->path . '/functions/' . $id . '.php';        if (file_exists($file)) {            require $file;            return new $id($this);        }    }    /**     *     */    public function renderTemplate($id, $changes, $headers = '1')    {        $file = $this->path . '/templates/' . $id . '.php';        if (file_exists($file)) {           return new template($file, $changes, $headers);        } else {            return '';        }    }    /**     * Get a plugin option.     *     * @param String $name Option ID.     *     * @return mixed     */    public function option($name)    {    	return $this->get_option('pg_' . $this->id . '_' . $name);    }    /**     * Update an option and run optional code if it exists.     */    public function updateOption($key, $value)    {        if (file_exists($this->path . '/options/' . $key . '.php')) {            include $this->path . '/options/' . $key . '.php';        }        $opt_type = $this->option_type($key);        if ($opt_type == 'timeframe') {            $value = $admin->construct_timeframe($value['number'], $value['unit']);        }        return $this->update_option($key, $value);    }    /**     * If a global has     */    public function connectLocal()    {        if (! empty($this->global)) {            $DBH = new PDO(                "mysql:host=" . $this->global->mysql_host . ";                dbname=" . $this->global->mysql_db,                $this->global->mysql_user,                $this->global->mysql_pass,                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")            );            $GLOBALS['DBH'] = $DBH;        } else {            throw new Exception('Could not find plugin globals. Please create a config.php file for the plugin and try again.');        }    }    /**     *      */    public function connectZen()    {        $DBH = new PDO(            "mysql:host=" . PP_MYSQL_HOST . ";                dbname=" . PP_MYSQL_DB,            PP_MYSQL_USER,            PP_MYSQL_PASS,            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")        );        $GLOBALS['DBH'] = $DBH;    }}