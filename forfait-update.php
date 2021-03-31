<?php
function forfait_update() {
    $DBAction = new DBActions();
    ?>

    <div class="forfait-main">
        <div class="head">
            <h2>Modifier un Forfait</h2>
            <p>Sélectionner un forfait à modifier dans la liste des forfaits ou modifiez les champs pré-rempli avec les données du forfait précédement sélectionné </p>
            <div class="head-infos">
                <div class="alert-red">
                    <i class="fas fa-times-circle"></i>
                    <span>Le temps du forfait est épuisé</span>
                </div>
                <div class="alert-orange">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Le forfait n'as bientôt plus de temps</span>
                </div>
            </div>
        </div>

        <div class="custom-plugin-update">
            <?php
            if (isset($_GET['id'])) :

            global $wpdb;

            $forfait = $DBAction->getForfaitByID($_GET["id"]);
            ?>
            <div class="custom-plugin-form">
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <input type="hidden" name="id" value="<?= $forfait[0]->id ?>">
                    <div class="custom-plugin-form-fields">
                        <label for="title">Nom</label>
                        <input name="title" type="text" value="<?= $forfait[0]->title ?>" placeholder="Titre du forfait" required>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label for="total_time">Temps Total</label>
                        <input name="total_time" type="time" value="<?= $forfait[0]->total_time ?>" required>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label for="description">Description</label>
                        <textarea name="description" placeholder="Description du forfait" rows="5" required><?= $forfait[0]->description ?></textarea>
                    </div>
                    <input class="custom-plugin-submit" type="submit" name="update_forfait" value="Modifier">
                </form>
            </div>
            <?php endif; ?>
            <div class="custom-plugin-list">
                <?php
                global $wpdb;

                $forfait_table = $wpdb->prefix. "forfait";
                $tasks_table = $wpdb->prefix. "tasks";
                $forfaits = $wpdb->get_results("SELECT * FROM $forfait_table");
                $tasks = $wpdb->get_results("SELECT * FROM $tasks_table");
                ?>
                <table class="custom-table-forfait">
                    <thead>
                    <tr>
                        <th class="custom-col">ID</th>
                        <th class="custom-col">Nom</th>
                        <th class="custom-col">Description</th>
                        <th class="custom-col">Temps Total</th>
                        <th class="custom-col">Temps Restant</th>
                        <th class="custom-col">Date de création</th>
                        <th class="custom-col">Date de modification</th>
                        <th class="custom-col">Tâches Attribuées</th>
                        <th class="custom-col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($forfaits as $forfait) : ?>
                        <?php
                        $forfaitTasks = $DBAction->getListTasks($forfait->id);
                        $tasksTotalTime = $DBAction->getTimeTotalsForTasks($forfait->id);
                        $forfaitTotalTime = $forfait->total_time;

                        if ($tasksTotalTime) {
                            $totalForfait = new DateTime($forfaitTotalTime, new DateTimeZone('Europe/Paris'));
                            $totalTasks = new DateTime($tasksTotalTime, new DateTimeZone('Europe/Paris'));

                            $interval = $totalForfait->diff($totalTasks);
                            $interval = $interval->format('%H:%I');
                        } else {
                            $interval = $forfaitTotalTime;
                        }
                        $totalForfaitDisplay = $totalForfait->format('H:i');
                        $totalTasksDisplay = $totalTasks->format('H:i');
                        ?>
                        <tr id="<?= $forfait->id ?>">
                            <?php if ($interval <= '00:00') : ?>
                                <th class="alert-red" scope="row"><i class="fas fa-times-circle"></i><?= $forfait->id ?></th>
                                <th class="alert-red"><?= $forfait->title ?></th>
                                <th class="alert-red"><?= $forfait->description ?></th>
                                <th class="alert-red"><?= $forfait->total_time ?></th>
                                <th class="alert-red"><?= $interval ?></th>
                                <th class="alert-red"><?= $forfait->created_at ?></th>
                                <th class="alert-red"><?= $forfait->updated_at ?></th>
                                <th class="alert-red"><?= $DBAction->getTasksNumberByForfait($forfait->id) ?></th>
                            <?php elseif ($interval <= '01:00') : ?>
                                <th class="alert-orange" scope="row"><i class="fas fa-exclamation-circle"></i><?= $forfait->id ?></th>
                                <th class="alert-orange" ><?= $forfait->title ?></th>
                                <th class="alert-orange" ><?= $forfait->description ?></th>
                                <th class="alert-orange" ><?= $forfait->total_time ?></th>
                                <th class="alert-orange" ><?= $interval ?></th>
                                <th class="alert-orange" ><?= $forfait->created_at ?></th>
                                <th class="alert-orange" ><?= $forfait->updated_at ?></th>
                                <th class="alert-orange" ><?= $DBAction->getTasksNumberByForfait($forfait->id) ?></th>
                            <?php else : ?>
                                <th scope="row"><?= $forfait->id ?></th>
                                <th><?= $forfait->title ?></th>
                                <th><?= $forfait->description ?></th>
                                <th><?= $forfait->total_time ?></th>
                                <th><?= $interval ?></th>
                                <th><?= $forfait->created_at ?></th>
                                <th><?= $forfait->updated_at ?></th>
                                <th><?= $DBAction->getTasksNumberByForfait($forfait->id) ?></th>
                            <?php endif; ?>
                            <th>
                                <a class="update-btn-container" href="admin.php?page=modifier_forfait&id=<?= $forfait->id ?>"><button class="update-btn">Modifier</button></a>
                                <form class="delete-btn-container" action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $forfait->id ?>">
                                    <input class="delete-btn" type="submit" name="delete_forfait" value="Supprimer">
                                </form>
                            </th>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
}
?>
