<?php
declare(strict_types=1);
require_once __DIR__ . '/../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/immobien/class/immobien.class.php';
require_once __DIR__ . '/class/immobail.class.php';

$langs->load("immolocatif@immolocatif");
$form = new Form($db);

$action = GETPOST('action', 'aZ09');
$id = GETPOST('id', 'int');
$tab = GETPOST('tab', 'alpha') ?: 'informations';

$object = new ImmoBail($db);

// CREATE
if ($action === 'create' && !empty($_POST['fk_bien'])) {
    $object->fk_bien = GETPOST('fk_bien', 'int');
    $object->fk_locataire = GETPOST('fk_locataire', 'int');
    $object->type_bail = GETPOST('type_bail', 'alpha');
    $object->date_debut = GETPOST('date_debut', 'alpha');
    $object->date_fin = GETPOST('date_fin', 'alpha');
    $object->loyer_nu = GETPOST('loyer_nu', 'alpha');
    $object->charges = GETPOST('charges', 'alpha');
    $object->taux_tlppu = GETPOST('taux_tlppu', 'alpha') ?: 15;
    $object->caution = GETPOST('caution', 'alpha');
    $object->avance = GETPOST('avance', 'int');
    $object->statut = 'ACTIF';
    $object->status = 1;
    $res = $object->create($user);
    if ($res > 0) {
        setEventMessages('Bail cree : ' . $object->ref . ' (' . $object->generateQuittances() . ' quittances generees)', null, 'mesgs');
        header("Location: card.php?id=" . $object->rowid); exit;
    } else {
        setEventMessages($object->error, null, 'errors');
    }
}

// UPDATE
if ($action === 'update' && $id > 0) {
    if ($object->fetch($id) > 0) {
        $object->fk_bien = GETPOST('fk_bien', 'int');
        $object->fk_locataire = GETPOST('fk_locataire', 'int');
        $object->type_bail = GETPOST('type_bail', 'alpha');
        $object->date_debut = GETPOST('date_debut', 'alpha');
        $object->date_fin = GETPOST('date_fin', 'alpha');
        $object->loyer_nu = GETPOST('loyer_nu', 'alpha');
        $object->charges = GETPOST('charges', 'alpha');
        $object->taux_tlppu = GETPOST('taux_tlppu', 'alpha');
        $object->caution = GETPOST('caution', 'alpha');
        $object->avance = GETPOST('avance', 'int');
        $res = $object->update($user);
        if ($res > 0) { setEventMessages('Modifications enregistrees', null, 'mesgs'); header("Location: card.php?id=" . $id); exit; }
    }
}

// PAIEMENT QUITTANCE
if ($action === 'pay_quittance' && $id > 0) {
    $qid = GETPOST('qid', 'int');
    $q = new ImmoQuittance($db);
    if ($q->fetch($qid) > 0) {
        $q->montant_paye = GETPOST('montant_paye', 'alpha');
        $q->date_paiement = GETPOST('date_paiement', 'alpha');
        $q->mode_paiement = GETPOST('mode_paiement', 'alpha');
        $q->solde = $q->total_du - $q->montant_paye;
        $q->statut = ($q->solde <= 0) ? 'PAYEE' : 'PARTIEL';
        $q->update($user);
        setEventMessages('Paiement enregistre', null, 'mesgs');
    }
    header("Location: card.php?id=" . $id . '&tab=quittances'); exit;
}

if ($id > 0) $object->fetch($id);

$title = ($action === 'create') ? 'Nouveau bail' : (($action === 'edit') ? 'Modifier bail' : 'Fiche bail');
llxHeader('', $title);
print load_fiche_titre($title, '', 'company.png');

