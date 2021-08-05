<?php

use Slim\App;

return function (App $app) {
    // e.g: $app->add(new \Slim\Csrf\Guard);

    //middleware untuk validasi token JWT 
    $app->add(new Tuupola\Middleware\JwtAuthentication([
        "path" => ["/cages", "/foods", "/animals", "/cares", "/animal_speciess"], /* or ["/api", "/admin"] */
        "secure" => false,
        "attribute" => "decoded_token_data",
        "secret" => "inikatakuncirahasianya",
        "algorithm" => ["HS256"],
        "error" => function ($res, $args) {
            $data["status"] = "error";
            $data["message"] = $args["message"];
            return $res
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    ]));
};
