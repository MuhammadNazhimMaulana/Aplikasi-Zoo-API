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

    // Get Hewan
    $app->get("/animals/", function (Request $request, Response $response) {
        $sql = "SELECT 
        a.id_hewan,
        a.id_jenis_hewan,
        b.nama_spesies as hewan,
        a.nama_hewan,
        a.usia_hewan 
    FROM tbl_hewan a
    JOIN 
        tbl_jenis_hewan b ON a.id_jenis_hewan = b.id_jenis_hewan
    ORDER BY 
        a.id_hewan ASC ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_hewan" => $result], 200);
    })->add($cekAPI);

    // Get 1 Hewan
    $app->get("/animals/{id_hewan}", function (Request $request, Response $response, $args) {
        $id_hewan = $args["id_hewan"];
        $sql = "SELECT 
        a.id_hewan,
        a.id_jenis_hewan,
        b.nama_spesies as hewan,
        a.nama_hewan,
        a.usia_hewan 
    FROM tbl_hewan a
    JOIN 
        tbl_jenis_hewan b ON a.id_jenis_hewan = b.id_jenis_hewan
    WHERE a.id_hewan = :id_hewan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_hewan" => $id_hewan]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data_hewan" => $result], 200);
    })->add($cekAPI);


    //SEARCH Hewan
    $app->get("/animals/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT 
        a.id_hewan,
        a.id_jenis_hewan,
        b.nama_spesies as hewan,
        a.nama_hewan,
        a.usia_hewan 
    FROM tbl_hewan a
    JOIN 
        tbl_jenis_hewan b ON a.id_jenis_hewan = b.id_jenis_hewan
    WHERE a.id_jenis_hewan LIKE '%$keyword%' OR a.nama_hewan  LIKE '%$keyword%' OR a.usia_hewan LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_hewan" => $result], 200);
    })->add($cekAPI);


    //POST Hewan
    $app->post("/animals/", function (Request $request, Response $response) {

        $spesie_baru = $request->getParsedBody();

        $sql = "INSERT INTO tbl_hewan (id_jenis_hewan, nama_hewan, usia_hewan) VALUE (:id_jenis_hewan, :nama_hewan, :usia_hewan)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_jenis_hewan" => $spesie_baru["id_jenis_hewan"],
            ":nama_hewan" => $spesie_baru["nama_hewan"],
            ":usia_hewan" => $spesie_baru["usia_hewan"],
        ];

        if ($data[":id_jenis_hewan"] == null || $data[":nama_hewan"] == null || $data[":usia_hewan"] == null) {
            return $response->withJson(["status" => "Gagal", "data" => "Tidak ada yang boleh dikosongkan"], 200);
        } elseif ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Input Hewan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Input Hewan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    //PUT Hewan
    $app->put("/animals/{id_hewan}", function (Request $request, Response $response, $args) {

        $id_hewan = $args["id_hewan"];
        $spesie_baru = $request->getParsedBody();

        $sql = "UPDATE tbl_hewan SET id_jenis_hewan = :id_jenis_hewan, nama_hewan = :nama_hewan , usia_hewan = :usia_hewan WHERE id_hewan = :id_hewan";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_hewan" => $id_hewan,
            ":id_jenis_hewan" => $spesie_baru["id_jenis_hewan"],
            ":nama_hewan" => $spesie_baru["nama_hewan"],
            ":usia_hewan" => $spesie_baru["usia_hewan"],
        ];

        if ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Update Hewan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Update Hewan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    // Delete 1 Hewan
    $app->delete("/animals/{id_hewan}", function (Request $request, Response $response, $args) {
        $id_hewan = $args["id_hewan"];
        $sql = "DELETE FROM tbl_hewan WHERE id_hewan = :id_hewan";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_hewan" => $id_hewan
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Hewan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Menghapus Hewan", "data" => "0"], 200);
        }
    })->add($cekAPI);
};
