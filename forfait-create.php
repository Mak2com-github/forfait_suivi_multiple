<?php
require_once('inc/class-db-actions.php');

function forfait_create() {
    $DBAction = new DBActions();
    ?>
    <div class="forfait-main">
        <div class="head">
            <?php
            if (isset($_SESSION['create_success'])) :
                echo '<div class="session-msg session-success"><p>'.$_SESSION['create_success'].'<i class="fas fa-smile"></i></p></div>';
            elseif (isset($_SESSION['delete_success'])) :
                echo '<div class="session-msg session-success"><p>'.$_SESSION['delete_success'].'<i class="fas fa-smile"></i></p></div>';
            elseif (isset($_SESSION['errors'])) :
                echo '<div class="session-msg session-alert">';
                foreach ($_SESSION['errors'] as $error) :
                    echo '<p>'.$error.'</p>';
                endforeach;
                echo '<i class="fas fa-frown"></i></div>';
            endif;
            ?>
            <h2>Ajouter un Forfait</h2>
            <p>Remplisser le formulaire pour ajouter un forfait</p>
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

        <div class="forfaits-main-container">
            <div class="custom-plugin-form">
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <div class="custom-plugin-form-fields">
                        <label for="title">Nom</label>
                        <input name="title" type="text" placeholder="Titre du forfait" required>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label for="total_time">Temps Total</label>
                        <input name="total_time" type="time" required>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label for="description">Description</label>
                        <textarea name="description" placeholder="Description du forfait" rows="5" required></textarea>
                    </div>
                    <input class="custom-plugin-submit" type="submit" name="save_forfait" value="Ajouter">
                </form>
            </div>

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
                    <?php if ($forfaits) : ?>
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

                                $totalForfaitDisplay = $totalForfait->format('H:i');
                                $totalTasksDisplay = $totalTasks->format('H:i');
                            } else {
                                $interval = $forfaitTotalTime;
                            }
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
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php
}