<?php

declare(strict_types=1);

require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';

class ImmoBail extends CommonObject
{
    public $table_element = 'llx_immo_bail';
    public $element = 'immolocatif';

    public int $rowid;
    public string $ref = '';
    public int $fk_user_creat;
    public string $datec = '';
    public string $tms = '';
    public int $status;

    protected array $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'visible' => -1, 'notnull' => 1, 'index' => 1, 'position' => 10, 'comment' => 'Id'),
        'ref' => array('type' => 'varchar(128)', 'label' => 'Ref', 'enabled' => 1, 'visible' => 1, 'notnull' => 1, 'showoncombobox' => 1, 'index' => 1, 'position' => 20, 'searchall' => 1, 'comment' => 'Reference'),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 510, 'foreignkey' => 'user.rowid'),
        'datec' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'visible' => -2, 'position' => 520),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 525),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'visible' => 1, 'notnull' => 1, 'default' => 0, 'index' => 1, 'position' => 1000, 'arrayofkeyval' => array(0 => 'Draft', 1 => 'Validated')),
    );

    public function __construct(DoliDB $db)
    {
        $this->db = $db;
    }

    public function create(User $user, bool $notrigger = false): int
    {
        $this->ref = $this->getNextNumRef();
        return $this->createCommon($user, $notrigger);
    }

    public function fetch(int $id, string $ref = ''): int
    {
        return $this->fetchCommon($id, $ref);
    }

    public function update(User $user, bool $notrigger = false): int
    {
        return $this->updateCommon($user, $notrigger);
    }

    public function delete(User $user, bool $notrigger = false): int
    {
        return $this->deleteCommon($user, $notrigger);
    }

    public function getNextNumRef(): string
    {
        global $conf;
        $prefix = strtoupper($this->element);
        $date = date('Y');
        $num = $this->getMaxNumRef() + 1;
        return sprintf("%s-%s-%04d", $prefix, $date, $num);
    }

    protected function getMaxNumRef(): int
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(ref FROM '-[0-9]+-([0-9]+)$') AS INTEGER)) as maxref";
        $sql .= " FROM ".$this->db->prefix().$this->table_element;
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            return (int) ($obj->maxref ?? 0);
        }
        return 0;
    }
}
