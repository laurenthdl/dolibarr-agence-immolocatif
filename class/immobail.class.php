<?php

declare(strict_types=1);

if (!class_exists('CommonObject')) { require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; }

class ImmoBail extends CommonObject
{
    public $table_element = 'llx_immo_bail';
    public $element = 'immolocatif';

    public $ref;
    public $fk_bien;
    public $fk_locataire;
    public $type_bail;
    public $date_debut;
    public $date_fin;
    public $duree_mois;
    public $loyer_nu;
    public $charges;
    public $taux_tlppu;
    public $caution;
    public $avance;
    public $date_prochaine_indexation;
    public $statut;
    public $fk_user_creat;
    public $datec;
    public $tms;
    public $status;

    protected $fields = array(
        'rowid'=>array('type'=>'integer','label'=>'ID','enabled'=>1,'visible'=>-1,'position'=>10,'notnull'=>1),
        'ref'=>array('type'=>'varchar(128)','label'=>'Ref','enabled'=>1,'visible'=>1,'position'=>20,'notnull'=>1,'searchall'=>1),
        'fk_bien'=>array('type'=>'integer:ImmoBien:custom/immobien/class/immobien.class.php','label'=>'Bien','enabled'=>1,'visible'=>1,'position'=>30,'notnull'=>1),
        'fk_locataire'=>array('type'=>'integer:Societe:societe/class/societe.class.php','label'=>'Locataire','enabled'=>1,'visible'=>1,'position'=>40,'notnull'=>1),
        'type_bail'=>array('type'=>'varchar(32)','label'=>'Type','enabled'=>1,'visible'=>1,'position'=>50),
        'date_debut'=>array('type'=>'date','label'=>'Debut','enabled'=>1,'visible'=>1,'position'=>60,'notnull'=>1),
        'date_fin'=>array('type'=>'date','label'=>'Fin','enabled'=>1,'visible'=>1,'position'=>70),
        'duree_mois'=>array('type'=>'integer','label'=>'Duree','enabled'=>1,'visible'=>1,'position'=>80),
        'loyer_nu'=>array('type'=>'decimal(24,8)','label'=>'Loyer','enabled'=>1,'visible'=>1,'position'=>90,'notnull'=>1),
        'charges'=>array('type'=>'decimal(24,8)','label'=>'Charges','enabled'=>1,'visible'=>1,'position'=>100),
        'taux_tlppu'=>array('type'=>'decimal(5,2)','label'=>'TLPPU %','enabled'=>1,'visible'=>1,'position'=>110),
        'caution'=>array('type'=>'decimal(24,8)','label'=>'Caution','enabled'=>1,'visible'=>1,'position'=>120),
        'avance'=>array('type'=>'integer','label'=>'Avance','enabled'=>1,'visible'=>1,'position'=>130),
        'date_prochaine_indexation'=>array('type'=>'date','label'=>'Indexation','enabled'=>1,'visible'=>1,'position'=>140),
        'statut'=>array('type'=>'varchar(32)','label'=>'Statut','enabled'=>1,'visible'=>1,'position'=>150),
        'fk_user_creat'=>array('type'=>'integer:User:user/class/user.class.php','label'=>'Auteur','enabled'=>1,'visible'=>-2,'position'=>510),
        'datec'=>array('type'=>'datetime','label'=>'DateCreation','enabled'=>1,'visible'=>-2,'position'=>520),
        'tms'=>array('type'=>'timestamp','label'=>'DateModif','enabled'=>1,'visible'=>-2,'position'=>530),
        'status'=>array('type'=>'integer','label'=>'Status','enabled'=>1,'visible'=>1,'position'=>1000,'default'=>0),
    );

    public function __construct(DoliDB $db) { $this->db = $db; }

    public function create(User $user, bool $notrigger = false): int {
        $this->ref = $this->getRefNum();
        $res = $this->createCommon($user, $notrigger);
        if ($res > 0 && $this->statut === 'ACTIF') {
            $this->generateQuittances();
        }
        return $res;
    }

    public function fetch(int $id, string $ref = ''): int { return $this->fetchCommon($id, $ref); }
    public function update(User $user, bool $notrigger = false): int { return $this->updateCommon($user, $notrigger); }
    public function delete(User $user, bool $notrigger = false): int { return $this->deleteCommon($user, $notrigger); }

    public function generateQuittances(): int
    {
        if (empty($this->date_debut) || empty($this->date_fin)) return 0;

        $start = new DateTime($this->date_debut);
        $end = new DateTime($this->date_fin);
        $count = 0;

        while ($start <= $end) {
            $q = new ImmoQuittance($this->db);
            $q->fk_bail = $this->rowid;
            $q->periode_annee = (int)$start->format('Y');
            $q->periode_mois = (int)$start->format('n');
            $q->loyer_nu = $this->loyer_nu;
            $q->charges = $this->charges;
            $q->tlppu = $this->calculTLPPU();
            $q->total_du = $q->loyer_nu + $q->charges + $q->tlppu;
            $q->statut = 'EMISE';
            $q->create($this->db);
            $count++;
            $start->modify('+1 month');
        }
        return $count;
    }

