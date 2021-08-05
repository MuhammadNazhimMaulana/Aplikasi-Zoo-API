<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

return function (App $app) {
    $container = $app->getContainer();

    //POST Saksi
    $app->post("/login/", function (Request $request, Response $response) {

        $new_login = $request->getParsedBody();

        $username = trim(strip_tags($new_login['username']));
        $password = trim(strip_tags($new_login['password']));

        $sql = "SELECT id_pengguna, username, api_key FROM tbl_pengguna WHERE username = :username AND password = :password";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":username" => $new_login["username"],
            ":password" => $new_login["password"],
        ];

        $stmt->execute($data);

        $user = $stmt->fetchObject();

        if (!$user) {

            return $response->withJson(["status" => "Gagal", "data" => "0"], 200);
        } else {

            $settings = $this->get('settings');
            $token = array(
                'id_pengguna' => $user->id_pengguna,
                'username' => $user->username
            );
            $token = JWT::encode($token, $settings['jwt']['secret'], "HS256");

            return $response->withJson(["status" => "Sukses", "Data" => $user, 'Token' => $token], 200);
        }
    });

    $app->post('/register/', function (Request $request, Response $response, array $args) {
        $input = $request->getParsedBody();
        $username = trim(strip_tags($input['username']));
        $nama_lengkap = trim(strip_tags($input['nama_lengkap']));
        $email = trim(strip_tags($input['email']));
        $password = trim(strip_tags($input['password']));
        $api_key = trim(strip_tags($input['password'] . $input['password']));
        $hit = 0;
        $sql = "INSERT INTO tbl_pengguna(username, nama_lengkap, email, password, api_key, hit) 
                VALUES(:username, :nama_lengkap, :email, :password, :api_key, :hit)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("username", $username);
        $sth->bindParam("nama_lengkap", $nama_lengkap);
        $sth->bindParam("email", $email);
        $sth->bindParam("password", $password);
        $sth->bindParam("api_key", $api_key);
        $sth->bindParam("hit", $hit);
        $StatusInsert = $sth->execute();
        if ($StatusInsert) {
            $id_pengguna = $this->db->lastInsertId();
            $settings = $this->get('settings');
            $token = array(
                'id_pengguna' =>  $id_pengguna,
                'username' => $username
            );
            $token = JWT::encode($token, $settings['jwt']['secret'], "HS256");
            $dataUser = array(
                'id_pengguna' => $id_pengguna,
                'api_key' => $api_key,
                'username' => $username
            );
            return $response->withJson(['status' => 'Sukses', 'Data_Pengguna' => $dataUser, 'token' => $token]);
        } else {
            return $response->withJson(['status' => 'error', 'Data_Pengguna' => 'error insert user.']);
        }
    });
};
