CREATE TABLE IF NOT EXISTS llx_immo_bail (
    rowid SERIAL PRIMARY KEY,
    ref VARCHAR(128) NOT NULL UNIQUE,
    fk_bien INTEGER NOT NULL,
    fk_locataire INTEGER NOT NULL,
    type_bail VARCHAR(32) DEFAULT 'RESIDENTIEL_VIDE',
    date_debut DATE NOT NULL,
    date_fin DATE,
    duree_mois INTEGER,
    loyer_nu DECIMAL(24,8) NOT NULL DEFAULT 0,
    charges DECIMAL(24,8) DEFAULT 0,
    taux_tlppu DECIMAL(5,2) DEFAULT 15.00,
    caution DECIMAL(24,8) DEFAULT 0,
    avance INTEGER DEFAULT 1,
    date_prochaine_indexation DATE,
    statut VARCHAR(32) DEFAULT 'BROUILLON',
    fk_user_creat INTEGER NOT NULL,
    fk_user_modif INTEGER,
    datec TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_immo_bail_ref ON llx_immo_bail(ref);
CREATE INDEX IF NOT EXISTS idx_immo_bail_fk_bien ON llx_immo_bail(fk_bien);
CREATE INDEX IF NOT EXISTS idx_immo_bail_fk_locataire ON llx_immo_bail(fk_locataire);
CREATE INDEX IF NOT EXISTS idx_immo_bail_statut ON llx_immo_bail(statut);

-- Table quittances
CREATE TABLE IF NOT EXISTS llx_immo_quittance (
    rowid SERIAL PRIMARY KEY,
    ref VARCHAR(128) NOT NULL UNIQUE,
    fk_bail INTEGER NOT NULL,
    periode_annee INTEGER NOT NULL,
    periode_mois INTEGER NOT NULL,
    loyer_nu DECIMAL(24,8) DEFAULT 0,
    charges DECIMAL(24,8) DEFAULT 0,
    tlppu DECIMAL(24,8) DEFAULT 0,
    total_du DECIMAL(24,8) DEFAULT 0,
    montant_paye DECIMAL(24,8) DEFAULT 0,
    solde DECIMAL(24,8) DEFAULT 0,
    date_paiement DATE,
    mode_paiement VARCHAR(32),
    statut VARCHAR(32) DEFAULT 'BROUILLON',
    fk_user_creat INTEGER NOT NULL,
    datec TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_immo_quittance_fk_bail ON llx_immo_quittance(fk_bail);
CREATE INDEX IF NOT EXISTS idx_immo_quittance_periode ON llx_immo_quittance(periode_annee, periode_mois);

-- Fonction calcul TLPPU
CREATE OR REPLACE FUNCTION calc_tlppu(loyer DECIMAL, taux DECIMAL)
RETURNS DECIMAL AS $$
BEGIN
    RETURN ROUND((loyer * 12 * taux / 100) / 12, 2);
END;
$$ LANGUAGE plpgsql;

-- Trigger auto-update tms
CREATE OR REPLACE FUNCTION update_tms()
RETURNS TRIGGER AS $$ BEGIN NEW.tms = CURRENT_TIMESTAMP; RETURN NEW; END; $$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_immo_bail_tms ON llx_immo_bail;
CREATE TRIGGER trg_immo_bail_tms BEFORE UPDATE ON llx_immo_bail FOR EACH ROW EXECUTE FUNCTION update_tms();

DROP TRIGGER IF EXISTS trg_immo_quittance_tms ON llx_immo_quittance;
CREATE TRIGGER trg_immo_quittance_tms BEFORE UPDATE ON llx_immo_quittance FOR EACH ROW EXECUTE FUNCTION update_tms();
