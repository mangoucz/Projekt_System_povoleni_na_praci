<?php
    require_once 'server.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id_pov'])) {
            $id = $_POST['id_pov'];

            $sql = "SELECT
                        p.ev_cislo,
                        CONCAT(z.jmeno, ' ', z.prijmeni) as 'zadal',
                        prace_na_zarizeni,
                        svarovani_ohen,
                        vstup_zarizeni_teren,
                        prostredi_vybuch,
                        predani_prevzeti_zarizeni,
                        povol_od as od,
                        povol_do as do,
                        popis_prace,
                        odeslano, 
                        upraveno,
                        (select CONCAT(COUNT(prd.id_prodl),'x') from Prodlouzeni as prd where prd.id_pov = p.id_pov) as pocet_prodl,
                        (select MAX(prd.do) from Prodlouzeni as prd where prd.id_pov = p.id_pov) as prodl_do
                    FROM Povolenka AS p JOIN Zamestnanci as z on p.id_zam = z.id_zam
                    WHERE p.id_pov = ?;";
            $params = [$id];
            $result = sqlsrv_query($conn, $sql, $params);

            if ($result === false) {
                echo json_encode([
                    "success" => false,
                    "message" => "Chyba SQL dotazu!",
                    "error" => sqlsrv_errors()
                ]);     
                exit;
            }            
            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

            if ($zaznam) {
                $zaznam["od"] = $zaznam["od"]->format("d.m.Y H:i");
                $zaznam["do"] = $zaznam["do"]->format("d.m.Y H:i");
                $zaznam["prodl_do"] = isset($zaznam["prodl_do"]) ? $zaznam["prodl_do"]->format("d.m.Y H:i") : "-";
                $zaznam["odeslano"] = $zaznam["odeslano"]->format("d.m.Y H:i");
                $zaznam["upraveno"] = isset($zaznam["upraveno"]) ? $zaznam["upraveno"]->format("d.m.Y H:i") : "Ne";
                $zaznam["pocet_prodl"] = $zaznam["pocet_prodl"] != "0x" ? $zaznam["pocet_prodl"] : "Ne";
                
                $povoleni_na = [];
                if ($zaznam["prace_na_zarizeni"] === 1) $povoleni_na[] = "Práce na zařízení";
                if ($zaznam["svarovani_ohen"] === 1) $povoleni_na[] = "Sváření / Práce s otevřeným ohněm";
                if ($zaznam["vstup_zarizeni_teren"] === 1) $povoleni_na[] = "Vstup do zařízení pod úroveň terénu";
                if ($zaznam["prostredi_vybuch"] === 1) $povoleni_na[] = "Prostředí s nebezpečím výbuchu";
                if ($zaznam["predani_prevzeti_zarizeni"] === 1) $povoleni_na[] = "Předání / převzetí zařízení";
                $zaznam["povoleni_na"] = $povoleni_na;

                echo json_encode([
                    "success" => true,
                    "data" => $zaznam
                ]);
            }
            else {
                echo json_encode(["success" => false, "message" => "Záznam nenalezen"]);
            }
            sqlsrv_free_stmt($result);
        }
        else {
            echo json_encode(["success" => false, "message" => "Neplatné ID"]);
        }
    }
?>