<?php
// Routes
$app->group('/api/v1', function () use ($app) {

    // get all tasks
    $app->get('/tasks', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM tasks ORDER BY task");
        $sth->execute();
        $tasks = $sth->fetchAll();
        return $this->response->withJson($tasks);
    })->add( new AuthenticationMiddleware() );

    // Retrieve task with id
    $app->get('/task/[{id}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM tasks WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $tasks = $sth->fetchObject();
        return $this->response->withJson($tasks);
    })->add( new AuthenticationMiddleware() );


    // Search for task with given search teram in their name
    $app->get('/tasks/search/[{query}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM tasks WHERE UPPER(task) LIKE :query ORDER BY task");
        $query = "%".$args['query']."%";
        $sth->bindParam("query", $query);
        $sth->execute();
        $tasks = $sth->fetchAll();
        return $this->response->withJson($tasks);
    });

    // Add a new task
    $app->post('/task', function ($request, $response) {
        $input = $request->getParsedBody();
        $sql = "INSERT INTO tasks (task) VALUES (:task)";
         $sth = $this->db->prepare($sql);
        $sth->bindParam("task", $input['task']);
        $sth->execute();
        $input['id'] = $this->db->lastInsertId();
        return $this->response->withJson($input);
    });


    // DELETE a task with given id
    $app->delete('/task/[{id}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("DELETE FROM tasks WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $tasks = $sth->fetchAll();
        return $this->response->withJson($tasks);
    });

    // Update task with given id
    $app->put('/task/[{id}]', function ($request, $response, $args) {
        $input = $request->getParsedBody();
        $sql = "UPDATE tasks SET task=:task WHERE id=:id";
         $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $args['id']);
        $sth->bindParam("task", $input['task']);
        $sth->execute();
        $input['id'] = $args['id'];
        return $this->response->withJson($input);
    });

});
