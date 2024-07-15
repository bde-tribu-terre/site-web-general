<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo TITLE ?> | Tribu-Terre</title>

    <!-- Meta tags -->
    <meta property="og:title" content="<?php echo TITLE ?>">
    <meta property="description" content="Tribu-Terre, Association des Étudiants en Sciences de l'Université d'Orléans.">
    <meta property="author" content="Tribu-Terre">

    <!-- Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script   src="https://code.jquery.com/jquery-3.7.1.js"   integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="   crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    <?php if (file_exists(ROOT . "styles/overall-styles")): ?>
        <!-- Feuilles de style générales -->
        <style>
            <?php foreach (scandir(ROOT . "styles/overall-styles") as $style): if (str_ends_with($style, ".min.css")): ?>
                /* <?= $style ?> */
                <?= file_get_contents(ROOT . "styles/overall-styles/" . $style) ?>
            <?php endif; endforeach; ?>
        </style>
    <?php endif; ?>

    <?php if (file_exists(ROOT . "styles/frame-styles")): ?>
        <!-- Feuilles de style du cadre -->
        <style>
            <?php foreach (scandir(ROOT . "styles/frame-styles") as $style): if (str_ends_with($style, ".min.css")): ?>
                /* <?= $style ?> */
                <?= file_get_contents(ROOT . "styles/frame-styles/" . $style) ?>
            <?php endif; endforeach; ?>
        </style>
    <?php endif; ?>

    <?php if (defined("STYLES") && !empty(STYLES)): ?>
        <!-- Feuilles de style du gabarit -->
        <style>
            <?php foreach (STYLES as $style): ?>
                /* <?= $style . ".min.css" ?> */
                <?php if (file_exists(ROOT . "styles/template-styles/" . $style . ".min.css")): ?>
                    <?= file_get_contents(ROOT . "styles/template-styles/" . $style . ".min.css") ?>
                <?php else: ?>
                    /* Impossible de trouver le style de gabarit "<?= $style ?>" */
                <?php endif; ?>
            <?php endforeach; ?>
        </style>
    <?php endif; ?>

    <?php if (file_exists(ROOT . "scripts/overall-scripts")): ?>
        <!-- Scripts généraux -->
        <script type="application/javascript">
            <?php foreach (scandir(ROOT . "scripts/overall-scripts") as $script): if (str_ends_with($script, ".min.js")): ?>
                /* <?= $script ?> */
                <?= file_get_contents(ROOT . "scripts/overall-scripts/" . $script) ?>
            <?php endif; endforeach; ?>
        </script>
    <?php endif; ?>

    <?php if (file_exists(ROOT . "scripts/frame-scripts")): ?>
        <!-- Scripts du cadre -->
        <script type="application/javascript">
            <?php foreach (scandir(ROOT . "scripts/frame-scripts") as $script): if (str_ends_with($script, ".min.js")): ?>
                /* <?= $script ?> */
                <?= file_get_contents(ROOT . "scripts/frame-scripts/" . $script) ?>
            <?php endif; endforeach; ?>
        </script>
    <?php endif; ?>

    <?php if (defined("SCRIPTS") && !empty(SCRIPTS)): ?>
        <!-- Scripts du gabarit -->
        <script type="application/javascript">
            <?php foreach (SCRIPTS as $script): ?>
                /* <?= $script . ".min.js" ?> */
                <?php if (file_exists(ROOT . "scripts/template-scripts/" . $script . ".min.js")): ?>
                    <?= file_get_contents(ROOT . "scripts/template-scripts/" . $script . ".min.js") ?>
                <?php else: ?>
                    /* Impossible de trouver le script de gabarit "<?= $script ?>" */
                <?php endif; ?>
            <?php endforeach; ?>
        </script>
    <?php endif; ?>
</head>
<body>
<header role="banner" style="background-image: url('/resources/jpg/fondJumbotron.jpg');">
    <a href="/">
        <picture>
            <source srcset="/resources/webp/imgLogoMini.webp" type="image/webp">
            <source srcset="/resources/png/imgLogoMini.png" type="image/png">
            <img class="<?= $_SERVER["REQUEST_URI"] == "/" ? "main" : "normal" ?>" src="/resources/png/imgLogoMini.png" alt="Logo de Tribu-Terre">
        </picture>
    </a>
    <p class="<?= $_SERVER["REQUEST_URI"] == "/" ? "main" : "normal" ?>">
        Association des Étudiants en Sciences de l'Université d'Orléans
    </p>
</header>

<nav class="navbar navbar-expand-sm py-0">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigationHeader" aria-label="Menu déroulant">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <div class="navbar-collapse collapse" id="navigationHeader">
        <ul class="nav navbar-nav">
            <li class="nav-item<?= $_SERVER["REQUEST_URI"] == '/' ? ' active' : ''; ?>">
                <a class="nav-link" href="/">
                    <i class="bi bi-house-door-fill" style="font-size: larger"></i>
                    <span class="alterneur-mini">Accueil</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-grid-fill" style="font-size: larger"></i>
                    À propos <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/adherer/' ? ' active' : ''; ?>" href="/association/adherer/">
                            <i class="bi bi-heart-fill" style="font-size: larger"></i>
                            Adhérer
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/federations/' ? ' active' : ''; ?>" href="/association/federations/">
                            <i class="bi bi-bank2" style="font-size: larger"></i>
                            Fédérations
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/statuts/' ? ' active' : ''; ?>" href="/association/statuts/">
                            <i class="bi bi-file-earmark-text-fill" style="font-size: larger"></i>
                            Statuts & R.I.
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/historique/' ? ' active' : ''; ?>" href="/association/historique/">
                            <i class="bi bi-award-fill" style="font-size: larger"></i>
                            Historique
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/contact/' ? ' active' : ''; ?>" href="/association/contact/">
                            <i class="bi bi-envelope-fill" style="font-size: larger"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://discord.gg/EfkUuC2">
                    <i class="bi bi-discord" style="font-size: larger"></i>
                    Serveur Discord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.instagram.com/tribu.terre/">
                    <i class="bi bi-instagram" style="font-size: larger"></i>
                    Instagram
                </a>
            </li>
        </ul>
    </div>
</nav>

<main>
    <?php if (!App\Message::empty()): ?>
        <section class="container" role="alert">
            <h1>Message(s)</h1>
            <hr>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <div class="well">
                        <ul class="text-left">
                            <?php foreach (App\Message::get() as $message): ?>
                                <li><?= $message ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3"></div>
            </div>
        </section>
    <?php endif; ?>

    <section class="container" role="document">
        <?php require GABARIT; ?>
    </section>
</main>

<footer>
    <p>
        Tribu-Terre est une association étudiante apartisane et asyndicale à but non lucratif, régie par la loi du 1er juillet 1901.
    </p>
    <p>
        Tribu-Terre <?= date("Y") ?> | 1A Rue de la Férollerie, 45071, Orléans Cedex 2
    </p>
    <p>
        <small>Développé avec ❤️ par Anaël BARODINE | <a href="https://github.com/bde-tribu-terre/site-web-general" target="_blank">Version <?= VERSION_SITE ?></a></small>
    </p>
</footer>
</body>
</html>