    public function calculTLPPU(): float
    {
        return round(($this->loyer_nu * 12 * (float)$this->taux_tlppu / 100) / 12, 2);
    }

    public function getQuittances(): array
    {
        $quittances = [];
        $sql = "SELECT * FROM " . $this->db->prefix() . "immo_quittance WHERE fk_bail = " . (int)$this->rowid . " ORDER BY periode_annee, periode_mois";
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $quittances[] = $obj;
            }
        }
        return $quittances;
    }

    protected function getRefNum(): string
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(ref FROM '.*-([0-9]+)$') AS INTEGER)) as maxref FROM " . $this->db->prefix() . $this->table_element;
        $resql = $this->db->query($sql);
        $num = ($resql && ($obj = $this->db->fetch_object($resql))) ? ((int)$obj->maxref + 1) : 1;
        return 'BL' . date('Y') . '-' . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}

class ImmoQuittance extends CommonObject
{
    public $table_element = 'llx_immo_quittance';
    public $element = 'immoquittance';

    public $ref;
    public $fk_bail;
    public $periode_annee;
    public $periode_mois;
    public $loyer_nu;
    public $charges;
    public $tlppu;
    public $total_du;
    public $montant_paye;
    public $solde;
    public $date_paiement;
    public $mode_paiement;
    public $statut;
    public $fk_user_creat;
    public $datec;
    public $tms;
    public $status;

    protected $fields = array(
        'rowid'=>array('type'=>'integer','label'=>'ID','enabled'=>1,'visible'=>-1,'position'=>10,'notnull'=>1),
        'ref'=>array('type'=>'varchar(128)','label'=>'Ref','enabled'=>1,'visible'=>1,'position'=>20,'notnull'=>1),
        'fk_bail'=>array('type'=>'integer:ImmoBail:custom/immolocatif/class/immobail.class.php','label'=>'Bail','enabled'=>1,'visible'=>1,'position'=>30,'notnull'=>1),
        'periode_annee'=>array('type'=>'integer','label'=>'Annee','enabled'=>1,'visible'=>1,'position'=>40,'notnull'=>1),
        'periode_mois'=>array('type'=>'integer','label'=>'Mois','enabled'=>1,'visible'=>1,'position'=>50,'notnull'=>1),
        'loyer_nu'=>array('type'=>'decimal(24,8)','label'=>'Loyer','enabled'=>1,'visible'=>1,'position'=>60),
        'charges'=>array('type'=>'decimal(24,8)','label'=>'Charges','enabled'=>1,'visible'=>1,'position'=>70),
        'tlppu'=>array('type'=>'decimal(24,8)','label'=>'TLPPU','enabled'=>1,'visible'=>1,'position'=>80),
        'total_du'=>array('type'=>'decimal(24,8)','label'=>'Total','enabled'=>1,'visible'=>1,'position'=>90),
        'montant_paye'=>array('type'=>'decimal(24,8)','label'=>'Paye','enabled'=>1,'visible'=>1,'position'=>100),
        'solde'=>array('type'=>'decimal(24,8)','label'=>'Solde','enabled'=>1,'visible'=>1,'position'=>110),
        'date_paiement'=>array('type'=>'date','label'=>'Date paiement','enabled'=>1,'visible'=>1,'position'=>120),
        'mode_paiement'=>array('type'=>'varchar(32)','label'=>'Mode','enabled'=>1,'visible'=>1,'position'=>130),
        'statut'=>array('type'=>'varchar(32)','label'=>'Statut','enabled'=>1,'visible'=>1,'position'=>140),
        'fk_user_creat'=>array('type'=>'integer:User:user/class/user.class.php','label'=>'Auteur','enabled'=>1,'visible'=>-2,'position'=>510),
        'datec'=>array('type'=>'datetime','label'=>'DateCreation','enabled'=>1,'visible'=>-2,'position'=>520),
        'tms'=>array('type'=>'timestamp','label'=>'DateModif','enabled'=>1,'visible'=>-2,'position'=>530),
        'status'=>array('type'=>'integer','label'=>'Status','enabled'=>1,'visible'=>1,'position'=>1000,'default'=>0),
    );

    public function __construct(DoliDB $db) { $this->db = $db; }

    public function create(User $user, bool $notrigger = false): int {
        $this->ref = $this->getRefNum();
        return $this->createCommon($user, $notrigger);
    }

    protected function getRefNum(): string
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(ref FROM '.*-([0-9]+)$') AS INTEGER)) as maxref FROM " . $this->db->prefix() . $this->table_element;
        $resql = $this->db->query($sql);
        $num = ($resql && ($obj = $this->db->fetch_object($resql))) ? ((int)$obj->maxref + 1) : 1;
        return 'Q' . date('Y') . '-' . str_pad((string)$num, 5, '0', STR_PAD_LEFT);
    }
}
