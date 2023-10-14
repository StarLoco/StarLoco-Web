<style>
    /* Style pour la date en haut à droite */
    .news-date {
        position: absolute;
        top: 4px; /* Ajustez la position verticale comme nécessaire */
        right: 10px; /* Ajustez la position horizontale comme nécessaire */
    }

    /* Style pour le conteneur de la date */
    .date-box {
        background-color: rgba(0, 0, 0, 0.7); /* Couleur de fond noir semi-transparente */
        color: #fff; /* Couleur du texte */
        text-align: center;
        padding: 5px;
        border-radius: 5px;
    }

    /* Style pour le jour */
    .day {
        font-size: 16px; /* Ajustez la taille de la police comme nécessaire */
        font-weight: bold;
    }

    /* Style pour le mois */
    .month {
        font-size: 12px; /* Ajustez la taille de la police comme nécessaire */
    }

    /* Style pour l'année */
    .year {
        font-size: 12px; /* Ajustez la taille de la police comme nécessaire */
    }
</style>
<div class="bxslider-wrapper">
    <div id="bx-tabs">
        <div class="bx-nav pull-left col-md-3 no-padding"><a data-slide-index="0" href="#"><div class="bx-section"><h3>Bienvenue</h3></div></a></div>
        <div class="bx-nav pull-left col-md-3 no-padding"><a data-slide-index="1" href="#"><div class="bx-section"><h3>L'IA parmi nous..</h3></div></a></div>
        <div class="bx-nav pull-left col-md-3 no-padding"><a data-slide-index="2" href="#"><div class="bx-section"><h3>Les fêtes s'annoncent joyeuses</h3></div></a></div>
        <div class="bx-nav pull-left col-md-3 no-padding"><a data-slide-index="3" href="#"><div class="bx-section"><h3>L'île de Noël ouverte</h3></div></a></div>
    </div>
    <div class="bxslider">
        <div class="img">
            <img src="img/carousel/1.jpg" alt="" />
            <a href="#">
                <div class="caption">
                    <h2 class="animated fadeInLeft">Bienvenue Sur</h2>
                    <div class="clearfix"></div>
                    <h1 class="animated fadeInRight">Starloco</h1>
                    <div class="clearfix"></div>
                    <p class="animated fadeInLeft">En savoir +</p>
                </div>
            </a>
        </div>
        <div class="img">
            <img src="img/carousel/2.jpg" alt="" />
            <a href="#">
                <div class="caption">
                    <h2>L'IA parmi nous..</h2>
                    <div class="clearfix"></div>
                    <h1>Nouvelle IA en approche</h1>
                    <div class="clearfix"></div>
                    <p>En savoir +</p>
                </div>
            </a>
        </div>
        <div class="img">
            <img src="img/carousel/3.jpg" alt="" />
            <a href="#">
                <div class="caption">
                    <h2>Fiesta</h2>
                    <div class="clearfix"></div>
                    <h1>Fiesta BOOM BOOM</h1>
                    <div class="clearfix"></div>
                    <p>En savoir +</p>
                </div>
            </a>
        </div>
        <div class="img">
            <img src="img/carousel/4.jpg" alt="" />
            <a href="#">
                <div class="caption">
                    <h2>NONOWEL</h2>
                    <div class="clearfix"></div>
                    <h1>Terre en vue!</h1>
                    <div class="clearfix"></div>
                    <p>En savoir +</p>
                </div>
            </a>
        </div>
    </div>
    <div class="bx-controls-direction"></div>
    <div id="bx-tabs">
        <div class="bx-nav">
            <a data-slide-index="0" href="#"></a>
            <a data-slide-index="1" href="#"></a>
            <a data-slide-index="2" href="#"></a>
            <a data-slide-index="3" href="#"></a>
        </div>
    </div>
