<h1>Trouver une salle</h1>
<hr>
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <p>
            <?= count($salles) ?> salles correspondantes ont été trouvées.
        </p>
        <hr>
    </div>
    <div class="col-sm-4"></div>
</div>
<div class="row">
    <?php foreach ($salles as $count => $salle): ?>
        <?php if ($count % 4 == 0): ?>
            <div class="row">
        <?php endif ?>
            <div class="col-sm-3">
                <div class="well">
                    <div class="salle">
                        <h2><?= $salle->name ?></h2>
                        <p>
                            <?= $salle->building_group_name ?>
                        </p>
                        <p>
                            <?= $salle->building_name ?>
                        </p>
                        <p>
                            <?= $salle->room_group_name ?>
                        </p>
                        <a href="/trouver-une-salle/salle/?id=<?= $salle->room_id ?>" class="btn btn-var btn-block">
                            Plus d'informations
                        </a>
                    </div>
                </div>
            </div>
        <?php if ($count == count($salles) - 1 || ($count + 1) % 4 == 0): ?>
            </div>
        <?php endif ?>
    <?php endforeach; ?>
</div>
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <hr>
        <p>
            <a
                    class="btn btn-var btn-block"
                    href="/trouver-une-salle/"
            >
                Rechercher une autre salle
            </a>
        </p>
    </div>
    <div class="col-sm-4"></div>
</div>
