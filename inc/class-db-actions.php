<?php

class DBActions
{
    public function createForfait($datas) {
        global $wpdb;

        $forfait_table = $wpdb->prefix. "forfait";

        $title = strip_tags($datas['title']);
        $total_time = strip_tags($datas['total_time']);
        $description = htmlspecialchars($datas['description']);
        $created_at = date('Y-m-d H:i:s', time());
        $updated_at = date('Y-m-d H:i:s', time());

        if (empty($datas['title'])) {
            $errors['title'] = 'Le titre est vide';
        }
        if (empty($datas['total_time'])) {
            $errors['total_time'] = 'Le temps total est vide';
        }
        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Prépare la requete
            $sql = $wpdb->prepare(
                "INSERT INTO {$forfait_table}
                        (title, total_time, description, created_at, updated_at) VALUES (%s,%s,%s,%s,%s )",
                $title,
                $total_time,
                $description,
                $created_at,
                $updated_at
            );
            // Execution de la requete
            $wpdb->query($sql);
            // Redirection sur url
            $_SESSION['create_success'] = "Forfait Ajouté ! ;)";
        }
    }

    public function deleteForfait($id) {
        global $wpdb;

        $table_forfait = $wpdb->prefix.'forfait';
        $table_tasks = $wpdb->prefix.'tasks';
        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);
        // Préparation de la requête
        $sqlTasks = "DELETE FROM ".$table_tasks." WHERE forfait_id={$id}";
        $sqlForfait = "DELETE FROM ".$table_forfait." WHERE id={$id}";
        // Execution de la requete
        $wpdb->query($sqlTasks);
        $wpdb->query($sqlForfait);

        $_SESSION['delete_success'] = "Forfait Supprimé !";
    }


    public function updateForfait($datas) {
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';

        // Nettoyer les données contre les injections XSS
        $id = strip_tags($datas['id']);
        $title = strip_tags($datas['title']);
        $totalTime = strip_tags($datas['total_time']);
        $description = htmlspecialchars($datas['description']);
        $updated_at = date('Y-m-d H:i:s', time());

        // Préparation de la requête
        $sql = "UPDATE $table_forfait SET 
                 title='$title', 
                 total_time='$totalTime', 
                 description='$description', 
                 updated_at='$updated_at' 
                WHERE id=$id";
        // Execution de la requete
        $wpdb->query($sql);
    }

    public function getForfaitTitleByID($forfait_id) {
        global $wpdb;

        $table_forfait = $wpdb->prefix.'forfait';

        $sql = "SELECT title FROM {$table_forfait} WHERE id=$forfait_id";
        $forfaitTitle = $wpdb->get_var($sql);

        return $forfaitTitle;
    }

    public function getTasksNumberByForfait($forfait_id) {
        global $wpdb;

        $table_forfait = $wpdb->prefix.'forfait';
        $table_tasks = $wpdb->prefix.'tasks';

        $sql = "SELECT count(*) FROM $table_tasks as tblTasks JOIN $table_forfait as tblForfait WHERE tblTasks.forfait_id=$forfait_id AND tblForfait.id=$forfait_id ";
        $forfaitCount = $wpdb->get_var($sql);

        return $forfaitCount;
    }

    /*
     * Delete a Tasks by id
     *
     */
    public function createTask($datas) {
        global $wpdb;

        $table_tasks = $wpdb->prefix.'tasks';
        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);
        // Préparation de la requête
        $sql = "DELETE FROM ".$table_tasks." WHERE id={$id}";
        // Execution de la requete
        $wpdb->query($sql);
    }

    /*
     * Delete a Tasks by id
     *
     */
    public function deleteTask($id) {
        global $wpdb;

        $table_tasks = $wpdb->prefix.'tasks';
        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);
        // Préparation de la requête
        $sql = "DELETE FROM ".$table_tasks." WHERE id={$id}";
        // Execution de la requete
        $wpdb->query($sql);
    }

    public function updateTask($datas) {
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';

        // Nettoyer les données contre les injections XSS
        $id = strip_tags($datas['id']);
        $forfait_id = strip_tags($datas['forfait_id']);
        $title = strip_tags($datas['title']);
        $taskTime = strip_tags($datas['task_time']);
        $description = htmlspecialchars($datas['description']);
        $updated_at = date('Y-m-d H:i:s', time());

        // Préparation de la requête
        $sql = "UPDATE $table_tasks SET 
                 forfait_id='$forfait_id', 
                 title='$title', 
                 task_time='$taskTime', 
                 description='$description', 
                 updated_at='$updated_at' 
                WHERE id=$id";
        // Execution de la requete
        $wpdb->query($sql);
    }

    public function getTimeTotalsForTasks($forfait_id) {
        global $wpdb;

        $table_tasks = $wpdb->prefix.'tasks';

        $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( task_time ) ) ) FROM {$table_tasks} WHERE forfait_id=$forfait_id";

        $tasksTotalTime = $wpdb->get_var($sql);

        return $tasksTotalTime;
    }

    public function getListTasks($forfait_id){
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';
        $sql = "SELECT * FROM {$table_tasks} WHERE forfait_id={$forfait_id} ORDER BY `created_at` DESC;";
        $tasksList = $wpdb->get_results($sql);
        return $tasksList;
    }

    public function getListForfaits(){
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $sql = "SELECT * FROM {$table_forfait}";
        $forfaitsList = $wpdb->get_results($sql);
        return $forfaitsList;
    }

    /** RECUPERATION D'UNE TÂCHE **/
    public function getTaskByID($id){
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';
        $sql = "SELECT * FROM $table_tasks WHERE id=$id";
        $task = $wpdb->get_results($sql);
        return $task;
    }

    /** RECUPERATION D'UN FORFAIT **/
    public function getForfaitByID($id){
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $sql = "SELECT * FROM $table_forfait WHERE id=$id";
        $forfait = $wpdb->get_results($sql);
        return $forfait;
    }


}
