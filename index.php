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

if ($action === 'delete' && $id > 0) {
    $object = new ImmoBail($db);
    if ($object->fetch($id) > 0) { $object->delete($user); setEventMessages('Bail supprime', null, 'mesgs'); }
    header("Location: " . $_SERVER["PHP_SELF"]); exit;
}

llxHeader('', 'Baux locatifs');
print load_fiche_titre('Baux locatifs', '', 'company.png');
print '<div class="tabsAction"><a class="butAction" href="card.php?action=create">Nouveau bail</a></div><br>';

$sql = "SELECT b.rowid, b.ref, b.fk_bien, b.fk_locataire, b.type_bail, b.date_debut, b.date_fin, b.loyer_nu, b.charges, b.statut, bi.label as bien_label, s.nom as locataire_nom";
$sql .= " FROM " . $db->prefix() . "immo_bail b";
$sql .= " LEFT JOIN " . $db->prefix() . "immo_bien bi ON bi.rowid = b.fk_bien";
$sql .= " LEFT JOIN " . $db->prefix() . "societe s ON s.rowid = b.fk_locataire";
$sql .= " ORDER BY b.datec DESC";

$resql = $db->query($sql);
print '<table class="noborder centpercent liste">';
print '<tr class="liste_titre"><th>Ref</th><th>Bien</th><th>Locataire</th><th>Type</th><th>Debut</th><th>Fin</th><th class="right">Loyer</th><th class="right">Charges</th><th>Statut</th><th class="center">Actions</th></tr>';

if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td><a href="card.php?id=' . $obj->rowid . '">' . $obj->ref . '</a></td>';
        print '<td>' . dol_escape_htmltag($obj->bien_label) . '</td>';
        print '<td>' . dol_escape_htmltag($obj->locataire_nom) . '</td>';
        print '<td>' . dol_escape_htmltag($obj->type_bail) . '</td>';
        print '<td>' . dol_print_date($obj->date_debut, 'day') . '</td>';
        print '<td>' . dol_print_date($obj->date_fin, 'day') . '</td>';
        print '<td class="right">' . price($obj->loyer_nu) . '</td>';
        print '<td class="right">' . price($obj->charges) . '</td>';
        print '<td>' . dol_escape_htmltag($obj->statut) . '</td>';
        print '<td class="center">';
        print '<a href="card.php?action=edit&id=' . $obj->rowid . '">' . img_edit() . '</a> ';
        print '<a href="' . $_SERVER["PHP_SELF"] . '?action=delete&id=' . $obj->rowid . '&token=' . newToken() . '" onclick="return confirm(\'Supprimer ce bail ?\')">' . img_delete() . '</a>';
        print '</td></tr>';
    }
}
print '</table>';
llxFooter();
