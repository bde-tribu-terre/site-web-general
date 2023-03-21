<h1>Journaux</h1>
<hr>
<?php foreach ($journaux as $journal): ?>
    <?php if ($journal->count % 3 == 0): ?><div class="row"><?php endif ?>
        <div class="col-sm-4">
            <div class="well">
                <div class="pdf-viewer">
                    <h2><?= $journal->titre ?></h2>
                    <img src="<?= "thumbnails/$journal->pdf.jpg" ?>" alt="Miniature du journal <?= $journal->titre ?>">
                    <a class="button" href="<?= $journal->pdf ?>">
                        <span>
                            <img src="/resources/svg/imgPdf.svg" alt="PDF">
                            &emsp;Lire en ligne
                        </span>
                        <span>
                            Lire
                        </span>
                    </a>
                </div>
            </div>
        </div>
    <?php if ($journal->count == count($journaux) - 1 || ($journal->count + 1) % 3 == 0): ?></div><?php endif ?>
<?php endforeach; ?>
