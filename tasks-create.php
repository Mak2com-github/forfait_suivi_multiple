<?php
require_once('inc/class-db-actions.php');

function tasks_create() {
    global $interval;
    $DBAction = new DBActions();
    ?>
    <div class="forfait-main">
        <div class="head">
            <h2>Ajouter une Tâche</h2>
            <p>Remplisser le formulaire pour ajouter une tâche sur un forfait</p>
        </div>

        <div class="forfaits-main-container">
            <?php
            if (isset($_POST['save_task'])) {
                global $wpdb;

                $tasks_table = $wpdb->prefix. "tasks";

                $forfait_id = $_POST['forfait_id'];
                $title = strip_tags($_POST['title']);
                $total_time = strip_tags($_POST['task_time']);
                $description = htmlspecialchars($_POST['description']);
                $created_at = date('Y-m-d H:i:s', time());
                $updated_at = date('Y-m-d H:i:s', time());

                if (empty($_POST['title'])) {
                    $errors['title'] = 'Le titre est vide';
                }
                if (empty($_POST['task_time'])) {
                    $errors['task_time'] = 'Le temps total est vide';
                }
                if (empty($_POST['description'])) {
                    $errors['description'] = 'La description est vide';
                }

                if (!empty($errors)) {
                    echo '<div id="forfaitAlertBloc" class="forfait-submit-alert">';
                    echo '<ul>';
                    foreach ($errors as $error) {
                        echo '<li>'.$error.'</li>';
                    }
                    echo '</ul>';
                    echo '<button id="forfaitAlertClose">X</button>';
                    echo '</div>';
                } else {
                    // Prépare la requete
                    $sql = $wpdb->prepare(
                        "INSERT INTO {$tasks_table}
                        (forfait_id, title, task_time, description, created_at, updated_at) VALUES (%d,%s,%s,%s,%s,%s )",
                        $forfait_id,
                        $title,
                        $total_time,
                        $description,
                        $created_at,
                        $updated_at
                    );

                    // Execution de la requete
                    $wpdb->query($sql);
                    // Redirection sur url
                    echo '<script> document.location.reload(); </script>';
                }
            }
            ?>
            <div class="custom-plugin-form">
                <?php
                global $wpdb;

                $forfait_table = $wpdb->prefix. "forfait";
                $forfaits = $wpdb->get_results("SELECT * FROM $forfait_table")
                ?>
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <div class="custom-plugin-form-fields">
                        <label for="forfait_id">Selectionner le forfait</label>
                        <p class="post-scriptum">(Forfait sur lequelle la tâche seras déduite)</p>
                        <select class="form-control" name="forfait_id" id="forfaitSelect" onchange="toggle_by_class_name(value); selectForfaitTimeCheck(value)" required>
                            <option value="">-- Selectionner un Forfait --</option>
                            <?php foreach ($forfaits as $forfait) : ?>
                                <?php
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
                                ?>
                                <?php if ($interval <= '00:00') : ?>
                                    <option id="forfait<?= $forfait->id ?>" class="alert-red" value="<?= $forfait->id; ?>" data-time="<?= $interval ?>"><?= $forfait->title ?> N'as plus de temps</option>
                                <?php elseif ($interval <= '01:00') : ?>
                                    <option id="forfait<?= $forfait->id ?>" class="alert-orange" value="<?= $forfait->id; ?>" data-time="<?= $interval ?>"><?= $forfait->title ?> N'as presque plus de temps</option>
                                <?php else : ?>
                                    <option id="forfait<?= $forfait->id ?>" class="<?= $forfait->id ?>" value="<?= $forfait->id; ?>" data-time="<?= $interval ?>"><?php echo $forfait->title; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label for="title">Nom</label>
                        <input name="title" type="text" placeholder="Titre de la tâche" required>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label id="taskTimeLabel" for="task_time">Durée</label>
                        <input id="taskTimeInput" name="task_time" type="time" required>
                    </div>
                    <div class="custom-plugin-form-fields">
                        <label for="description">Description</label>
                        <textarea name="description" placeholder="Description de la tâche" rows="5" required></textarea>
                    </div>
                    <input id="addTaskSubmit" class="custom-plugin-submit" type="submit" name="save_task" value="Ajouter">
                </form>
            </div>

            <div class="forfaits-list">
                <?php
                global $wpdb;

                $forfait_table = $wpdb->prefix. "forfait";
                $tasks_table = $wpdb->prefix. "tasks";
                $forfaits = $wpdb->get_results("SELECT * FROM $forfait_table");
                $tasks = $wpdb->get_results("SELECT * FROM $tasks_table");
                ?>
                <p class="post-scriptum">Tâches déjà présentes sur le forfait sélectionné :</p>
                <table class="custom-table-tasks">
                    <thead>
                    <tr>
                        <th class="custom-col">ID</th>
                        <th class="custom-col">Nom</th>
                        <th class="custom-col">Description</th>
                        <th class="custom-col">Durée</th>
                        <th class="custom-col">Date de création</th>
                        <th class="custom-col">Forfait attribué</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task) : ?>
                        <tr class="<?= $task->forfait_id; ?>">
                            <th scope="row"><?= $task->id ?></th>
                            <th><?= $task->title ?></th>
                            <th><?= $task->description ?></th>
                            <th><?= $task->task_time ?></th>
                            <th><?= $task->created_at ?></th>
                            <?php $forfaitTitle = new DBActions(); ?>
                            <th><?= $forfaitTitle->getForfaitTitleByID($task->forfait_id) ?></th>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php
}