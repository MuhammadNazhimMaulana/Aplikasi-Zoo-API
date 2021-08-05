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
    $app->get("/workers/", function (Request $request, Response $response) {
        $sql = "SELECT * FROM tbl_pegawai";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_pekerja" => $result], 200);
    })->add($cekAPI);

    // Get 1 Jenis Hewan
    $app->get("/workers/{id_pegawai}", function (Request $request, Response $response, $args) {
        $id_pegawai = $args["id_pegawai"];
        $sql = "SELECT * FROM tbl_pegawai WHERE id_pegawai = :id_pegawai";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_pegawai" => $id_pegawai]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data_pekerja" => $result], 200);
    })->add($cekAPI);


    //SEARCH Jenis Hewan
    $app->get("/workers/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM tbl_pegawai
        WHERE nama_pegawai LIKE '%$keyword%' OR tugas_pegawai LIKE '%$keyword%' OR usia_pegawai LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_pekerja" => $result], 200);
    })->add($cekAPI);


    //POST Jenis Hewan
    $app->post("/workers/", function (Request $request, Response $response) {

        $spesie_baru = $request->getParsedBody();

        $sql = "INSERT INTO tbl_pegawai (nama_pegawai, tugas_pegawai, usia_pegawai) VALUE (:nama_pegawai, :tugas_pegawai, :usia_pegawai)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":nama_pegawai" => $spesie_baru["nama_pegawai"],
            ":tugas_pegawai" => $spesie_baru["tugas_pegawai"],
            ":usia_pegawai" => $spesie_baru["usia_pegawai"],
        ];

        if ($data[":nama_pegawai"] == null || $data[":tugas_pegawai"] == null || $data[":usia_pegawai"] == null) {
            return $response->withJson(["status" => "Gagal", "data" => "Tidak ada yang boleh dikosongkan"], 200);
        } elseif ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Input Pegawai", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Input Pegawai", "data" => "0"], 200);
        }
    })->add($cekAPI);

    //PUT Jenis Hewan
    $app->put("/workers/{id_pegawai}", function (Request $request, Response $response, $args) {

        $id_pegawai = $args["id_pegawai"];
        $spesie_baru = $request->getParsedBody();

        $sql = "UPDATE tbl_pegawai SET nama_pegawai = :nama_pegawai, tugas_pegawai = :tugas_pegawai, usia_pegawai = :usia_pegawai WHERE id_pegawai = :id_pegawai";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_pegawai" => $id_pegawai,
            ":nama_pegawai" => $spesie_baru["nama_pegawai"],
            ":tugas_pegawai" => $spesie_baru["tugas_pegawai"],
            ":usia_pegawai" => $spesie_baru["usia_pegawai"],
        ];

        if ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Update Pegawai", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Update Pegawai", "data" => "0"], 200);
        }
    })->add($cekAPI);

    // Delete 1 Jenis Hewan
    $app->delete("/workers/{id_pegawai}", function (Request $request, Response $response, $args) {
        $id_pegawai = $args["id_pegawai"];
        $sql = "DELETE FROM tbl_pegawai WHERE id_pegawai = :id_pegawai";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_pegawai" => $id_pegawai
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Pegawai", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Menghapus Pegawai", "data" => "0"], 200);
        }
    })->add($cekAPI);
};
