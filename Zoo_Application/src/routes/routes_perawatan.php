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

    // Get Perawatan
    $app->get("/cares/", function (Request $request, Response $response) {
        $sql = "SELECT 
        a.id_perawatan,
        a.id_kandang,
        b.nama_kandang as kandang,
        a.id_hewan,
        c.nama_hewan as hewan,
        a.id_pegawai,
        d.nama_pegawai as pegawai,
        a.id_pakan,
        e.jenis_pakan as pakan,
        a.nama_kegiatan,
        a.keterangan,
        a.tanggal_kegiatan 
    FROM tbl_perawatan a
    JOIN 
        tbl_kandang b ON a.id_kandang = b.id_kandang
    JOIN 
        tbl_hewan c ON a.id_hewan = c.id_hewan
    JOIN 
        tbl_pegawai d ON a.id_pegawai = d.id_pegawai
    JOIN 
        tbl_pakan e ON a.id_pakan = e.id_pakan
    ORDER BY 
        a.id_perawatan ASC ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_perawatan" => $result], 200);
    })->add($cekAPI);

    // Get 1 Perawatan
    $app->get("/cares/{id_perawatan}", function (Request $request, Response $response, $args) {
        $id_perawatan = $args["id_perawatan"];
        $sql = "SELECT 
        a.id_perawatan,
        a.id_kandang,
        b.nama_kandang as kandang,
        a.id_hewan,
        c.nama_hewan as hewan,
        a.id_pegawai,
        d.nama_pegawai as pegawai,
        a.id_pakan,
        e.jenis_pakan as pakan,
        a.nama_kegiatan,
        a.keterangan,
        a.tanggal_kegiatan 
    FROM tbl_perawatan a
    JOIN 
        tbl_kandang b ON a.id_kandang = b.id_kandang
    JOIN 
        tbl_hewan c ON a.id_hewan = c.id_hewan
    JOIN 
        tbl_pegawai d ON a.id_pegawai = d.id_pegawai
    JOIN 
        tbl_pakan e ON a.id_pakan = e.id_pakan
    WHERE a.id_perawatan = :id_perawatan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_perawatan" => $id_perawatan]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data_perawatan" => $result], 200);
    })->add($cekAPI);


    //SEARCH Perawatan
    $app->get("/cares/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT 
        a.id_perawatan,
        a.id_kandang,
        b.nama_kandang as kandang,
        a.id_hewan,
        c.nama_hewan as hewan,
        a.id_pegawai,
        d.nama_pegawai as pegawai,
        a.id_pakan,
        e.jenis_pakan as pakan,
        a.nama_kegiatan,
        a.keterangan,
        a.tanggal_kegiatan 
    FROM tbl_perawatan a
    JOIN 
        tbl_kandang b ON a.id_kandang = b.id_kandang
    JOIN 
        tbl_hewan c ON a.id_hewan = c.id_hewan
    JOIN 
        tbl_pegawai d ON a.id_pegawai = d.id_pegawai
    JOIN 
        tbl_pakan e ON a.id_pakan = e.id_pakan
    WHERE a.nama_kegiatan LIKE '%$keyword%' OR a.keterangan  LIKE '%$keyword%' OR a.tanggal_kegiatan LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data_perawatan" => $result], 200);
    })->add($cekAPI);


    //POST Perawatan
    $app->post("/cares/", function (Request $request, Response $response) {

        $new_care = $request->getParsedBody();

        $sql = "INSERT INTO tbl_perawatan (id_kandang, id_hewan, id_pegawai, id_pakan, nama_kegiatan, keterangan, tanggal_kegiatan) VALUE (:id_kandang, :id_hewan, :id_pegawai, :id_pakan, :nama_kegiatan, :keterangan, :tanggal_kegiatan)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_kandang" => $new_care["id_kandang"],
            ":id_hewan" => $new_care["id_hewan"],
            ":id_pegawai" => $new_care["id_pegawai"],
            ":id_pakan" => $new_care["id_pakan"],
            ":nama_kegiatan" => $new_care["nama_kegiatan"],
            ":keterangan" => $new_care["keterangan"],
            ":tanggal_kegiatan" => $new_care["tanggal_kegiatan"],
        ];

        if ($data[":id_kandang"] == null || $data[":id_hewan"] == null || $data[":id_pegawai"] == null || $data[":id_pakan"] == null) {
            return $response->withJson(["status" => "Gagal", "data" => "Tidak ada ID yang boleh dikosongkan"], 200);
        } elseif ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Input Perawatan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Input Perawatan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    //PUT Perawatan
    $app->put("/cares/{id_perawatan}", function (Request $request, Response $response, $args) {

        $id_perawatan = $args["id_perawatan"];
        $new_care = $request->getParsedBody();

        $sql = "UPDATE tbl_perawatan SET id_kandang = :id_kandang, id_hewan = :id_hewan , id_pegawai = :id_pegawai, id_pakan = :id_pakan, nama_kegiatan = :nama_kegiatan, keterangan = :keterangan, tanggal_kegiatan = :tanggal_kegiatan WHERE id_perawatan = :id_perawatan";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_perawatan" => $id_perawatan,
            ":id_kandang" => $new_care["id_kandang"],
            ":id_hewan" => $new_care["id_hewan"],
            ":id_pegawai" => $new_care["id_pegawai"],
            ":id_pakan" => $new_care["id_pakan"],
            ":nama_kegiatan" => $new_care["nama_kegiatan"],
            ":keterangan" => $new_care["keterangan"],
            ":tanggal_kegiatan" => $new_care["tanggal_kegiatan"],
        ];

        if ($stmt->execute($data)) {
            return $response->withJson(["status" => "Sukses Update Perawatan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Update Perawatan", "data" => "0"], 200);
        }
    })->add($cekAPI);

    // Delete 1 Perawatan
    $app->delete("/cares/{id_perawatan}", function (Request $request, Response $response, $args) {
        $id_perawatan = $args["id_perawatan"];
        $sql = "DELETE FROM tbl_perawatan WHERE id_perawatan = :id_perawatan";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_perawatan" => $id_perawatan
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Perawatan", "data" => "1"], 200);
        } else {
            return $response->withJson(["status" => "Gagal Menghapus Perawatan", "data" => "0"], 200);
        }
    })->add($cekAPI);
};
