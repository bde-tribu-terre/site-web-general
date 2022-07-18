<div class="container text-center">
    <h3>Journaux</h3>
    <hr>
    <?php foreach ($journaux as $journal): ?>
        <?php if ($journal->count % 4 == 0): ?><div class="row"><?php endif ?>
            <div class="col-sm-3">
                <div class="well">
                    <h3><?= $journal->titre ?></h3>
                    <time datetime="<?= $journal->date ?>"><?= preg_replace('/^[^ ]* /', '', genererDate($journal->date)) ?></time>
                    <a href="<?= $journal->pdf ?>" class="btn btn-var btn-block">
                        <h4 class="alterneur-grand-tres-petit"><img src="/resources/imgPdf.svg" height="28" alt="(PDF)">&emsp;Lire en ligne</h4>
                        <h4 class="alterneur-petit">Lire</h4>
                    </a>
                </div>
            </div>
        <?php if ($journal->count == count($journaux) - 1 || ($journal->count + 1) % 4 == 0): ?></div><?php endif ?>
    <?php endforeach; ?>
</div>
