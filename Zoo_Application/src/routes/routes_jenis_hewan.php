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

    // Get Jenis Hewan
    $app->get("/animal_speciess/", function (Request $request, Response $response) {
        $sql = "SELECT * FROM tbl_jenis_hewan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_jenis" => $result], 200);
    })->add($cekAPI);

    // Get 1 Jenis Hewan
    $app->get("/animal_speciess/{id_jenis_hewan}", function (Request $request, Response $response, $args) {
        $id_jenis_hewan = $args["id_jenis_hewan"];
        $sql = "SELECT * FROM tbl_jenis_hewan WHERE id_jenis_hewan = :id_jenis_hewan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_jenis_hewan" => $id_jenis_hewan]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data_jenis" => $result], 200);
    })->add($cekAPI);


    //SEARCH Jenis Hewan
    $app->get("/animal_speciess/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM tbl_jenis_hewan
        WHERE nama_spesies LIKE '%$keyword%' OR ket_spesies LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_jenis" => $result], 200);
    })->add($cekAPI);


    //POST Jenis Hewan
    $app->post("/animal_speciess/", function (Request $request, Response $response) {

        $spesie_baru = $request->getParsedBody();

        $sql = "INSERT INTO tbl_jenis_hewan (nama_spesies, ket_spesies) VALUE (:nama_spesies, :ket_spesies)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":nama_spesies" => $spesie_baru["nama_spesies"],
            ":ket_spesies" => $spesie_baru["ket_spesies"],
        ];

        if ($data[":nama_spesies"] == null || $data[":ket_spesies"] == null) {
            return $response->withJson(["status" => "Gagal", "data" => "Nama Spesies dan Keterangannya Tidak Boleh Kosong"], 200);
        } elseif ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Input Jenis Hewan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Input Jenis Hewan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    //PUT Jenis Hewan
    $app->put("/animal_speciess/{id_jenis_hewan}", function (Request $request, Response $response, $args) {

        $id_jenis_hewan = $args["id_jenis_hewan"];
        $spesie_baru = $request->getParsedBody();

        $sql = "UPDATE tbl_jenis_hewan SET nama_spesies = :nama_spesies, ket_spesies = :ket_spesies WHERE id_jenis_hewan = :id_jenis_hewan";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_jenis_hewan" => $id_jenis_hewan,
            ":nama_spesies" => $spesie_baru["nama_spesies"],
            ":ket_spesies" => $spesie_baru["ket_spesies"],
        ];

        if ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Update Jenis Hewan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Update Jenis Hewan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    // Delete 1 Jenis Hewan
    $app->delete("/animal_speciess/{id_jenis_hewan}", function (Request $request, Response $response, $args) {
        $id_jenis_hewan = $args["id_jenis_hewan"];
        $sql = "DELETE FROM tbl_jenis_hewan WHERE id_jenis_hewan = :id_jenis_hewan";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_jenis_hewan" => $id_jenis_hewan
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Jenis Hewan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Menghapus Jenis Hewan", "data" => "0"], 200);
        }
    })->add($cekAPI);
};
