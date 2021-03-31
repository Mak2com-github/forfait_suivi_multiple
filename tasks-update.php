<?php
function tasks_update() {
    $DBAction = new DBActions();
    ?>
    <div class="forfait-main">
        <div class="head">
            <h2>Modifier une tâche</h2>
            <p>Sélectionner une tâche à modifier dans la liste des tâches ou modifiez les champs pré-rempli avec les données du forfait précédement sélectionné </p>
        </div>

        <div class="custom-plugin-update">
            <?php
            if (isset($_GET['id'])) :

                global $wpdb;

                $task = $DBAction->getTaskByID($_GET["id"]);

                $forfait_table = $wpdb->prefix. "forfait";
                $forfaits = $wpdb->get_results("SELECT * FROM $forfait_table");
                ?>
                <div class="custom-plugin-form">
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <input type="hidden" name="id" value="<?= $task[0]->id ?>">
                        <div class="custom-plugin-form-fields">
                            <label for="forfait_id">Selectionner le forfait</label>
                            <p class="post-scriptum">(Forfait sur lequelle la tâche seras déduite)</p>
                            <select class="form-control" name="forfait_id" id="forfaitSelect" required>
                                <option value="">-- Selectionner un Forfait --</option>
                                <?php foreach ($forfaits as $forfait) : ?>
                                    <option value="<?php echo $forfait->id; ?>" <?php if ($task[0]->forfait_id === $forfait->id) { echo 'selected'; } ?>><?php echo $forfait->title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="custom-plugin-form-fields">
                            <label for="title">Nom</label>
                            <input name="title" type="text" value="<?= $task[0]->title ?>" placeholder="Titre du forfait" required>
                        </div>
                        <div class="custom-plugin-form-fields">
                            <label for="total_time">Temps Total</label>
                            <input name="task_time" type="time" value="<?= $task[0]->task_time ?>" required>
                        </div>
                        <div class="custom-plugin-form-fields">
                            <label for="description">Description</label>
                            <textarea name="description" placeholder="Description du forfait" rows="5" required><?= $task[0]->description ?></textarea>
                        </div>
                        <input class="custom-plugin-submit" type="submit" name="update_task" value="Modifier">
                    </form>
                </div>
            <?php endif; ?>
            <div class="custom-plugin-list">
                <?php
                global $wpdb;

                $tasks_table = $wpdb->prefix. "tasks";
                $tasks = $wpdb->get_results("SELECT * FROM $tasks_table");
                ?>
                <table class="custom-table-forfait">
                    <thead>
                    <tr>
                        <th class="custom-col">ID</th>
                        <th class="custom-col">Nom</th>
                        <th class="custom-col">Description</th>
                        <th class="custom-col">Temps Total</th>
                        <th class="custom-col">Forfait Attribué</th>
                        <th class="custom-col">Date de création</th>
                        <th class="custom-col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task) : ?>
                        <tr id="<?= $task->id ?>">
                            <th scope="row"><?= $task->id ?></th>
                            <th><?= $task->title ?></th>
                            <th><?= $task->description ?></th>
                            <th><?= $task->task_time ?></th>
                            <th><?= $DBAction->getForfaitTitleByID($task->forfait_id) ?></th>
                            <th><?= $task->created_at ?></th>
                            <th>
                                <a class="update-btn-container" href="admin.php?page=modifier_tache&id=<?= $task->id ?>"><button class="update-btn">Modifier</button></a>
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