// Formulaire create/edit
if ($action === 'create' || $action === 'edit') {
    print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    print '<input type="hidden" name="token" value="' . newToken() . '">';
    if ($action === 'edit') print '<input type="hidden" name="id" value="' . $id . '">';
    print '<input type="hidden" name="action" value="' . ($action === 'create' ? 'create' : 'update') . '">';
    print '<table class="border centpercent">';
    print '<tr><td class="fieldrequired">Bien</td><td>' . $form->selectarray('fk_bien', [], $object->fk_bien ?? '') . '</td></tr>';
    print '<tr><td class="fieldrequired">Locataire</td><td>' . $form->select_company($object->fk_locataire ?? '', 'fk_locataire', 's.client IN (1,2,3)', 0, 1, 0, []) . '</td></tr>';
    print '<tr><td>Type de bail</td><td><select name="type_bail"><option value="RESIDENTIEL_VIDE"' . (($object->type_bail??'')=='RESIDENTIEL_VIDE'?' selected':'') . '>Residentiel vide</option><option value="RESIDENTIEL_MEUBLE"' . (($object->type_bail??'')=='RESIDENTIEL_MEUBLE'?' selected':'') . '>Residentiel meuble</option><option value="COMMERCIAL"' . (($object->type_bail??'')=='COMMERCIAL'?' selected':'') . '>Commercial</option></select></td></tr>';
    print '<tr><td class="fieldrequired">Date debut</td><td>' . $form->selectDate(strtotime($object->date_debut ?? ''), 'date_debut', 0, 0, 1, '', 1, 1) . '</td></tr>';
    print '<tr><td>Date fin</td><td>' . $form->selectDate(strtotime($object->date_fin ?? ''), 'date_fin', 0, 0, 1, '', 1, 1) . '</td></tr>';
    print '<tr><td class="fieldrequired">Loyer nu (FCFA)</td><td><input name="loyer_nu" value="' . ($object->loyer_nu ?? '') . '"></td></tr>';
    print '<tr><td>Charges (FCFA)</td><td><input name="charges" value="' . ($object->charges ?? '') . '"></td></tr>';
    print '<tr><td>Taux TLPPU (%)</td><td><input name="taux_tlppu" value="' . ($object->taux_tlppu ?? '15') . '"></td></tr>';
    print '<tr><td>Caution (FCFA)</td><td><input name="caution" value="' . ($object->caution ?? '') . '"></td></tr>';
    print '<tr><td>Avance (mois)</td><td><input name="avance" value="' . ($object->avance ?? '1') . '"></td></tr>';
    print '</table>';
    print '<div class="center"><input type="submit" class="button" value="Enregistrer"> <a class="butActionDelete" href="index.php">Annuler</a></div>';
    print '</form>';
} else {
    // FICHE
    print '<div class="fichecenter">';
    print '<div class="fichehalfleft">';
    print '<table class="border centpercent">';
    print '<tr><td class="titlefield">Ref</td><td>' . dol_escape_htmltag($object->ref) . '</td></tr>';
    print '<tr><td>Bien</td><td>' . ($object->fk_bien > 0 ? '<a href="' . DOL_URL_ROOT . '/custom/immobien/card.php?id=' . $object->fk_bien . '">Bien ' . $object->fk_bien . '</a>' : '') . '</td></tr>';
    print '<tr><td>Locataire</td><td>' . ($object->fk_locataire > 0 ? '<a href="' . DOL_URL_ROOT . '/societe/card.php?socid=' . $object->fk_locataire . '">Locataire ' . $object->fk_locataire . '</a>' : '') . '</td></tr>';
    print '<tr><td>Type</td><td>' . dol_escape_htmltag($object->type_bail) . '</td></tr>';
    print '<tr><td>Periode</td><td>' . dol_print_date($object->date_debut, 'day') . ' - ' . dol_print_date($object->date_fin, 'day') . '</td></tr>';
    print '<tr><td>Loyer nu</td><td>' . price($object->loyer_nu) . ' FCFA</td></tr>';
    print '<tr><td>Charges</td><td>' . price($object->charges) . ' FCFA</td></tr>';
    print '<tr><td>TLPPU (' . $object->taux_tlppu . '%)</td><td>' . price($object->calculTLPPU()) . ' FCFA</td></tr>';
    print '<tr><td>Total mensuel</td><td>' . price($object->loyer_nu + $object->charges + $object->calculTLPPU()) . ' FCFA</td></tr>';
    print '<tr><td>Caution</td><td>' . price($object->caution) . ' FCFA</td></tr>';
    print '<tr><td>Avance</td><td>' . ($object->avance ?? 0) . ' mois</td></tr>';
    print '</table>';
    print '</div>';
    print '</div>';

    // Onglets
    print '<div class="tabsAction">';
    print '<a class="butAction" href="card.php?action=edit&id=' . $id . '">Modifier</a>';
    print '<a class="butAction" href="index.php">Retour liste</a>';
    print '</div>';

    print '<br><br>';
    print '<h2>Quittances</h2>';

    $quittances = $object->getQuittances();
    if (empty($quittances)) {
        print '<div class="info">Aucune quittance generee.</div>';
    } else {
        print '<table class="noborder centpercent liste">';
        print '<tr class="liste_titre"><th>Ref</th><th>Periode</th><th>Loyer</th><th>Charges</th><th>TLPPU</th><th>Total du</th><th>Paye</th><th>Solde</th><th>Statut</th><th class="center">Actions</th></tr>';
        foreach ($quittances as $q) {
            print '<tr class="oddeven">';
            print '<td>' . $q->ref . '</td>';
            print '<td>' . str_pad((string)$q->periode_mois, 2, '0', STR_PAD_LEFT) . '/' . $q->periode_annee . '</td>';
            print '<td class="right">' . price($q->loyer_nu) . '</td>';
            print '<td class="right">' . price($q->charges) . '</td>';
            print '<td class="right">' . price($q->tlppu) . '</td>';
            print '<td class="right">' . price($q->total_du) . '</td>';
            print '<td class="right">' . price($q->montant_paye) . '</td>';
            print '<td class="right">' . price($q->solde) . '</td>';
            print '<td>' . $q->statut . '</td>';
            print '<td class="center">';
            if ($q->statut !== 'PAYEE') {
                print '<a href="card.php?id=' . $id . '&action=pay_quittance&qid=' . $q->rowid . '" class="butAction" style="padding:4px 8px">Payer</a>';
            }
            print '</td></tr>';
        }
        print '</table>';
    }
}

llxFooter();
