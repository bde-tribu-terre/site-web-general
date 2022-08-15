<h1>Journaux</h1>
<hr>
<?php foreach ($journaux as $journal): ?>
    <?php if ($journal->count % 3 == 0): ?><div class="row"><?php endif ?>
        <div class="col-sm-4">
            <div class="well">
                <div class="row">
                    <h2><?= $journal->titre ?></h2>
                </div>
                <div class="row">
                    <img class="miniature" src="<?= "thumbnails/$journal->pdf.webp" ?>" alt="miniatureJournal">
                </div>
                <div class="row">
                    <a href="<?= $journal->pdf ?>" class="btn btn-var btn-block" style="position: relative; z-index: 1;">
                        <span class="alterneur-grand-tres-petit"><img src="/resources/svg/imgPdf.svg" height="28" alt="(PDF)">&emsp;Lire en ligne</span>
                        <span class="alterneur-petit">Lire</span>
                    </a>
                </div>
            </div>
        </div>
    <?php if ($journal->count == count($journaux) - 1 || ($journal->count + 1) % 3 == 0): ?></div><?php endif ?>
<?php endforeach; ?>
