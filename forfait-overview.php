<?php
require_once('inc/class-db-actions.php');

function forfait_overview() {
    ?>
    <div>
        <div class="overview-head">
            <h2>Vue Générale</h2>
            <p>Liste de tous les forfaits et taches</p>
            <p class="post-scriptum">Ici vous pouvez modifier ou supprimer une tâche, et consulter les informations, modifier ou supprimer un forfait selectionné</p>
        </div>

        <div>
            <?php
            global $wpdb;

            $forfait_table = $wpdb->prefix. "forfait";
            $tasks_table = $wpdb->prefix. "tasks";
            $forfaits = $wpdb->get_results("SELECT * FROM $forfait_table");
            $tasks = $wpdb->get_results("SELECT * FROM $tasks_table");
            ?>
            <div class="overview-filters-container">
                <h3>Les Forfaits</h3>
                <p>Filtrer la liste des tâches par forfaits</p>
                <div class="overview-filters-forfaits">
                    <?php foreach ($forfaits as $forfait) : ?>
                        <div class="overview-forfaits-btn">
                            <button class="forfait-custom-btn" id="<?= $forfait->id ?>"><?= $forfait->title ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overview-list-container">
                <div class="overview-forfaits-infos">
                    <?php foreach ($forfaits as $forfait) : ?>
                        <div class="selected-forfait-datas <?= $forfait->id ?>">
                            <?php $DBAction = new DBActions(); ?>
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
                            <?php if ($interval <= '00:00') : ?>
                                <div class="selected-forfait-alert">
                                    <p>Il ne reste plus de temps sur ce forfait !</p>
                                </div>
                            <?php elseif ($interval <= '01:00') : ?>
                                <div class="selected-forfait-alert">
                                    <p>Attention !</br> Le temps de ce forfait est bientôt épuisé !</p>
                                </div>
                            <?php endif; ?>
                            <div class="selected-forfait-datas-group">
                                <p>Nom du Forfait: </p>
                                <p><?= $forfait->title ?></p>
                            </div>
                            <div class="selected-forfait-datas-group">
                                <p>Temps Total: </p>
                                <p><?= $forfait->total_time ?></p>
                            </div>
                            <div class="selected-forfait-datas-group">
                                <p>Nombres de tâches attribuées: </p>
                                <p><?= $DBAction->getTasksNumberByForfait($forfait->id) ?></p>
                            </div>
                            <div class="selected-forfait-datas-group">
                                <p>Total temps des tâches :</p>
                                <p><?= $totalTasksDisplay ?></p>
                            </div>
                            <div class="selected-forfait-datas-group">
                                <p>Temps Restant: </p>
                                <p><?= $interval ?></p>
                            </div>
                            <div class="selected-forfait-datas-actions">
                                <a class="update-btn-container" href="admin.php?page=modifier_forfait&id=<?= $forfait->id ?>"><button class="update-btn">Modifier</button></a>
                                <form class="delete-btn-container" action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $forfait->id ?>">
                                    <input class="delete-btn" type="submit" name="delete_forfait" value="Supprimer">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <table class="custom-table-overview">
                    <thead>
                    <tr>
                        <th class="custom-col">ID</th>
                        <th class="custom-col">Nom</th>
                        <th class="custom-col">Liée au forfait</th>
                        <th class="custom-col">Description</th>
                        <th class="custom-col">Durée de la tâche</th>
                        <th class="custom-col">Date de création</th>
                        <th class="custom-col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task) : ?>
                        <tr class="overview-tasks <?= $task->forfait_id ?>">
                            <th scope="row"><?= $task->id ?></th>
                            <th><?= $task->title ?></th>
                            <?php $forfaitTitle = new DBActions(); ?>
                            <th><?= $forfaitTitle->getForfaitTitleByID($task->forfait_id) ?></th>
                            <th><?= $task->description ?></th>
                            <th><?= $task->task_time ?></th>
                            <th><?= $task->created_at ?></th>
                            <th>
                                <a class="update-btn-container" href="admin.php?page=modifier_tache&id=<?= $task->id ?>"><button class="update-btn">Modifier</button></a>
                                <form class="delete-btn-container" action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $task->id ?>">
                                    <input class="delete-btn" type="submit" name="delete_task" value="Supprimer">
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
