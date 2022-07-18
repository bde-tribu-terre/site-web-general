<div class="container text-center">
    <h3>Trouver une salle</h3>
    <hr>
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <?php
            echo NOMBRE > 1 ?
                '<h4>' . NOMBRE . ' salles correspondantes ont été trouvées.</h4>' :
                '<h4>Une salle correspondante a été trouvée.</h4>';
            echo SALLES
            ?>
        </div>
        <div class="col-sm-3"></div>
    </div>
    <div class="row" <?php echo NOMBRE > 1 ? 'style="display: none"' : ''; ?>>
        <div class="col-sm-12">
            <h3>Localisation du bâtiment</h3>
            <div class="img-arrondi ombre text-center" style="height: 50vh">
                <iframe
                        style="border:none;overflow:hidden"
                        width="100%"
                        height="100%"
                        title="Plan Interactif du Campus"
                        src="https://campus.bde-tribu-terre.fr/?groupeBatiments=<?php echo CODE_COMPOSANTE ?>&batiment=<?php echo ID_BATIMENT ?>"
                ></iframe>
            </div>
            <small class="form-text text-muted">
                Carte dynamique. Vous pouvez déplacer et zoomer la vue.
            </small>
        </div>
        <!--
        <div class="col-sm-6">
            <h3>Emplacement de la salle</h3>
            <p>
                Bientôt disponible...
            </p>
        </div>
        -->
    </div>
    <hr>
    <h3>Lancer une nouvelle recherche</h3>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <form id="formConnexion" method="get" onsubmit="return verifForm(this);">
                <p> <!-- Nom Salle -->
                    <label style="display: none" for="nom">Nom de la salle</label>
                    <!-- En display none JS prends les retours à la ligne avant et après de l'innerText -->
                    <input
                            id="nom"
                            name="nom"
                            type="text"
                            class="form-control"
                            placeholder="Saisir le nom de la salle"
                            onblur="verifNonVide(this);"
                            oninput="garderMoins(this, 64);"
                    >
                    <small class="form-text text-muted">
                        Telle qu'indiquée sur l'emploi du temps.
                        Ex. : "S110", "Amphi. 3 Sciences", "NG02", "Salle 157".
                    </small>
                </p>
                <hr>
                <p> <!-- Rechercher -->
                    <input
                            type="submit"
                            class="btn btn-var btn-block"
                            value="Lancer la recherche"
                    >
                </p>
            </form>
        </div>
        <div class="col-sm-4"></div>
    </div>
</div>
