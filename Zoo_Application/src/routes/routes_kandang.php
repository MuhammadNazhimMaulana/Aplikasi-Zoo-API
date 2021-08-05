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

    // Get Kandang
    $app->get("/cages/", function (Request $request, Response $response) {
        $sql = "SELECT * FROM tbl_kandang";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_kandang" => $result], 200);
    })->add($cekAPI);

    // Get 1 Kandang
    $app->get("/cages/{id_kandang}", function (Request $request, Response $response, $args) {
        $id_kandang = $args["id_kandang"];
        $sql = "SELECT * FROM tbl_kandang WHERE id_kandang = :id_kandang";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_kandang" => $id_kandang]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data_kandang" => $result], 200);
    })->add($cekAPI);


    //SEARCH Kandang
    $app->get("/cages/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM tbl_kandang
        WHERE nama_kandang LIKE '%$keyword%' OR posisi LIKE '%$keyword%' OR jumlah_hewan LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_kandang" => $result], 200);
    })->add($cekAPI);


    //POST Kandang
    $app->post("/cages/", function (Request $request, Response $response) {

        $spesie_baru = $request->getParsedBody();

        $sql = "INSERT INTO tbl_kandang (nama_kandang, posisi, jumlah_hewan) VALUE (:nama_kandang, :posisi, :jumlah_hewan)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":nama_kandang" => $spesie_baru["nama_kandang"],
            ":posisi" => $spesie_baru["posisi"],
            ":jumlah_hewan" => $spesie_baru["jumlah_hewan"],
        ];

        if ($data[":nama_kandang"] == null || $data[":posisi"] == null || $data[":jumlah_hewan"] == null) {
            return $response->withJson(["status" => "Gagal", "data" => "Tidak ada yang boleh dikosongkan"], 200);
        } elseif ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Input Kandang", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Input Kandang", "data" => "0"], 200);
        }
    })->add($cekAPI);

    //PUT Kandang
    $app->put("/cages/{id_kandang}", function (Request $request, Response $response, $args) {

        $id_kandang = $args["id_kandang"];
        $spesie_baru = $request->getParsedBody();

        $sql = "UPDATE tbl_kandang SET nama_kandang = :nama_kandang, posisi = :posisi, jumlah_hewan = :jumlah_hewan WHERE id_kandang = :id_kandang";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_kandang" => $id_kandang,
            ":nama_kandang" => $spesie_baru["nama_kandang"],
            ":posisi" => $spesie_baru["posisi"],
            ":jumlah_hewan" => $spesie_baru["jumlah_hewan"],
        ];

        if ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Update Kandang", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Update Kandang", "data" => "0"], 200);
        }
    })->add($cekAPI);

    // Delete 1 Kandang
    $app->delete("/cages/{id_kandang}", function (Request $request, Response $response, $args) {
        $id_kandang = $args["id_kandang"];
        $sql = "DELETE FROM tbl_kandang WHERE id_kandang = :id_kandang";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_kandang" => $id_kandang
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Kandang", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Menghapus Kandang", "data" => "0"], 200);
        }
    })->add($cekAPI);
};