</div>
<div class="leftside">
    <ul class="section-title no-margin-top">
        <li>
        <h3 style="font-family: Arial, sans-sherif!important;"><img src="./img/icon_news/news.png"/> <?php echo $translations["MOTS_088"]; ?></h3>
        </li>
    </ul>
    <div style="width:100%!important;" class="col-md-9 col-xs-12">
        <div class="row">
            <div class="col-md-12">
                <ul class="timeline">
                    <?php
                   // ini_set('display_errors', 1);
                   // ini_set('display_startup_errors', 1);
                   // error_reporting(E_ALL);

                    $configFile = 'configuration/configuration.php';
                    require_once($configFile);

                    $i = 0;
                    $query = $connection->prepare('SELECT COUNT(*) FROM `website_timeline_news`;');
                    $query->execute();
                    $row = $query->fetch();
                    $query->closeCursor();

                    $moyenne = ceil($row['COUNT(*)'] / 5);

                    if (isset($_GET['num']) && is_numeric($_GET['num']))
                        $page = $_GET['num'];
                    else
                        $page = 1;

                    $start = ($page - 1) * 5;
                    $query = $connection->query("SELECT * FROM `website_timeline_news` ORDER BY id DESC LIMIT $start, 5;");
                    $query->execute();
                    $query->setFetchMode(PDO::FETCH_OBJ);

                    $locale = 'fr_FR';
                    $timezone = 'Europe/Paris';
                    date_default_timezone_set($timezone);
                    $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);

                    while ($news = $query->fetch()) {
                        $dateVisibilite = new DateTime($news->visibilite);

                        // Comparer la date actuelle avec la date de visibilité
                        if (new DateTime() >= $dateVisibilite) {
                            ?>
                            <li <?php if ($i % 2) echo 'class="timeline-inverted"'; ?>>
                                <div class="timeline-badge primary"></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="padding-5"><img src="./img/icon_news/<?php echo $news->icon; ?>.png" alt="News" style="width: 25px; height: auto;"; /> <?php echo $news->title; ?></a></h4>
                                        <?php
                                        if (!empty($news->img))
                                            echo '<img class="img-responsive full-width" src="./img/news/' . $news->img . '" alt="" />';
                                        ?>

                                    </div>
                                    <div class="timeline-body">
                                        <p><?php echo $news->content; ?></p>
                                    </div>
                                    <div class="timeline-footer">
                                        <div class="news-date">
                                            <div class="date-box">
                                                <div class="day">
                                                    <?php
                                                    $formattedDay = $dateVisibilite->format('d'); // Jour (par exemple, "01")
                                                    echo $formattedDay;
                                                    ?>
                                                </div>
                                                <div class="month">
                                                    <?php
                                                    $formattedMonth = $dateVisibilite->format('M'); // Mois (par exemple, "Jan")
                                                    echo $formattedMonth;
                                                    ?>
                                                </div>
                                                <!--<div class="year">
                                                    <?php
/*                                                    $year = $dateVisibilite->format('Y'); // Année (par exemple, "2023")
                                                    echo $year;
                                                    */?>
                                                </div>-->
                                            </div>
                                        </div>
                                        <?php
                                            $queryCommentCount = $web->prepare('SELECT COUNT(*) AS comment_count FROM website_timeline_news_comments WHERE article_id = ?');
                                            $queryCommentCount->bindParam(1, $news->id, PDO::PARAM_INT);
                                            $queryCommentCount->execute();
                                            $commentCount = $queryCommentCount->fetch(PDO::FETCH_OBJ)->comment_count;
                                        ?>
                                        <a class="pull-left"><i class="ion-android-forums"></i><?php echo $commentCount; ?></a>
                                        <!--                                        <a class="pull-right" href="--><?php //echo $news->url; ?><!--" target="_blank">--><?php //echo $translations["MOTS_089"]; ?><!--</a>-->
                                        <a class="pull-right" href="?page=article&id=<?php echo $news->id; ?>"><?php echo $translations["MOTS_089"]; ?></a>

                                    </div>
                                    <br>
                                </div>
                            </li>
                            <?php
                        }

                        $i++;
                    }
                    ?>
                    <li class="clearfix" style="float: none;"></li>
                </ul>
                <center>
                    <div class="btn-group">
                        <a href="?num=1" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>

                        <?php if ($page > 1) : ?>
                            <a href="?num=<?php echo ($page - 1); ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                        <?php endif; ?>

                        <?php if ($page > 4) : ?>
                            <span class="btn btn-sm btn-default">...</span>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 1); $i <= min($page + 1, $moyenne); $i++) : ?>
                            <a href="?num=<?php echo $i; ?>" class="btn btn-sm <?php echo ($i == $page) ? 'btn-primary' : 'btn-default'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $moyenne - 3) : ?>
                            <span class="btn btn-sm btn-default">...</span>
                        <?php endif; ?>

                        <?php if ($page < $moyenne - 1) : ?>
                            <a href="?num=<?php echo $moyenne; ?>" class="btn btn-sm btn-default"><?php echo $moyenne; ?></a>
                        <?php endif; ?>

                        <?php if ($page < $moyenne) : ?>
                            <a href="?num=<?php echo ($page + 1); ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                        <?php endif; ?>

                        <?php if ($page < $moyenne) : ?>
                            <a href="?num=<?php echo $moyenne; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                        <?php endif; ?>
                    </div>
                </center>
            </div>
        </div>
    </div>
</div>
<!-- ./leftside -->