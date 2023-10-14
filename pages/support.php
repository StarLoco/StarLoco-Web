<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_002"]; ?></li>
    </ol>
    <style>
        /* Styles for open and closed tickets */
        .reponse-open {
            color: green; /* Change the color as desired */
            /* You can also add additional styles here */
        }

        .reponse-closed {
            color: red; /* Change the color as desired */
            /* You can also add additional styles here */
        }
    </style>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        echo "<script>window.location.replace(\"?page=signin\")</script>";
        exit();
    }
	
    // Vérifier si la connexion est autorisée
    $query = $web->prepare("SELECT support FROM website_general;");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $supportAllowed = $result['support'];
    $query->closeCursor();
	
	 if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Assurez-vous que le formulaire est valide et que les données sont correctes
    $subject = $_POST['subject'];
    $content = $_POST['content'];
    $account = $_SESSION['user'];
    $guid = $_SESSION['id'];
    $date = date('Y-m-d H:i:s');

    $query = $web->prepare("INSERT INTO website_ticket_site (guid, account, subject, content, date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $query->execute([$guid, $account, $subject, $content, $date, 'En cours']);

        // Message de succès
        $_SESSION['ticket_success_message'] = "Le ticket a été envoyé avec succès !";
        echo "<script>window.location.href = '?page=support';</script>";

    }
	
    ?>

    <div class="page-header margin-top-10">
        <h4><?php echo $translations["MOTS_002"]; ?></h4>
    </div>

    <?php
    // Afficher un message si le support est désactivé
    if ($supportAllowed == 'non') {
        echo $translations["ALERTES_041"] . "<br/>";
    } else {
        // Si le support est activé, afficher le contenu
        ?>
        <img src="img/support/support.jpg" width="700" height="200">
        <div class="wheel" id="wheel">
            <br/>
<!--            <center><h3>--><?php //echo $translations["MOTS_197"]; ?><!--</h3></center>-->
            <br/>
        </div>
        <div class="page-header margin-top-10">
            <h4><?php echo $translations["MOTS_198"]; ?></h4>
        </div>
        <div class="section section-default padding-25">
            <div class="row">
                <form action="?page=support" method="post" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="subject"><?php echo $translations["MOTS_199"]; ?></label>
                        <input type="hidden" name="status" value="En cours">
                        <input type="text" class="form-control" name="subject" id="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="content"><?php echo $translations["MOTS_200"]; ?></label>
                        <textarea class="form-control" name="content" id="editor" rows="4" width="300"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $translations["MOTS_201"]; ?></button>
                </form>
                <script>
                    function validateForm() {
                        var editorData = CKEDITOR.instances.editor.getData();
                        var subject = document.getElementById('subject').value;
                        if (subject.trim() === "") {
                            alert("Le sujet du ticket ne peut pas être vide.");
                            return false; // Empêche l'envoi du formulaire
                        }
                        if (editorData.trim() === "") {
                            alert("Le contenu du ticket ne peut pas être vide.");
                            return false; // Empêche l'envoi du formulaire
                        }
                        return true; // Soumet le formulaire
                    };
                </script>
            </div>
        </div>
        <div class="page-header margin-top-10">
            <h4><?php echo $translations["MOTS_202"]; ?></h4>
        </div>
        <div class="page-header margin-top-10">
            <div class="section section-default padding-25">
                <div class="row">
                    <div class="wheel" id="wheel">
                        <?php
                        // Interrogez la base de données pour récupérer les tickets de l'utilisateur connecté
                        $account = $_SESSION['user'];
                        $query = $web->prepare("SELECT * FROM website_ticket_site WHERE account = ? ORDER BY date DESC");
                        $query->execute([$account]);
                        $query->execute([$account]);
                        $tickets = $query->fetchAll(PDO::FETCH_ASSOC);

                        // Afficher les tickets de l'utilisateur
                        if (count($tickets) > 0) {
                            echo '<table class="table table-bordered">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th class="text-center">' . $translations["MOTS_076"] . '</th>';
                            echo '<th class="text-center">' . $translations["MOTS_077"] . '</th>';
                            echo '<th class="text-center">' . $translations["MOTS_078"] . '</th>';
                            echo '<th class="text-center">' . $translations["MOTS_079"] . '</th>';
                            echo '<th class="text-center">' . $translations["MOTS_080"] . '</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            foreach ($tickets as $ticket) {
                                echo '<tr>';
                                echo '<td style="text-align: center;">' . $ticket['id'] . '</td>';
                                echo '<td style="text-align: center;">' . $ticket['subject'] . '</td>';
                                echo '<td style="text-align: center;">' . date('d-m-y H:i:s', strtotime($ticket['date'])) . '</td>';
                                $statusClass = ($ticket['status'] === 'En cours') ? 'reponse-open' : 'reponse-closed';
                                echo '<td style="text-align: center;"><span class="' . $statusClass . '">' . $ticket['status'] . '</span></td>';
                                echo '<td style="text-align: center;"><a href="?page=ticket&id=' .$ticket['id'] . '"><img src="img/devtool/eye.png" alt="Détails du ticket"></a></td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo $translations["INFOS_006"];
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
