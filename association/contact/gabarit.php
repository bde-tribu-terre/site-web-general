<h1>Contact</h1>
<hr>
<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="bloc-paragraphes">
            <p>
                Vous découvrez Tribu-Terre et voulez en savoir plus ? Vous avez simplement une question à poser à votre
                BDE ? Vous pouvez nous contacter par courriel !
            </p>

        </div>
    </div>
    <div class="col-sm-2"></div>
</div>
<hr>
<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <div class="well">
            <h3>Adresse générale</h3>
            <hr>
            <div id="generale_div" style="height: 50px; word-wrap: break-word;">
                <p id="generale_base64">
                    <strong><?= base64_encode('contact@bde-tribu-terre.fr') ?></strong>
                </p>
            </div>
            <button
                    id="generale_button"
                    class="button"
                    onclick="decoder('generale', 'h4');"
            >
                Cliquer pour décrypter
            </button>
        </div>
    </div>
    <div class="col-sm-3"></div>
</div>
