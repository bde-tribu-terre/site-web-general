<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <h3 class="text-center">L'équipe actuelle !</h3>
            <a href="/association">
                <img
                    class="arrondi ombre"
                    src="resources/jpg/imgAG.jpg"
                    alt="Image d'accueil"
                    style="width: 100%; min-height: 200px;"
                >
            </a>
        </div>
        <div class="col-sm-4">
            <h3 class="text-center">Les derniers journaux !</h3>
            <?php foreach ($journaux as $journal): ?>
            <div class="well text-center">
                <h4><?= $journal->titre ?></h4>
                <time datetime="<?= $journal->date ?>"><?= preg_replace('/^[^ ]* /', '', genererDate($journal->date)) ?></time>
                <a href="/journaux/<?= $journal->pdf ?>" class="btn btn-var btn-block">
                    <span class="alterneur-grand-tres-petit"><img src="/resources/svg/imgPdf.svg" height="28" alt="(PDF)">&emsp;Lire en ligne</span>
                    <span class="alterneur-petit">Lire</span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
