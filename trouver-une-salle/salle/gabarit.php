<h1><?= $salle->name ?></h1>
<hr>
<div class="row">
    <div class="col-sm-6">
        <h2>Informations</h2>
        <h3>Composante ou établissement</h3>
        <p>
            <?= $salle->building_group_name ?>
        </p>
        <h3>Bâtiment</h3>
        <p>
            <?= $salle->building_name ?>
        </p>
        <h3>Localisation</h3>
        <p>
            <?= $salle->room_group_name ?>
        </p>
    </div>
    <div class="col-sm-6">
        <h2>Localisation du bâtiment</h2>
        <div class="arrondi ombre text-center" style="height: 50vh; width: 100%">
            <iframe
                    style="border:none;overflow:hidden"
                    width="100%"
                    height="100%"
                    title="Plan Interactif du Campus"
                    src="https://campus.bde-tribu-terre.fr/?buildings=<?php echo $salle->building_id ?>"
            ></iframe>
        </div>
        <small class="form-text text-muted">
            Carte dynamique. Vous pouvez déplacer et zoomer la vue.
        </small>
    </div>
</div>
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <hr>
        <p>
            <a
                    class="button"
                    href="/trouver-une-salle/"
            >
                Rechercher une autre salle
            </a>
        </p>
    </div>
    <div class="col-sm-4"></div>
</div>
