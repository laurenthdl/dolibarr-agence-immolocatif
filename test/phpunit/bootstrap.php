<?php
declare(strict_types=1);
// Bootstrap pour PHPUnit - environnement Dolibarr simule

if (!defined('DOL_DOCUMENT_ROOT')) {
    define('DOL_DOCUMENT_ROOT', dirname(__DIR__, 4));
}

if (!class_exists('DoliDB')) {
    class DoliDB {
        public function prefix() { return 'llx_'; }
        public function query($sql) { return true; }
        public function fetch_object($resql) { return null; }
    }
}

if (!class_exists('User')) {
    class User {
        public $id = 1;
    }
}

if (!class_exists('DolibarrModules')) {
    class DolibarrModules {
        public $db;
        public $numero;
        public $rights_class;
        public $name;
        public $version;
        public $const_name;
        public $picto;
        public $module_parts = [];
        public $dirs = [];
        public $depends = [];
        public $requiredby = [];
        public $conflictwith = [];
        public $langfiles = [];
        public $menu = [];
        public $rights = [];

        protected function _init($sql, $options = '') { return 1; }
        protected function _remove($sql, $options = '') { return 1; }
    }
}

if (!class_exists('CommonObject')) {
    class CommonObject {
        public $db;
        public $table_element;
        public $element;
        public $ref = '';
        public $fk_user_creat;
        public $datec = '';
        public $tms = '';
        public $status;

        protected $fields = [];

        protected function createCommon(User $user, $notrigger = false) { return 1; }
        protected function fetchCommon($id, $ref = '') { return 1; }
        protected function updateCommon(User $user, $notrigger = false) { return 1; }
        protected function deleteCommon(User $user, $notrigger = false) { return 1; }
    }
}

if (!defined('MAIN_DB_PREFIX')) {
    define('MAIN_DB_PREFIX', 'llx_');
}
