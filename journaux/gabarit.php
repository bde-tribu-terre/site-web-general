<div class="container text-center">
    <h1>Journaux</h1>
    <hr>
    <?php foreach ($journaux as $journal): ?>
        <?php if ($journal->count % 3 == 0): ?><div class="row"><?php endif ?>
            <div class="col-sm-4">
                <div class="well">
                    <div class="row">
                        <h3><?= $journal->titre ?></h3>
                    </div>
                    <div class="row">
                        <time datetime="<?= $journal->date ?>"><?= preg_replace('/^[^ ]* /', '', genererDate($journal->date)) ?></time>
                    </div>
                    <div class="row">
                        <img class="miniature" src="<?= "thumbnails/$journal->pdf.webp" ?>" alt="miniatureJournal">
                    </div>
                    <div class="row">
                        <a href="<?= $journal->pdf ?>" class="btn btn-var btn-block" style="position: relative; z-index: 1;">
                            <h4 class="alterneur-grand-tres-petit"><img src="/resources/svg/imgPdf.svg" height="28" alt="(PDF)">&emsp;Lire en ligne</h4>
                            <h4 class="alterneur-petit">Lire</h4>
                        </a>
                    </div>
                </div>
            </div>
        <?php if ($journal->count == count($journaux) - 1 || ($journal->count + 1) % 3 == 0): ?></div><?php endif ?>
    <?php endforeach; ?>
</div>
