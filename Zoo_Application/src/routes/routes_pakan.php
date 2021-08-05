<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    //middleware
    $cekAPI = function ($request, $response, $next) {
        $key = $request->getQueryParam("key");

        if (!isset($key)) {
            return $response->withJson(["status" => "API Key Dibutuhkan"], 401);
        }

        $sql = "SELECT * FROM tbl_pengguna WHERE api_key = :api_key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":api_key" => $key]);

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch();
            if ($result['hit'] > 55) {
                return $response->withJson(["status" => "API Key Kadaluarsa"], 401);
            } elseif ($key == $result["api_key"]) {

                // Update hit
                $sql = "UPDATE tbl_pengguna SET hit = hit + 1 WHERE api_key = :api_key";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([":api_key" => $key]);

                return $response = $next($request, $response);
            }
        }

        return $response->withJson(["status" => "Unauthorized"], 401);
    };

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    })->add($cekAPI);


    // Get Pakan
    $app->get("/foods/", function (Request $request, Response $response) {
        $sql = "SELECT * FROM tbl_pakan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_pakan" => $result], 200);
    })->add($cekAPI);

    // Get 1 Pakan
    $app->get("/foods/{id_pakan}", function (Request $request, Response $response, $args) {
        $id_pakan = $args["id_pakan"];
        $sql = "SELECT * FROM tbl_pakan WHERE id_pakan = :id_pakan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_pakan" => $id_pakan]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data_pakan" => $result], 200);
    })->add($cekAPI);


    //SEARCH Pakan
    $app->get("/foods/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM tbl_pakan
        WHERE jenis_pakan LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_pakan" => $result], 200);
    });


    //POST Pakan
    $app->post("/foods/", function (Request $request, Response $response) {

        $new_food = $request->getParsedBody();

        $sql = "INSERT INTO tbl_pakan (jenis_pakan) VALUE (:jenis_pakan)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":jenis_pakan" => $new_food["jenis_pakan"],
        ];

        if ($data[":jenis_pakan"] == null) {
            return $response->withJson(["status" => "Gagal", "data" => "Jenis Pakan Tidak Boleh Kosong"], 200);
        } elseif ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Input Pakan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Input Pakan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    //PUT Pakan
    $app->put("/foods/{id_pakan}", function (Request $request, Response $response, $args) {

        $id_pakan = $args["id_pakan"];
        $new_food = $request->getParsedBody();

        $sql = "UPDATE tbl_pakan SET jenis_pakan = :jenis_pakan WHERE id_pakan = :id_pakan";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_pakan" => $id_pakan,
            ":jenis_pakan" => $new_food["jenis_pakan"],
        ];

        if ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Update Pakan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Update Pakan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    // Delete 1 Pakan
    $app->delete("/foods/{id_pakan}", function (Request $request, Response $response, $args) {
        $id_pakan = $args["id_pakan"];
        $sql = "DELETE FROM tbl_pakan WHERE id_pakan = :id_pakan";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_pakan" => $id_pakan
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Pakan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Menghapus Pakan", "data" => "0"], 200);
        }
    })->add($cekAPI);
};
