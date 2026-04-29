<?php

declare(strict_types=1);

require_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/immocore/core/modules/modimmocore.class.php';
        
require_once DOL_DOCUMENT_ROOT . '/custom/immobien/core/modules/modimmobien.class.php';
        
require_once DOL_DOCUMENT_ROOT . '/custom/immoclient/core/modules/modimmoclient.class.php';

class modImmolocatif extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;
        $this->numero = 700003;
        $this->rights_class = 'immolocatif';
        $this->family = "other";
        $this->module_position = '90';
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Gestion locative et quittances";
        $this->version = '1.0.0';
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        $this->picto = 'building';
        $this->module_parts = array();
        $this->dirs = array();
        $this->config_page_url = array("immolocatif@immobilier");
        $this->depends = array('mod_immocore' => 1, 'mod_immobien' => 1, 'mod_immoclient' => 1, 'mod_societe' => 1);
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->langfiles = array("immolocatif@immobilier");
        $this->phpmin = array(8, 1);
        $this->need_dolibarr_version = array(23, 0);
        $this->warnings_activation = array();
        $this->warnings_activation_ext = array();

        $this->const = array();
        $this->tabs = array();
        $this->dictionaries = array();

        $this->menu = array();
        $r = 0;
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=immobilier',
            'type' => 'top',
            'titre' => 'Immobilier - Location',
            'mainmenu' => 'immobilier',
            'leftmenu' => 'immolocatif',
            'url' => '/custom/immolocatif/index.php',
            'langs' => 'immolocatif@immobilier',
            'position' => 700003,
            'perms' => '1',
            'target' => '',
            'user' => 2,
        );
        $r++;

        $this->rights = array();
        $this->rights_class = 'immolocatif';
        $r = 0;
        $this->rights[$r][0] = 700003001;
        $this->rights[$r][1] = 'Lire les Immobilier - Location';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'read';
        $r++;
        $this->rights[$r][0] = 700003002;
        $this->rights[$r][1] = 'Créer/Modifier les Immobilier - Location';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'write';
        $r++;
        $this->rights[$r][0] = 700003003;
        $this->rights[$r][1] = 'Supprimer les Immobilier - Location';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'delete';
    }

    public function init($options = ''): int
    {
        $sql = array();
        $result = $this->loadTables();

        if ($result < 0) {
            return -1;
        }

        return $this->init($options);
    }

    public function remove($options = ''): int
    {
        $sql = array();
        return $this->remove($options);
    }
}
