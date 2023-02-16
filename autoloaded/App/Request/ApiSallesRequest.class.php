<?php

namespace App\Request;

use App\Message;

class ApiSallesRequest {
    // Attributs statiques.
    private const ENDPOINT = "https://api.bde-tribu-terre.fr/v2.0/university";
    private const PATH = "/room/";

    // Constructeur statique.
    public static function new(): ApiSallesRequest {
        return new ApiSallesRequest();
    }

    // Attributs
    // -- Aucuns --

    // Constructeur
    private function __construct() {}

    // Méthodes
    public function execute(string $name): array {
        // Initialisation d'un CurlHandle.
        $curlHandle = curl_init();

        // Initialisation des options.
        $options = array();

        // Construction des arguments GET.
        $getFields = array(
            "name" => $name
        );

        // Construction de l'URL.
        $url = self::ENDPOINT . self::PATH . '?' . http_build_query($getFields);

        // Setting du tableau des options.
        $options[CURLOPT_RETURNTRANSFER] = true;
        // $options[CURLOPT_CUSTOMREQUEST] = "POST"; À n'utiliser que lorsque ce n'est ni GET ni HEAD.
        $options[CURLOPT_URL] = $url; // Contenant les champs si la méthode n'est pas POST.
        $options[CURLOPT_HTTPHEADER] = array();
        // $options[CURLOPT_POSTFIELDS] contient les champs dont les fichiers si la méthode est POST.

        // Mise en place du tableau des options dans le CurlHandle.
        curl_setopt_array($curlHandle, $options);

        // Création de l'objet réponse.
        $response = (object) [
            "data" => curl_exec($curlHandle),
            "curlInfo" => curl_getinfo($curlHandle),
            "curlErrorNumber" => curl_errno($curlHandle),
            "curlErrorMessage" => curl_error($curlHandle)
        ];

        // Fermeture du CurlHandle
        curl_close($curlHandle);

        // Décodage du JSON.
        $response->data = json_decode($response->data, true);

        // Vérification de la réponse.
        if (
            $response->curlInfo["http_code"] != 200 &&
            !isset($response->data, $response->data["data"], $response->data["data"]["objects"])
        ) {
            Message::add("Une erreur s'est produite suite à la requête auprès de l'API de requête de salles.");
            return array();
        }

        // Retour de la réponse.
        for ($i = 0; $i < count($response->data["data"]["objects"]); $i++) {
            $response->data["data"]["objects"][$i] = (object) $response->data["data"]["objects"][$i];
        }

        return $response->data["data"]["objects"];
    }
}